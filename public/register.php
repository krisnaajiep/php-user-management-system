<?php

include_once "../templates/head.php";
include_once "../includes/guest.php";

if ($request->post("register") !== null) {
  if ($user->register($request)) {
    Flasher::setFlash("Your account has been successfully created. Please check your email to", "activate your account.", "success");
    header("Location: login.php");
    exit;
  }
}

?>

<body class="bg-gradient-primary">

  <div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">
      <div class="col-7">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                  </div>
                  <?php Flasher::getFlash() ?>
                  <form class="user" method="post">
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user <?= Validator::hasValidationError("username") ? "is-invalid" : "" ?>" name="username" id="username"
                        placeholder="Username" value="<?= $request->getOldData("username"); ?>">
                      <div class="invalid-feedback text-center">
                        <?= Validator::getValidationError("username"); ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <input type="email" class="form-control form-control-user <?= Validator::hasValidationError("email") ? "is-invalid" : "" ?>" name="email" id="email"
                        placeholder="Email Address" value="<?= $request->getOldData("email"); ?>">
                      <div class="invalid-feedback text-center">
                        <?= Validator::getValidationError("email"); ?>
                      </div>
                    </div>
                    <div class="form-group row">
                      <div class="col-sm-6 mb-3 mb-sm-0">
                        <input type="password" class="form-control form-control-user <?= Validator::hasValidationError("password") ? "is-invalid" : "" ?>" name="password"
                          id="password" placeholder="Password">
                        <div class="invalid-feedback text-center">
                          <?= Validator::getValidationError("password"); ?>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <input type="password" class="form-control form-control-user <?= Validator::hasValidationError("repeat_password") ? "is-invalid" : "" ?>" name="repeat_password"
                          id="repeat_password" placeholder="Repeat Password">
                        <div class="invalid-feedback text-center">
                          <?= Validator::getValidationError("repeat_password"); ?>
                        </div>
                      </div>
                    </div>
                    <button type="submit" name="register" class="btn btn-primary btn-user btn-block">
                      Register Account
                    </button>
                  </form>
                  <hr>
                  <div class="text-center">
                    <a class="small" href="forgot-password.php">Forgot Password?</a>
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
  </div>

  <?php include_once "../templates/foot.php" ?>