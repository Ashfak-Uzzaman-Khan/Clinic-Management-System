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

// Check if ID is passed
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM patientlog WHERE patient_ID = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
    } else {
        die("Patient not found!");
    }
} else {
    die("No patient ID provided!");
}

// Handle form submission
$success_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_name = $_POST['patient_name'];
    $patient_email = $_POST['patient_email'];
    $patient_contactNo = $_POST['patient_contactNo'];
    $patient_age = $_POST['patient_age'];
    $patient_gender = $_POST['patient_gender'];
    $patient_bloodGrp = $_POST['patient_bloodGrp'];
    $patient_dob = $_POST['patient_dob'];
    $patient_address = $_POST['patient_address'];
    $patient_emNo = $_POST['patient_emNo'];

    $update_sql = "UPDATE patientlog SET 
                    patient_name=?, patient_email=?, patient_contactNo=?, patient_age=?, 
                    patient_gender=?, patient_bloodGrp=?, patient_dob=?, patient_address=?, patient_emNo=?
                   WHERE patient_ID=?";
    
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssisssssi", 
        $patient_name, $patient_email, $patient_contactNo, $patient_age,
        $patient_gender, $patient_bloodGrp, $patient_dob, $patient_address, $patient_emNo, $id
    );
    
    if ($stmt->execute()) {
        $success_message = "Patient information updated successfully!";
        // Refresh patient data
        $sql = "SELECT * FROM patientlog WHERE patient_ID = $id";
        $result = $conn->query($sql);
        $patient = $result->fetch_assoc();
    } else {
        $success_message = "Error updating patient: " . $conn->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient - Admin</title>
    <link rel="stylesheet" href="editpatient.css">
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
            <li><a href="patientlist.php">Patient List</a></li>
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
            <h2 class="welcome-text">Edit Patient Information</h2>
            
            <!-- Success Message -->
            <?php if (!empty($success_message)): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <!-- Edit Form -->
            <div class="card card-form">
                <h3>Patient Details - <?php echo $patient['patient_name']; ?> (ID: <?php echo $patient['patient_ID']; ?>)</h3>
                <form method="post" action="" class="patient-form">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name:</label>
                            <input type="text" name="patient_name" value="<?php echo $patient['patient_name']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" name="patient_email" value="<?php echo $patient['patient_email']; ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Contact Number:</label>
                            <input type="text" name="patient_contactNO" value="<?php echo $patient['patient_contactNO']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Age:</label>
                            <input type="number" name="patient_age" value="<?php echo $patient['patient_age']; ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Gender:</label>
                            <select name="patient_gender" required>
                                <option value="Male" <?php if($patient['patient_gender']=='Male') echo 'selected'; ?>>Male</option>
                                <option value="Female" <?php if($patient['patient_gender']=='Female') echo 'selected'; ?>>Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Blood Group:</label>
                            <input type="text" name="patient_bloodGrp" value="<?php echo $patient['patient_bloodGrp']; ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Date of Birth:</label>
                            <input type="date" name="patient_dob" value="<?php echo $patient['patient_dob']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Emergency Contact:</label>
                            <input type="text" name="patient_emNo" value="<?php echo $patient['patient_emNo']; ?>" required>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label>Address:</label>
                        <textarea name="patient_address" required><?php echo $patient['patient_address']; ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Update Patient</button>
                        <a href="patientlist.php" class="btn-secondary">Back to List</a>
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