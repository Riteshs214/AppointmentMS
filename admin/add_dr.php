<?php

$alertMessage = ""; // Initialize variables for alerts
$alertClass = "d-none"; // Default to hide the alert
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $conn->real_escape_string($_POST['name']);
  $specialize = $conn->real_escape_string($_POST['specialize']);
  $m_start = $conn->real_escape_string($_POST['m_start']);
  $m_end = $conn->real_escape_string($_POST['m_end']);
  $e_start = $conn->real_escape_string($_POST['e_start']);
  $e_end = $conn->real_escape_string($_POST['e_end']);
  $fees = (int)$_POST['fees'];
  $created_at = date('Y-m-d H:i:s');
  $updated_at = date('Y-m-d H:i:s');
  if (
    !empty($name) && !empty($specialize) &&
    (($m_start && $m_end) || ($e_start && $e_end)) &&
    $fees >= 200 && $fees <= 2000
  ) {
    $stmt = $conn->prepare("INSERT INTO doctors (name, specialize, m_start, m_end, e_start, e_end, fees, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssiss", $name, $specialize, $m_start, $m_end, $e_start, $e_end, $fees, $created_at, $updated_at);

    if ($stmt->execute()) {
      $alertMessage = "Doctor added successfully!";
      $alertClass = "alert-success";
    } else {
      $alertMessage = "Error adding doctor: " . $stmt->error;
      $alertClass = "alert-danger";
    }
    $stmt->close();
  } else {
    $alertMessage = "Please fill all fields correctly!";
    $alertClass = "alert-warning";
  }
}
$conn->close();
?>
<div class="card-header bg-primary text-white text-center">
  <h5>Add Doctor</h5>
</div>
<div class="card-body fw-bold mt-4">
  <!-- Alert Message -->
  <div class="alert text-center <?php echo $alertClass; ?>" role="alert">
    <?php echo $alertMessage; ?>
  </div>
  <form method="POST">
    <div class="mb-3 row">
      <label for="name" class="col-sm-3 col-form-label">Doctor Name:</label>
      <div class="col-sm-6">
        <input type="text" class="form-control" placeholder="Enter Name" name="name" required>
      </div>
    </div>
    <div class="mb-3 row">
      <label for="specialization" class="col-sm-3 col-form-label ">Specialization:</label>
      <div class="col-sm-6">
        <select class="form-select" name="specialize" required>
          <option value="" disabled selected>Select Specialization</option>
          <option value="Cardiologist">Cardiologist (Dil ke doctor)</option>
          <option value="Dermatologist">Dermatologist (Twacha ke doctor)</option>
          <option value="Neurologist">Neurologist (Dimag aur nervous system ke doctor)</option>
          <option value="Orthopedic">Orthopedic (Haddi aur joints ke doctor)</option>
          <option value="Pediatrician">Pediatrician (Bachchon ke doctor)</option>
          <option value="Gynecologist">Gynecologist (Mahila aur pregnancy ke doctor)</option>
          <option value="Oncologist">Oncologist (Cancer ke doctor)</option>
          <option value="Psychiatrist">Psychiatrist (Mansik swasthya ke doctor)</option>
          <option value="Dentist">Dentist (Daant ke doctor)</option>
          <option value="Radiologist">Radiologist (X-ray aur scans karne wale doctor)</option>
          <option value="ENT Specialist">ENT Specialist (Kaan, naak, aur gale ke doctor)</option>
          <option value="Ophthalmologist">Ophthalmologist (Aankhon ke doctor)</option>
          <option value="Urologist">Urologist (Peshab aur kidney ke doctor)</option>
          <option value="Endocrinologist">Endocrinologist (Hormones aur diabetes ke doctor)</option>
          <option value="General Physician">General Physician (Saadharan bimaariyon ke doctor)</option>
          <option value="Pulmonologist">Pulmonologist (Fefdon ke doctor)</option>
          <option value="Nephrologist">Nephrologist (Kidney ke doctor)</option>
        </select>
      </div>
    </div>
    <div class="mb-3 row">
      <label for="morning-time" class="col-sm-3 col-form-label">Morning Time:</label>
      <div class="col-sm-7 d-flex align-items-center">
        <input type="time" class="form-control w-auto" name="m_start">
        <span class="mx-2"><b>To</b></span>
        <input type="time" class="form-control w-auto" name="m_end">
      </div>
    </div>
    <div class="mb-3 row">
      <label for="evening-time" class="col-sm-3 col-form-label">Evening Time:</label>
      <div class="col-sm-7 d-flex align-items-center">
        <input type="time" class="form-control w-auto" name="e_start">
        <span class="mx-2"><b>To</b></span>
        <input type="time" class="form-control w-auto" name="e_end">
      </div>
    </div>
    <div class="mb-3 row">
      <label for="fees" class="col-sm-3 col-form-label">Fees:</label>
      <div class="col-sm-6 d-flex">
        <span class="input-group-text">₹</span>
        <input type="number" class="form-control" name="fees" id="fees" placeholder="Enter Fees" required min="200" max="2000" style="appearance: none; -moz-appearance: textfield;">
      </div>
    </div>
    <div class="text-center">
      <button type="submit" class="btn btn-primary">Add Doctor</button>
    </div>
  </form>
</div>