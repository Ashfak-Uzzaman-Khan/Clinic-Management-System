<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Appointment Slots</title>
<link rel="stylesheet" href="..\css/manageSlot.css">
</head>
<body>

<?php 
session_start();
$doctor_name = "Dr. Golam Rahman"; 

$slots = [
  ["date" => "2025-09-10", "time" => "10:00 AM - 11:00 AM", "status" => "Available"],
  ["date" => "2025-09-10", "time" => "11:00 AM - 12:00 PM", "status" => "Booked"],
  ["date" => "2025-09-11", "time" => "02:00 PM - 03:00 PM", "status" => "Available"],
];
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
      <li><a href="manageSlot.php"><img src="../images/apointment.avif" class="sidebar-icon">Manage Slots</a></li>
      <li><a href="reportsPrescriptions.php"><img src="../images/report.jpg" class="sidebar-icon">Reports & Prescriptions</a></li>
      <li><a href="patientChat.php"><img src="../images/message.webp" class="sidebar-icon">Patient Communication</a></li>
      <li><a href="../admin/editdoctorApp.php" class="active"><img src="../images/settings.png" class="sidebar-icon">Settings</a></li>
      <li><a href="myProfile.php" class="active"><img src="../images/settings.png" class="sidebar-icon">Profile</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="dashboard-container">

      <h2 class="welcome-text">Manage Appointment Slots - <?php echo $doctor_name; ?></h2>

      <!-- Form to Add New Slot -->
      <div class="card card-form">
        <h3>Create New Slot</h3>
        <form action="" method="POST" class="slot-form">
          <label>Date:
            <input type="date" name="slot_date" required>
          </label>
          <label>Time:
            <input type="time" name="start_time" required> - 
            <input type="time" name="end_time" required>
          </label>
          <button type="submit" class="card-btn">Add Slot</button>
        </form>
      </div>

      <hr class="divider">

      <!-- Table to Manage Slots -->
      <div class="card card-table">
        <h3>My Slots</h3>
        <table class="slots-table">
          <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
          <?php foreach($slots as $slot): ?>
          <tr>
            <td><?php echo $slot["date"]; ?></td>
            <td><?php echo $slot["time"]; ?></td>
            <td><?php echo $slot["status"]; ?></td>
            <td>
              <a href="slotEdit.php" class="card-btn">Edit</a>
              <a href="#" class="card-btn delete-btn">Delete</a>
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
