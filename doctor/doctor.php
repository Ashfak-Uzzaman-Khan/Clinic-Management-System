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

// Fetch doctor's appointments (only approved ones)
$doctor_id = $_SESSION['user_id'];
$sql = "SELECT * FROM appointments WHERE doctor_id = ? AND status = 'approved' ORDER BY appointment_date ASC, appointment_time ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}

// Calculate appointment statistics
$today_date = date('Y-m-d');
$today_appointments = 0;
$upcoming_appointments = 0;
$total_appointments = count($appointments);

foreach($appointments as $appointment) {
    if($appointment['appointment_date'] == $today_date) {
        $today_appointments++;
    }
    if($appointment['appointment_date'] > $today_date) {
        $upcoming_appointments++;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doctor Dashboard</title>
  <link rel="stylesheet" href="doctor.css"> 
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
      <li><a href="../index.html">Home</a></li>
      <li><a href="doctor.php">Dashboard</a></li>
      <li><a href="../log.php">Log Out</a></li>
    </ul>
  </nav>
</header>

<!-- Dashboard Wrapper -->
<div class="dashboard-wrapper">

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="sidebar-header">
      <img src="../images/logo.png" alt="Doctor" class="sidebar-logo">
      <h3>Doctor Panel</h3>
      <hr style="border:none; height:3px; background-color:white; width:80%; margin-top:10px;">
    </div>
    <ul class="sidebar-menu">
      <li><a href="doctor.php"><img src="../images/profile.png" class="sidebar-icon">Dashboard</a></li>
      <li><a href="manageSlot.php"><img src="../images/apointment.avif" class="sidebar-icon">Manage Slots</a></li>
      <li><a href="doctorAppointments.php"><img src="../images/appointment.png" class="sidebar-icon">My Appointments</a></li>
      <li><a href="reportsPrescriptions.php"><img src="../images/report.jpg" class="sidebar-icon">Reports & Prescriptions</a></li>
      <li><a href="patientChat.php"><img src="../images/message.webp" class="sidebar-icon">Patient Communication</a></li>
      <li><a href="settings.php"><img src="../images/settings.png" class="sidebar-icon">Settings</a></li>
      <li><a href="myProfile.php"><img src="../images/settings.png" class="sidebar-icon">My Profile</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="dashboard-container">

      <h2 class="welcome-text">Welcome - Dr. <?php echo $_SESSION['user_name']; ?>!</h2>

      <!-- Stats Overview -->
      <div class="cards-container">
        <div class="card stats">
          <img src="../images/appointment.png" alt="Appointment Icon">
          <h2><?php echo $total_appointments; ?></h2>
          <p>Total Appointments</p>
        </div>

        <div class="card stats">
          <img src="../images/today.png" alt="Today Icon">
          <h2><?php echo $today_appointments; ?></h2>
          <p>Today's Appointments</p>
        </div>

        <div class="card stats">
          <img src="../images/upcoming.png" alt="Upcoming Icon">
          <h2><?php echo $upcoming_appointments; ?></h2>
          <p>Upcoming Appointments</p>
        </div>
      </div>

      <hr class="divider">

      <!-- Action Cards -->
      <div class="cards-container">
        <div class="card">
          <h3>My Appointment Details</h3>
          <p>See the appointment details set by the admin</p>
          <a href="doctorAppointments.php" class="card-btn">My Appointments</a>
        </div>

        

        <div class="card">
          <h3>Medical Reports & Prescriptions</h3>
          <p>Update patient medical reports and create prescriptions</p>
          <a href="reportsPrescriptions.php" class="card-btn">Update Records</a>
        </div>

        <div class="card">
          <h3>Patient Communication</h3>
          <p>Chat or message patients for after-appointment care</p>
          <a href="patientChat.php" class="card-btn">Start Chat</a>
        </div>

        

       
      </div>

      <!-- Today's Appointments Preview -->
      <?php if ($today_appointments > 0): ?>
      <hr class="divider">
      
      <div class="card card-table">
        <h3>Today's Appointments</h3>
        <div class="table-container">
          <table class="appointments-table">
            <thead>
              <tr>
                <th>Patient Name</th>
                <th>Time</th>
                <th>Reason</th>
                <th>Contact</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $today_count = 0;
              foreach($appointments as $appointment): 
                if($appointment['appointment_date'] == $today_date && $today_count < 5):
                  $today_count++;
              ?>
              <tr>
                <td><?php echo $appointment['patient_name']; ?></td>
                <td><?php echo $appointment['appointment_time']; ?></td>
                <td><?php echo $appointment['reason']; ?></td>
                <td><?php echo $appointment['patient_contact']; ?></td>
              </tr>
              <?php 
                endif;
              endforeach; 
              ?>
            </tbody>
          </table>
        </div>
        <?php if ($today_appointments > 5): ?>
        <div style="text-align: center; margin-top: 15px;">
          <a href="doctorAppointments.php" class="card-btn">View All Today's Appointments</a>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

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