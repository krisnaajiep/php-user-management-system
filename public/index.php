<?php

include_once "../templates/head.php";
include_once "../includes/auth.php";

if ($request->post("update") !== null) {
  if ($user->updateProfile($request))
    Flasher::setFlash("Your profile has been", "updated.", "success");
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
          <div class="row mb-3">
            <div class="col-10">
              <h1 class="h3 text-gray-800">Welcome Back, <?= $auth->getUser()->full_name; ?></h1>
            </div>
            <div class="col-2">
              <button class="btn btn-warning btn-icon-split" data-toggle="modal" data-target="#editModal">
                <span class="icon text-white-50">
                  <i class="fas fa-pen"></i>
                </span>
                <span class="text">Edit Profile</span>
              </button>
            </div>
          </div>
          <?php Flasher::getFlash() ?>
          <div class="row">
            <div class="col-4">
              <!-- Basic Card Example -->
              <div class="card shadow mb-4 h-100">
                <div class="card-body text-center">
                  <img src="../assets/img/<?= $auth->getUser()->profile_picture; ?>" alt="" class="rounded-circle" width="200">
                  <h4 class="mt-3"><?= $auth->getUser()->full_name; ?></h4>
                  <span class="badge badge-<?= $auth->getUser()->role_name === "Admin" ? "info" : ($auth->getUser()->role_name === "Moderator" ? "primary" : "secondary") ?>"><?= $auth->getUser()->role_name ?></span>
                  <span class="badge badge-<?= $auth->getUser()->status === "active" ? "success" : ($auth->getUser()->status === "inactive" ? "warning" : "danger") ?>"><?= $auth->getUser()->status ?></span>
                </div>
              </div>
            </div>
            <div class="col-8">
              <!-- Basic Card Example -->
              <div class="card shadow h-100">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Data Profile</h6>
                </div>
                <div class="card-body">
                  <h6>Username : </h6>
                  <p><?= $auth->getUser()->username; ?></p>
                  <h6>Email : </h6>
                  <p><?= $auth->getUser()->email; ?></p>
                  <h6>Address : </h6>
                  <p><?= $auth->getUser()->address; ?></p>
                  <h6>Phone Number : </h6>
                  <p><?= $auth->getUser()->phone_number; ?></p>
                </div>
              </div>
            </div>
          </div>
          <!-- Basic Card Example -->
          <div class="card shadow my-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Bio</h6>
            </div>
            <div class="card-body">
              <?= $auth->getUser()->bio; ?>
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

  include_once "../templates/edit_modal.php";
  include_once "../templates/foot.php";

  ?>