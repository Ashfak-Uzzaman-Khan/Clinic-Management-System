<?php
session_start();

// Check if patient is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'patient') {
    header("Location: ../log.php");
    exit();
}

$patient_name = $_SESSION['user_name'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Search functionality
$search = "";
$doctors = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT doc_name, doc_email, doc_ContactNO, doc_age, doc_gender, doc_dept, doc_quali, doc_exp, doc_college, doc_licenceNo, doc_shift, doc_fees, doc_statusT 
            FROM doctorlog 
            WHERE (doc_name LIKE '%$search%' OR doc_email LIKE '%$search%' OR doc_dept LIKE '%$search%') 
            AND status = 'approved'
            ORDER BY doc_name ASC";
} else {
    $sql = "SELECT doc_name, doc_email, doc_ContactNO, doc_age, doc_gender, doc_dept, doc_quali, doc_exp, doc_college, doc_licenceNo, doc_shift, doc_fees, doc_statusT 
            FROM doctorlog 
            WHERE status = 'approved'
            ORDER BY doc_name ASC";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Doctors - Dhaka Centralized Hospital</title>
    <link rel="stylesheet" href="doclist.css">
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
            <li><a href="ph.php"><img src="../images/profile.png" class="sidebar-icon">Dashboard</a></li>
            <li><a href="appointstatus.php"><img src="../images/appointment.png" class="sidebar-icon">My Appointments</a></li>
            <li><a href="../patient/seetest.php"><img src="../images/test.png" class="sidebar-icon">Test Status</a></li>
            <li><a href="../patient/seeambulance.php"><img src="../images/test.png" class="sidebar-icon">Ambulance Status</a></li>
            <li><a href="../patient/patientsetting.php"><img src="../images/setting.png" class="sidebar-icon">Settings</a></li>
            <li><a href="../log.php"><img src="../images/logout.png" class="sidebar-icon">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard-container">
            <h2 class="welcome-text">Welcome - <?php echo $patient_name; ?>!</h2>
            <p class="page-description">Find the best medical specialists for your healthcare needs</p>

            <!-- Search Box -->
            <div class="card search-card">
                <h3>Find a Doctor</h3>
                <form method="GET" action="" class="search-form">
                    <input type="text" name="search" placeholder="Search by doctor name or department..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="search-btn">Search</button>
                    <?php if (!empty($search)): ?>
                        <a href="doclist.php" class="clear-btn">Clear Search</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Search Results Info -->
            <?php if (!empty($search)): ?>
                <div class="search-info">
                    <p>Showing results for: "<strong><?php echo htmlspecialchars($search); ?></strong>"</p>
                    <p>Found <?php echo count($doctors); ?> doctor(s)</p>
                </div>
            <?php endif; ?>

            <!-- Doctors Table -->
            <div class="card card-table">
                <h3>Doctor Directory</h3>
                
                <?php if (count($doctors) > 0): ?>
                    <table class="doctors-table">
                        <thead>
                            <tr>
                                <th>Doctor Name</th>
                                <th>Contact</th>
                                <th>Department</th>
                                <th>Qualification</th>
                                <th>Experience</th>
                                <th>Shift</th>
                                <th>Fees (BDT)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($doctors as $doctor): ?>
                            <tr>
                                <td class="doctor-name">
                                    <strong><?php echo $doctor['doc_name']; ?></strong>
                               
                                </td>
                                <td><?php echo $doctor['doc_ContactNO']; ?></td>
                                <td><?php echo $doctor['doc_dept']; ?></td>
                                <td><?php echo $doctor['doc_quali']; ?></td>
                                <td><?php echo $doctor['doc_exp']; ?> years</td>
                                <td><?php echo $doctor['doc_shift']; ?></td>
                                <td class="fees"><?php echo number_format($doctor['doc_fees']); ?></td>
                                <td>
                                    <span class="status-<?php echo strtolower($doctor['doc_statusT']); ?>">
                                        <?php echo $doctor['doc_statusT']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-doctors">
                        <p>No doctors found<?php echo !empty($search) ? ' matching your search criteria' : ''; ?>.</p>
                        <?php if (!empty($search)): ?>
                            <p>Please try a different search term or <a href="doclist.php">view all doctors</a>.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Total Count -->
            <div class="total-count">
                <p>Total Doctors Available: <strong><?php echo count($doctors); ?></strong></p>
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