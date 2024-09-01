<?php

if (!Auth::getLogin()) {
  header("Location: login.php");
  exit;
}

$auth = new Auth();

if ($auth->getUser()->status === "banned") {
  $auth->setLogin(false);
  Flasher::setFlash("Your account has been banned. Please contact the moderator or admin.", "", "warning");
  header("Location: login.php");
  exit;
}
