<?php
ob_start(); // Start output buffering
$p_id = $_SESSION['p_id'];
$search = "";

// Get current date and max appointment date (2 months ahead)
$currentDate = date('Y-m-d');
$maxDate = date('Y-m-d', strtotime('+2 months'));

// Fetch doctors' details
$query = "SELECT d_id, name, specialize, fees, m_start, m_end, e_start, e_end, status FROM doctors WHERE status='Active'";

// Process input
if (isset($_POST['search'])) {
    $search = isset($_POST['dr_name']) ? trim($_POST['dr_name']) : "";
    //Check if search filter exist
    if (!empty($search)) {
        $query .= " AND (name like ? or specialize Like ?)";
    }
}

// Check if clear button is clicked
if (isset($_POST['clear_data'])) {
    $search = "";
}
$stmt = $conn->prepare($query);

//Bind paramenters if search is applied
if (!empty($search)) {
    $searchParam = "%$search%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
}

//Execute and get results
$stmt->execute();
$result = $stmt->get_result();

// Display alert messages if available
if (isset($_SESSION['alertMessage'])) {
    $alertMessage = $_SESSION['alertMessage'];
    $alertType = $_SESSION['alertType'];
    unset($_SESSION['alertMessage'], $_SESSION['alertType']); // Clear session
}
?>
<div class="card-header bg-primary text-white text-center">
    <h5>Book Appointment</h5>
</div>

<!-- Alert Message -->
<?php if (!empty($alertMessage)): ?>
    <div class="alert mt-2 <?= htmlspecialchars($alertType); ?> text-center">
        <?= htmlspecialchars($alertMessage); ?>
        <button type="button" class="btn-close position-absolute end-0 me-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card-body">
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
                        <tr>
                            <td><?= htmlspecialchars($row['d_id']); ?></td>
                            <td>Dr. <?= ucwords(htmlspecialchars($row['name'])); ?></td>
                            <td><?= htmlspecialchars($row['specialize']); ?></td>
                            <td>₹<?= htmlspecialchars($row['fees']); ?></td>

                            <td>
                                <?= ($row['m_start'] !== '00:00:00' && $row['m_end'] !== '00:00:00')
                                    ? date('h:i', strtotime($row['m_start'])) . ' - ' . date('h:i', strtotime($row['m_end']))
                                    : "<span class='text-danger fw-bold'>OFF</span>"; ?>
                            </td>

                            <td>
                                <?= ($row['e_start'] !== '00:00:00' && $row['e_end'] !== '00:00:00')
                                    ? date('h:i', strtotime($row['e_start'])) . ' - ' . date('h:i', strtotime($row['e_end']))
                                    : "<span class='text-danger fw-bold'>OFF</span>"; ?>
                            </td>
                            <td>
                                <!-- Trigger Modal -->
                                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#bookModal<?= $row['d_id']; ?>">
                                    Book Now
                                </button>
                            </td>
                        </tr>
                        <!-- Modal for Booking -->
                        <div class="modal fade mt-5" id="bookModal<?= $row['d_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="bookModalLabel<?= $row['d_id']; ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="bookModalLabel<?= $row['d_id']; ?>">Book Appointment</h5>

                                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body fw-bold text-center">
                                        <!-- Doctor's Name on Top -->
                                        <h5 class="mb-1">Dr. <?= ucwords(htmlspecialchars($row['name'])); ?></h5>

                                        <form method="POST" class="row">
                                            <input type="hidden" name="d_id" value="<?= htmlspecialchars($row['d_id']); ?>">
                                            <!-- Appointment Date -->
                                            <div class="my-4 d-flex">
                                                <label for="appointment_date_<?= $row['d_id']; ?>" class="form-label col-md-5 my-auto">Appointment Date</label>
                                                <input type="date" class="form-control col-md-6 w-50" id="appointment_date_<?= $row['d_id']; ?>" name="date" required min="<?= $currentDate; ?>" max="<?= $maxDate; ?>">
                                            </div>
                                            <!-- Shift Selection -->
                                            <div class="my-4 d-flex">
                                                <label for="shift_<?= $row['d_id']; ?>" class="form-label col-md-5">Shift</label>

                                                <select class="form-control col-md-6 w-50" id="shift_<?= $row['d_id']; ?>" name="shift" required>
                                                    <?php if ($row['m_start'] !== '00:00:00' && $row['m_end'] !== '00:00:00'): ?>
                                                        <option value="Morning">Morning</option>
                                                    <?php else: ?>
                                                        <option disabled class="text-danger">Morning (OFF)</option>
                                                    <?php endif; ?>

                                                    <?php if ($row['e_start'] !== '00:00:00' && $row['e_end'] !== '00:00:00'): ?>
                                                        <option value="Evening">Evening</option>
                                                    <?php else: ?>
                                                        <option disabled class="text-danger">Evening (OFF)</option>
                                                    <?php endif; ?>
                                                </select>


                                            </div>
                                            <button type="submit" class="btn btn-primary " name="book_appointment">Confirm Appointment</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-danger">No doctors found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
