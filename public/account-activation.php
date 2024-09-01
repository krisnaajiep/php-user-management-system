<?php

include_once "../templates/head.php";
include_once "../includes/guest.php";

if ($request->get("token") !== null) {
  if ($account_activation->activateAccount($request)) {
    Flasher::setFlash("Your account has been activated.", "Please login.", "success");
    header("Location: login.php");
    exit;
  }
} else {
  header("Location: 404.php");
  exit;
}
