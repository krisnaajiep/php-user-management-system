<?php include_once "../templates/head.php" ?>

<body id="page-top">

    <!-- Main Content -->
    <div id="content">

        <!-- Begin Page Content -->
        <div class="container">

            <!-- 404 Error Text -->
            <div class="text-center mt-5">
                <div class="error m-auto" data-text="404">404</div>
                <p class="lead text-gray-800 mb-5">Page Not Found</p>
                <?php Flasher::getFlash() ?>
                <p class="text-gray-500 mb-0">It looks like you found a glitch in the matrix...</p>
                <a href="login.php">&larr; Back to Login</a>
            </div>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->


    <?php include_once "../templates/foot.php" ?>