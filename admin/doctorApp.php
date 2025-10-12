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

// Fetch all doctor applications
$sql = "SELECT * FROM doctorlog ORDER BY doctor_ID DESC";
$result = $conn->query($sql);

$doctors = [];
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
    <title>Doctor Applications - Admin</title>
    <link rel="stylesheet" href="doctorApp.css">
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
            <h2 class="welcome-text">Doctor Applications Management</h2>
            
            <!-- Stats Overview -->
            <div class="cards-container">
                <div class="card stats">
                    <img src="../images/doctor.png" alt="Doctor Icon">
                    <h2><?php echo count($doctors); ?></h2>
                    <p>Total Applications</p>
                </div>

                <div class="card stats">
                    <img src="../images/pending.png" alt="Pending Icon">
                    <h2><?php 
                        $pending = 0;
                        foreach($doctors as $doctor) {
                            if($doctor['status'] == 'pending') $pending++;
                        }
                        echo $pending;
                    ?></h2>
                    <p>Pending Applications</p>
                </div>

                <div class="card stats">
                    <img src="../images/approved.png" alt="Approved Icon">
                    <h2><?php 
                        $approved = 0;
                        foreach($doctors as $doctor) {
                            if($doctor['status'] == 'approved') $approved++;
                        }
                        echo $approved;
                    ?></h2>
                    <p>Approved Doctors</p>
                </div>
            </div>

            <!-- Divider -->
            <hr class="divider">

            <!-- Doctor Applications Table -->
            <div class="card card-table">
                <h3>Doctor Applications List</h3>
                
                <?php if (count($doctors) > 0): ?>
                <table class="doctors-table">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Experience</th>
                        <th>License No</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach($doctors as $doctor): ?>
                    <tr>
                        <td><?php echo $doctor['doctor_ID']; ?></td>
                        <td><?php echo $doctor['doc_name']; ?></td>
                        <td><?php echo $doctor['doc_email']; ?></td>
                        <td><?php echo $doctor['doc_dept']; ?></td>
                        <td><?php echo $doctor['doc_exp']; ?> years</td>
                        <td><?php echo $doctor['doc_licenceNo']; ?></td>
                        <td>
                            <span class="status-<?php echo strtolower($doctor['status']); ?>">
                                <?php echo $doctor['status']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="editdoctorApp.php?id=<?php echo $doctor['doctor_ID']; ?>" class="card-btn">Review</a>
                            <a href="deletedoctorApp.php?id=<?php echo $doctor['doctor_ID']; ?>" 
                               class="card-btn delete-btn"
                               onclick="return confirm('Are you sure you want to delete this application?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <?php else: ?>
                <p style="text-align: center; color: #666; margin: 20px 0;">No doctor applications found.</p>
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