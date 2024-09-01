<?php

class FileHandler
{
  private static $target_dir, $target_file;

  public static function upload(array $file): string|false
  {
    if (strpos($file["type"], "image") !== false) {
      self::$target_dir = "../assets/img/";

      list($image_name, $image_extension) = explode(".", $file["name"]);
      $image_name = uniqid($image_name);
      $file_name = implode(".", [$image_name, $image_extension]);

      self::$target_file = self::$target_dir . basename($file_name);

      $auth = new Auth();

      $old_file = "../assets/img/" . $auth->getUser()->profile_picture;

      if (file_exists($old_file) && $auth->getUser()->profile_picture !== "default.jpg")
        unlink($old_file);

      try {
        if (!move_uploaded_file($file["tmp_name"], self::$target_file))
          throw new Exception("Error Uploading Image.", 1);

        return $file_name;
      } catch (\Throwable $th) {
        Flasher::setFlash($th->getMessage(), "Please try again.", "warning");
        return false;
      }
    }
  }

  public static function remove(string $type, string $file_name): void
  {
    if ($type === "image") {
      $file = "../assets/img/" . $file_name;
      if (file_exists($file) && $file_name !== "default.jpg")
        unlink($file);
    }
  }
}
