<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment System</title>
    <!-- Bootstrap & CSS -->
    <?php include("../common/links.php"); ?>
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

    if (!isset($_SESSION['admin'])) {
        header('Location: ../other/logout.php');
        exit();
    }
    ?>
    <!-- Main Content -->
    <div class="container-fluid mt-2">
        <div class="row vh-100">
            <!-- Sidebar -->
            <div class="col-md-3 bg-light sidebar">
                <div class="card border-secondary h-100">
                    <div class="card-header bg-primary text-white text-center">
                        <h5>Profile Details</h5>
                    </div>
                    <div class="card-body bg-light text-center">
                        <img src="../img/Drprofile.png" alt="Admin Profile" class="img-fluid rounded-circle" style="height: 80px; width: 80px;">

                        <!-- Responsive Sidebar Menu -->
                        <button class="btn btn-success w-50 m-3 d-md-none" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                            Menu
                        </button>
                        <div class="collapse d-md-block" id="sidebarMenu">
                            <ul class="list-group text-center mt-2">
                                <li class="list-group-item mb-1" id="lgi">
                                    <a href="?page=home.php" class="text-decoration-none text-dark d-block">Dashboard</a>
                                </li>
                                <li class="list-group-item mb-1" id="lgi">
                                    <a href="?page=profile.php" class="text-decoration-none text-dark d-block">Admin Profile</a>
                                </li>
                                <li class="list-group-item mb-1" id="lgi">
                                    <a href="?page=add_dr.php" class="text-decoration-none text-dark d-block">Add Doctor</a>
                                </li>
                                <li class="list-group-item mb-1" id="lgi">
                                    <a href="?page=manage_dr.php" class="text-decoration-none text-dark d-block">Manage Doctor</a>
                                </li>
                                <li class="list-group-item mb-1" id="lgi">
                                    <a href="?page=app_status.php" class="text-decoration-none text-dark d-block">Appointments</a>
                                </li>
                                <li class="list-group-item mb-1" id="lgi">
                                    <a href="?page=app_record.php" class="text-decoration-none text-dark d-block">Records</a>
                                </li>
                                <li class="list-group-item mb-1" id="lgi">
                                    <a href="?page=patient.php" class="text-decoration-none text-dark d-block">Patient Details</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Section -->
            <main class="col-md-9 bg-light">
                <div class="card border-secondary h-100">
                    <?php
                    if (isset($_GET['page'])) {
                        $page = $_GET['page'];
                        $safe_page = basename($page);
                        if (file_exists($safe_page)) {
                            include $safe_page;
                        } else {
                            echo "<div class='alert mt-2 text-light bg-danger text-center col-4 m-auto' role='alert'>
                      <b>Page not found! Try again</b>
                    </div>";
                        }
                    } else {
                        include("./home.php");
                    }
                    ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>