<?php
$server="localhost";
$user="root";
$pass="";
$db="appointmentms";

$conn=mysqli_connect($server,$user,$pass,$db);

// if(!$conn){
//     die(mysqli_connect_error($conn));
// }

 // Check connection
 if ($conn->connect_error) {
    die("<div class='text-danger'>Connection failed: " . $conn->connect_error . "</div>");
}




?>