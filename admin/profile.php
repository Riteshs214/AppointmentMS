<?php
// Fetch Admin Data
$query = "SELECT * FROM admin";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
} else {
    die("<div class='alert alert-danger'>Admin Details not found in the database.</div>");
}

// Handle Update Request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['edit'])) {
        // Show the edit form
        $show_edit_form = true;
    } elseif (isset($_POST['update'])) {
        // Process the update form submission
        $admin_name = trim($_POST['name']);
        $new_email = trim($_POST['email']);
        $admin_contact = trim($_POST['contact']);
        $admin_password = !empty($_POST['password']) ? password_hash(trim($_POST['password']), PASSWORD_DEFAULT) : null;

        // Check if any changes were actually made
        $is_changed = false;

        if ($admin_name !== $admin['name'] || $new_email !== $admin['email'] || $admin_contact !== $admin['contact'] || $admin_password) {
            $is_changed = true;
        }

        if (!$is_changed) {
            $error_message = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                                  No changes detected. Please update some details before saving.
                                  <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                              </div>";
        } else {

            // **Phone number validation** 
            if (!preg_match('/^[6-9]\d{9}$/', $admin_contact)) {
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
                $update_query = "UPDATE admin SET name = ?, contact = ?, email = ?,  updated_at = NOW()";
                if ($admin_password) {
                    $update_query .= ", password = ?";
                }
                $update_query .= " WHERE email = ?";
                // Prepare statement
                $stmt = $conn->prepare($update_query);
                if ($admin_password) {
                    $stmt->bind_param("sssss", $admin_name, $admin_contact, $new_email, $admin_password, $admin['email']);
                } else {
                    $stmt->bind_param("ssss", $admin_name, $admin_contact, $new_email, $admin['email']);
                }
                // Execute the query
                if ($stmt->execute()) {
                    $success_message = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                     Profile updated successfully!
                                     <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                   </div>";
                    // **NEW: Refresh admin data to show updated phone number**
                    $query = "SELECT * FROM admin WHERE email = '$new_email'"; // Refresh admin details with new email
                    $result = $conn->query($query);
                    $admin = $result->fetch_assoc();
                    $show_edit_form = false;
                } else {
                    $error_message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                   Error updating profile: " . $conn->error . "
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
    <h5>Admin Profile</h5>
</div>
<!-- Success/Error Messages -->
<div class="card-body mt-3 fw-bold">
    <?= isset($success_message) ? $success_message : ''; ?>
    <?= isset($error_message) ? $error_message : ''; ?>
    <div class="container">
        <?php if (!isset($show_edit_form) || !$show_edit_form): ?>
            <!-- Non-editable Profile -->
            <form method="post" class="p-3">
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label text-dark">Name:</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control" value="<?= htmlspecialchars($admin['name']); ?>" disabled>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label text-dark">Email:</label>
                    <div class="col-sm-7">
                        <input type="email" class="form-control" value="<?= htmlspecialchars($admin['email']); ?>" disabled>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label text-dark">Phone no:</label>
                    <div class="col-sm-7 d-flex">
                        <span class="input-group-text">+91</span>
                        <input type="tel" class="form-control" value="<?= htmlspecialchars($admin['contact']); ?>" disabled>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="password" class="col-sm-3 col-form-label text-dark">Password:</label>
                    <div class="col-sm-7">
                        <input type="password" class="form-control" id="password" value="********" disabled>
                    </div>
                </div>
                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-primary" name="edit">Edit Profile</button>
                </div>
            </form>
        <?php else: ?>
            <!-- Editable Profile Form -->
            <form method="post">
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label text-dark">Name:</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($admin['name']); ?>" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label text-dark">Email:</label>
                    <div class="col-sm-7">
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($admin['email']); ?>" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label text-dark">Phone no:</label>
                    <div class="col-sm-7 d-flex">
                        <span class="input-group-text">+91</span>
                        <input type="tel" class="form-control" name="contact" value="<?= htmlspecialchars($admin['contact']); ?>" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label text-dark">Password:</label>
                    <div class="col-sm-7">
                        <input type="password" class="form-control" name="password" placeholder="Enter new password (optional)" minlength="8">
                    </div>
                </div>
                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-success" name="update">Save Changes</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>