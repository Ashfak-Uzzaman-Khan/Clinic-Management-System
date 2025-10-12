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

// Fetch admin data from database
$admin_id = $_SESSION['user_id'];
$sql = "SELECT admin_name, admin_email FROM adminlog WHERE admin_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    $name = $admin['admin_name'];
    $email = $admin['admin_email'];
} else {
    // Fallback values if no data found
    $name = "Admin Name";
    $email = "admin@hospital.com";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="adprofile.css">
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

<div class="dashboard-wrapper">

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../images/logo.png" alt="Admin" class="sidebar-logo">
            <hr style="border:none; height:3px; background-color:white; width:80%; margin-top:10px;">
            <h3>Admin Panel</h3>
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

            <h2 class="welcome-text">Admin Profile</h2>

            <div class="card profile-card">
                <div class="profile-item">
                    <h3>Name</h3>
                    <p><?php echo htmlspecialchars($name); ?></p>
                </div>
                <div class="profile-item">
                    <h3>Email</h3>
                    <p><?php echo htmlspecialchars($email); ?></p>
                </div>
                <div class="profile-item">
                    <h3>Role</h3>
                    <p>Administrator</p>
                </div>
                
                <div class="profile-item">
                    <h3>Account Type</h3>
                    <p>Hospital Management System Admin</p>
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
    
  </div>
</footer>

</body>
</html>