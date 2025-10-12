<?php 
session_start();

// Check if patient is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'patient') {
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

// Fetch patient data from database
$patient_id = $_SESSION['user_id'];
$sql = "SELECT patient_name, patient_email, patient_contactNo, patient_address, patient_age, patient_gender, patient_bloodGrp, patient_dob, patient_emNo FROM patientlog WHERE patient_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $patient = $result->fetch_assoc();
    $name = $patient['patient_name'];
    $email = $patient['patient_email'];
    $contact = $patient['patient_contactNo'];
    $address = $patient['patient_address'];
    $age = $patient['patient_age'];
    $gender = $patient['patient_gender'];
    $blood_group = $patient['patient_bloodGrp'];
    $dob = $patient['patient_dob'];
    $emergency_contact = $patient['patient_emNo'];
} else {
    // Fallback values if no data found
    $name = "Patient Name";
    $email = "email@hospital.com";
    $contact = "Not provided";
    $address = "Not provided";
    $age = "Not provided";
    $gender = "Not provided";
    $blood_group = "Not provided";
    $dob = "Not provided";
    $emergency_contact = "Not provided";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile</title>
    <link rel="stylesheet" href="ptprofile.css">
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
            <li><a href="ph.php">Dashboard</a></li>
            <li><a href="#">About Us</a></li>
            <li><a href="../log.php">Log Out</a></li>
        </ul>
    </nav>
</header>

<div class="dashboard-wrapper">

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../images/logo.png" alt="Patient" class="sidebar-logo">
            <hr style="border:none; height:3px; background-color:white; width:80%; margin-top:10px;">
            <h3>Patient Panel</h3>
        </div>
        <ul class="sidebar-menu">
            <li><a href="ph.php"><img src="../images/dashboard.png" class="sidebar-icon">Dashboard</a></li>
            <li><a href="appointments.php"><img src="../images/apointment.avif" class="sidebar-icon">My Appointments</a></li>
            <li><a href="medical_history.php"><img src="../images/report.jpg" class="sidebar-icon">Medical History</a></li>
            <li><a href="prescriptions.php"><img src="../images/prescription.png" class="sidebar-icon">Prescriptions</a></li>
            <li><a href="ptprofile.php" class="active"><img src="../images/profile.png" class="sidebar-icon">Profile</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard-container">

            <h2 class="welcome-text">Patient Profile</h2>

            <div class="card profile-card">
                <div class="profile-item">
                    <h3>Full Name</h3>
                    <p><?php echo htmlspecialchars($name); ?></p>
                </div>
                <div class="profile-item">
                    <h3>Email Address</h3>
                    <p><?php echo htmlspecialchars($email); ?></p>
                </div>
                <div class="profile-item">
                    <h3>Contact Number</h3>
                    <p><?php echo htmlspecialchars($contact); ?></p>
                </div>
                <div class="profile-item">
                    <h3>Emergency Contact</h3>
                    <p><?php echo htmlspecialchars($emergency_contact); ?></p>
                </div>
                <div class="profile-item">
                    <h3>Age</h3>
                    <p><?php echo htmlspecialchars($age); ?> years</p>
                </div>
                <div class="profile-item">
                    <h3>Gender</h3>
                    <p><?php echo htmlspecialchars($gender); ?></p>
                </div>
                <div class="profile-item">
                    <h3>Blood Group</h3>
                    <p><?php echo htmlspecialchars($blood_group); ?></p>
                </div>
                <div class="profile-item">
                    <h3>Date of Birth</h3>
                    <p><?php echo htmlspecialchars($dob); ?></p>
                </div>
                <div class="profile-item full-width">
                    <h3>Address</h3>
                    <p><?php echo htmlspecialchars($address); ?></p>
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