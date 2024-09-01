<?php

class Request
{
  private $old_data = [];

  public function post(?string $key = null)
  {
    if ($key === null) {
      foreach ($_POST as $key => $value) {
        if ($key !== "password" && $key !== "repeat_password") {
          $this->setOldData($key, $value);
        }
      }

      return $_POST;
    }

    if (isset($_POST[$key])) {
      $value = $_POST[$key];

      if (is_string($value)) {
        $sanitized_value = trim(stripslashes(htmlspecialchars($value)));
        $this->setOldData($key, $sanitized_value);

        return $sanitized_value;
      }

      $this->setOldData($key, $value);

      return $value;
    }

    return null;
  }

  public function get(?string $key = null)
  {
    if ($key === null) {
      return $_GET;
    }

    if (isset($_GET[$key])) {
      $value = $_GET[$key];

      if (is_string($value)) {
        $sanitized_value = trim(stripslashes(htmlspecialchars($value)));
        return $sanitized_value;
      }

      return $value;
    }

    return null;
  }

  public function files(?string $key = null)
  {
    if ($key === null) {
      return $_FILES;
    }

    return $_FILES[$key] ?? null;
  }

  public function setOldData(string $field, $value)
  {
    $this->old_data[$field] = $value;
  }

  public function getOldData(string $field)
  {
    return $this->old_data[$field] ?? null;
  }
}
