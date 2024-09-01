<?php

class Auth extends Database
{
  public static function setLogin(bool $login): void
  {
    $_SESSION["login"] = $login;
  }

  public static function getLogin(): bool
  {
    return $_SESSION["login"] ?? false;
  }

  public static function setUserAccountId(int $user_account_id): void
  {
    $_SESSION["user_account_id"] = $user_account_id;
  }

  public static function getUserAccountId(): int
  {
    return $_SESSION["user_account_id"];
  }

  public function getUser(): stdClass
  {
    $conn = $this->getConnection();

    $id = self::getUserAccountId();

    $query = "SELECT * FROM user_accounts 
              LEFT JOIN user_roles ON user_accounts.role_id = user_roles.id 
              LEFT JOIN user_profiles ON user_accounts.id = user_profiles.user_account_id
              WHERE user_accounts.id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    $stmt->execute();

    $result = $stmt->get_result();

    $user = $result->fetch_object();

    $stmt->close();
    $conn->close();

    return $user;
  }
}
