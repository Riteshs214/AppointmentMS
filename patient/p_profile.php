<?php
$p_id = $_SESSION['p_id']; // Ensure this is set during login 
$query = "SELECT * FROM patient where p_id =?";
$stmt=$conn->prepare($query);
$stmt->bind_param("i",$p_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $patient = $result->fetch_assoc();
} else {
    die("<div class='alert alert-danger'>Patient Details not found in the database.</div>");
}

// Handle form submission for updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['edit'])) {
        // Show the edit form
        $show_edit_form = true;
    } elseif (isset($_POST['update'])) {
        //  Process the update form submission
        $name = trim($_POST['name']);
        $new_email = trim($_POST['email']);
        $contact = trim($_POST['contact']);
        $password = !empty($_POST['password']) ? password_hash(trim($_POST['password']), PASSWORD_DEFAULT) : null;

        // Check if any changes were actually made
        $is_changed = false;

        if ($name !== $patient['name'] || $new_email !== $patient['email'] || $contact !== $patient['contact'] || $password) {
            $is_changed = true;
        }

        if (!$is_changed) {
            $error_message = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                                  No changes detected. Please update some details before saving.
                                  <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                              </div>";
        } else {

            // **Phone number validation** 
            if (!preg_match('/^[6-9]\d{9}$/', $contact)) {
                $error_message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                              Invalid phone number. Enter a valid 10-digit number.
                              <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>";
            } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
                $error_message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                              Invalid email format.
                              <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>";
            } else {
                // Update query
                $update_query = "UPDATE patient SET name = ?, contact = ?, email = ?,  update_at = NOW()";
                if ($password) {
                    $update_query .= ", password = ?";
                }
                $update_query .= " WHERE email = ?";
                // Prepare statement
                $stmt = $conn->prepare($update_query);
                if ($password) {
                    $stmt->bind_param("sssss", $name, $contact, $new_email, $password, $patient['email']);
                } else {
                    $stmt->bind_param("ssss", $name, $contact, $new_email, $patient['email']);
                }
                // Execute the query
                if ($stmt->execute()) {
                    $success_message = "<div class='alert alert-success alert-dismissible fade show' role='alert'>Profile updated successfully!<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                   </div>";
                    $query = "SELECT * FROM patient WHERE email = '$new_email'"; // Refresh  details with new email
                    $result = $conn->query($query);
                    $patient = $result->fetch_assoc();
                    $show_edit_form = false;
                } else {
                    $error_message = "<div class='alert alert-danger'>Error updating profile: " . $conn->error . "
                                   <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                 </div>";
                }
                $stmt->close();
            }
        }
    }
}
?>
<!-- Display Profile -->
<div class="card-header bg-primary text-white text-center">
    <h5>Patient Profile</h5>
</div>
<div class="card-body fw-bold">
    <?= isset($success_message) ? $success_message : ''; ?>
    <?= isset($error_message) ? $error_message : ''; ?>

    <?php if (!isset($show_edit_form) || !$show_edit_form): ?>
        <!-- Non-editable Profile -->
        <form method="post">
            <div class="text-center mb-4">
                <img src="../img/profile.png" alt="Patient Profile" class="img-fluid rounded-circle" style="height: 100px; width: 100px;">
            </div>
            <div class="mb-3 row">
                <label for="name" class="col-sm-3 col-form-label text-dark">Name:</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" id="name" value="<?php echo htmlspecialchars($patient['name']); ?>" disabled>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="email" class="col-sm-3 col-form-label text-dark">Email:</label>
                <div class="col-sm-7">
                    <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($patient['email']); ?>" disabled>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="contact" class="col-sm-3 col-form-label text-dark">Phone no:</label>
                <div class="col-sm-7 d-flex">
                    <span class="input-group-text">+91</span>
                    <input type="tel" class="form-control" id="contact" value="<?php echo htmlspecialchars($patient['contact']); ?>" disabled>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="password" class="col-sm-3 col-form-label text-dark">Password:</label>
                <div class="col-sm-7">
                    <input type="password" class="form-control" id="password" value="********" disabled>
                </div>
            </div>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary" name="edit">Edit Profile</button>
            </div>
        </form>
    <?php else: ?>
        <!-- Editable Profile Form -->
        <form method="post">
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label text-dark">Name:</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($patient['name']); ?>" required>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label text-dark">Email:</label>
                <div class="col-sm-7">
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($patient['email']); ?>" required>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label text-dark">Phone no:</label>
                <div class="col-sm-7 d-flex">
                    <span class="input-group-text">+91</span>
                    <input type="tel" class="form-control" name="contact" value="<?= htmlspecialchars($patient['contact']); ?>" required>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label text-dark">Password:</label>
                <div class="col-sm-7">
                    <input type="password" class="form-control" name="password" placeholder="Enter new password (optional)">
                </div>
            </div>
            <div class="text-center mt-5">
                <button type="submit" class="btn btn-success" name="update">Save Changes</button>
            </div>
        </form>
    <?php endif; ?>
</div>