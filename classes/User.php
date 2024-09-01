<?php

class User extends Database
{
  private $table = "user_accounts", $user_account_id, $username, $email, $password, $role, $status, $full_name, $phone_number, $address, $bio, $profile_picture;

  public function __construct()
  {
    $conn = $this->getConnection();

    try {
      $query = "SHOW TABLES LIKE '{$this->table}'";
      $result = $conn->query($query);

      if ($result->fetch_object() === null) {
        if (!$this->createUserAccountsTable($conn))
          throw new Exception("Error creating user accounts table.");
      }

      $query = "SHOW TABLES LIKE 'user_profiles'";
      $result = $conn->query($query);

      if ($result->fetch_object() === null) {
        if (!$this->createUserProfilesTable($conn))
          throw new Exception("Error creating user profiles table.");
      }
    } catch (\Throwable $th) {
      Flasher::setFlash($th->getMessage(), "", "danger");

      header("Location: login.php");
      exit;
    } finally {
      if ($conn) $conn->close();
    }
  }

  private function createUserAccountsTable(mysqli $conn): bool
  {
    $query = "CREATE TABLE {$this->table} (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL,
                email VARCHAR(100) NOT NULL,
                password VARCHAR(255) NOT NULL,
                role_id INT(11) UNSIGNED NOT NULL,
                status ENUM('active', 'inactive', 'banned') NOT NULL DEFAULT 'inactive',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE (username, email),
                FOREIGN KEY (role_id) REFERENCES user_roles(id)
              )";

    if (!$conn->query($query)) return false;

