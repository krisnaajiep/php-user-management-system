<?php

class Authentication extends Database
{
  private $table = "user_accounts", $username, $email, $password;

  public function login(Request $request): bool
  {
    $conn = $this->getConnection();
    $stmt = null;

    $validatedData = Validator::setRules($request->post(), [
      "username" => ["required"],
      "password" => ["required"]
    ]);

    if (!$validatedData) return false;

    $this->username = $validatedData["username"];
    $this->password = $validatedData["password"];

    try {
      $query = "SELECT id, username, password, status FROM {$this->table} WHERE BINARY username = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $this->username);

      $stmt->execute();

      $result = $stmt->get_result();

      if ($result->num_rows === 0)
        throw new Exception("Invalid Username or Password", 1);

      $user_account = $result->fetch_object();

      if (!password_verify($this->password, $user_account->password))
        throw new Exception("Invalid Username or Password", 1);

      if ($user_account->status === "inactive")
        throw new Exception("Your account is inactive", 2);

      if ($user_account->status === "banned")
        throw new Exception("Your account is banned", 3);

      Auth::setLogin(true);
      Auth::setUserAccountId($user_account->id);

      if ($request->post("remember_me") !== null) {
        setcookie("id", $user_account->id, time() + (86400 * 30));
        setcookie("key", hash("sha256", $user_account->username), time() + (86400 * 30));
      }

      return true;
    } catch (\Throwable $th) {
      $action = $th->getCode() === 2 ? "Please check your email to activate your account." : ($th->getCode() === 3 ? "Please contact the moderator or admin." : "Please try again.");
      $type = $th->getCode() === 3 ? "danger" : "warning";

      Flasher::setFlash($th->getMessage() . ".", $action, $type);

      return false;
    } finally {
      if ($stmt) $stmt->close();
      if ($conn) $conn->close();
    }
  }

  public function logout(): void
  {
    $_SESSION = [];

    session_unset();
    session_destroy();

    setcookie("id", "", time() - 3600);
    setcookie("key", "", time() - 3600);

    header("Location: login.php");
    exit;
  }
}
