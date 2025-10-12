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
if (isset($_POST['update_status'])) {
    $a_ID = intval($_POST['a_ID']);
    $new_status = $conn->real_escape_string($_POST['a_status']);
    
    $update_sql = "UPDATE ambulanceD SET a_status = ? WHERE a_ID = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $a_ID);
    
    if ($stmt->execute()) {
        $success_message = "Ambulance request status updated successfully!";
    } else {
        $error_message = "Error updating status: " . $conn->error;
    }
    $stmt->close();
}

// Fetch all ambulance requests
$sql = "SELECT * FROM ambulanceD ORDER BY a_ID DESC";
$result = $conn->query($sql);

$ambulance_requests = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $ambulance_requests[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambulance Requests - Admin</title>
    <link rel="stylesheet" href="arequest.css">
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
            <h2 class="welcome-text">Ambulance Requests Management</h2>
            
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

                <div class="card stats">
                    <img src="../images/rejected.png" alt="Rejected Icon">
                    <h2><?php 
                        $rejected = 0;
                        foreach($ambulance_requests as $request) {
                            if($request['a_status'] == 'rejected') $rejected++;
                        }
                        echo $rejected;
                    ?></h2>
                    <p>Rejected Requests</p>
                </div>
            </div>

            <!-- Divider -->
            <hr class="divider">

            <!-- Messages -->
            <?php if (isset($success_message)): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Ambulance Requests Table -->
            <div class="card card-table">
                <h3>Ambulance Requests</h3>
                
                <?php if (count($ambulance_requests) > 0): ?>
                <div class="table-container">
                    <table class="requests-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient Details</th>
                                <th>Situation</th>
                                <th>Ambulance Type</th>
                                <th>Contact</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ambulance_requests as $request): ?>
                            <tr>
                                <td><?php echo $request['a_ID']; ?></td>
                                <td>
                                    <strong><?php echo $request['patient_name']; ?></strong><br>
                                    <small><?php echo $request['patient_email']; ?></small>
                                </td>
                                <td>
                                    <span class="situation-<?php echo strtolower($request['situation_case']); ?>">
                                        <?php echo $request['situation_case']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                        $ambulance_type = isset($request['a_type']) ? $request['a_type'] : 'Standard';
                                        echo $ambulance_type;
                                    ?>
                                </td>
                                <td><?php echo $request['patient_contactNO']; ?></td>
                                <td>
                                    <div class="address-container">
                                        <?php echo $request['a_address']; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-<?php echo strtolower($request['a_status']); ?>">
                                        <?php echo $request['a_status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="" class="status-form">
                                        <input type="hidden" name="a_ID" value="<?php echo $request['a_ID']; ?>">
                                        <select name="a_status" class="status-select">
                                            <option value="pending" <?php if($request['a_status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                            <option value="approved" <?php if($request['a_status'] == 'approved') echo 'selected'; ?>>Approve</option>
                                            <option value="rejected" <?php if($request['a_status'] == 'rejected') echo 'selected'; ?>>Reject</option>
                                        </select>
                                        <button type="submit" name="update_status" class="card-btn update-btn">Update</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="no-data">
                    <p>No ambulance requests found in the system.</p>
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