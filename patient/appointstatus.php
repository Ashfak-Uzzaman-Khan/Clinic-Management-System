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

// Fetch patient's appointments
$patient_id = $_SESSION['user_id'];
$sql = "SELECT * FROM appointments WHERE patient_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
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
    <title>Appointment Status - Patient</title>
    <link rel="stylesheet" href="appointstatus.css">
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
            <li><a href="patient_dashboard.php">Dashboard</a></li>
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
            <img src="../images/logo.png" alt="Logo" class="sidebar-logo">
            <h3>Patient Panel</h3>
            <hr style="border:none; height:3px; background-color:white; width:80%; margin-top:10px;">
        </div>
        <ul class="sidebar-menu">
            <li><a href="patient_dashboard.php"><img src="../images/profile.png" class="sidebar-icon">Dashboard</a></li>
            <li><a href="docrequ.php"><img src="../images/appointment.png" class="sidebar-icon">Book Appointment</a></li>
            <li><a href="appointstatus.php"><img src="../images/status.png" class="sidebar-icon">Appointment Status</a></li>
            <li><a href="#"><img src="../images/test.png" class="sidebar-icon">Test Reports</a></li>
            <li><a href="../log.php"><img src="../images/logout.png" class="sidebar-icon">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard-container">
            <h2 class="welcome-text">My Appointment Status</h2>
            
            <!-- Stats Overview -->
            <div class="cards-container">
                <div class="card stats">
                    <img src="../images/appointment.png" alt="Appointment Icon">
                    <h2><?php echo count($appointments); ?></h2>
                    <p>Total Appointments</p>
                </div>

                <div class="card stats">
                    <img src="../images/pending.png" alt="Pending Icon">
                    <h2><?php 
                        $pending = 0;
                        foreach($appointments as $appointment) {
                            if($appointment['status'] == 'pending') $pending++;
                        }
                        echo $pending;
                    ?></h2>
                    <p>Pending</p>
                </div>

                <div class="card stats">
                    <img src="../images/approved.png" alt="Approved Icon">
                    <h2><?php 
                        $approved = 0;
                        foreach($appointments as $appointment) {
                            if($appointment['status'] == 'approved') $approved++;
                        }
                        echo $approved;
                    ?></h2>
                    <p>Approved</p>
                </div>
            </div>

            <!-- Divider -->
            <hr class="divider">

            <!-- Appointments Table -->
            <div class="card card-table">
                <h3>My Appointment Requests</h3>
                
                <?php if (count($appointments) > 0): ?>
                <div class="table-container">
                    <table class="appointments-table">
                        <thead>
                            <tr>
                                <th>Appointment ID</th>
                                <th>Doctor Details</th>
                                <th>Date & Time</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Request Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($appointments as $appointment): ?>
                            <tr>
                                <td>#<?php echo $appointment['appointment_id']; ?></td>
                                <td>
                                    <strong>Dr. <?php echo $appointment['doctor_name']; ?></strong><br>
                                    <small><?php echo $appointment['doctor_department']; ?></small><br>
                                    <small><?php echo $appointment['doctor_email']; ?></small>
                                </td>
                                <td>
                                    <strong><?php echo $appointment['appointment_date']; ?></strong><br>
                                    <small><?php echo $appointment['appointment_time']; ?></small>
                                </td>
                                <td><?php echo $appointment['reason']; ?></td>
                                <td>
                                    <span class="status-<?php echo $appointment['status']; ?>">
                                        <?php echo ucfirst($appointment['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($appointment['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="no-data">
                    <p>You haven't made any appointment requests yet.</p>
                    <a href="docrequ.php" class="card-btn">Book Your First Appointment</a>
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