<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
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

// Handle status updates
if (isset($_GET['action']) && isset($_GET['id'])) {
    $appointment_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if ($action == 'approve') {
        $update_sql = "UPDATE appointments SET status = 'approved' WHERE appointment_id = ?";
    } elseif ($action == 'reject') {
        $update_sql = "UPDATE appointments SET status = 'rejected' WHERE appointment_id = ?";
    }
    
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all appointments
$sql = "SELECT * FROM appointments ORDER BY created_at DESC";
$result = $conn->query($sql);

$appointments = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments - Admin</title>
    <link rel="stylesheet" href="appoint.css">
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
            <li><a href="admin.php">Dashboard</a></li>
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
            <h3>Admin Panel</h3>
            <hr style="border:none; height:3px; background-color:white; width:80%; margin-top:10px;">
        </div>
        <ul class="sidebar-menu">
          <li><a href="../admin/adprofile.php">Profile</a></li>
            <li><a href="../admin/adsetting.php">Setting</a></li>
                        <li><a href="../admin/admin.php">Dashboard</a></li>
            <li><a href="../log.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard-container">
            <h2 class="welcome-text">Manage Doctor Appointments</h2>
            
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
                    <p>Pending Approval</p>
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
                <h3>All Appointment Requests</h3>
                
                <?php if (count($appointments) > 0): ?>
                <div class="table-container">
                    <table class="appointments-table">
                        <thead>
                            <tr>
                                <th>Appointment ID</th>
                                <th>Patient Details</th>
                                <th>Doctor Details</th>
                                <th>Date & Time</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($appointments as $appointment): ?>
                            <tr>
                                <td>#<?php echo $appointment['appointment_id']; ?></td>
                                <td>
                                    <strong><?php echo $appointment['patient_name']; ?></strong><br>
                                    <small><?php echo $appointment['patient_email']; ?></small><br>
                                    <small><?php echo $appointment['patient_contact']; ?></small>
                                </td>
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
                                <td>
                                    <?php if($appointment['status'] == 'pending'): ?>
                                        <a href="appoint.php?action=approve&id=<?php echo $appointment['appointment_id']; ?>" class="card-btn approve-btn">Approve</a>
                                        <a href="appoint.php?action=reject&id=<?php echo $appointment['appointment_id']; ?>" class="card-btn reject-btn">Reject</a>
                                    <?php else: ?>
                                        <span class="action-completed">Action Taken</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="no-data">
                    <p>No appointment requests found.</p>
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