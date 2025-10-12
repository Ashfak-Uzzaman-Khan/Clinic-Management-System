<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Report / Prescription</title>
  <link rel="stylesheet" href="..\css/prescriptionEdit.css">
</head>
<body>

<?php 
session_start();
$doctor_name = "Dr. Golam Rahman"; 
include "db_connect.php";  

// Check if ID is passed
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM reports WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $record = $result->fetch_assoc();
    } else {
        die(" Record not found!");
    }
} else {
    die(" No record ID provided!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_name = $_POST['patient_name'];
    $report_type  = $_POST['report_type'];
    $prescription = $_POST['prescription'];
    $status       = $_POST['status'];

    $sql = "UPDATE reports 
            SET patient='$patient_name', report='$report_type', prescription='$prescription', status='$status'
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: reportsPrescriptions.php?msg=updated");
        exit();
    } else {
        echo "❌ Error updating record: " . $conn->error;
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

      <h2 class="welcome-text">Edit Report / Prescription - <?php echo $doctor_name; ?></h2>

      <!-- Form to Edit Record -->
      <div class="card card-form">
        <h3>Edit Record</h3>
        <form action="" method="POST" enctype="multipart/form-data" class="record-form">

          <div class="form-group">
            <input type="text" name="patient_name" value="<?php echo $record['patient']; ?>" placeholder="Patient Name" required>
            <input type="text" name="report_type" value="<?php echo $record['report']; ?>" placeholder="Report Type (e.g. Blood Test, MRI)" required>
            <textarea name="prescription" rows="3" placeholder="Prescription details..." required><?php echo $record['prescription']; ?></textarea>
             <input type="file" name="report_file">
            <label>
              <select name="status" required>
                <option value="Pending" <?php if($record['status']=="Pending") echo "selected"; ?>>Pending</option>
                <option value="Completed" <?php if($record['status']=="Completed") echo "selected"; ?>>Completed</option>
                <option value="In Progress" <?php if($record['status']=="In Progress") echo "selected"; ?>>In Progress</option>
              </select>
            </label>

           
          </div>

          <button type="submit" class="card-btn">Update Record</button>
        </form>
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
