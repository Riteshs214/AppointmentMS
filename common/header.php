<!-- Navbar -->
<nav class="navbar navbar-dark navbar-expand-lg bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <i class="fa-regular fa-hospital text-danger me-2"></i>
            <h4 class="text-light fw-bold m-0 d-none d-lg-block">Appointment Management System</h4>
            <h4 class="text-light fw-bold m-0 d-lg-none">AMS</h4>
        </a>

        <ul class="navbar-nav ms-auto d-flex align-items-center">
            <?php if (isset($_SESSION['user']) || isset($_SESSION['admin'])): ?>
                <li class="nav-item">
                    <a href="../other/logout.php" class="btn btn-sm btn-outline-danger me-2">Logout</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>