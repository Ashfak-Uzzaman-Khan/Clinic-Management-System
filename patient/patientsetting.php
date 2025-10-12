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

// Get current patient data
$patient_id = $_SESSION['user_id'];
$sql = "SELECT * FROM patientlog WHERE patient_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $patient = $result->fetch_assoc();
} else {
    die("Patient not found!");
}
$stmt->close();

// Handle form submission
$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_name = $_POST['patient_name'];
    $patient_email = $_POST['patient_email'];
    $patient_pass = $_POST['patient_pass'];
    $patient_address = $_POST['patient_address'];
    
    // Validation
    $valid = true;
    
    if (empty($patient_name)) {
        $error_message = "Patient name is required";
        $valid = false;
    }
    
    if (empty($patient_email) || !filter_var($patient_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Valid email is required";
        $valid = false;
    }
    
    if (empty($patient_pass) || strlen($patient_pass) < 6) {
        $error_message = "Password must be at least 6 characters long";
        $valid = false;
    }
    
    if (empty($patient_address)) {
        $error_message = "Address is required";
        $valid = false;
    }
    
    // Update database if valid
    if ($valid) {
        $update_sql = "UPDATE patientlog SET patient_name = ?, patient_email = ?, patient_pass = ?, patient_address = ? WHERE patient_ID = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssssi", $patient_name, $patient_email, $patient_pass, $patient_address, $patient_id);
        
        if ($update_stmt->execute()) {
            $success_message = "Profile updated successfully!";
            // Update session data
            $_SESSION['user_name'] = $patient_name;
            $_SESSION['user_email'] = $patient_email;
            // Refresh patient data
            $sql = "SELECT * FROM patientlog WHERE patient_ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $patient_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $patient = $result->fetch_assoc();
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
    <title>Patient Settings</title>
    <link rel="stylesheet" href="patientsetting.css">
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
            <li><a href="patient_dashboard.php">Dashboard</a></li>
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
            <li><a href="#"><img src="../images/appointment.png" class="sidebar-icon">My Appointments</a></li>
            <li><a href="#"><img src="../images/test.png" class="sidebar-icon">Test Reports</a></li>
            <li><a href="patientsetting.php"><img src="../images/setting.png" class="sidebar-icon">Settings</a></li>
            <li><a href="../log.php"><img src="../images/logout.png" class="sidebar-icon">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard-container">
            <h2 class="welcome-text">Patient Settings - <?php echo $_SESSION['user_name']; ?></h2>
            
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
                            <label>Patient Name:</label>
                            <input type="text" name="patient_name" value="<?php echo htmlspecialchars($patient['patient_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" name="patient_email" value="<?php echo htmlspecialchars($patient['patient_email']); ?>" required>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label>Password:</label>
                        <input type="password" name="patient_pass" value="<?php echo htmlspecialchars($patient['patient_pass']); ?>" required>
                        <small>Password must be at least 6 characters long</small>
                    </div>

                    <div class="form-group full-width">
                        <label>Address:</label>
                        <textarea name="patient_address" rows="4" required><?php echo htmlspecialchars($patient['patient_address']); ?></textarea>
                    </div>

                   

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Update Profile</button>
                        <button type="reset" class="btn-secondary">Reset Form</button>
                        <a href="../patient/ph.php" class="btn-cancel">Cancel</a>
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