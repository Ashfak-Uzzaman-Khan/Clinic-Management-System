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
    $sql = "SELECT * FROM doctorlog WHERE doctor_ID = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $doctor = $result->fetch_assoc();
    } else {
        die("Doctor application not found!");
    }
} else {
    die("No doctor ID provided!");
}

// Handle form submission
$success_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doc_name = $_POST['doc_name'];
    $doc_email = $_POST['doc_email'];
    $doc_ContactNO = $_POST['doc_ContactNO'];
    $doc_age = $_POST['doc_age'];
    $doc_gender = $_POST['doc_gender'];
    $doc_bloodGrp = $_POST['doc_bloodGrp'];
    $doc_dob = $_POST['doc_dob'];
    $doc_address = $_POST['doc_address'];
    $doc_emgContactNo = $_POST['doc_emgContactNo'];
    $doc_dept = $_POST['doc_dept'];
    $doc_quali = $_POST['doc_quali'];
    $doc_exp = $_POST['doc_exp'];
    $doc_college = $_POST['doc_college'];
    $doc_licenceNo = $_POST['doc_licenceNo'];
    $doc_shift = $_POST['doc_shift'];
    $doc_fees = $_POST['doc_fees'];
    $status = $_POST['status'];

    $update_sql = "UPDATE doctorlog SET 
                    doc_name=?, doc_email=?, doc_ContactNO=?, doc_age=?, doc_gender=?, 
                    doc_bloodGrp=?, doc_dob=?, doc_address=?, doc_emgContactNo=?, 
                    doc_dept=?, doc_quali=?, doc_exp=?, doc_college=?, doc_licenceNo=?, 
                    doc_shift=?, doc_fees=?, status=?
                   WHERE doctor_ID=?";
    
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssisssssssisssisi", 
        $doc_name, $doc_email, $doc_ContactNO, $doc_age, $doc_gender,
        $doc_bloodGrp, $doc_dob, $doc_address, $doc_emgContactNo,
        $doc_dept, $doc_quali, $doc_exp, $doc_college, $doc_licenceNo,
        $doc_shift, $doc_fees, $status, $id
    );
    
    if ($stmt->execute()) {
        $success_message = "Doctor application updated successfully!";
        // Refresh doctor data
        $sql = "SELECT * FROM doctorlog WHERE doctor_ID = $id";
        $result = $conn->query($sql);
        $doctor = $result->fetch_assoc();
    } else {
        $success_message = "Error updating application: " . $conn->error;
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
    <title>Edit Doctor Application - Admin</title>
    <link rel="stylesheet" href="editdoctorApp.css">
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
            <li><a href="doctorApp.php">Doctor Applications</a></li>
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
            <h2 class="welcome-text">Edit Doctor Application</h2>
            
            <!-- Success Message -->
            <?php if (!empty($success_message)): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <!-- Edit Form -->
            <div class="card card-form">
                <h3>Application Details - <?php echo $doctor['doc_name']; ?></h3>
                <form method="post" action="" class="doctor-form">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name:</label>
                            <input type="text" name="doc_name" value="<?php echo $doctor['doc_name']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" name="doc_email" value="<?php echo $doctor['doc_email']; ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Contact Number:</label>
                            <input type="text" name="doc_ContactNO" value="<?php echo $doctor['doc_ContactNO']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Age:</label>
                            <input type="number" name="doc_age" value="<?php echo $doctor['doc_age']; ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Gender:</label>
                            <select name="doc_gender" required>
                                <option value="Male" <?php if($doctor['doc_gender']=='Male') echo 'selected'; ?>>Male</option>
                                <option value="Female" <?php if($doctor['doc_gender']=='Female') echo 'selected'; ?>>Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Blood Group:</label>
                            <input type="text" name="doc_bloodGrp" value="<?php echo $doctor['doc_bloodGrp']; ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Date of Birth:</label>
                            <input type="date" name="doc_dob" value="<?php echo $doctor['doc_dob']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Emergency Contact:</label>
                            <input type="text" name="doc_emgContactNo" value="<?php echo $doctor['doc_emgContactNo']; ?>" required>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label>Address:</label>
                        <textarea name="doc_address" required><?php echo $doctor['doc_address']; ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Department:</label>
                            <select name="doc_dept" required>
                                <option value="Neurology" <?php if($doctor['doc_dept']=='Neurology') echo 'selected'; ?>>Neurology</option>
                                <option value="Orthopedic" <?php if($doctor['doc_dept']=='Orthopedic') echo 'selected'; ?>>Orthopedic</option>
                                <option value="Cardiology" <?php if($doctor['doc_dept']=='Cardiology') echo 'selected'; ?>>Cardiology</option>
                                <option value="Gynecology" <?php if($doctor['doc_dept']=='Gynecology') echo 'selected'; ?>>Gynecology</option>
                                <option value="Surgon" <?php if($doctor['doc_dept']=='Surgon') echo 'selected'; ?>>Surgon</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Years of Experience:</label>
                            <input type="number" name="doc_exp" value="<?php echo $doctor['doc_exp']; ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Qualifications:</label>
                            <input type="text" name="doc_quali" value="<?php echo $doctor['doc_quali']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>MBBS College:</label>
                            <input type="text" name="doc_college" value="<?php echo $doctor['doc_college']; ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>License Number (BMDC):</label>
                            <input type="text" name="doc_licenceNo" value="<?php echo $doctor['doc_licenceNo']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Shift:</label>
                            <select name="doc_shift" required>
                                <option value="Morning" <?php if($doctor['doc_shift']=='Morning') echo 'selected'; ?>>Morning</option>
                                <option value="Evening" <?php if($doctor['doc_shift']=='Evening') echo 'selected'; ?>>Evening</option>
                                <option value="Night" <?php if($doctor['doc_shift']=='Night') echo 'selected'; ?>>Night</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Consultation Fees (BDT):</label>
                            <input type="number" name="doc_fees" value="<?php echo $doctor['doc_fees']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Application Status:</label>
                            <select name="status" required>
                                <option value="pending" <?php if($doctor['status']=='pending') echo 'selected'; ?>>Pending</option>
                                <option value="approved" <?php if($doctor['status']=='approved') echo 'selected'; ?>>Approved</option>
                                <option value="rejected" <?php if($doctor['status']=='rejected') echo 'selected'; ?>>Rejected</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Update Application</button>
                        <a href="doctorApp.php" class="btn-secondary">Back to List</a>
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