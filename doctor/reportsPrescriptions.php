<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports & Prescriptions</title>
   <link rel="stylesheet" href="../css/prescription.css">
</head>
<body>

<?php 
session_start();
$doctor_name = "Dr. Golam Rahman"; 

include "db_connect.php";  //

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_name = $_POST['patient_name'];
    $report_type  = $_POST['report_type'];
    $prescription = $_POST['prescription'];
    $date         = date("Y-m-d");

    // Insert into DB
    $sql = "INSERT INTO reports (patient, date, report, prescription, status)
            VALUES ('$patient_name', '$date', '$report_type', '$prescription', 'Pending')";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'> New record added successfully</p>";
    } else {
        echo "<p style='color:red;'> Error: " . $conn->error . "</p>";
    }
}

// Fetch all records from DB
$records = [];
$result = $conn->query("SELECT * FROM reports ORDER BY date DESC");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
}
?>


<!-- Header -->
<header>
  <div class="logo">
    <img src="../images/logo.png" alt="Hospital Logo">
  </div>
  <div class="hospital-name">
    <p>Dhaka Centralized Hospital</p>
  </div>
  <nav>
    <ul>
      <li><a href="doctor.php">Dashboard</a></li>
      <li><a href="#">About us</a></li>
      <li><a href="../log.php">Log Out</a></li>
    </ul>
  </nav>
</header>

<!-- Wrapper -->
<div class="dashboard-wrapper">

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="sidebar-header">
      <img src="../images/logo.png" alt="Doctor" class="sidebar-logo">
      <h3>Doctor Panel</h3>
    </div>
    <ul class="sidebar-menu">
      <li><a href="manageSlot.php"><img src="../images/apointment.avif" class="sidebar-icon">Manage Slots</a></li>
      <li><a href="reportsPrescriptions.php"><img src="../images/report.jpg" class="sidebar-icon">Reports & Prescriptions</a></li>
      <li><a href="patientChat.php"><img src="../images/message.webp" class="sidebar-icon">Patient Communication</a></li>
      <li><a href="settings.php" class="active"><img src="../images/settings.png" class="sidebar-icon">Settings</a></li>
      <li><a href="myProfile.php" class="active"><img src="../images/settings.png" class="sidebar-icon">Profile</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="dashboard-container">

      <h2 class="welcome-text">Reports & Prescriptions - <?php echo $doctor_name; ?></h2>

      <!-- Form to Add New Report/Prescription -->
      <div class="card card-form">
        <h3>Create Prescription / Upload Report</h3>
        <form action="" method="POST" enctype="multipart/form-data" class="record-form">

          <div class="form-group">
            <input type="text" name="patient_name" placeholder="Patient Name" required>
            <input type="text" name="report_type" placeholder="Report Type (e.g. Blood Test, MRI)" required>
            <textarea name="prescription" rows="3" placeholder="Prescription details..." required></textarea>
            <input type="file" name="report_file">
          </div>

          <button type="submit" class="card-btn">Save Record</button>
        </form>
      </div>

      <hr class="divider">

      <!-- Table to View Existing Records -->
      <div class="card card-table">
        <h3>My Reports & Prescriptions</h3>
        <table class="records-table">
          <tr>
            <th>Patient</th>
            <th>Date</th>
            <th>Report</th>
            <th>Prescription</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
         <?php foreach($records as $rec): ?>
<tr>
  <td><?php echo $rec["patient"]; ?></td>
  <td><?php echo $rec["date"]; ?></td>
  <td><?php echo $rec["report"]; ?></td>
  <td><?php echo $rec["prescription"]; ?></td>
  <td><?php echo $rec["status"]; ?></td>
  <td>
    <a href="prescriptionEdit.php?id=<?php echo $rec['id']; ?>" class="card-btn">Edit</a>
    <a href="deletePrescription.php?id=<?php echo $rec['id']; ?>" 
    class="card-btn delete-btn"
    onclick="return confirm('Are you sure you want to delete this record?');">
    Delete
</a>

  </td>
</tr>
<?php endforeach; ?>

        </table>
      </div>

    </div>
  </div>
</div>

<footer>
  <div class="contain">
    <div class="contactInfo">
      <div class="socialLink">
        <a href="#"><img src="../images/icon_x.png" alt="X"></a>
        <a href="#"><img src="../images/icon_instagram.png" alt="Instagram"></a>
        <a href="#"><img src="../images/icon_fb.png" alt="Facebook"></a>
      </div>
        <div class="addressInfo">
          <h3>Contact Us</h3>
          <p> 📍 Bashundhara R/A, Dhaka, Bangladesh</p>
          <p> ☎  +880 1234 567 890</p>
          <p> ✉︎  info@dchospital.com</p>
        </div>
    </div>
  </div>
  <div class="copyRight">
    <p>&copy; 2025 Dhaka Centralized Hospital. All rights reserved.</p>
    
  </div>
</footer>
</body>
</html>
