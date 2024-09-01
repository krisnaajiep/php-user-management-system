<?php

if (Auth::getLogin()) {
  header("Location: index.php");
  exit;
}
