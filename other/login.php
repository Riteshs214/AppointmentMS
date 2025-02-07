<?php
@session_start();
$redirect = "";
// Patient Login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch hashed password from database
    $query = "SELECT * FROM `patient` WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $hashed_password = $user['password']; 
        $p_id = $user['p_id']; // Get patient ID
    
        // Verify the entered password with the hashed password
        if (password_verify($password, $hashed_password)) {

            // Store email and any required data in the session
            $_SESSION['user'] = [
                'email' => $email,
                'password' => $password,

            ];
            $_SESSION['p_id'] = $p_id; // Store patient ID
            echo "<div class='alert mt-2 text-light bg-success text-center col-4 m-auto' role='alert'>
            <b><b>Welcome</b></b>
          </div>";
            header("Location: ./patient/index.php");
            exit();
        } else {
            echo "<div class='alert mt-2 text-light bg-danger text-center col-4 m-auto' role='alert'>
            <b><b>Invalid Email or Password</b></b>
          </div>";
        }
    } else {
        echo "<div class='alert mt-2 text-light bg-danger text-center col-4 m-auto' role='alert'>
        <b><b>Email not found</b></b>
      </div>";
    }
}

// Admin Login
else if (isset($_POST['a_login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch hashed password from database
    $a_query = "SELECT * FROM `admin` WHERE email='$email'";
    $result = mysqli_query($conn, $a_query);

    if (mysqli_num_rows($result) > 0) {
        $admin_data = mysqli_fetch_assoc($result);
        $hashed_password = $admin_data['password']; 

        // Verify the entered password with the hashed password
        if (password_verify($password, $hashed_password)) {

            // Store email and any required data in the session
            $_SESSION['admin'] = [
                'email' => $email,
                'password' => $password
            ];

            echo "<div class='alert mt-2 text-light bg-success text-center col-4 m-auto' role='alert'>
            <b><b>Welcome to Admin Dashboard</b></b>
          </div>";
            header("Location: ./admin/index.php");
            exit();
        } else {
            echo "<div class='alert mt-2 text-light bg-danger text-center col-4 m-auto' role='alert'>
            <b><b>Invalid Email or Password</b></b>
          </div>";
        }
    } else {
        echo "<div class='alert mt-2 text-light bg-danger text-center col-4 m-auto' role='alert'>
        <b><b>Admin Email not found</b></b>
      </div>";
    }
} 
?>
<div class="container">
    <div class="row">
        <div class="col-md-4 offset-md-4">
            <div class="login-form py-3 mt-5 text-center rounded shadow">
                <div class="box col-md-6  m-auto">
                    <div class="box d-flex flex-column  justify-content-center align-items-center">
                        <h3 class="text-light text-center fw-bolder" style="white-space: nowrap; ">
                            <p class="text-warning fw-lighter fs-3 mb-1">Appointment</p>
                            <p class="text-warning fw-lighter fs-4">Management System</p>
                        </h3>
                    </div>
                </div>
                <form action="" method="post" class="text-center">
                    <input type="email" class="form-control text-center w-75 mx-auto" placeholder="Enter Email ID" name="email" id="email" required="required"><br>
                    <input type="password" class="form-control text-center w-75 mx-auto" placeholder="Enter Password" name="password" id="password" required="required"><br>
                    <div class="box text-center mb-2">
                        <input type="submit" class="btn btn-success" value="User Login" name="login">
                        <input type="submit" class="btn btn-primary" value="Admin Login" name="a_login">
                    </div>
                    <p class="text-white">Don't have an account? <a href="index.php?register"
                            class="text-successful">Register here</a></p>
                </form>
            </div>
        </div>
    </div>
</div>