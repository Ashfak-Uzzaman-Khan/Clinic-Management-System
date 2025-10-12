<?php 
session_start();

// Check if patient is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'patient') {
    header("Location: ../log.php");
    exit();
}

// Get patient info from session
$patient_email = $_SESSION['user_email'];
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

// Fetch test requests for this patient
$sql = "SELECT * FROM testop WHERE patient_email = ? ORDER BY request_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patient_email);
$stmt->execute();
$result = $stmt->get_result();

$test_requests = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $test_requests[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Test Requests - Patient</title>
    <link rel="stylesheet" href="seetest.css">
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
            <li><a href="seetest.php"><img src="../images/test.png" class="sidebar-icon">My Test Requests</a></li>
            <li><a href="#"><img src="../images/appointment.png" class="sidebar-icon">My Appointments</a></li>
            <li><a href="#"><img src="../images/report.png" class="sidebar-icon">Test Reports</a></li>
            <li><a href="#"><img src="../images/setting.png" class="sidebar-icon">Setting</a></li>
            <li><a href="../log.php"><img src="../images/logout.png" class="sidebar-icon">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard-container">
            <h2 class="welcome-text">My Test Requests - <?php echo $patient_name; ?></h2>
            
            <!-- Stats Overview -->
            <div class="cards-container">
                <div class="card stats">
                    <img src="../images/test.png" alt="Test Icon">
                    <h2><?php echo count($test_requests); ?></h2>
                    <p>Total Requests</p>
                </div>

                <div class="card stats">
                    <img src="../images/pending.png" alt="Pending Icon">
                    <h2><?php 
                        $pending = 0;
                        foreach($test_requests as $test) {
                            if($test['test_status'] == 'Pending') $pending++;
                        }
                        echo $pending;
                    ?></h2>
                    <p>Pending Tests</p>
                </div>

                <div class="card stats">
                    <img src="../images/completed.png" alt="Completed Icon">
                    <h2><?php 
                        $completed = 0;
                        foreach($test_requests as $test) {
                            if($test['test_status'] == 'Completed') $completed++;
                        }
                        echo $completed;
                    ?></h2>
                    <p>Completed Tests</p>
                </div>
            </div>

            <!-- Divider -->
            <hr class="divider">

            <!-- Test Requests Table -->
            <div class="card card-table">
                <h3>My Test Request History</h3>
                
                <?php if (count($test_requests) > 0): ?>
                <div class="table-container">
                    <table class="tests-table">
                        <thead>
                            <tr>
                                <th>Test ID</th>
                                <th>Test Type</th>
                                <th>Request Date</th>
                                <th>Scheduled Date</th>
                                <th>Scheduled Time</th>
                                <th>Status</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($test_requests as $test): ?>
                            <tr>
                                <td><?php echo $test['test_ID']; ?></td>
                                <td>
                                    <strong><?php echo $test['test_type']; ?></strong><br>
                                    <small>Patient: <?php echo $test['patient_name']; ?></small>
                                </td>
                                <td>
                                    <?php echo date('M j, Y', strtotime($test['request_date'])); ?><br>
                                    <small><?php echo date('g:i A', strtotime($test['request_date'])); ?></small>
                                </td>
                                <td>
                                    <?php if(!empty($test['test_date'])): ?>
                                        <?php echo date('M j, Y', strtotime($test['test_date'])); ?>
                                    <?php else: ?>
                                        <em>Not scheduled</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(!empty($test['test_time'])): ?>
                                        <?php echo date('g:i A', strtotime($test['test_time'])); ?>
                                    <?php else: ?>
                                        <em>Not scheduled</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-<?php echo strtolower($test['test_status']); ?>">
                                        <?php echo $test['test_status']; ?>
                                    </span>
                                </td>
                                <td>
                                    
                                    <?php if($test['test_status'] == 'Pending'): ?>
                                        <a href="edittest.php?id=<?php echo $test['test_ID']; ?>" class="card-btn edit-btn">Edit</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="no-data">
                    <p>You haven't made any test requests yet.</p>
                    <a href="pt.php" class="card-btn">Request a Test</a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Test Details Section -->
            <?php if (count($test_requests) > 0): ?>
            <div class="card card-details">
                <h3>Test Request Details</h3>
                <div class="details-grid">
                    <?php foreach($test_requests as $test): ?>
                    <div class="detail-card">
                        <h4>Test #<?php echo $test['test_ID']; ?> - <?php echo $test['test_type']; ?></h4>
                        <div class="detail-row">
                            <span class="label">Patient Name:</span>
                            <span class="value"><?php echo $test['patient_name']; ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Patient Email:</span>
                            <span class="value"><?php echo $test['patient_email']; ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Test Type:</span>
                            <span class="value"><?php echo $test['test_type']; ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Scheduled Date:</span>
                            <span class="value">
                                <?php if(!empty($test['test_date'])): ?>
                                    <?php echo date('F j, Y', strtotime($test['test_date'])); ?>
                                <?php else: ?>
                                    <em>To be scheduled</em>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Scheduled Time:</span>
                            <span class="value">
                                <?php if(!empty($test['test_time'])): ?>
                                    <?php echo date('g:i A', strtotime($test['test_time'])); ?>
                                <?php else: ?>
                                    <em>To be scheduled</em>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Request Date:</span>
                            <span class="value"><?php echo date('F j, Y g:i A', strtotime($test['request_date'])); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Status:</span>
                            <span class="value status-<?php echo strtolower($test['test_status']); ?>">
                                <?php echo $test['test_status']; ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

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