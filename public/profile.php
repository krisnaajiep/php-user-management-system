<?php

include_once "../templates/head.php";
include_once "../includes/auth.php";

if ($auth->getUser()->role_name === "Admin" && $request->post("delete_user") !== null) {
  if ($user->deleteUser($request)) {
    Flasher::setFlash("An account has been", "permanently deleted.", "success");
    header("Location: users.php");
    exit;
  }
}

if ($auth->getUser()->role_name !== "User" && $request->post("ban_user") !== null) {
  if ($user->banUser($request))
    Flasher::setFlash("This account status has been", "updated.", "success");
}

if ($auth->getUser()->role_name === "Admin" && $request->post("update_role") !== null) {
  if ($user->updateRole($request))
    Flasher::setFlash("This account role has been", "updated.", "success");
}

if ($request->get("username") !== null) $user = $user->readOne($request);

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
          <div class="row">
            <div class="col">
              <?php if ($user->username !== $auth->getUser()->username && $user->role_name !== "Admin" && $auth->getUser()->role_name !== "User" && $user->status !== "inactive"): ?>
                <button class="btn btn-<?= $user->status === "banned" ? "success" : "warning"; ?> btn-icon-split mb-3" data-toggle="modal" data-target="#banUserModal">
                  <span class="icon text-white-50">
                    <i class="fas fa-user<?= $user->status === "banned" ? "" : "-slash"; ?>"></i>
                  </span>
                  <span class="text"><?= $user->status === "banned" ? "Unban" : "Ban"; ?></span>
                </button>
              <?php
              endif;
              if ($auth->getUser()->role_name === "Admin" && $user->username !== $auth->getUser()->username && $user->role_name !== "Moderator"):
              ?>
                <button class="btn btn-danger btn-icon-split mb-3" data-toggle="modal" data-target="#deleteUserModal">
                  <span class="icon text-white-50">
                    <i class="fas fa-trash"></i>
                  </span>
                  <span class="text">Delete</span>
                </button>
              <?php endif ?>
            </div>
            <div class="col text-right">
              <?php if ($auth->getUser()->role_name === "Admin" && $user->role_name !== "Admin"): ?>
                <div class="dropdown mb-4">
                  <button class="btn btn-<?= $user->role_name === "Moderator" ? "primary" : "secondary"; ?> dropdown-toggle" type="button"
                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    Change Role
                  </button>
                  <div class="dropdown-menu animated--fade-in"
                    aria-labelledby="dropdownMenuButton">
                    <form method="post">
                      <input type="hidden" name="username" value="<?= $user->username; ?>">
                      <input type="hidden" name="role_id" value="<?= $user->role_id; ?>">
                      <input type="hidden" name="role_name" value="<?= $user->role_name; ?>">
                      <button type="submit" class="dropdown-item <?= $user->role_name === "Moderator" ? "active" : ""; ?>" name="update_role" value="2">Moderator</button>
                      <button type="submit" class="dropdown-item <?= $user->role_name === "User" ? "active" : ""; ?>" name="update_role" value="3">User</button>
                    </form>
                  </div>
                </div>
              <?php endif ?>
            </div>
          </div>
          <?= Flasher::getFlash(); ?>
          <div class="row">
            <div class="col-4">
              <!-- Basic Card Example -->
              <div class="card shadow mb-4 h-100">
                <div class="card-body text-center">
                  <img src="../assets/img/<?= $user->profile_picture; ?>" alt="" class="rounded-circle" width="200">
                  <h4 class="mt-3"><?= $user->full_name; ?></h4>
                  <span class="badge badge-<?= $user->role_name === "Admin" ? "info" : ($user->role_name === "Moderator" ? "primary" : "secondary") ?>"><?= $user->role_name ?></span>
                  <span class="badge badge-<?= $user->status === "active" ? "success" : ($user->status === "inactive" ? "warning" : "danger") ?>"><?= $user->status ?></span>
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
                  <p><?= $user->username; ?></p>
                  <h6>Email : </h6>
                  <p><?= $user->email; ?></p>
                  <h6>Address : </h6>
                  <p><?= $user->address; ?></p>
                  <h6>Phone Number : </h6>
                  <p><?= $user->phone_number; ?></p>
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
              <?= $user->bio; ?>
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
  include_once "../templates/ban_user_modal.php";
  include_once "../templates/delete_user_modal.php";

  ?>