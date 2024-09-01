<?php

include_once "../templates/head.php";
include_once "../includes/guest.php";

if ($request->get("token") === null) {
  header("Location: forgot-password.php");
  exit;
}

if ($request->post("update_password") !== null) {
  if ($reset_password->updatePassword($request)) {
    Flasher::setFlash("Your password has been updated.", "Please login", "success");
    header("Location: login.php");
    exit;
  }
}

?>

<body class="bg-gradient-primary">

  <div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-6">

        <div class="card o-hidden border-0 shadow-lg mt-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Reset Your Password</h1>
                  </div>
                  <?php Flasher::getFlash() ?>
                  <form class="user" method="post">
                    <div class="form-group">
                      <input type="hidden" name="token" value="<?= $request->get("token"); ?>">
                      <input type="password" class="form-control form-control-user <?= Validator::hasValidationError("new_password") ? "is-invalid" : ""; ?>" name="new_password"
                        id="password" placeholder="New Password">
                      <div class="invalid-feedback text-center">
                        <?= Validator::getValidationError("new_password") ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control form-control-user <?= Validator::hasValidationError("repeat_new_password") ? "is-invalid" : ""; ?>" name="repeat_new_password"
                        id="password" placeholder="Repeat New Password">
                      <div class="invalid-feedback text-center">
                        <?= Validator::getValidationError("repeat_new_password"); ?>
                      </div>
                    </div>
                    <button type="submit" name="update_password" class="btn btn-primary btn-user btn-block">
                      Update Password
                    </button>
                  </form>
                  <hr>
                  <div class="text-center">
                    <a class="small" href="forgot-password.php">Forgot Password?</a>
                  </div>
                  <div class="text-center">
                    <a class="small" href="register.php">Create an Account!</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>

  <?php include_once "../templates/foot.php" ?>