// Appointment Booking Logic
if (isset($_POST['book_appointment'])) {
    $d_id = intval($_POST['d_id']);
    $date = $_POST['date'];
    $shift = htmlspecialchars($_POST['shift']);
    $p_id = intval($_SESSION['p_id']);
    $a_id = isset($_POST['a_id']) ? intval($_POST['a_id']) : null; // Check if it's rescheduling
    $today = strtotime(date('Y-m-d'));
    $maxAllowed = strtotime('+2 months');
    if (strtotime($date) < $today || strtotime($date) > $maxAllowed) {
        $_SESSION['alertMessage'] = "Invalid appointment date.";
        $_SESSION['alertType'] = "alert-danger";
        header("Location: index.php?page=book_app.php");
        exit();
    }
    if ($a_id) {
        // Fetch the appointment details
        $queryCheck = "SELECT status, rescheduled FROM appointment WHERE a_id = ? AND p_id = ?";
        $stmtCheck = $conn->prepare($queryCheck);
        $stmtCheck->bind_param('ii', $a_id, $p_id);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        $appointment = $resultCheck->fetch_assoc();
        $stmtCheck->close();

        if (!$appointment) {
            $_SESSION['alertMessage'] = "Error: Appointment not found.";
            $_SESSION['alertType'] = "alert-danger";
        } elseif ($appointment['status'] !== 'Pending') {
            // If the status is not Pending, rescheduling is not allowed
            $_SESSION['alertMessage'] = "You cannot reschedule a non-pending appointment.";
            $_SESSION['alertType'] = "alert-danger";
        } elseif ($appointment['rescheduled'] >= 1) {
            // If the appointment has already been rescheduled once
            $_SESSION['alertMessage'] = "This appointment has already been rescheduled.";
            $_SESSION['alertType'] = "alert-warning";
        } else {
            // Proceed with rescheduling
            $queryUpdate = "UPDATE appointment SET date = ?, shift = ?, rescheduled = 1 WHERE a_id = ?";
            $stmtUpdate = $conn->prepare($queryUpdate);
            $stmtUpdate->bind_param('ssi', $date, $shift, $a_id);

            if ($stmtUpdate->execute()) {
                $_SESSION['alertMessage'] = "Appointment rescheduled successfully!";
                $_SESSION['alertType'] = "alert-success";
            } else {
                $_SESSION['alertMessage'] = "Error rescheduling appointment.";
                $_SESSION['alertType'] = "alert-danger";
            }
            $stmtUpdate->close();
        }
    } else {
        // New Booking Logic

        // Check if the patient already has a pending appointment with the same doctor
        $queryCheckExisting = "SELECT COUNT(*) AS count FROM appointment WHERE p_id = ? AND d_id = ? AND status = 'Pending'";
        $stmtCheckExisting = $conn->prepare($queryCheckExisting);
        $stmtCheckExisting->bind_param('ii', $p_id, $d_id);
        $stmtCheckExisting->execute();
        $resultCheckExisting = $stmtCheckExisting->get_result();
        $appointmentCountExisting = $resultCheckExisting->fetch_assoc()['count'];
        $stmtCheckExisting->close();

        if ($appointmentCountExisting > 0) {
            // If a pending appointment with the same doctor exists, show an error message
            $_SESSION['alertMessage'] = "You already have a pending appointment with this doctor.";
            $_SESSION['alertType'] = "alert-danger";
        } else {
            // Check the number of pending appointments
            $queryCheckPending = "SELECT COUNT(*) AS count FROM appointment WHERE p_id = ? AND status = 'Pending'";
            $stmtCheckPending = $conn->prepare($queryCheckPending);
            $stmtCheckPending->bind_param('i', $p_id);
            $stmtCheckPending->execute();
            $resultCheckPending = $stmtCheckPending->get_result();
            $pendingAppointmentCount = $resultCheckPending->fetch_assoc()['count'];
            $stmtCheckPending->close();

            if ($pendingAppointmentCount >= 3) {
                $_SESSION['alertMessage'] = "You cannot book more than 3 pending appointments.";
                $_SESSION['alertType'] = "alert-danger";
            } else {
                // Check the number of appointments booked in the last 20 days
                $queryCount = "SELECT COUNT(*) as count FROM appointment 
                        WHERE p_id = ? 
                        AND date BETWEEN DATE_SUB(CURDATE(), INTERVAL 20 DAY) AND CURDATE() 
                        AND status NOT IN ('Canceled')";
                $stmtCount = $conn->prepare($queryCount);
                $stmtCount->bind_param('i', $p_id);
                $stmtCount->execute();
                $resultCount = $stmtCount->get_result();
                $appointmentCount = $resultCount->fetch_assoc()['count'];
                $stmtCount->close();

                if ($appointmentCount >= 3) {
                    $_SESSION['alertMessage'] = "You can book up to 3 appointments in 20 days unless all are canceled.";
                    $_SESSION['alertType'] = "alert-danger";
                } else {
                    // Insert new appointment
                    $queryInsert = "INSERT INTO appointment (p_id, d_id, date, shift, status, rescheduled) 
                                    VALUES (?, ?, ?, ?, 'Pending', 0)";
                    $stmtInsert = $conn->prepare($queryInsert);
                    $stmtInsert->bind_param('iiss', $p_id, $d_id, $date, $shift);

                    if ($stmtInsert->execute()) {
                        $_SESSION['alertMessage'] = "Appointment booked successfully!";
                        $_SESSION['alertType'] = "alert-success";
                    } else {
                        $_SESSION['alertMessage'] = "Error booking appointment.";
                        $_SESSION['alertType'] = "alert-danger";
                    }
                    $stmtInsert->close();
                }
            }
        }
    }
    // Redirect to prevent form resubmission
    header("Location: index.php?page=book_app.php");
    exit();
}
?>