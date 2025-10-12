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

// Initialize variables
$patient_name = $patient_email = $test_date = $test_time = "";
$test_types = [];
$success_message = $error_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_name = trim($_POST['patient_name']);
    $patient_email = trim($_POST['patient_email']);
    $test_date = $_POST['test_date'];
    $test_time = $_POST['test_time'];
    
    // Get selected test types
    if(isset($_POST['test_type'])) {
        $test_types = $_POST['test_type'];
    }
    
    // Validation
    $valid = true;
    
    if (empty($patient_name)) {
        $error_message = "Patient name is required";
        $valid = false;
    }
    
    if (empty($patient_email)) {
        $error_message = "Email is required";
        $valid = false;
    } elseif (!filter_var($patient_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Valid email is required";
        $valid = false;
    }
    
    if (empty($test_types)) {
        $error_message = "At least one test type must be selected";
        $valid = false;
    }
    
    if (empty($test_date)) {
        $error_message = "Test date is required";
        $valid = false;
    }
    
    if (empty($test_time)) {
        $error_message = "Test time is required";
        $valid = false;
    }
    
    // Insert into database
    if ($valid) {
        // Convert test types array to string
        $test_type_str = implode(", ", $test_types);
        
        $sql = "INSERT INTO testop (patient_name, patient_email, test_type, test_date, test_time, test_status) 
                VALUES (?, ?, ?, ?, ?, 'pending')";
        
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssss", $patient_name, $patient_email, $test_type_str, $test_date, $test_time);
            
            if ($stmt->execute()) {
                $success_message = "Test request submitted successfully!";
                // Clear form fields
                $patient_name = $patient_email = $test_date = $test_time = "";
                $test_types = [];
            } else {
                $error_message = "Error submitting request: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Error preparing statement: " . $conn->error;
        }
    }
}

// Fetch test requests for the logged-in patient
$patient_email_session = $_SESSION['user_email'];
$test_requests = [];

// First, let's debug the session email
// echo "Debug: Session Email = " . $patient_email_session . "<br>";

$sql = "SELECT * FROM testop WHERE patient_email = ? ORDER BY test_date DESC, test_time DESC";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $patient_email_session);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $test_requests[] = $row;
            }
            // Debug: Show count of fetched rows
            // echo "Debug: Fetched " . count($test_requests) . " rows<br>";
        } else {
            // Debug: No rows found
            // echo "Debug: No test requests found for this email<br>";
        }
    } else {
        $error_message = "Error executing query: " . $stmt->error;
    }
    $stmt->close();
} else {
    $error_message = "Error preparing query: " . $conn->error;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Operation Request</title>
    <link rel="stylesheet" href="testop.css">
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
            <li><a href="testop.php"><img src="../images/test.png" class="sidebar-icon">Test Operation</a></li>
            <li><a href="#"><img src="../images/appointment.png" class="sidebar-icon">Appointments</a></li>
            <li><a href="#"><img src="../images/setting.png" class="sidebar-icon">Settings</a></li>
            <li><a href="../log.php"><img src="../images/logout.png" class="sidebar-icon">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="form-container">
            <h2 class="form-title">Test Operation Request Form</h2>
            
            <!-- Success/Error Messages -->
            <?php if ($success_message): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="testop.php" method="POST" class="test-request-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="patient_name">Patient Name *</label>
                        <input type="text" id="patient_name" name="patient_name" 
                               value="<?php echo htmlspecialchars($patient_name); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="patient_email">Patient Email *</label>
                        <input type="email" id="patient_email" name="patient_email" 
                               value="<?php echo htmlspecialchars($patient_email); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Test Types * (Select one or more)</label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="test_type[]" value="MRI" 
                                <?php echo (in_array('MRI', $test_types)) ? 'checked' : ''; ?>> MRI
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="test_type[]" value="X-Ray"
                                <?php echo (in_array('X-Ray', $test_types)) ? 'checked' : ''; ?>> X-Ray
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="test_type[]" value="Lipid Profile"
                                <?php echo (in_array('Lipid Profile', $test_types)) ? 'checked' : ''; ?>> Lipid Profile
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="test_type[]" value="Blood Test"
                                <?php echo (in_array('Blood Test', $test_types)) ? 'checked' : ''; ?>> Blood Test
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="test_type[]" value="ECG"
                                <?php echo (in_array('ECG', $test_types)) ? 'checked' : ''; ?>> ECG
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="test_type[]" value="CT-scan"
                                <?php echo (in_array('CT-scan', $test_types)) ? 'checked' : ''; ?>> CT-scan
                        </label>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="test_date">Preferred Date *</label>
                        <input type="date" id="test_date" name="test_date" 
                               value="<?php echo $test_date; ?>" 
                               min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="test_time">Preferred Time *</label>
                        <input type="time" id="test_time" name="test_time" 
                               value="<?php echo $test_time; ?>" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Submit Request</button>
                    <button type="reset" class="btn-secondary">Reset Form</button>
                </div>
            </form>

            <!-- Divider -->
            <hr class="divider">

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