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

// Handle status update
if (isset($_GET['action']) && isset($_GET['id'])) {
    $test_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if ($action == 'approve') {
        $new_status = 'approved';
        $message = "Test request approved successfully!";
    } elseif ($action == 'reject') {
        $new_status = 'rejected';
        $message = "Test request rejected!";
    } else {
        $message = "Invalid action!";
    }
    
    if (isset($new_status)) {
        $sql = "UPDATE testop SET test_status = ? WHERE test_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_status, $test_id);
        
        if ($stmt->execute()) {
            $success_message = $message;
        } else {
            $error_message = "Error updating status: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch all test requests
$sql = "SELECT * FROM testop ORDER BY request_date DESC";
$result = $conn->query($sql);

$test_requests = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $test_requests[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Requests - Admin</title>
    <link rel="stylesheet" href="testrequest.css">
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
            <h2 class="welcome-text">Test Requests Management</h2>
            
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
                        foreach($test_requests as $request) {
                            if($request['test_status'] == 'pending') $pending++;
                        }
                        echo $pending;
                    ?></h2>
                    <p>Pending Requests</p>
                </div>

                <div class="card stats">
                    <img src="../images/approved.png" alt="Approved Icon">
                    <h2><?php 
                        $approved = 0;
                        foreach($test_requests as $request) {
                            if($request['test_status'] == 'approved') $approved++;
                        }
                        echo $approved;
                    ?></h2>
                    <p>Approved Tests</p>
                </div>
            </div>

            <!-- Messages -->
            <?php if (isset($success_message)): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Divider -->
            <hr class="divider">

            <!-- Test Requests Table -->
            <div class="card card-table">
                <h3>Test Requests List</h3>
                
                <?php if (count($test_requests) > 0): ?>
                <div class="table-container">
                    <table class="test-requests-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient Name</th>
                                <th>Patient Email</th>
                                <th>Test Type</th>
                                <th>Test Date</th>
                                <th>Test Time</th>
                                <th>Request Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($test_requests as $request): ?>
                            <tr>
                                <td><?php echo $request['test_ID']; ?></td>
                                <td>
                                    <strong><?php echo $request['patient_name']; ?></strong>
                                </td>
                                <td><?php echo $request['patient_email']; ?></td>
                                <td>
                                    <strong><?php echo $request['test_type']; ?></strong>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($request['test_date'])); ?></td>
                                <td><?php echo date('h:i A', strtotime($request['test_time'])); ?></td>
                                <td><?php echo date('M j, Y', strtotime($request['request_date'])); ?></td>
                                <td>
                                    <span class="status-<?php echo strtolower($request['test_status']); ?>">
                                        <?php echo ucfirst($request['test_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($request['test_status'] == 'pending'): ?>
                                        <a href="testrequest.php?action=approve&id=<?php echo $request['test_ID']; ?>" 
                                           class="card-btn approve-btn"
                                           onclick="return confirm('Are you sure you want to approve this test request?')">
                                            Approve
                                        </a>
                                        <a href="testrequest.php?action=reject&id=<?php echo $request['test_ID']; ?>" 
                                           class="card-btn reject-btn"
                                           onclick="return confirm('Are you sure you want to reject this test request?')">
                                            Reject
                                        </a>
                                    <?php else: ?>
                                        <span class="action-completed">Action Taken</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="no-data">
                    <p>No test requests found in the system.</p>
                </div>
                <?php endif; ?>
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