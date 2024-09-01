<?php

include_once "../templates/head.php";
include_once "../includes/guest.php";

if ($request->post("reset_password") !== null) {
  if ($reset_password->sendResetLink($request))
    Flasher::setFlash("Password reset link sent.", "Please check your email.", "success");
}

?>

<body class="bg-gradient-primary">

  <div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">
      <div class="col-6">
        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-2">Forgot Your Password?</h1>
                    <p class="mb-4">We get it, stuff happens. Just enter your email address below
                      and we'll send you a link to reset your password!</p>
                    <?php Flasher::getFlash() ?>
                  </div>
                  <form class="user" method="post">
                    <div class="form-group">
                      <input type="email" class="form-control form-control-user <?= Validator::hasValidationError("email") ? "is-invalid" : "" ?>" name="email"
                        id="email" aria-describedby="emailHelp"
                        placeholder="Enter Email Address..." value="<?= $request->getOldData("email"); ?>">
                      <div class="invalid-feedback text-center">
                        <?= Validator::getValidationError("email"); ?>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-user btn-block" name="reset_password">
                      Reset Password
                    </button>
                  </form>
                  <hr>
                  <div class="text-center">
                    <a class="small" href="register.php">Create an Account!</a>
                  </div>
                  <div class="text-center">
                    <a class="small" href="login.php">Already have an account? Login!</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php include_once "../templates/foot.php" ?>