<?php

include_once "../templates/head.php";
include_once "../includes/guest.php";

if ($request->post("login") !== null) {
  if ($authentication->login($request)) {
    header("Location: index.php");
    exit;
  }
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
                    <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                  </div>
                  <?php Flasher::getFlash() ?>
                  <form class="user" method="post">
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user <?= Validator::hasValidationError("username") ? "is-invalid" : "" ?>" name="username" id="username" aria-describedby="usernameHelp" placeholder="Enter Username..." value="<?= $request->getOldData("username"); ?>" />
                      <div class="invalid-feedback text-center">
                        <?= Validator::getValidationError("username"); ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control form-control-user <?= Validator::hasValidationError("password") ? "is-invalid" : "" ?>" name="password" id="password" placeholder="Password" />
                      <div class="invalid-feedback text-center">
                        <?= Validator::getValidationError("password"); ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="custom-control custom-checkbox small">
                        <input type="checkbox" class="custom-control-input" name="remember_me" id="remember_me" />
                        <label class="custom-control-label" for="remember_me">Remember Me</label>
                      </div>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary btn-user btn-block"> Login </button>
                  </form>
                  <hr />
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