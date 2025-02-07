<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointment Booking System</title>
  <!-- Bootstrap & CSS -->
  <?php include("./common/links.php") ?>

  <style>
    body {
      background-image: url("./img/index_img.jpeg");
      background-size: cover;
      background-position: center;
      height: 100vh;
      margin: 0;
      justify-content: center;
      align-items: center;
    }

    .login-form {
      background-color: rgba(33, 37, 41, 0.53);
      border-radius: 10px;
      box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(9px);
      /* Blur effect */
      -webkit-backdrop-filter: blur(9px);
      /* Safari fix for blur */
    }

    .tile {
      width: 100px;
    }
  </style>
</head>

<body>
  <?php
  include("./common/header.php");
  include("./database/connection.php");
  $redirect = "";

  if (isset($_GET['register'])) {
    include './other/register.php';
  } elseif (!isset($_SESSION['user']['password']) || isset($_GET['login'])) {
    $redirect = './other/login.php';
  }
  ?>
</body>
</html>
<?php
if ($redirect) {
  include("$redirect");
}
?>