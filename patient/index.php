<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment System</title>
    <!-- Bootstrap & CSS -->
    <?php include("../common/links.php") ?>
    <style>
        #lgi:hover {
            background-color: rgba(237, 244, 16, 0.73);
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php
    ob_start();
    session_start();
    include("../common/header.php");
    include("../database/connection.php");
    $redirect = '';
    if (!isset($_SESSION['user'])) {
        header('Location: ../other/logout.php');
        exit();
    }
    ?>

    <!-- main content -->
    <div class="container-fluid mt-2">
        <div class="row vh-100">
            <!-- profile -->
            <div class="col-md-3  bg-light sidebar">
                <div class="card border-secondary h-100">
                    <div class="card-header bg-primary text-white text-center">
                        <h5>Profile Details</h5>
                    </div>
                    <div class="card-body bg-light text-center">
                        <img src="../img/profile.png" alt="user Profile" class="img-fluid rounded-circle" style="height: 90px; width: 90px;">

                        <!-- Responsive Sidebar Menu -->
                        <button class="btn btn-success w-50 m-3 d-md-none" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">Menu</button>
                        
                        <div class="collapse d-md-block" id="sidebarMenu">
                            <ul class="list-group border mt-4 text-center">
                                <li class="list-group-item mb-2" id="lgi">
                                    <a href="?page=home.php" class="text-decoration-none text-dark d-block">Dashboard</a>
                                </li>
                                <li class="list-group-item mb-2" id="lgi">
                                    <a href="?page=p_profile.php" class="text-decoration-none text-dark d-block">Patient Profile</a>
                                </li>
                                <li class="list-group-item mb-2" id="lgi">
                                    <a href="?page=book_app.php" class="text-decoration-none text-dark d-block">Book Appointment</a>
                                </li>
                                <li class="list-group-item mb-2" id="lgi">
                                    <a href="?page=app_status.php" class="text-decoration-none text-dark d-block">Appointment Status</a>
                                </li>
                                <li class="list-group-item" id="lgi">
                                    <a href="?page=app_history.php" class="text-decoration-none text-dark d-block">Appointment History</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- main section -->
            <main class="col-md-9 bg-light ">
                <div class="card border-secondary h-100">
                    <?php if (isset($_GET['page'])) {
                        $page = $_GET['page'];
                        $safe_page = basename($page);
                        if (file_exists($safe_page)) {
                            include $safe_page;
                        } else {
                            echo "<div class='alert mt-2 text-light bg-danger text-center col-4 m-auto' role='alert'>
        <b><b>Page not found ! try again</b></b>
      </div>";
                        }
                    } else {
                        include("./home.php");
                    } ?>
                </div>
        </div>
    </div>
    <?php
    if ($redirect) {
        echo "<script>window.open($redirect,'_self')</script>";
    }
    ?>
</body>

</html>