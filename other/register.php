 <?php
    $redirect = "";
    if (isset($_POST['register'])) {
        $name = $_POST['name'];
        $gander = $_POST['gander'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        $number = $_POST['number'];
        if ($password != $cpassword) {
            echo "<div class='alert mt-2 text-light bg-danger text-center col-4 m-auto' role='alert'>
        <b>Password not match !</b>
      </div>";
        }elseif (!preg_match('/^[6-9]\d{9}$/',$number)){
            echo '<div class="alert mt-2 text-light bg-danger text-center col-4 m-auto alert-dismissible fade show" role="alert"><b>
            Invalid phone number.
            Enter a valid  10 digit number.</b></div>
           ';
        } 
        
        else {
            // Check if email or phone number already exists
            $checkUser = $conn->prepare("SELECT * FROM `patient` WHERE `email` = ? OR `contact` = ?");
            $checkUser->bind_param("ss", $email, $number);
            $checkUser->execute();
            $result = $checkUser->get_result();
            if ($result->num_rows > 0) {
                echo "<div class='alert mt-2 text-light bg-danger text-center col-4 m-auto' role='alert'>
                    <b>Email or Phone Number already registered!</b>
                  </div>";
            } else {
                // Hash password
                $hashedPass = password_hash($password, PASSWORD_DEFAULT);
                // Insert new user
                $user = $conn->prepare("INSERT INTO `patient` (`name`, `gander`, `email`, `password`, `contact`) VALUES (?, ?, ?, ?, ?)");
                $user->bind_param("sssss", $name, $gander, $email, $hashedPass, $number);
                if ($user->execute()) {
                    header("Location: index.php?login");
                    exit;
                } else {
                    echo "<div class='alert mt-2 text-light bg-danger text-center col-4 m-auto' role='alert'>
                   <b>Something went wrong. Please try again.</b>
                 </div>";
                }
                $user->close();
            }
            $checkUser->close();
        }
    }
    ?>
 <div class="container">
     <div class="row">
         <div class="col-md-4 offset-md-4">
             <div class="login-form py-3 mt-4 text-center">
                 <div class="box col-md-6  m-auto">
                     <div class="box d-flex flex-column  justify-content-center align-items-center">
                         <h3 class="text-light text-center fw-bolder" style="white-space: nowrap; ">
                             <p class="text-warning fw-lighter fs-3 mb-1">Appointment</p>
                             <p class="text-warning fw-lighter fs-4">Management System</p>
                         </h3>
                     </div>
                 </div>
                 <form action="" method="post" class="text-center">
                     <input type="text" class="form-control text-center w-75 mx-auto" placeholder="Enter Full Name" name="name" id="name" required="required">
                     <select name="gander" id="gander" class="form-select w-75 mx-auto text-center my-2" required>
                         <option value="" disabled selected>Select Gander</option>
                         <option value="Male">Male</option>
                         <option value="Female">Female</option>
                         <option value="Other">Other</option>
                     </select>
                     <input type="email" class="form-control text-center w-75 mx-auto my-2" placeholder="Enter Email ID" name="email" id="email" required="required">
                     <input type="password" class="form-control text-center w-75 mx-auto my-2" placeholder="Enter Password" name="password" id="password" required="required" minlength="8">
                     <input type="password" class="form-control text-center w-75 mx-auto my-2" placeholder="Conform Password" name="cpassword" id="cpassword" required="required" minlength="8">
                     <input type="tel" class="form-control text-center w-75 mx-auto my-2 mb-3" placeholder="Enter Phone no" name="number" id="number" required="required" pattern="[0-9]{10}" title="Please enter a valid 10 digit mobile number">
                     <div class="box text-center"> <input type="submit" class="btn btn-success" value="Register" name="register">
                         <a href="index.php?login" class="btn btn-primary">Back</a>
                     </div>
                 </form>
             </div>
         </div>
     </div>
 </div>