<?php

class Validator
{
  private static $validation_errors = [];

  public static function setRules(array $data, array $rules): array|string|false
  {
    $validatedData = [];

    foreach ($rules as $field => $ruleset) {
      $value = $data[$field];

      foreach ($ruleset as $rule) {
        if (empty(self::$validation_errors[$field]))
          $validatedValue = self::validate($value, $rule, $field, $data);
      }

      $validatedData[$field] = $validatedValue;
    }

    if (!empty(self::$validation_errors)) return false;

    if (is_array($validatedData[$field]) && $validatedData[$field]["error"] === 0)
      return FileHandler::upload($validatedData[$field]);

    return $validatedData;
  }

  public static function validate($value, string $rule, string $field, array $data)
  {
    if ($rule === "required" && empty($value)) {
      self::setValidationError($field, $field . " field is required.");
    }

    if (strpos($rule, "min_length") !== false && strpos($rule, ":") !== false) {
      $min_length = explode(":", $rule);
      if (strlen($value) < (int)$min_length[1]) {
        self::setValidationError($field, $field . " field must be at least 8.");
      }
    }

    if ($rule === "email") {
      $value = filter_var($value, FILTER_SANITIZE_EMAIL);

      if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
        self::setValidationError($field, $field . " field must be a valid email address.");
      }
    }

    if (strpos($rule, "match") !== false && strpos($rule, ":") !== false) {
      $match = explode(":", $rule);
      if ($value !== $data[$match[1]]) {
        self::setValidationError($field, $field . " doesn't match.");
      }
    }

    if ($rule === "old_password") {
      $auth = new Auth();

      if (!password_verify($value, $auth->getUser()->password)) {
        self::setValidationError($field, $field . " is invalid.");
      }
    }

    if ($rule === "phone_number" && !empty($value) && !preg_match("/^08[0-9]{10,12}$/", $value)) {
      self::setValidationError($field, $field . " field must be a valid phone number.");
    }

    if (strpos($rule, "image") !== false && strpos($rule, "|") !== false && $value["error"] === 0) {
      $file_rules = explode("|", $rule);
      unset($file_rules[0]);

      foreach ($file_rules as $file_rule) {
        if (strpos($file_rule, "type") !== false && strpos($file_rule, ":") !== false) {
          $valid_types = explode(":", $file_rule);
          $valid_types = explode(",", $valid_types[1]);
          $data_type = explode("/", $value["type"])[1];
          if (!in_array($data_type, $valid_types)) {
            $valid_types = implode(", ", $valid_types);
            self::setValidationError($field, $field . " file must be of type: " . $valid_types);
          }
        }

        if (strpos($file_rule, "max") !== false && strpos($file_rule, ":") !== false) {
          $max_size = (int)explode(":", $file_rule)[1];
          if ($value["size"] > ($max_size * 1024)) {
            self::setValidationError($field, $field . " file size must not exceed " . floor($max_size / 1024) . "MB");
          }
        }
      }
    }

    return $value;
  }

  public static function setValidationError($field, $message)
  {
    self::$validation_errors[$field] = $message;
  }

  public static function hasValidationErrors(): bool
  {
    return !empty(self::$validation_errors);
  }

  public static function hasValidationError($field): bool
  {
    return !empty(self::$validation_errors[$field]);
  }

  public static function getValidationError($field): string|null
  {
    return self::$validation_errors[$field] ?? null;
  }
}
