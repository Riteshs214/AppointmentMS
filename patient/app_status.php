<?php
// Get patient ID
$pid = $_SESSION['p_id'] ?? $_GET['p_id'] ?? null;
$p_id = intval($pid);

// Check if patient ID is available
if (!$p_id) {
    die("<div class='text-danger text-center'>Invalid Patient ID</div>");
}

// Get the date 15 days ago from today
$recentDate = date('Y-m-d', strtotime('-15 days'));

// Fetch appointments only from the last 15 days
$sql = "SELECT a.a_id, p.name AS patient_name, d.name AS name, d.specialize, a.date, a.shift, a.status 
    FROM appointment a
    JOIN doctors d ON a.d_id = d.d_id
    JOIN patient p ON a.p_id = p.p_id
     WHERE a.date >= ? and a.p_id=? -- Only fetch records from last 15 days
     ORDER BY a.date DESC";

// Prepare the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $recentDate,$p_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="card-header bg-primary text-white text-center">
    <h5>Appointment Status</h5>
</div>
<!-- Label for recently 15 days appointments -->
<div class="text-center mt-2">
    <span class="badge bg-info text-dark fs-6">Recently 15 Days Appointments</span>
</div>
<div class="card-body">
    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center">
            <thead class="table-info">
                <tr>
                    <th>S.no</th>
                    <th>Doctor Name</th>
                    <th>Specialist</th>
                    <th>Date</th>
                    <th>Shift</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if results are found
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Change badge color based on status
                        $statusClass = [
                            "Pending" => "bg-warning",
                            "Confirmed" => "bg-success",
                            "Cancelled" => "bg-danger"
                        ];
                        $statusColor = $statusClass[$row['status']] ?? "bg-secondary";
                        echo "<tr>
                            <td>{$row['a_id']}</td>
                            <td>Dr. " . ucwords($row['name']) . "</td>
                            <td>{$row['specialize']}</td>
                            <td>" . date('d-m-Y', strtotime($row['date'])) . "</td>
                            <td>{$row['shift']}</td>
                            <td><span class='badge $statusColor'>" . htmlspecialchars($row['status']) . "</span></td>
                        </tr>";
                    }
                } else {
                    // Display message when no appointments are found
                    echo "<tr><td colspan='8' class='text-danger'>No recent appointments found</td></tr>";
                }

                // Close statement
                $stmt->close();
                ?>
            </tbody>
        </table>
    </div>
</div>