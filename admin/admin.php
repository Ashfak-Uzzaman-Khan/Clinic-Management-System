<?php 
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../log.php");
    exit();
}

// Get admin name from session
$admin_name = $_SESSION['user_name']; 

// Example data (later fetch from DB)
$total_doctors = 12;
$total_patients = 150;
$total_ambulances = 5;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../admin/admin.css"> <!-- External CSS -->
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
            <li><a href="../log.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">

        <div class="dashboard-container">
            <h2 class="welcome-text">Welcome - <?php echo $admin_name; ?>!</h2>
            
            <!-- Stats Boxes Row -->
            <div class="cards-container">
                <div class="card stats">
                    <img src="../images/doctor.png" alt="Doctor Icon">
                    <h2><?php echo $total_doctors; ?></h2>
                    <p>Total Doctors</p>
                </div>

                <div class="card stats">
                    <img src="../images/patient.png" alt="Patient Icon">
                    <h2><?php echo $total_patients; ?></h2>
                    <p>Total Patients</p>
                </div>

                <div class="card stats">
                    <img src="../images/ambulance.png" alt="Ambulance Icon">
                    <h2><?php echo $total_ambulances; ?></h2>
                    <p>Total Ambulances</p>
                </div>
            </div>

            <!-- Divider -->
            <hr class="divider">

            <!-- Action Cards -->
            <div class="cards-container">
                <div class="card">
                    <h3>Patient List</h3>
                    <p>View and manage all users in the system</p>
                    <a href="../admin/patientlist.php" class="card-btn">See Patients</a>
                </div>

                <div class="card">
                    <h3>Doctor List</h3>
                    <p>View and manage all users in the system</p>
                    <a href="../admin/doclist.php" class="card-btn">See Doctors</a>
                </div>

                <div class="card">
                    <h3>Test Requests</h3>
                    <p>Review and approve test/operation requests from patients</p>
                    <a href="../admin/testrequest.php" class="card-btn">Go to Requests</a>
                </div>

                <div class="card">
                    <h3>Doctor Appointments</h3>
                    <p>Appoint doctors for patients and notify them</p>
                    <a href="appoint.php" class="card-btn">Appoint Doctors</a>
                </div>

                <div class="card">
                    <h3>Ambulance Requests</h3>
                    <p>Review and approve requests from patients</p>
                    <a href="../admin/arequest.php" class="card-btn">Go to Requests</a>
                </div>

                <div class="card">
                    <h3>Doctor Approvals</h3>
                    <p>Approve doctor registration/approval requests</p>
                    <a href="../admin/doctorApp.php" class="card-btn">Approve Requests</a>
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