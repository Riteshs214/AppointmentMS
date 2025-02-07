<?php
$search = "";
$selectedDate = "";
$maxDate = date('Y-m-d', strtotime('+2 months'));

//Process from input
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search = isset($_POST['app_nameid']) ? trim($_POST['app_nameid']) : "";
    $selectedDate = isset($_POST['appointmentDate']) ? $_POST['appointmentDate'] : "";

    // Reset date if "Clear Date" is clicked
    if (isset($_POST['clear_date'])) {
        $selectedDate = "";
        $search = "";
    }
}

$query = "SELECT 
a.a_id, 
a.p_id, 
a.d_id, 
a.date, 
a.shift, 
a.status, 
p.name AS patient_name, 
d.name AS doctor_name, 
d.specialize, 
d.fees 
FROM appointment a
INNER JOIN patient p ON a.p_id = p.p_id
INNER JOIN doctors d ON a.d_id = d.d_id";

$conditions = []; // Initialize an empty array for conditions

// Search filter
if (!empty($search)) {
    $conditions[] = "(p.name LIKE '%$search%' OR d.name LIKE '%$search%' OR a.a_id = '$search')";
}

// Date filter (show past records)
if (!empty($selectedDate)) {
    $conditions[] = "a.date <= '$selectedDate'";
}

// Append conditions to the query
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$result = $conn->query($query);
?>

<div class="card-header bg-primary text-white text-center">
    <h5>Appointment Records</h5>
</div>
<div class="card-body">
    <!-- Search & Date Filter Section -->
    <form action="" method="post">
        <div class="row justify-content-center align-items-center mb-2">
            <div class="col-sm-12 d-flex align-items-center">
                <label for="app_nameid" class="form-label col-md-1 my-auto">Search:</label>
                <input type="text" id="app_nameid" name="app_nameid"
                    class="form-control col-md-3 w-25"
                    placeholder="Search by name or ID..."
                    value="<?= htmlspecialchars($search); ?>">

                <label for="appointmentDate" class="form-label mx-2  my-auto">Date:</label>
                <input type="date" id="appointmentDate" name="appointmentDate"
                    class="form-control col-md-3 w-25"
                    max="<?= $maxDate; ?>"
                    value="<?= htmlspecialchars($selectedDate); ?>">

                <!-- Clear Date Button -->
                <button type="submit" name="clear_date" class="btn btn-sm btn-danger ms-2">❌</button>

                <button type="submit" class="btn btn-sm btn-primary ms-2">Search</button>
            </div>
        </div>
    </form>

    <!-- Appointment Records Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center">
            <thead class="table-info">
                <tr>
                    <th>S.no</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Specialist</th>
                    <th>Fees</th>
                    <th>Date</th>
                    <th>Shift</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['a_id']); ?></td>
                            <td><?= htmlspecialchars($row['patient_name']); ?></td>
                            <td><?= htmlspecialchars($row['doctor_name']); ?></td>
                            <td><?= htmlspecialchars($row['specialize']); ?></td>
                            <td><?= htmlspecialchars($row['fees']); ?></td>
                            <td><?= date('d-m-Y', strtotime($row['date'])); ?></td>
                            <td><?= htmlspecialchars($row['shift']); ?></td>
                            <td>
                                <?php
                                // Change badge color based on status
                                $statusClass = [
                                    "Pending" => "bg-warning",
                                    "Confirmed" => "bg-success",
                                    "Cancelled" => "bg-danger"
                                ];
                                $statusColor = $statusClass[$row['status']] ?? "bg-secondary";
                                echo "<span class='badge $statusColor'>" . htmlspecialchars($row['status']) . "</span>";
                                ?>
                            </td>
                        </tr>

                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-danger">No appointment records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$conn->close();
?>