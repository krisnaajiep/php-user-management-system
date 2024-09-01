<?php

class ResetPassword extends Database
{
  private $table = "password_resets", $user_account_id, $token, $expires;

  public function __construct()
  {
    $conn = $this->getConnection();

    $query = "SHOW TABLES LIKE '{$this->table}'";
    $result = $conn->query($query);

    if ($result->fetch_object() === null) {
      if (!$this->createTable($conn)) {
        Flasher::setFlash("Error creating password resets table.", "", "danger");
        header("Location: login.php");
        exit;
      }
    }
  }

  private function createTable(mysqli $conn): bool
  {
    $query = "CREATE TABLE {$this->table} (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_account_id INT(11) UNSIGNED NOT NULL,
                token VARCHAR(64) NOT NULL,
                expires INT(11) NOT NULL,
                FOREIGN KEY (user_account_id) REFERENCES user_accounts(id) ON DELETE CASCADE
              )";

    if (!$conn->query($query)) return false;

    return true;
  }

  public function sendResetLink(Request $request): bool
  {
    $conn = $this->getConnection();
    $stmt = null;

    $validatedData = Validator::setRules($request->post(), [
      "email" => ["required", "email"]
    ]);

    if (!$validatedData) return false;

    try {
      $query = "SELECT id, status FROM user_accounts WHERE BINARY email = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $validatedData["email"]);

      $stmt->execute();

      $result = $stmt->get_result();

      if ($result->num_rows === 0)
        throw new Exception("Email is not registered");

      $stmt->execute();

      $stmt->bind_result($id, $status);

      while ($stmt->fetch()) {
        if ($status === "inactive")
          throw new Exception("Your account is inactive. Please activate your account first");

        if ($status === "banned")
          throw new Exception("Your account is banned. Please contact the moderator or admin");

        $this->user_account_id = $id;
      }

      $this->token = bin2hex(random_bytes(32));
      $this->expires = date("U") + 3600;

      $query = "INSERT INTO {$this->table} (user_account_id, token, expires) VALUES (?, ?, ?)";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("isi", $this->user_account_id, $this->token, $this->expires);

      if (!$stmt->execute())
        throw new Exception("Error inserting password reset token.");

      $reset_link = "http://localhost/latihan/project-sederhana/user_management_system/public/reset-password.php?token=" . $this->token;
      $subject = "Password Reset Request";
      $message = "To reset your password, please click on the following link: $reset_link";

      if (!mail($validatedData["email"], $subject, $message))
        throw new Exception("Error sending reset password link");

      return true;
    } catch (\Throwable $th) {
      Flasher::setFlash($th->getMessage(), "", "warning");

      return false;
    } finally {
      if ($stmt) $stmt->close();
      if ($conn) $conn->close();
    }
  }

  public function updatePassword(Request $request): bool
  {
    $conn = $this->getConnection();
    $stmt = null;

    $validatedData = Validator::setRules($request->post(), [
      "new_password" => ["required", "min_length:8", "match:repeat_new_password"],
      "repeat_new_password" => ["required", "match:new_password"]
    ]);

    if (!$validatedData) return false;

    unset($validatedData["repeat_new_password"]);

    $this->token = $request->post("token");

    $conn->begin_transaction();

    try {
      $query = "SELECT user_account_id FROM {$this->table} WHERE token = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $this->token);

      $stmt->execute();

      $result = $stmt->get_result();

      if ($result->num_rows === 0)
        throw new Exception("Invalid or expired token.");

      $this->user_account_id = $result->fetch_object()->user_account_id;

      $new_password = password_hash($validatedData["new_password"], PASSWORD_DEFAULT);

      $query = "UPDATE user_accounts SET password = ? WHERE id = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("si", $new_password, $this->user_account_id);
      if (!$stmt->execute())
        throw new mysqli_sql_exception("Error updating password");

      $query = "DELETE FROM {$this->table} WHERE token = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $this->token);

      if (!$stmt->execute())
        throw new mysqli_sql_exception("Error deleting token");

      $conn->commit();

      return true;
    } catch (\Throwable $th) {
      $conn->rollback();

      Flasher::setFlash($th->getMessage(), "", "warning");

      return false;
    } finally {
      if ($stmt) $stmt->close();
      if ($conn) $conn->close();
    }
  }

  public function deleteExpiredTokens(): void
  {
    $conn = $this->getConnection();
    $stmt = null;

    $now = date("U");

    try {
      $query = "DELETE FROM {$this->table} WHERE expires < ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("i", $now);

      if (!$stmt->execute()) throw new Exception("Error deleting expired password reset tokens");
    } catch (\Throwable $th) {
      Flasher::setFlash($th->getMessage(), "", "danger");
    } finally {
      if ($stmt) $stmt->close();
      if ($conn) $conn->close();
    }
  }
}
