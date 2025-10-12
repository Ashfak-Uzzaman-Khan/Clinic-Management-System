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

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - Doctor</title>
    <link rel="stylesheet" href="../doctor/doctorAppointments.css">
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

<!-- Wrapper -->
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
            <li><a href="doctorAppointments.php"><img src="../images/appointment.png" class="sidebar-icon">My Appointments</a></li>
            <li><a href="reportsPrescriptions.php"><img src="../images/report.jpg" class="sidebar-icon">Reports & Prescriptions</a></li>
            <li><a href="patientChat.php"><img src="../images/message.webp" class="sidebar-icon">Patient Communication</a></li>
            <li><a href="../log.php"><img src="../images/logout.png" class="sidebar-icon">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard-container">
            <h2 class="welcome-text">My Appointments - Dr. <?php echo $_SESSION['user_name']; ?></h2>
            
            <!-- Stats Overview -->
            <div class="cards-container">
                <div class="card stats">
                    <img src="../images/appointment.png" alt="Appointment Icon">
                    <h2><?php echo count($appointments); ?></h2>
                    <p>Total Appointments</p>
                </div>

                <div class="card stats">
                    <img src="../images/today.png" alt="Today Icon">
                    <h2><?php 
                        $today = 0;
                        $today_date = date('Y-m-d');
                        foreach($appointments as $appointment) {
                            if($appointment['appointment_date'] == $today_date) $today++;
                        }
                        echo $today;
                    ?></h2>
                    <p>Today's Appointments</p>
                </div>

                <div class="card stats">
                    <img src="../images/upcoming.png" alt="Upcoming Icon">
                    <h2><?php 
                        $upcoming = 0;
                        foreach($appointments as $appointment) {
                            if($appointment['appointment_date'] > $today_date) $upcoming++;
                        }
                        echo $upcoming;
                    ?></h2>
                    <p>Upcoming</p>
                </div>
            </div>

            <!-- Divider -->
            <hr class="divider">

            <!-- Appointments Table -->
            <div class="card card-table">
                <h3>My Approved Appointments</h3>
                
                <?php if (count($appointments) > 0): ?>
                <div class="table-container">
                    <table class="appointments-table">
                        <thead>
                            <tr>
                                <th>Appointment ID</th>
                                <th>Patient Details</th>
                                <th>Date & Time</th>
                                <th>Reason</th>
                                <th>Contact</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($appointments as $appointment): ?>
                            <tr>
                                <td>#<?php echo $appointment['appointment_id']; ?></td>
                                <td>
                                    <strong><?php echo $appointment['patient_name']; ?></strong><br>
                                    <small><?php echo $appointment['patient_email']; ?></small>
                                </td>
                                <td>
                                    <strong><?php echo $appointment['appointment_date']; ?></strong><br>
                                    <small><?php echo $appointment['appointment_time']; ?></small>
                                </td>
                                <td><?php echo $appointment['reason']; ?></td>
                                <td><?php echo $appointment['patient_contact']; ?></td>
                                <td>
                                    <span class="status-approved">
                                        Approved
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="no-data">
                    <p>No approved appointments scheduled for you yet.</p>
                </div>
                <?php endif; ?>
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