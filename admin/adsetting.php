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

// Get current admin data
$admin_id = $_SESSION['user_id'];
$sql = "SELECT * FROM adminlog WHERE admin_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
} else {
    die("Admin not found!");
}
$stmt->close();

// Handle form submission
$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_name = $_POST['admin_name'];
    $admin_email = $_POST['admin_email'];
    $admin_pass = $_POST['admin_pass'];
    
    // Validation
    $valid = true;
    
    if (empty($admin_name)) {
        $error_message = "Admin name is required";
        $valid = false;
    }
    
    if (empty($admin_email) || !filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Valid email is required";
        $valid = false;
    }
    
    if (empty($admin_pass) || strlen($admin_pass) < 6) {
        $error_message = "Password must be at least 6 characters long";
        $valid = false;
    }
    
    // Update database if valid
    if ($valid) {
        $update_sql = "UPDATE adminlog SET admin_name = ?, admin_email = ?, admin_pass = ? WHERE admin_ID = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $admin_name, $admin_email, $admin_pass, $admin_id);
        
        if ($update_stmt->execute()) {
            $success_message = "Profile updated successfully!";
            // Update session data
            $_SESSION['user_name'] = $admin_name;
            $_SESSION['user_email'] = $admin_email;
            // Refresh admin data
            $sql = "SELECT * FROM adminlog WHERE admin_ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();
            $stmt->close();
        } else {
            $error_message = "Error updating profile: " . $conn->error;
        }
        $update_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link rel="stylesheet" href="adsetting.css">
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
            <h2 class="welcome-text">Admin Settings - <?php echo $_SESSION['user_name']; ?></h2>
            
            <!-- Success/Error Messages -->
            <?php if (!empty($success_message)): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Settings Form -->
            <div class="card card-form">
                <h3>Update Your Profile</h3>
                <form method="post" action="" class="settings-form">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Admin Name:</label>
                            <input type="text" name="admin_name" value="<?php echo htmlspecialchars($admin['admin_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" name="admin_email" value="<?php echo htmlspecialchars($admin['admin_email']); ?>" required>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label>Password:</label>
                        <input type="password" name="admin_pass" value="<?php echo htmlspecialchars($admin['admin_pass']); ?>" required>
                        <small>Password must be at least 6 characters long</small>
                    </div>

                    <!-- Read-only Information Section -->
                    <div class="readonly-info">
                        <h4>Account Information</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Admin ID:</label>
                                <span><?php echo $admin['admin_ID']; ?></span>
                            </div>
                            <div class="info-item">
                                <label>Account Type:</label>
                                <span>Administrator</span>
                            </div>
                            <div class="info-item">
                                <label>Registration Date:</label>
                                <span>System Administrator</span>
                            </div>
                            <div class="info-item">
                                <label>Last Updated:</label>
                                <span><?php echo date('Y-m-d H:i:s'); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Update Profile</button>
                        <button type="reset" class="btn-secondary">Reset Form</button>
                        <a href="admin.php" class="btn-cancel">Cancel</a>
                    </div>
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