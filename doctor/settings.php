<?php
session_start();

// Check if doctor is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'doctor') {
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

// Fetch current doctor data
$doctor_id = $_SESSION['user_id'];
$sql = "SELECT * FROM doctorlog WHERE doctor_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $doctor = $result->fetch_assoc();
} else {
    die("Doctor not found!");
}
$stmt->close();

// Initialize variables with current data
$name = $doctor['doc_name'];
$email = $doctor['doc_email'];
$qualification = $doctor['doc_quali'];
$contact = $doctor['doc_ContactNO'];
$fees = $doctor['doc_fees'];
$password = ''; // Don't show current password for security

$success_message = "";
$error_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $qualification = trim($_POST['qualification']);
    $contact = trim($_POST['contact']);
    $new_password = trim($_POST['password']);
    $fees = trim($_POST['fees']);

    // Validation
    $valid = true;

    if (empty($name)) {
        $error_message = "Name is required";
        $valid = false;
    }

    if (empty($email)) {
        $error_message = "Email is required";
        $valid = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format";
        $valid = false;
    }

    if (empty($qualification)) {
        $error_message = "Qualification is required";
        $valid = false;
    }

    if (empty($contact)) {
        $error_message = "Contact number is required";
        $valid = false;
    } elseif (!preg_match("/^01[0-9]{9}$/", $contact)) {
$error_message = '<span style="color: red;">Invalid contact number (must start with 01 and be 11 digits)</span>';
        $valid = false;
    }
    
    if (empty($fees)) {
        $error_message = "Fees is required";
        $valid = false;
    } elseif (!ctype_digit($fees)) {
        $error_message = "Fees must be numeric";
        $valid = false;
    }

    // Update database if validation passes
    if ($valid) {
        // Check if password is being updated
        if (!empty($new_password)) {
            $update_sql = "UPDATE doctorlog SET doc_name=?, doc_email=?, doc_quali=?, doc_ContactNO=?, doc_pass=?, doc_fees=? WHERE doctor_ID=?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("sssssii", $name, $email, $qualification, $contact, $new_password, $fees, $doctor_id);
        } else {
            $update_sql = "UPDATE doctorlog SET doc_name=?, doc_email=?, doc_quali=?, doc_ContactNO=?, doc_fees=? WHERE doctor_ID=?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ssssii", $name, $email, $qualification, $contact, $fees, $doctor_id);
        }

        if ($stmt->execute()) {
$success_message = '<span style="color: green;">Profile updated successfully!</span>';
            // Update session data
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            
            // Refresh doctor data
            $sql = "SELECT * FROM doctorlog WHERE doctor_ID = ?";
            $refresh_stmt = $conn->prepare($sql);
            $refresh_stmt->bind_param("i", $doctor_id);
            $refresh_stmt->execute();
            $result = $refresh_stmt->get_result();
            $doctor = $result->fetch_assoc();
            $refresh_stmt->close();
        } else {
            $error_message = "Error updating profile: " . $conn->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doctor Settings</title>
  <link rel="stylesheet" href="..\css\settings.css">
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
      <li><a href="myProfile.php"><img src="../images/profile.png" class="sidebar-icon">Profile</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="dashboard-container">

      <h2 class="welcome-text">Doctor Settings - <?php echo $_SESSION['user_name']; ?></h2>

      <!-- Success/Error Messages -->
      <?php if (!empty($success_message)): ?>
          <div class="message success"><?php echo $success_message; ?></div>
      <?php endif; ?>

      <?php if (!empty($error_message)): ?>
          <div class="message error"><?php echo $error_message; ?></div>
      <?php endif; ?>

      <div class="card settings-card">
        <h3>Update Your Profile</h3>
        <form method="POST" class="settings-form">
          <div class="form-group">
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
          </div>

          <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
          </div>

          <div class="form-group">
            <label>Qualification:</label>
            <input type="text" name="qualification" value="<?php echo htmlspecialchars($qualification); ?>" required>
          </div>

          <div class="form-group">
            <label>Contact No:</label>
            <input type="text" name="contact" value="<?php echo htmlspecialchars($contact); ?>" required> <br>
          <small style="color: red;">Must be 11 digits starting with 01</small>

          </div>

          <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" placeholder="Enter new password (leave blank to keep current)">
            
          </div>

          <div class="form-group">
            <label>Consultation Fees (BDT):</label>
            <input type="text" name="fees" value="<?php echo htmlspecialchars($fees); ?>" required>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-primary">Save Changes</button>
            <button type="reset" class="btn-secondary">Reset Form</button>
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