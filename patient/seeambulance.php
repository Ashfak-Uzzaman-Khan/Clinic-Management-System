<?php 
session_start();

// Check if patient is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'patient') {
    header("Location: ../log.php");
    exit();
}

// Get patient info from session
$patient_name = $_SESSION['user_name'];
$patient_email = $_SESSION['user_email'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch ambulance requests for this patient
$sql = "SELECT * FROM ambulanceD WHERE patient_email = ? ORDER BY a_ID DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patient_email);
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Ambulance Requests - Patient</title>
    <link rel="stylesheet" href="seeambulance.css">
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
            <li><a href="seeambulance.php"><img src="../images/ambulance.png" class="sidebar-icon">My Ambulance Requests</a></li>
            <li><a href="seetest.php"><img src="../images/test.png" class="sidebar-icon">My Test Requests</a></li>
            <li><a href="#"><img src="../images/appointment.png" class="sidebar-icon">My Appointments</a></li>
            <li><a href="#"><img src="../images/setting.png" class="sidebar-icon">Setting</a></li>
            <li><a href="../log.php"><img src="../images/logout.png" class="sidebar-icon">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard-container">
            <h2 class="welcome-text">My Ambulance Requests - <?php echo $patient_name; ?></h2>
            
            <!-- Stats Overview -->
            <div class="cards-container">
                <div class="card stats">
                    <img src="../images/ambulance.png" alt="Ambulance Icon">
                    <h2><?php echo count($ambulance_requests); ?></h2>
                    <p>Total Requests</p>
                </div>

                <div class="card stats">
                    <img src="../images/pending.png" alt="Pending Icon">
                    <h2><?php 
                        $pending = 0;
                        foreach($ambulance_requests as $request) {
                            if($request['a_status'] == 'pending') $pending++;
                        }
                        echo $pending;
                    ?></h2>
                    <p>Pending Requests</p>
                </div>

                <div class="card stats">
                    <img src="../images/approved.png" alt="Approved Icon">
                    <h2><?php 
                        $approved = 0;
                        foreach($ambulance_requests as $request) {
                            if($request['a_status'] == 'approved') $approved++;
                        }
                        echo $approved;
                    ?></h2>
                    <p>Approved Requests</p>
                </div>
            </div>

            <!-- Divider -->
            <hr class="divider">

            <!-- Ambulance Requests Table -->
            <div class="card card-table">
                <h3>My Ambulance Request History</h3>
                
                <?php if (count($ambulance_requests) > 0): ?>
                <div class="table-container">
                    <table class="ambulance-table">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Patient Name</th>
                                <th>Email</th>
                                <th>Situation Case</th>
                                <th>Ambulance Type</th>
                                <th>Contact No</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ambulance_requests as $request): ?>
                            <tr>
                                <td><?php echo $request['a_ID']; ?></td>
                                <td><?php echo $request['patient_name']; ?></td>
                                <td><?php echo $request['patient_email']; ?></td>
                                <td>
                                    <span class="situation-<?php echo strtolower($request['situation_case']); ?>">
                                        <?php echo $request['situation_case']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="type-<?php echo strtolower($request['a_type']); ?>">
                                        <?php echo $request['a_type']; ?>
                                    </span>
                                </td>
                                <td><?php echo $request['patient_contactNO']; ?></td>
                                <td><?php echo $request['a_address']; ?></td>
                                <td>
                                    <span class="status-<?php echo strtolower($request['a_status']); ?>">
                                        <?php echo $request['a_status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit_ambulance.php?id=<?php echo $request['a_ID']; ?>" class="card-btn edit-btn">Edit</a>
                                    <a href="delete_ambulance.php?id=<?php echo $request['a_ID']; ?>" 
                                       class="card-btn delete-btn"
                                       onclick="return confirm('Are you sure you want to delete this ambulance request?');">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="no-data">
                    <p>You haven't made any ambulance requests yet.</p>
                    <a href="pa.php" class="card-btn">Request Ambulance Now</a>
                </div>
                <?php endif; ?>
            </div>

           
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