<?php 
session_start();

// Define variables and set to empty
$patient_name = $patient_email = $test_type = $priority = $preferred_date = $contact_no = $emergency_contact = $address = "";
$patient_nameErr = $patient_emailErr = $test_typeErr = $priorityErr = $preferred_dateErr = $contact_noErr = $emergency_contactErr = $addressErr = "";

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Form submission handling
$success_message = "";

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

    // Test Type validation
    if (empty($_POST["test_type"])) {
        $test_typeErr = "Test type is required";
    } else {
        $test_type = test_input($_POST["test_type"]);
    }

    // Priority validation
    if (empty($_POST["priority"])) {
        $priorityErr = "Priority is required";
    } else {
        $priority = test_input($_POST["priority"]);
    }

    // Preferred Date validation
    if (empty($_POST["preferred_date"])) {
        $preferred_dateErr = "Preferred date is required";
    } else {
        $preferred_date = test_input($_POST["preferred_date"]);
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

    // Emergency contact validation
    if (empty($_POST["emergency_contact"])) {
        $emergency_contactErr = "Emergency contact is required";
    } else {
        $emergency_contact = test_input($_POST["emergency_contact"]);
        if (!preg_match("/^01[0-9]{9}$/", $emergency_contact)) {
            $emergency_contactErr = "Invalid emergency number (must start with 01 and be 11 digits)";
        }
    }

    // Address validation
    if (empty($_POST["address"])) {
        $addressErr = "Address is required";
    } else {
        $address = test_input($_POST["address"]);
    }

    // File upload handling
    $report_file = "";
    if (isset($_FILES['report_file']) && $_FILES['report_file']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES["report_file"]["name"], PATHINFO_EXTENSION);
        $report_file = $target_dir . "report_" . time() . "." . $file_extension;
        move_uploaded_file($_FILES["report_file"]["tmp_name"], $report_file);
    }

    // Insert into database if no errors
    if (empty($patient_nameErr) && empty($patient_emailErr) && empty($test_typeErr) && empty($priorityErr) && 
        empty($preferred_dateErr) && empty($contact_noErr) && empty($emergency_contactErr) && empty($addressErr)) {
        
        $sql = "INSERT INTO test_requests (patient_name, patient_email, test_type, priority, preferred_date, report_file, contact_no, emergency_contact, address, request_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssss", $patient_name, $patient_email, $test_type, $priority, $preferred_date, $report_file, $contact_no, $emergency_contact, $address);
        
        if ($stmt->execute()) {
            $success_message = "Test request submitted successfully!";
            // Clear form fields
            $patient_name = $patient_email = $test_type = $priority = $preferred_date = $contact_no = $emergency_contact = $address = "";
        } else {
            $success_message = "Error submitting request: " . $conn->error;
        }
        $stmt->close();
    }
}

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
    <title>Test Operation Request</title>
    <link rel="stylesheet" href="pt.css">
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
            <li><a href="#"><img src="../images/setting.png" class="sidebar-icon">Setting</a></li>
            <li><a href="../log.php"><img src="../images/logout.png" class="sidebar-icon">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="form-container">
            <h2 class="form-title">Test/Operation Request Form</h2>
            
            <!-- Success Message -->
            <?php if ($success_message): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data" class="test-request-form">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Patient Name:</label>
                        <input type="text" name="patient_name" value="<?php echo $patient_name; ?>">
                        <span class="error"><?php echo $patient_nameErr; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label>Patient Email:</label>
                        <input type="text" name="patient_email" value="<?php echo $patient_email; ?>">
                        <span class="error"><?php echo $patient_emailErr; ?></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Test Type:</label>
                        <select name="test_type">
                            <option value="">Select Test Type</option>
                            <option value="Blood Test" <?php if ($test_type=="Blood Test") echo "selected"; ?>>Blood Test</option>
                            <option value="Urine Test" <?php if ($test_type=="Urine Test") echo "selected"; ?>>Urine Test</option>
                            <option value="X-Ray" <?php if ($test_type=="X-Ray") echo "selected"; ?>>X-Ray</option>
                            <option value="MRI" <?php if ($test_type=="MRI") echo "selected"; ?>>MRI</option>
                            <option value="CT Scan" <?php if ($test_type=="CT Scan") echo "selected"; ?>>CT Scan</option>
                            <option value="Ultrasound" <?php if ($test_type=="Ultrasound") echo "selected"; ?>>Ultrasound</option>
                            <option value="ECG" <?php if ($test_type=="ECG") echo "selected"; ?>>ECG</option>
                            <option value="Liver Function Test" <?php if ($test_type=="Liver Function Test") echo "selected"; ?>>Liver Function Test</option>
                        </select>
                        <span class="error"><?php echo $test_typeErr; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label>Priority:</label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="priority" value="normal" <?php if ($priority=="normal") echo "checked"; ?>> Normal
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="priority" value="urgent" <?php if ($priority=="urgent") echo "checked"; ?>> Urgent
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="priority" value="emergency" <?php if ($priority=="emergency") echo "checked"; ?>> Emergency
                            </label>
                        </div>
                        <span class="error"><?php echo $priorityErr; ?></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Preferred Date:</label>
                        <input type="date" name="preferred_date" value="<?php echo $preferred_date; ?>" min="<?php echo date('Y-m-d'); ?>">
                        <span class="error"><?php echo $preferred_dateErr; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label>Upload Previous Reports:</label>
                        <input type="file" name="report_file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        <small>Supported formats: PDF, JPG, PNG, DOC (Max: 5MB)</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Contact No:</label>
                        <input type="text" name="contact_no" value="<?php echo $contact_no; ?>" placeholder="01XXXXXXXXX">
                        <span class="error"><?php echo $contact_noErr; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label>Emergency Contact No:</label>
                        <input type="text" name="emergency_contact" value="<?php echo $emergency_contact; ?>" placeholder="01XXXXXXXXX">
                        <span class="error"><?php echo $emergency_contactErr; ?></span>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Address:</label>
                    <textarea name="address"><?php echo $address; ?></textarea>
                    <span class="error"><?php echo $addressErr; ?></span>
                </div>

                <div class="form-actions">
                    <button type="submit">Submit Request</button>
                    <button type="reset">Reset Form</button>
                   
                </div>



            </form>
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