<?php
// Handle status updates
if (isset($_GET['confirm_id'])) {
    $a_id = intval($_GET['confirm_id']);
    $sql = "UPDATE appointment SET status='Confirmed' WHERE a_id=$a_id";
    if ($conn->query($sql)) {
        header("Location: index.php?page=app_status.php&success=Appointment confirmed successfully.");
        exit();
    } else {
        header("Location: index.php?page=app_status.php&error=" . urlencode($conn->error));
        exit();
    }
}

if (isset($_GET['cancel_id'])) {
    $a_id = intval($_GET['cancel_id']);
    $sql = "UPDATE appointment SET status='Cancelled' WHERE a_id=$a_id";
    if ($conn->query($sql)) {
        header("Location: index.php?page=app_status.php&success=Appointment cancelled successfully.");
        exit();
    } else {
        header("Location: index.php?page=app_status.php&error=" . urlencode($conn->error));
        exit();
    }
}

if (isset($_GET['pending_id'])) {
    $a_id = intval($_GET['pending_id']);
    $sql = "UPDATE appointment SET status='Pending' WHERE a_id=$a_id";
    if ($conn->query($sql)) {
        header("Location: index.php?page=app_status.php&success=Status changed back to Pending.");
        exit();
    } else {
        header("Location: index.php?page=app_status.php&error=" . urlencode($conn->error));
        exit();
    }
}
?>
<div class="card-header bg-primary text-white text-center">
    <h5>Appointment Status</h5>
</div>
<!-- Display Success/Error Messages -->
<?php
$alertMessage = "";
$alertClass = "d-none";

if (isset($_GET['success'])) {
    $alertMessage = $_GET['success'];
    $alertClass = "alert-success";
} elseif (isset($_GET['error'])) {
    $alertMessage = $_GET['error'];
    $alertClass = "alert-danger";
}
?>

<!-- Alert Message -->
<?php if ($alertMessage): ?>
    <div class="container mt-2">
        <div class="alert <?php echo $alertClass; ?> alert-dismissible fade show text-center" role="alert">
            <?php echo $alertMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>

<div class="card-body">
    <div class="table-responsive">
        <table class="table text-center table-striped table-bordered">
            <thead class="table-info">
                <tr>
                    <th>S.no</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Specialist</th>
                    <th>Date</th>
                    <th>Shift</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch appointments
                $sql = "
                    SELECT 
                        a.a_id, 
                        p.name AS patient_name, 
                        d.name AS doctor_name, 
                        d.specialize AS specialize, 
                        a.date, 
                        a.shift, 
                        a.status 
                    FROM appointment a
                    JOIN patient p ON a.p_id = p.p_id
                    JOIN doctors d ON a.d_id = d.d_id
                    ORDER BY a.date ASC, a.shift ASC
                ";
                $result = $conn->query($sql);
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $formatted_date = date('d-m-Y', strtotime($row['date']));

                        // Status badges
                        $status_text = match ($row['status']) {
                            'Pending' => '<span class="badge bg-warning">Pending</span>',
                            'Confirmed' => '<span class="badge bg-success">Confirmed</span>',
                            'Cancelled' => '<span class="badge bg-danger">Cancelled</span>',
                        };
                        echo "<tr>
                            <td>{$row['a_id']}</td>
                            <td>" . ucwords($row['patient_name']) . "</td>
                            <td>Dr. " . ucwords($row['doctor_name']) . "</td>
                            <td>{$row['specialize']}</td>
                            <td>{$formatted_date}</td>
                            <td>{$row['shift']}</td>
                            <td>{$status_text}</td>
                            <td class='d-flex flex-wrap justify-content-center'>";

                        // Dynamic Action Buttons
                        if ($row['status'] == 'Pending') {
                            echo "<a href='index.php?page=app_status.php&confirm_id={$row['a_id']}' class='btn btn-success btn-sm me-1 mb-1'>Confirm</a>
                                  <a href='index.php?page=app_status.php&cancel_id={$row['a_id']}' class='btn btn-danger btn-sm mb-1'>Cancel</a>";
                        } elseif ($row['status'] == 'Confirmed' || $row['status'] == 'Cancelled') {
                            echo "<a href='index.php?page=app_status.php&pending_id={$row['a_id']}' class='btn btn-warning btn-sm'>Pending</a>";
                        }

                        echo "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-danger'>No appointments found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>