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
$patient_name = $patient_email = $contact_no = $situation_case = $address = $ambulance_type = "";
$patient_nameErr = $patient_emailErr = $contact_noErr = $situation_caseErr = $addressErr = $ambulance_typeErr = "";
$success_message = $error_message = "";

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Patient Name validation
    if (empty($_POST["patient_name"])) {
        $patient_nameErr = "Patient name is required";
    } else {
        $patient_name = test_input($_POST["patient_name"]);
    }

    // Email validation
    if (empty($_POST["patient_email"])) {
        $patient_emailErr = "Email is required";
    } else {
        $patient_email = test_input($_POST["patient_email"]);
        if (!filter_var($patient_email, FILTER_VALIDATE_EMAIL) || !str_contains($patient_email, "@") || !str_contains($patient_email, ".")) {
            $patient_emailErr = "Invalid email format (must include @ and .)";
        }
    }

    // Contact Number validation
    if (empty($_POST["contact_no"])) {
        $contact_noErr = "Contact number is required";
    } else {
        $contact_no = test_input($_POST["contact_no"]);
        if (!preg_match("/^01[0-9]{9}$/", $contact_no)) {
            $contact_noErr = "Invalid contact number (must start with 01 and be 11 digits)";
        }
    }

    // Situation Case validation
    if (empty($_POST["situation_case"])) {
        $situation_caseErr = "Situation case is required";
    } else {
        $situation_case = test_input($_POST["situation_case"]);
    }

    // Ambulance Type validation
    if (empty($_POST["ambulance_type"])) {
        $ambulance_typeErr = "Ambulance type is required";
    } else {
        $ambulance_type = test_input($_POST["ambulance_type"]);
    }

    // Address validation
    if (empty($_POST["address"])) {
        $addressErr = "Address is required";
    } else {
        $address = test_input($_POST["address"]);
    }

    // Insert into database if no errors
    if (empty($patient_nameErr) && empty($patient_emailErr) && empty($contact_noErr) && 
        empty($situation_caseErr) && empty($ambulance_typeErr) && empty($addressErr)) {
        
        $sql = "INSERT INTO ambulanceD (patient_name, patient_email, patient_contactNO, situation_case, a_type, a_address) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $patient_name, $patient_email, $contact_no, $situation_case, $ambulance_type, $address);
        
        if ($stmt->execute()) {
            $success_message = "Ambulance request submitted successfully!";
            // Clear form fields
            $patient_name = $patient_email = $contact_no = $situation_case = $ambulance_type = $address = "";
        } else {
            $error_message = "Error submitting request: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch ambulance requests for the logged-in patient
$patient_email_session = $_SESSION['user_email'];
$sql = "SELECT * FROM ambulanceD WHERE patient_email = ? ORDER BY a_ID DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patient_email_session);
$stmt->execute();
$result = $stmt->get_result();

$ambulance_requests = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $ambulance_requests[] = $row;
    }
}
$stmt->close();
$conn->close();

// Input cleaning function
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
    <title>Ambulance Request</title>
    <link rel="stylesheet" href="pa.css">
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
            <li><a href="pa.php"><img src="../images/ambulance.png" class="sidebar-icon">Ambulance Request</a></li>
            <li><a href="#"><img src="../images/appointment.png" class="sidebar-icon">Appointments</a></li>
            <li><a href="#"><img src="../images/setting.png" class="sidebar-icon">Settings</a></li>
            <li><a href="../log.php"><img src="../images/logout.png" class="sidebar-icon">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="form-container">
            <h2 class="form-title">Ambulance Request Form</h2>
            
            <!-- Success/Error Messages -->
            <?php if ($success_message): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="ambulance-request-form">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Patient Name *</label>
                        <input type="text" name="patient_name" value="<?php echo $patient_name; ?>">
                        <span class="error"><?php echo $patient_nameErr; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label>Patient Email *</label>
                        <input type="text" name="patient_email" value="<?php echo $patient_email; ?>">
                        <span class="error"><?php echo $patient_emailErr; ?></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Contact No *</label>
                        <input type="text" name="contact_no" value="<?php echo $contact_no; ?>" placeholder="01XXXXXXXXX">
                        <span class="error"><?php echo $contact_noErr; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label>Situation Case *</label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="situation_case" value="normal" <?php if ($situation_case=="normal") echo "checked"; ?>> Normal
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="situation_case" value="emergency" <?php if ($situation_case=="emergency") echo "checked"; ?>> Emergency
                            </label>
                        </div>
                        <span class="error"><?php echo $situation_caseErr; ?></span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Ambulance Type *</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="ambulance_type" value="Oxygen" <?php if ($ambulance_type=="Oxygen") echo "checked"; ?>> Oxygen
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="ambulance_type" value="Non-Oxygen" <?php if ($ambulance_type=="Non-Oxygen") echo "checked"; ?>> Non-Oxygen
                        </label>
                    </div>
                    <span class="error"><?php echo $ambulance_typeErr; ?></span>
                </div>

                <div class="form-group full-width">
                    <label>Address *</label>
                    <textarea name="address" placeholder="Enter your complete address for ambulance pickup"><?php echo $address; ?></textarea>
                    <span class="error"><?php echo $addressErr; ?></span>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Request Ambulance</button>
            
                
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