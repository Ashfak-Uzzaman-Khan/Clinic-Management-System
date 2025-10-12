<?php 
session_start();
$doctor_name = "Dr. Golam Rahman";

// Example: selected slot data (in real use, get from DB using slot ID)
$slot = [
    "date" => "2025-09-10",
    "start_time" => "10:00",
    "end_time" => "11:00",
    "status" => "Available"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Appointment Slot</title>
  <link rel="stylesheet" href="..\css/slotEdit.css">
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
    </div>
    <ul class="sidebar-menu">
      <li><a href="manageSlot.php"><img src="../images/apointment.avif" class="sidebar-icon">Manage Slots</a></li>
      <li><a href="reportsPrescriptions.php"><img src="../images/report.jpg" class="sidebar-icon">Reports & Prescriptions</a></li>
      <li><a href="patientChat.php"><img src="../images/message.webp" class="sidebar-icon">Patient Communication</a></li>
      <li><a href="settings.php"><img src="../images/settings.png" class="sidebar-icon">Settings</a></li>
      <li><a href="myProfile.php"><img src="../images/settings.png" class="sidebar-icon">Profile</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="dashboard-container">

      <h2 class="welcome-text">Edit Appointment Slot - <?php echo $doctor_name; ?></h2>

      <!-- Form to Edit Slot -->
      <div class="card card-form">
        <h3>Edit Slot</h3>
        <form action="" method="POST" class="slot-form">
          <label>Date:
            <input type="date" name="slot_date" value="<?php echo $slot['date']; ?>" required>
          </label>
          <label>Time:
            <input type="time" name="start_time" value="<?php echo $slot['start_time']; ?>" required> - 
            <input type="time" name="end_time" value="<?php echo $slot['end_time']; ?>" required>
          </label>
          <label>Status:
            <select name="status" required>
              <option value="Available" <?php if($slot['status']=='Available') echo 'selected'; ?>>Available</option>
              <option value="Booked" <?php if($slot['status']=='Booked') echo 'selected'; ?>>Booked</option>
            </select>
          </label>
          <button type="submit" class="card-btn">Update Slot</button>
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
