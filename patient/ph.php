<?php 
session_start();
$patient_name = "Patient"; 

// Example data (later fetch from DB)
$total_doctors = 12;
$total_ambulances = 5;
$my_appointments = 3;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="ph.css"> <!-- External CSS -->
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
            <li><a href="../patient/ptprofile.php"><img src="../images/profile.png" class="sidebar-icon">Profile</a></li>
            <li><a href="appointstatus.php"><img src="../images/appointment.png" class="sidebar-icon">My Appointments</a></li>
                        <li><a href="../patient/seetest.php"><img src="../images/test.png" class="sidebar-icon">Test Status</a></li>

            <li><a href="../patient/seeambulance.php"><img src="../images/test.png" class="sidebar-icon">Ambulance Status</a></li>
            <li><a href="../patient/patientsetting.php"><img src="../images/setting.png" class="sidebar-icon">Settings</a></li>
            <li><a href="#"><img src="../images/logout.png" class="sidebar-icon">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">

        <div class="dashboard-container">
            <h2 class="welcome-text">Welcome - <?php echo $patient_name; ?>!</h2>
            
           


                <div class="card stats">
                    
                    <h2><?php echo $my_appointments; ?></h2>
                    <p>My Appointments</p>
                </div>
            </div>

            <!-- Divider -->
            <hr class="divider">

            <!-- Action Cards -->
            <div class="cards-container">
                <div class="card">
                    <h3>Doctor List</h3>
                    <p>View all available doctors and their specialties</p>
                    <a href="doclist.php" class="card-btn">View Doctors</a>
                </div>


                <div class="card">
                    <h3>Apply for Test/Operation</h3>
                    <p>Request medical tests or surgical operations</p>
                    <a href="../patient/testop.php" class="card-btn">Apply Now</a>
                </div>


                    
                

                <div class="card">
                    <h3>Book Appointment</h3>
                    <p>Schedule an appointment with a doctor</p>
                    <a href="docrequ.php" class="card-btn">Book Now</a>
                </div>

                <div class="card">
                    <h3>Ambulance Service</h3>
                    <p>Request ambulance for emergency services</p>
                    <a href="../patient/pa.php" class="card-btn">Request Ambulance</a>
                </div>

                <div class="card">
                    <h3>Doctor Communication</h3>
                    <p>Chat with Doctor for Appointment details and after Visit Care</p>
                    <a href="../patient/patient_chat.php" class="card-btn">Start Chat</a>
                </div>

                <div class="card">
                    <h3>Prescriptions</h3>
                    <p>Access your current and past prescriptions</p>
                    <a href="prescriptions.php" class="card-btn">View Prescriptions</a>
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