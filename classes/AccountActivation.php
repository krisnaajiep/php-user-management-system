<?php

class AccountActivation extends Database
{
  private $table = "account_activations", $user_account_id, $token, $expires;

  public function __construct()
  {
    $conn = $this->getConnection();

    $query = "SHOW TABLES LIKE '{$this->table}'";
    $result = $conn->query($query);

    if ($result->fetch_object() === null) {
      if (!$this->createTable($conn)) {
        Flasher::setFlash("Error creating account activations table.", "", "danger");
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

  public function sendActivationLink(int $user_account_id): bool
  {
    $conn = $this->getConnection();
    $stmt = null;

    $this->user_account_id = $user_account_id;
    $this->token = bin2hex(random_bytes(32));
    $this->expires = date("U") + 3600;

    try {
      $query = "INSERT INTO {$this->table} (user_account_id, token, expires) VALUES (?, ?, ?)";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("isi", $this->user_account_id, $this->token, $this->expires);

      if (!$stmt->execute()) throw new mysqli_sql_exception("Error inserting email activation token.");

      $query = "SELECT email FROM user_accounts WHERE id = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("i", $this->user_account_id);

      if (!$stmt->execute()) throw new mysqli_sql_exception("Error selecting email from user_accounts");

      $stmt->bind_result($email);
      $stmt->fetch();

      $reset_link = "http://localhost/latihan/project-sederhana/user_management_system/public/account-activation.php?token=" . $this->token;
      $subject = "Email Activation Request";
      $message = "To activate your account, please click on the following link: $reset_link";

      if (!mail($email, $subject, $message))
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

  public function activateAccount(Request $request): true
  {
    $conn = $this->getConnection();
    $stmt = null;

    $this->token = $request->get("token");

    $conn->begin_transaction();

    try {
      $query = "SELECT user_account_id FROM {$this->table} WHERE token = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $this->token);

      $stmt->execute();

      $result = $stmt->get_result();

      if ($result->num_rows === 0) throw new Exception("Invalid or expired token.");

      $this->user_account_id = $result->fetch_object()->user_account_id;

      $status = "active";

      $query = "UPDATE user_accounts SET status = ? WHERE id = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("si", $status, $this->user_account_id);

      if (!$stmt->execute())
        throw new mysqli_sql_exception("Error activating account");

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
      header("Location: 404.php");
      exit;
    } finally {
      if ($stmt) $stmt->close();
      if ($conn) $conn->close();
    }
  }
}
