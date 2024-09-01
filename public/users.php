<?php

include_once "../templates/head.php";
include_once "../includes/auth.php";

$users = $user->readAll();

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

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Users DataTables</h6>
            </div>
            <div class="card-body">
              <?php Flasher::getFlash() ?>
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Full Name</th>
                      <th>Email</th>
                      <th>Phone Number</th>
                      <th>Role</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>Full Name</th>
                      <th>Email</th>
                      <th>Phone Number</th>
                      <th>Role</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </tfoot>
                  <tbody>
                    <?php foreach ($users as $user): ?>
                      <tr>
                        <td><?= $user->full_name; ?></td>
                        <td><?= $user->email; ?></td>
                        <td><?= $user->phone_number; ?></td>
                        <td><span class="badge badge-<?= $user->role_name === "Admin" ? "info" : ($user->role_name === "Moderator" ? "primary" : "secondary"); ?>"><?= $user->role_name; ?></span></td>
                        <td><span class="badge badge-<?= $user->status === "active" ? "success" : ($user->status === "inactive" ? "warning" : "danger"); ?>"><?= $user->status; ?></span></td>
                        <td>
                          <a href="profile.php?username=<?= $user->username; ?>" class="btn btn-info btn-sm btn-icon-split">
                            <span class="icon text-white-50">
                              <i class="fas fa-user-circle"></i>
                            </span>
                            <span class="text">Profile</span>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
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

  ?>