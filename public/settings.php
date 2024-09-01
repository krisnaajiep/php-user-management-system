<?php

include_once "../templates/head.php";
include_once "../includes/auth.php";

if ($request->post("update") !== null) {
  if ($user->updateAccount($request))
    Flasher::setFlash("Your account has been", "updated.", "success");
}

if ($request->post("delete") !== null) {
  if ($user->deleteAccount($request)) {
    Flasher::setFlash("Your account has been", "permanently deleted.", "success");
    $auth->setLogin(false);
    header("Location: login.php");
    exit;
  }
}

?>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <?php include_once "../templates/sidebar.php" ?>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <?php include_once "../templates/topbar.php" ?>

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="h3 mb-4 text-gray-800 d-inline mr-3">Account Settings</h1>
          <button class="btn btn-danger btn-sm btn-icon-split mb-2" data-toggle="modal" data-target="#deleteModal">
            <span class="icon text-white-50">
              <i class="fas fa-pen"></i>
            </span>
            <span class="text">Delete Account</span>
          </button>
          <div class="row mb-5">
            <div class="col-6">
              <?php Flasher::getFlash() ?>
              <form method="post">
                <input type="hidden" name="id" value="">
                <div class="mb-3">
                  <label for="username" class="form-label">Username</label>
                  <input type="text" class="form-control <?= Validator::hasValidationError("username") ? "is-invalid" : "" ?>" name="username" id="username" aria-describedby="usernameHelp" value="<?= $request->getOldData("username") ?? $auth->getUser()->username ?>" autofocus>
                  <div class="invalid-feedback">
                    <?= Validator::getValidationError("username"); ?>
                  </div>
                </div>
                <div class="mb-3">
                  <label for="email" class="form-label">Email address</label>
                  <input type="email" class="form-control <?= Validator::hasValidationError("email") ? "is-invalid" : "" ?>" name="email" id="email" aria-describedby="emailHelp" value="<?= $request->getOldData("email") ?? $auth->getUser()->email ?>">
                  <div class="invalid-feedback">
                    <?= Validator::getValidationError("email"); ?>
                  </div>
                </div>
                <div class="mb-3">
                  <label for="old_password" class="form-label">Old Password</label>
                  <input type="password" class="form-control <?= Validator::hasValidationError("old_password") ? "is-invalid" : "" ?>" name="old_password" id="old_password">
                  <div class="invalid-feedback">
                    <?= Validator::getValidationError("old_password"); ?>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control <?= Validator::hasValidationError("new_password") ? "is-invalid" : "" ?>" name="new_password" id="new_password">
                    <div class="invalid-feedback">
                      <?= Validator::getValidationError("new_password"); ?>
                    </div>
                  </div>
                  <div class="col">
                    <label for="repeat_new_password" class="form-label">Repeat New Password</label>
                    <input type="password" class="form-control <?= Validator::hasValidationError("repeat_new_password") ? "is-invalid" : "" ?>" name="repeat_new_password" id="repeat_new_password">
                    <div class="invalid-feedback">
                      <?= Validator::getValidationError("repeat_new_password"); ?>
                    </div>
                  </div>
                </div>
                <button type="submit" name="update" class="btn btn-primary">Submit</button>
              </form>
            </div>
          </div>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <?php include_once "../templates/footer.php" ?>

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->


  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <?php

  include_once "../templates/foot.php";
  include_once "../templates/delete_modal.php";

  ?>