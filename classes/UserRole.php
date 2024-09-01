<?php

class UserRole extends Database
{
  private $table = "user_roles";

  public function __construct()
  {
    $conn = $this->getConnection();

    try {
      $query = "SHOW TABLES LIKE '{$this->table}'";
      $result = $conn->query($query);

      if ($result->fetch_object() === null) {
        if (!$this->createTable($conn))
          throw new Exception("Error creating user roles table.");
      }

      $query = "SELECT id FROM {$this->table}";
      $result = $conn->query($query);

      if ($result->num_rows === 0) {
        if (!$this->create($conn))
          throw new Exception("Error inserting user roles");
      }
    } catch (\Throwable $th) {
      Flasher::setFlash($th->getMessage(), "", "danger");

      header("Location: login.php");
      exit;
    } finally {
      if ($conn) $conn->close();
    }
  }

  private function createTable(mysqli $conn): bool
  {
    $query = "CREATE TABLE {$this->table} (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            role_name VARCHAR(50) NOT NULL
            )";

    if (!$conn->query($query)) return false;

    return true;
  }

  private function create(mysqli $conn): bool
  {
    $query = "INSERT INTO {$this->table} (id, role_name) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $id, $role_name);

    $data = [
      [1, "Admin"],
      [2, "Moderator"],
      [3, "User"]
    ];

    foreach ($data as $row) {
      $id = $row[0];
      $role_name = $row[1];
      $stmt->execute();
    }

    $error = $stmt->error;

    $stmt->close();

    if ($error) return false;

    return true;
  }
}