    return true;
  }

  private function createUserProfilesTable(mysqli $conn): bool
  {
    $query = "CREATE TABLE user_profiles (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_account_id INT(11) UNSIGNED NOT NULL UNIQUE,
                full_name VARCHAR(100) NOT NULL,
                phone_number VARCHAR(15) NOT NULL,
                address TEXT NOT NULL,
                bio TEXT NOT NULL,
                profile_picture VARCHAR(255) NOT NULL,
                FOREIGN KEY (user_account_id) REFERENCES user_accounts(id) ON DELETE CASCADE
              )";

    if (!$conn->query($query)) return false;

    return true;
  }

  public function register(Request $request): bool
  {
    $conn = $this->getConnection();
    $stmt = null;

    $validatedData = Validator::setRules($request->post(), [
      "username" => ["required", "min_length:8"],
      "email" => ["required", "email"],
      "password" => ["required", "min_length:8", "match:repeat_password"],
      "repeat_password" => ["required", "match:password"]
    ]);

    if (!$validatedData) return false;

    unset($validatedData["repeat_password"]);

    $this->username = $validatedData["username"];
    $this->email = $validatedData["email"];
    $this->password = password_hash($validatedData["password"], PASSWORD_DEFAULT);
    $this->role = 3;

    $conn->begin_transaction();

    try {
      $query = "SELECT id FROM {$this->table}";
      $result = $conn->query($query);

      if ($result->num_rows === 0) $this->role = 1;

      $query = "INSERT INTO {$this->table} (username, email, password, role_id) VALUES (?, ?, ?, ?)";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("sssi", $this->username, $this->email, $this->password, $this->role);

      if (!$stmt->execute())
        throw new mysqli_sql_exception("Error inserting user account.");


      $last_id = $conn->insert_id;
      $default_profile_picture = "default.jpg";

      $query = "INSERT INTO user_profiles (user_account_id, profile_picture) VALUES (?, ?)";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("is", $last_id, $default_profile_picture);

      if (!$stmt->execute())
        throw new mysqli_sql_exception("Error inserting user profile.");

      $conn->commit();

      $account_activation = new AccountActivation();
      if (!$account_activation->sendActivationLink($last_id)) return false;

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

  public function readAll(): array
  {
    $conn = $this->getConnection();

    $query = "SELECT * FROM {$this->table} 
              LEFT JOIN user_roles ON {$this->table}.role_id = user_roles.id
              LEFT JOIN user_profiles ON {$this->table}.id = user_profiles.user_account_id";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    $result = $stmt->get_result();
    $rows = [];

    while ($row = $result->fetch_object()) {
      $rows[] = $row;
    }

    $stmt->close();
    $conn->close();

    return $rows;
  }

  public function readOne(Request $request)
  {
    $conn = $this->getConnection();

    $this->username = $request->get("username");

    $query = "SELECT * FROM {$this->table} 
              LEFT JOIN user_roles ON {$this->table}.role_id = user_roles.id
              LEFT JOIN user_profiles ON {$this->table}.id = user_profiles.user_account_id
              WHERE {$this->table}.username = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $this->username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
      header("Location: users.php");
      exit;
    }

    $row = $result->fetch_object();

    return $row;
  }

  public function updateAccount(Request $request): bool
  {
    $conn = $this->getConnection();
    $stmt = null;

    $auth = new Auth();

    try {
      $validatedData = Validator::setRules($request->post(), [
        "username" => ["required", "min_length:8"],
        "email" => ["required", "email"]
      ]);

      if (!$validatedData) return false;

      if (!empty($request->post("old_password")) || !empty($request->post("new_password")) || !empty($request->post("repeat_new_password"))) {
        if (!$this->updatePassword($request)) return false;
      } elseif (
        $validatedData["username"] === $auth->getUser()->username &&
        $validatedData["email"] === $auth->getUser()->email
      ) {
        throw new Exception("No data is changed.", 2);
      }

      $this->user_account_id = $auth->getUser()->user_account_id;
      $this->username = $validatedData["username"];
      $this->email = $validatedData["email"];
      $query = "UPDATE {$this->table} SET username = ?, email = ? WHERE id = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("ssi", $this->username, $this->email, $this->user_account_id);

      if (!$stmt->execute())
        throw new Exception("Error updating user account.");

      return true;
    } catch (\Throwable $th) {
      $errorType = $th->getCode() === 2 ? "warning" : "danger";
      Flasher::setFlash($th->getMessage(), "", $errorType);

      return false;
    } finally {
      if ($stmt) $stmt->close();
      if ($conn) $conn->close();
    }
  }

  public function updatePassword(Request $request): bool
  {
    $conn = $this->getConnection();

    $validatedData = Validator::setRules($request->post(), [
      "old_password" => ["required", "old_password"],
      "new_password" => ["required", "min_length:8", "match:repeat_new_password"],
      "repeat_new_password" => ["required", "match:new_password"]
    ]);

    if (!$validatedData) return false;

    unset($validatedData["repeat_new_password"]);

    $auth = new Auth();

    $this->user_account_id = $auth->getUser()->user_account_id;
    $this->password = password_hash($validatedData["new_password"], PASSWORD_DEFAULT);

    $query = "UPDATE {$this->table} SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $this->password, $this->user_account_id);

    $stmt->execute();

    $stmt->close();
    $conn->close();

    return true;
  }

  public function updateProfile(Request $request): bool
  {
    $conn = $this->getConnection();
    $stmt = null;

    $validatedData = Validator::setRules($request->post(), ["phone_number" => ["phone_number"]]);
    $validatedImage = Validator::setRules($request->files(), ["profile_picture" => ["image|type:jpeg,png|max:1024"]]);

    if (!$validatedData && !$validatedImage) return false;

    $auth = new Auth();

    $this->user_account_id = $auth->getUser()->user_account_id;
    $this->full_name = $request->post("full_name");
    $this->phone_number = $validatedData["phone_number"];
    $this->address = $request->post("address");
    $this->bio = $request->post("bio");

    if ($request->files("profile_picture")["error"] === 4) {
      $this->profile_picture = $auth->getUser()->profile_picture;
    } else {
      $this->profile_picture = $validatedImage;
    }

    try {
      $query = "UPDATE user_profiles SET 
                full_name = ?,
                phone_number = ?,
                address = ?,
                bio = ?,
                profile_picture = ?
                WHERE user_account_id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param(
        "sssssi",
        $this->full_name,
        $this->phone_number,
        $this->address,
        $this->bio,
        $this->profile_picture,
        $this->user_account_id
      );

      if (!$stmt->execute())
        throw new Exception("Updating profile failed.", 1);

      return true;
    } catch (\Throwable $th) {
      Flasher::setFlash($th->getMessage(), "", "warning");

      return false;
    } finally {
      if ($stmt) $stmt->close();
      if ($conn) $conn->close();
    }
  }

  public function updateRole(Request $request): bool
  {
    $conn = $this->getConnection();
    $stmt = null;

    $this->username = $request->post("username");
    $this->role = (int)$request->post("update_role");

    try {
      if ((int)$request->post("role_id") === 1)
        throw new Exception("Cannot change admin role.");

      if ((int)$request->post("role_id") === $this->role)
        throw new Exception("This account has already a " . $request->post("role_name") . ".");

      $query = "SELECT id FROM {$this->table} WHERE role_id = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("i", $this->role);

      if (!$stmt->execute())
        throw new mysqli_sql_exception("Error selecting id from user_accounts.");

      $result = $stmt->get_result();

      if ($this->role === 2 && $result->num_rows > 0)
        throw new Exception("Cannot be more than 1 Moderator.");

      $query = "UPDATE {$this->table} SET role_id = ? WHERE username = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("is", $this->role, $this->username);

      if (!$stmt->execute())
        throw new mysqli_sql_exception("Error updating user role.");

      return true;
    } catch (\Throwable $th) {
      Flasher::setFlash($th->getMessage(), "", "warning");

      return false;
    } finally {
      if ($stmt) $stmt->close();
      if ($conn) $conn->close();
    }
  }

  public function deleteAccount(Request $request): bool
  {
    $conn = $this->getConnection();
    $stmt = null;

    $this->username = $request->post("username");
    $this->profile_picture = $request->post("profile_picture");

    try {
      if (!$this->username)
        throw new Exception("Invalid account username.");

      $query = "DELETE FROM {$this->table} WHERE username = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $this->username);

      if (!$stmt->execute())
        throw new Exception("Delete account failed.");

      FileHandler::remove("image", $this->profile_picture);

      return true;
    } catch (\Throwable $th) {
      Flasher::setFlash($th->getMessage(), "", "danger");
      return false;
    } finally {
      if ($stmt) $stmt->close();
      if ($conn) $conn->close();
    }
  }

  public function deleteExpiredInactiveAccounts(): void
  {
    $conn = $this->getConnection();
    $stmt = null;

    $now = date("U");

    try {
      $query = "DELETE FROM {$this->table} WHERE status = 'inactive' AND id IN (
                SELECT user_account_id FROM account_activations WHERE expires < ?
              )";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("i", $now);

      if (!$stmt->execute()) throw new Exception("Error deleting expired inactive accounts.");
    } catch (\Throwable $th) {
      Flasher::setFlash($th->getMessage(), "", "danger");
    } finally {
      if ($stmt) $stmt->close();
      if ($conn) $conn->close();
    }
  }

  public function deleteUser(Request $request): bool
  {
    $conn = $this->getConnection();
    $stmt = null;

    $this->username = $request->post("username");
    $this->role = $request->post("role");
    $this->profile_picture = $request->post("profile_picture");

    try {
      if (!$this->username)
        throw new Exception("Invalid account username.");

      if ($this->role === "Admin" || $this->role === "Moderator")
        throw new Exception("Cannot delete admin or moderator account.");

      $query = "DELETE FROM {$this->table} WHERE username = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $this->username);

      if (!$stmt->execute())
        throw new Exception("Delete account failed.");

      FileHandler::remove("image", $this->profile_picture);

      return true;
    } catch (\Throwable $th) {
      Flasher::setFlash($th->getMessage(), "", "danger");
      return false;
    } finally {
      if ($stmt) $stmt->close();
      if ($conn) $conn->close();
    }
  }

  public function banUser(Request $request)
  {
    $conn = $this->getConnection();
    $stmt = null;

    $auth = new Auth();

    $this->username = $request->post("username");
    $this->role = $request->post("role");
    $this->status = $request->post("status");

    try {
      if (!$this->username)
        throw new Exception("Invalid account username.");

      if ($this->username === $auth->getUser()->username)
        throw new Exception("Cannot ban your own account");

      if ($this->role === "Admin")
        throw new Exception("Admin cannot be banned");

      if ($this->status === "inactive")
        throw new Exception("This account is inactive");

      $query = "UPDATE {$this->table} SET status = ? WHERE username = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("ss", $this->status, $this->username);

      if (!$stmt->execute())
        throw new Exception("Ban account failed.");

      return true;
    } catch (\Throwable $th) {
      Flasher::setFlash($th->getMessage(), "", "danger");
      return false;
    } finally {
      if ($stmt) $stmt->close();
      if ($conn) $conn->close();
    }
  }
}
