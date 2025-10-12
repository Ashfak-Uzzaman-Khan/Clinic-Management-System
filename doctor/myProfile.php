<?php 
session_start();

// Check if doctor is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'doctor') {
    header("Location: ../log.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch doctor data from database
$doctor_id = $_SESSION['user_id'];
$sql = "SELECT doc_name, doc_email, doc_dept, doc_shift, doc_fees FROM doctorlog WHERE doctor_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $doctor = $result->fetch_assoc();
    $name = $doctor['doc_name'];
    $email = $doctor['doc_email'];
    $department = $doctor['doc_dept'];
    $shift = $doctor['doc_shift'];
    $fees = $doctor['doc_fees'];
} else {
    // Fallback values if no data found
    $name = "Doctor Name";
    $email = "email@hospital.com";
    $department = "Department";
    $shift = "Shift";
    $fees = "0";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile</title>
    <link rel="stylesheet" href="../css/myProfile.css">
</head>
<body>

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
            <li><a href="#">About Us</a></li>
            <li><a href="../log.php">Log Out</a></li>
        </ul>
    </nav>
</header>

<div class="dashboard-wrapper">

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../images/logo.png" alt="Doctor" class="sidebar-logo">
            <hr style="border:none; height:3px; background-color:white; width:80%; margin-top:10px;">
            <h3>Doctor Panel</h3>
        </div>
        <ul class="sidebar-menu">
            <li><a href="manageSlot.php"><img src="../images/apointment.avif" class="sidebar-icon">Manage Slots</a></li>
            <li><a href="reportsPrescriptions.php"><img src="../images/report.jpg" class="sidebar-icon">Reports & Prescriptions</a></li>
            <li><a href="patientChat.php"><img src="../images/message.webp" class="sidebar-icon">Patient Communication</a></li>
            <li><a href="settings.php"><img src="../images/settings.png" class="sidebar-icon">Settings</a></li>
            <li><a href="docprofile.php" class="active"><img src="../images/profile.png" class="sidebar-icon">Profile</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard-container">

            <h2 class="welcome-text">Doctor Profile</h2>

            <div class="card profile-card">
                <div class="profile-item">
                    <h3>Name</h3>
                    <p><?php echo htmlspecialchars($name); ?></p>
                </div>
                <div class="profile-item">
                    <h3>Email</h3>
                    <p><?php echo htmlspecialchars($email); ?></p>
                </div>
                <div class="profile-item">
                    <h3>Department</h3>
                    <p><?php echo htmlspecialchars($department); ?></p>
                </div>
                <div class="profile-item">
                    <h3>Shift</h3>
                    <p><?php echo htmlspecialchars($shift); ?></p>
                </div>
                <div class="profile-item">
                    <h3>Consultation Fees</h3>
                    <p>৳<?php echo htmlspecialchars($fees); ?></p>
                </div>
                
               
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