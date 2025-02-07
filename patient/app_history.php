<?php
$search = "";
$selectedDate = "";
$maxDate = date('Y-m-d', strtotime('+2 months'));

// Process input
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search = isset($_POST['app_nameid']) ? trim($_POST['app_nameid']) : "";
    $selectedDate = isset($_POST['appointmentDate']) ? $_POST['appointmentDate'] : "";

    // Reset filters if "Clear Date" is clicked
    if (isset($_POST['clear_date'])) {
        $selectedDate = "";
        $search = "";
    }
}

// Get patient ID
$pid = $_SESSION['p_id'] ?? $_GET['p_id'] ?? null;
$p_id = intval($pid);

// Base query
$query = "SELECT a.a_id, d.name AS name, d.specialize AS specialize, d.fees, a.date, a.shift, a.status 
          FROM appointment a 
          JOIN doctors d ON a.d_id = d.d_id 
          WHERE a.p_id = ?";

$paramTypes = "i"; // First parameter (patient ID)
$paramValues = [$p_id];

// Check if search or date filters exist
if (!empty($search)) {
    $query .= " AND (d.name LIKE ? OR a.a_id = ?)";
    $paramTypes .= "si";
    $searchParam = "%$search%";
    $paramValues[] = $searchParam;
    $paramValues[] = $search;
}

if (!empty($selectedDate)) {
    $query .= " AND a.date <= ?";
    $paramTypes .= "s";
    $paramValues[] = $selectedDate;
}

// Add ORDER BY at the end
$query .= " ORDER BY a.date DESC";

// Prepare statement
$stmt = $conn->prepare($query);

// Prepare and execute the statement
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("SQL Error: " . $conn->error); // Debugging output
}

// Bind parameters dynamically
$stmt->bind_param($paramTypes, ...$paramValues);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="card-header bg-primary text-white text-center">
    <h5>Appointment History</h5>
</div>
<div class="card-body">
    <!-- Search & Date Filter Section -->
    <form action="" method="post">
        <div class="row justify-content-center align-items-center mb-2">
            <div class="col-md-12 d-flex align-items-center">
                <label for="app_nameid" class="form-label col-md-1 my-auto">Search:</label>
                <input type="text" id="app_nameid" name="app_nameid"
                    class="form-control col-md-3 w-25"
                    placeholder="Search by name or ID..."
                    value="<?= htmlspecialchars($search); ?>">

                <label for="appointmentDate" class="form-label mx-2 my-auto">Date:</label>
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
    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center">
            <thead class="table-info">
                <tr>
                    <th>ID</th>
                    <th>Doctor Name</th>
                    <th>Specialist</th>
                    <th>Fees</th>
                    <th>Date</th>
                    <th>Shift</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if any rows are returned
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                         // Change badge color based on status
                         $statusClass = [
                            "Pending" => "bg-warning",
                            "Confirmed" => "bg-success",
                            "Cancelled" => "bg-danger"
                        ];
                        $statusColor = $statusClass[$row['status']] ?? "bg-secondary";
                        echo "<tr>";
                        echo "<td>" . $row['a_id'] . "</td>";
                        echo "<td>Dr. " . ucwords($row['name']) . "</td>";
                        echo "<td>" . $row['specialize'] . "</td>";
                        echo "<td>₹" . $row['fees'] . "</td>";
                        echo "<td>" . date('d-m-Y', strtotime($row['date'])) . "</td>";
                        echo "<td>" . $row['shift'] . "</td>";
                        echo "<td><span class='badge $statusColor'>" . htmlspecialchars($row['status']) . "</span></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>No appointments found.</td></tr>";
                }
                // Close the connection
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>
</div>