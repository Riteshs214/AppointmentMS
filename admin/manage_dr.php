<?php
$search = "";
//Handle status updates
if (isset($_GET['active_id'])) {
    $d_id = intval($_GET['active_id']);
    $sql = "UPDATE doctors set status='Active' where d_id=$d_id";
    if ($conn->query($sql)) {
        header("Location: index.php?page=manage_dr.php&success=Doctor activated successfully");
        exit();
    } else {
        header("Location: index.php?page=manage_dr.php&error" . urlencode($conn->error));
        exit();
    }
}
if (isset($_GET['inactive_id'])) {
    $d_id = intval($_GET['inactive_id']);
    $sql = "UPDATE doctors set status='Inactive' where d_id=$d_id";
    if ($conn->query($sql)) {
        header("Location: index.php?page=manage_dr.php&success=Doctor deativated successfully");
        exit();
    } else {
        header("Location:index.php?page=manage_dr.php&error" . urlencode($conn->error));
        exit();
    }
}
//Fetch all doctors
$sql = "SELECT * FROM doctors where 1";

// Process input
if (isset($_POST['search'])) {
    $search = isset($_POST['dr_name']) ? trim($_POST['dr_name']) : "";
    //Check if search filter exist
    if (!empty($search)) {
        $sql .= " AND (name like ? or specialize Like ?)";
    }
}

// Check if clear button is clicked
if (isset($_POST['clear_data'])) {
    $search = "";
}
$stmt = $conn->prepare($sql);

//Bind paramenters if search is applied
if (!empty($search)) {
    $searchParam = "%$search%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
}
// Execute query
$stmt->execute();
$result = $stmt->get_result();

$errors = [];
$alertMessage = "";
$alertClass = "d-none";
// Update doctor details when form is submitted
if (isset($_POST['update'])) {
    $name = trim($conn->real_escape_string($_POST['name']));
    $specialize = trim($conn->real_escape_string($_POST['specialize']));
    $m_start = $conn->real_escape_string($_POST['m_start']);
    $m_end = $conn->real_escape_string($_POST['m_end']);
    $e_start = $conn->real_escape_string($_POST['e_start']);
    $e_end = $conn->real_escape_string($_POST['e_end']);
    $fees = filter_var($_POST['fees'], FILTER_VALIDATE_INT);
    $d_id = $conn->real_escape_string($_POST['d_id']);
    // Validation rules
    if (empty($name) || strlen($name) < 3 || strlen($name) > 50) {
        $errors[] = "Name must be between 3 and 50 characters.";
    }
    if (empty($specialize)) {
        $errors[] = "Specialization is required.";
    }
    if (!($m_start && $m_end) && !($e_start && $e_end)) {
        $errors[] = "Morning or Evening time must be filled.";
    }
    if ($fees < 200 || $fees > 2000) {
        $errors[] = "Fees must be between ₹200 and ₹2000.";
    }
    // If no errors, update doctor details
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE doctors SET name=?, specialize=?, m_start=?, m_end=?, e_start=?, e_end=?, fees=? WHERE d_id=?");
        $stmt->bind_param("ssssssdi", $name, $specialize, $m_start, $m_end, $e_start, $e_end, $fees, $d_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Doctor details updated successfully.";
            $_SESSION['alertClass'] = "alert-success";
        } else {
            $_SESSION['message'] = "Failed to update doctor details: " . $stmt->error;
            $_SESSION['alertClass'] = "alert-danger";
        }

        // Refresh the page after update
        header("Location:  index.php?page=manage_dr.php");
        exit();
    } else {
        $_SESSION['message'] = implode("<br>", $errors);
        $_SESSION['alertClass'] = "alert-danger";

        // Refresh the page to show errors
        header("Location: index.php?page=manage_dr.php");
        exit();
    }
}
// Show session messages and clear them after displaying
if (isset($_SESSION['message'])) {
    $alertMessage = $_SESSION['message'];
    $alertClass = $_SESSION['alertClass'];
    unset($_SESSION['message']);
    unset($_SESSION['alertClass']);
}
?>
<!-- Bootstrap UI -->
<div class="card-header bg-primary text-white text-center">
    <h5>Manage Doctors</h5>
