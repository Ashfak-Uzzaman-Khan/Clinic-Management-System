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

// Fetch approved doctors for dropdown
$doctors_sql = "SELECT doctor_ID, doc_name, doc_email, doc_dept FROM doctorlog WHERE status = 'approved'";
$doctors_result = $conn->query($doctors_sql);

$doctors = [];
if ($doctors_result->num_rows > 0) {
    while($row = $doctors_result->fetch_assoc()) {
        $doctors[] = $row;
    }
}

// Form validation and submission
$patient_name = $patient_email = $patient_contact = $doctor_id = $appointment_date = $appointment_time = $reason = "";
$patient_nameErr = $patient_emailErr = $patient_contactErr = $doctor_idErr = $appointment_dateErr = $appointment_timeErr = $reasonErr = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get patient info from session
    $patient_id = $_SESSION['user_id'];
    $patient_name = $_SESSION['user_name'];
    $patient_email = $_SESSION['user_email'];
    
    // Get form data
    $patient_contact = test_input($_POST["patient_contact"]);
    $doctor_id = test_input($_POST["doctor_id"]);
    $appointment_date = test_input($_POST["appointment_date"]);
    $appointment_time = test_input($_POST["appointment_time"]);
    $reason = test_input($_POST["reason"]);
    
    // Validation
    $valid = true;
    
    if (empty($patient_contact)) {
        $patient_contactErr = "Contact number is required";
        $valid = false;
    } elseif (!preg_match("/^01[0-9]{9}$/", $patient_contact)) {
        $patient_contactErr = "Invalid contact number (must start with 01 and be 11 digits)";
        $valid = false;
    }
    
    if (empty($doctor_id)) {
        $doctor_idErr = "Please select a doctor";
        $valid = false;
    }
    
    if (empty($appointment_date)) {
        $appointment_dateErr = "Appointment date is required";
        $valid = false;
    }
    
    if (empty($appointment_time)) {
        $appointment_timeErr = "Appointment time is required";
        $valid = false;
    }
    
    if (empty($reason)) {
        $reasonErr = "Please describe the reason for appointment";
        $valid = false;
    }
    
    // Insert into database
    if ($valid) {
        // Get doctor details
        $doctor_sql = "SELECT doc_name, doc_email, doc_dept FROM doctorlog WHERE doctor_ID = ?";
        $stmt = $conn->prepare($doctor_sql);
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $doctor_result = $stmt->get_result();
        $doctor_data = $doctor_result->fetch_assoc();
        $stmt->close();
        
        $insert_sql = "INSERT INTO appointments (patient_id, patient_name, patient_email, patient_contact, doctor_id, doctor_name, doctor_email, doctor_department, appointment_date, appointment_time, reason) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("isssissssss", $patient_id, $patient_name, $patient_email, $patient_contact, $doctor_id, $doctor_data['doc_name'], $doctor_data['doc_email'], $doctor_data['doc_dept'], $appointment_date, $appointment_time, $reason);
        
        if ($stmt->execute()) {
            $success_message = "Appointment request submitted successfully! Waiting for admin approval.";
            // Clear form
            $patient_contact = $doctor_id = $appointment_date = $appointment_time = $reason = "";
        } else {
            $success_message = "Error submitting appointment: " . $conn->error;
        }
        $stmt->close();
    }
}

$conn->close();

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Doctor Appointment</title>
    <link rel="stylesheet" href="docrequ.css">
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
            <li><a href="patient_dashboard.php">Dashboard</a></li>
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
            <li><a href="patient_dashboard.php"><img src="../images/profile.png" class="sidebar-icon">Dashboard</a></li>
            <li><a href="docrequ.php"><img src="../images/appointment.png" class="sidebar-icon">Book Appointment</a></li>
            <li><a href="appointstatus.php"><img src="../images/status.png" class="sidebar-icon">Appointment Status</a></li>
            <li><a href="#"><img src="../images/test.png" class="sidebar-icon">Test Reports</a></li>
            <li><a href="../log.php"><img src="../images/logout.png" class="sidebar-icon">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard-container">
            <h2 class="welcome-text">Book Doctor Appointment</h2>
            
            <!-- Success Message -->
            <?php if (!empty($success_message)): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <!-- Appointment Form -->
            <div class="card card-form">
                <h3>Appointment Request Form</h3>
                <form method="post" action="" class="appointment-form">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Your Name:</label>
                            <input type="text" value="<?php echo $_SESSION['user_name']; ?>" disabled>
                            <small>Logged in as patient</small>
                        </div>
                        <div class="form-group">
                            <label>Your Email:</label>
                            <input type="email" value="<?php echo $_SESSION['user_email']; ?>" disabled>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Contact Number *</label>
                            <input type="text" name="patient_contact" value="<?php echo $patient_contact; ?>" placeholder="01XXXXXXXXX" required>
                            <span class="error"><?php echo $patient_contactErr; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Select Doctor *</label>
                            <select name="doctor_id" required>
                                <option value="">Choose a Doctor</option>
                                <?php foreach($doctors as $doctor): ?>
                                <option value="<?php echo $doctor['doctor_ID']; ?>" <?php if($doctor_id == $doctor['doctor_ID']) echo 'selected'; ?>>
                                    Dr. <?php echo $doctor['doc_name']; ?> - <?php echo $doctor['doc_dept']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="error"><?php echo $doctor_idErr; ?></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Appointment Date *</label>
                            <input type="date" name="appointment_date" value="<?php echo $appointment_date; ?>" min="<?php echo date('Y-m-d'); ?>" required>
                            <span class="error"><?php echo $appointment_dateErr; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Preferred Time *</label>
                            <select name="appointment_time" required>
                                <option value="">Select Time</option>
                                <option value="09:00 AM - 10:00 AM" <?php if($appointment_time == '09:00 AM - 10:00 AM') echo 'selected'; ?>>09:00 AM - 10:00 AM</option>
                                <option value="10:00 AM - 11:00 AM" <?php if($appointment_time == '10:00 AM - 11:00 AM') echo 'selected'; ?>>10:00 AM - 11:00 AM</option>
                                <option value="11:00 AM - 12:00 PM" <?php if($appointment_time == '11:00 AM - 12:00 PM') echo 'selected'; ?>>11:00 AM - 12:00 PM</option>
                                <option value="02:00 PM - 03:00 PM" <?php if($appointment_time == '02:00 PM - 03:00 PM') echo 'selected'; ?>>02:00 PM - 03:00 PM</option>
                                <option value="03:00 PM - 04:00 PM" <?php if($appointment_time == '03:00 PM - 04:00 PM') echo 'selected'; ?>>03:00 PM - 04:00 PM</option>
                                <option value="04:00 PM - 05:00 PM" <?php if($appointment_time == '04:00 PM - 05:00 PM') echo 'selected'; ?>>04:00 PM - 05:00 PM</option>
                            </select>
                            <span class="error"><?php echo $appointment_timeErr; ?></span>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label>Reason for Appointment *</label>
                        <textarea name="reason" placeholder="Please describe your symptoms or reason for appointment..." required><?php echo $reason; ?></textarea>
                        <span class="error"><?php echo $reasonErr; ?></span>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Submit Appointment Request</button>
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