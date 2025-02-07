<?php
$search = "";
$query = "SELECT * FROM patient";  // Base query

// Process from input
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search = isset($_POST['app_nameid']) ? trim($_POST['app_nameid']) : "";

    // Reset search if "Clear" button is clicked
    if (isset($_POST['clear_date'])) {
        $search = "";
    }
}

$conditions = []; // Initialize an empty array for conditions

// Search filter - Allow searching by name, patient ID, or contact
if (!empty($search)) {
    $conditions[] = "(name LIKE '%$search%' OR p_id LIKE '%$search%' OR contact LIKE '%$search%')";
}

// Append conditions to the query
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

// Execute query after conditions are applied
$result = $conn->query($query);
?>

<div class="card-header bg-primary text-white text-center">
    <h5>Patient Details</h5>
</div>
<div class="card-body">
    <!-- Search & Date Filter Section -->
    <form action="" method="post">
        <div class="row justify-content-center mb-2">
            <div class="col-md-12 d-flex align-items-center">
                <label for="app_nameid" class="form-label col-md-1 my-auto">Search:</label>
                <input type="text" id="app_nameid" name="app_nameid"
                    class="form-control col-md-3 w-25"
                    placeholder="Search by Name or Phone..."
                    value="<?= htmlspecialchars($search); ?>">

                <!-- Clear Button -->
                <button type="submit" name="clear_date" class="btn btn-sm btn-danger ms-2">❌</button>

                <button type="submit" class="btn btn-sm btn-primary ms-2">Search</button>
            </div>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table text-center table-striped table-bordered">
            <thead class="table-info">
                <tr>
                    <th>S.no</th>
                    <th>Patient Name</th>
                    <th>Gander</th>
                    <th>Phone no</th>
                    <th>Email ID</th>
                    <th>Updation</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['p_id']); ?></td>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['gander']); ?></td>
                            <td><?= htmlspecialchars($row['contact']); ?></td>
                            <td><?= htmlspecialchars($row['email']); ?></td>
                            <td><?= date('d-m-Y', strtotime(htmlspecialchars($row['update_at']))); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-danger">No patient records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$conn->close();
?>