</div>
<div class="card-body">
    <!-- Alert Message -->
    <?php if ($alertMessage): ?>
        <div class="alert <?php echo $alertClass; ?> alert-dismissible fade show text-center" role="alert">
            <?php echo $alertMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <!-- Search Filter Section -->
    <form action="" method="post">
        <div class="row justify-content-center align-items-center mb-2">
            <div class="col-md-12 d-flex align-items-center">
                <label for="dr_name" class="form-label col-md-1 my-auto">Search:</label>
                <input type="text" id="dr_name" name="dr_name"
                    class="form-control col-md-3 w-50"
                    placeholder="Search by name or specialization..."
                    value="<?= htmlspecialchars($search); ?>">

                <!-- Clear Date Button -->
                <button type="submit" name="clear_data" class="btn btn-sm btn-danger ms-2">❌</button>

                <button type="submit" class="btn btn-sm btn-primary ms-2" name="search">Search</button>
            </div>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table text-center table-striped table-bordered">
            <thead class="table-info">
                <tr>
                    <th>S.no</th>
                    <th>Dr Name</th>
                    <th>Specialist</th>
                    <th>Fees</th>
                    <th>Morning Time</th>
                    <th>Evening Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php $disabled = ($row['status'] == 'Inactive') ? 'disabled' : ''; ?>
                        <tr>
                            <td><?php echo $row['d_id']; ?></td>
                            <td>Dr. <?php echo ucwords($row['name']); ?></td>
                            <td><?php echo $row['specialize']; ?></td>
                            <td>₹<?php echo $row['fees']; ?></td>

                            <td><?php echo ($row['m_start'] !== '00:00:00' && $row['m_end'] !== '00:00:00')
                                    ? date('h:i', strtotime($row['m_start'])) . ' - ' . date('h:i', strtotime($row['m_end']))
                                    : "<span class='text-danger fw-bold'>OFF</span>";  ?></td>

                            <td><?php echo ($row['e_start'] !== '00:00:00' && $row['e_end'] !== '00:00:00')
                                    ? date('h:i', strtotime($row['e_start'])) . ' - ' . date('h:i', strtotime($row['e_end']))
                                    : "<span class='text-danger fw-bold'>OFF</span>"; ?></td>

                            <td>
                                <!-- Button to trigger modal -->
                                <button type="button" class="btn btn-primary btn-sm mb-1"
                                    data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $row['d_id']; ?>" <?php echo $disabled; ?>>
                                    Update
                                </button>
                                <!-- Status Toggle Button -->
                                <?php if ($row['status'] == 'Active'): ?>
                                    <a href='index.php?page=manage_dr.php&inactive_id=<?php echo $row['d_id']; ?>' class='btn btn-sm mb-1 btn-warning'>Deactivate</a>
                                <?php else: ?>
                                    <a href='index.php?page=manage_dr.php&active_id=<?php echo $row['d_id']; ?>' class='btn btn-success btn-sm mb-1'>Active</a>
                                <?php endif; ?>
                            </td>
                        </tr>


                        <!-- Modal for Update Doctor -->
                        <div class="modal fade" id="updateModal<?php echo $row['d_id']; ?>" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-primary fw-bold">Update Doctor Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body fw-bold">
                                        <form method="POST">
                                            <input type="hidden" name="d_id" value="<?php echo $row['d_id']; ?>">

                                            <div class="mb-3 row">
                                                <label for="name" class="col-form-label col-md-4">Doctor Name</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $row['name']; ?>" required>
                                                </div>
                                            </div>

                                            <div class="mb-3 row">
                                                <label for="specialization" class="col-form-label col-md-4">Specialization</label>
                                                <div class="col-md-8">
                                                    <select name="specialize" class="form-select" required>
                                                        <option value="" disabled>Select Specialization</option>

                                                        <option value="Cardiologist" <?php echo $row['specialize'] === 'Cardiologist' ? 'selected' : ''; ?>>Cardiologist (Dil ke doctor)</option>
                                                        <option value="Dermatologist" <?php echo $row['specialize'] === 'Dermatologist' ? 'selected' : ''; ?>>Dermatologist (Twacha ke doctor)</option>

                                                        <option value="Neurologist" <?php echo $row['specialize'] === 'Neurologist' ? 'selected' : ''; ?>>Neurologist (Dimag aur nervous system ke doctor)</option>
                                                        <option value="Orthopedic" <?php echo $row['specialize'] === 'Orthopedic' ? 'selected' : ''; ?>>Orthopedic (Haddi aur joints ke doctor)</option>
                                                        <option value="Pediatrician" <?php echo $row['specialize'] === 'Pediatrician' ? 'selected' : ''; ?>>Pediatrician (Bachchon ke doctor)</option>
                                                        <option value="Gynecologist" <?php echo $row['specialize'] === 'Gynecologist' ? 'selected' : ''; ?>>Gynecologist (Mahila aur pregnancy ke doctor)</option>
                                                        <option value="Oncologist" <?php echo $row['specialize'] === 'Oncologist' ? 'selected' : ''; ?>>Oncologist (Cancer ke doctor)</option>
                                                        <option value="Psychiatrist" <?php echo $row['specialize'] === 'Psychiatrist' ? 'selected' : ''; ?>>Psychiatrist (Mansik swasthya ke doctor)</option>
                                                        <option value="Dentist" <?php echo $row['specialize'] === 'Dentist' ? 'selected' : ''; ?>>Dentist (Daant ke doctor)</option>
                                                        <option value="Radiologist" <?php echo $row['specialize'] === 'Radiologist' ? 'selected' : ''; ?>>Radiologist (X-ray aur scans karne wale doctor)</option>
                                                        <option value="ENT Specialist" <?php echo $row['specialize'] === 'ENT Specialist' ? 'selected' : ''; ?>>ENT Specialist (Kaan, naak, aur gale ke doctor)</option>
                                                        <option value="Ophthalmologist" <?php echo $row['specialize'] === 'Ophthalmologist' ? 'selected' : ''; ?>>Ophthalmologist (Aankhon ke doctor)</option>
                                                        <option value="Urologist" <?php echo $row['specialize'] === 'Urologist' ? 'selected' : ''; ?>>Urologist (Peshab aur kidney ke doctor)</option>
                                                        <option value="Endocrinologist" <?php echo $row['specialize'] === 'Endocrinologist' ? 'selected' : ''; ?>>Endocrinologist (Hormones aur diabetes ke doctor)</option>
                                                        <option value="General Physician" <?php echo $row['specialize'] === 'General Physician' ? 'selected' : ''; ?>>General Physician (Saadharan bimaariyon ke doctor)</option>
                                                        <option value="Pulmonologist" <?php echo $row['specialize'] === 'Pulmonologist' ? 'selected' : ''; ?>>Pulmonologist (Fefdon ke doctor)</option>
                                                        <option value="Nephrologist" <?php echo $row['specialize'] === 'Nephrologist' ? 'selected' : ''; ?>>Nephrologist (Kidney ke doctor)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mb-3 row">
                                                <label for="morning-time" class="col-sm-4 col-form-label">Morning Time:</label>
                                                <div class="col-sm-8 d-flex align-items-center">
                                                    <input type="time" class="form-control w-auto" name="m_start" value="<?php echo $row['m_start']; ?>" required>
                                                    <span class="mx-2"><b>To</b></span>
                                                    <input type="time" class="form-control w-auto" name="m_end" value="<?php echo $row['m_end']; ?>" required>
                                                </div>
                                            </div>

                                            <div class="mb-3 row">
                                                <label for="evening-time" class="col-sm-4 col-form-label">Evening Time:</label>
                                                <div class="col-sm-8 d-flex align-items-center">
                                                    <input type="time" class="form-control w-auto" name="e_start" value="<?php echo $row['e_start']; ?>" required>
                                                    <span class="mx-2"><b>To</b></span>
                                                    <input type="time" class="form-control w-auto" name="e_end" value="<?php echo $row['e_end']; ?>" required>
                                                </div>
                                            </div>

                                            <div class="mb-3 row">
                                                <label for="fees" class="col-form-label col-md-4">Fees</label>
                                                <div class="col-md-8">
                                                    <input type="number" class="form-control" id="fees" name="fees" value="<?php echo $row['fees']; ?>" required min="200" max="2000" style="appearance: none; -moz-appearance: textfield;">
                                                </div>
                                            </div>

                                            <div class="text-center">
                                                <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Modal -->
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-danger">No doctors found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>