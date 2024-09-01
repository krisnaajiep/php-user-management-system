<?php

require_once "../helpers/Validator.php";

class Database
{
  private $hostname = "localhost", $username = "root", $password = "", $database = "simple_project_user_management";

  protected function getConnection(): mysqli
  {
    $conn = new mysqli($this->hostname, $this->username, $this->password);

    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $query = "SHOW DATABASES LIKE '{$this->database}'";
    $result = $conn->query($query);

    if ($result->fetch_object() === null) {
      if (!$this->createDB($conn))
        die("Error creating database: " . $conn->error);
    }

    $conn->select_db($this->database);

    return $conn;
  }

  private function createDB(mysqli $conn): bool
  {
    $query = "CREATE DATABASE {$this->database}";

    if (!$conn->query($query)) return false;

    return true;
  }
}
