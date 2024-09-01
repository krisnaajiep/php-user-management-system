<?php

class Cookie extends Database
{
  public function rememberMe(): void
  {
    $conn = $this->getConnection();

    if (isset($_COOKIE["id"]) && isset($_COOKIE["key"])) {
      $id = (int)$_COOKIE["id"];

      $query = "SELECT id, username FROM user_accounts WHERE user_accounts.id = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("i", $id);

      $stmt->execute();

      $result = $stmt->get_result();

      $user_account = $result->fetch_object();

      if ($_COOKIE["key"] === hash("sha256", $user_account->username)) {
        Auth::setLogin(true);
        Auth::setUserAccountId($user_account->id);
      }

      $stmt->close();
      $conn->close();
    }
  }
}
