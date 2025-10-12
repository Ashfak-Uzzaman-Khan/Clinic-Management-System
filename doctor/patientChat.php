<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Communication</title>
  <link rel="stylesheet" href="..\css/patientChat.css">
</head>
<body>

<?php 
session_start();
$doctor_name = "";

$chats = [
  ["patient" => "Anupom", "date" => "2025-09-07 10:30", "message" => "Thank you for the consultation.", "status" => "Read"],
  ["patient" => "Bhowmik", "date" => "2025-09-08 11:00", "message" => "Can I reschedule my appointment?", "status" => "Unread"],
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
                  <hr style="border:none; height:3px; background-color:white; width:80%; margin-top:10px;">

      <h3>Doctor Panel</h3>
    </div>
    <ul class="sidebar-menu">
      <li><a href="manageSlot.php"><img src="../images/apointment.avif" class="sidebar-icon">Manage Slots</a></li>
      <li><a href="reportsPrescriptions.php"><img src="../images/report.jpg" class="sidebar-icon">Reports & Prescriptions</a></li>
      <li><a href="patientChat.php"><img src="../images/message.webp" class="sidebar-icon">Patient Communication</a></li>
      <li><a href="settings.php" class="active"><img src="../images/settings.png" class="sidebar-icon">Settings</a></li>
      <li><a href="myProfile.php" class="active"><img src="../images/settings.png" class="sidebar-icon">Profile</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="dashboard-container">

      <h2 class="welcome-text">Patient Communication - <?php echo $doctor_name; ?></h2>

      <!-- Form to Send Message -->
      <div class="card card-form">
        <h3>Send Message</h3>
        <form action="" method="POST" class="chat-form">
          <input type="text" name="patient_name" placeholder="Patient Name" required>
          <textarea name="message" rows="4" placeholder="Write your message..." required></textarea>
          <button type="submit" class="card-btn">Send Message</button>
        </form>
      </div>

      <hr class="divider">

      <!-- Table to Show Previous Chats -->
      <div class="card card-table">
        <h3>Previous Messages</h3>
        <table class="chats-table">
          <tr>
            <th>Patient</th>
            <th>Date & Time</th>
            <th>Message</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
          <?php foreach($chats as $chat): ?>
          <tr>
            <td><?php echo $chat["patient"]; ?></td>
            <td><?php echo $chat["date"]; ?></td>
            <td><?php echo $chat["message"]; ?></td>
            <td><?php echo $chat["status"]; ?></td>
            <td>
              <a href="#" class="card-btn">Reply</a>
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
