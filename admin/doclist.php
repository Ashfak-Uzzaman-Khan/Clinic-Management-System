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

// Handle search
$search = "";
$where_clause = "WHERE status = 'approved'";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where_clause = "WHERE status = 'approved' AND doc_name LIKE '%$search%'";
}

// Fetch approved doctors
$sql = "SELECT * FROM doctorlog $where_clause ORDER BY doc_name ASC";
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
    <title>Doctor List - Admin</title>
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
            <h2 class="welcome-text">Approved Doctors List</h2>
            
            <!-- Stats Overview -->
            <div class="cards-container">
                <div class="card stats">
                    <img src="../images/doctor.png" alt="Doctor Icon">
                    <h2><?php echo count($doctors); ?></h2>
                    <p>Total Approved Doctors</p>
                </div>

                <div class="card stats">
                    <img src="../images/department.png" alt="Department Icon">
                    <h2><?php 
                        $departments = [];
                        foreach($doctors as $doctor) {
                            if(!in_array($doctor['doc_dept'], $departments)) {
                                $departments[] = $doctor['doc_dept'];
                            }
                        }
                        echo count($departments);
                    ?></h2>
                    <p>Departments</p>
                </div>

                <div class="card stats">
                    <img src="../images/experience.png" alt="Experience Icon">
                    <h2><?php 
                        $total_exp = 0;
                        foreach($doctors as $doctor) {
                            $total_exp += $doctor['doc_exp'];
                        }
                        echo $total_exp;
                    ?></h2>
                    <p>Total Years Experience</p>
                </div>
            </div>

            <!-- Search Section -->
            <div class="card card-search">
                <h3>Search Doctors</h3>
                <form method="GET" action="" class="search-form">
                    <div class="search-group">
                        <input type="text" name="search" placeholder="Search by doctor name..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="search-btn">Search</button>
                        <?php if(!empty($search)): ?>
                            <a href="doclist.php" class="clear-btn">Clear</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Divider -->
            <hr class="divider">

            <!-- Doctors Table -->
            <div class="card card-table">
                <h3>Approved Doctors - Complete Details</h3>
                
                <?php if (count($doctors) > 0): ?>
                <div class="table-container">
                    <table class="doctors-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>Contact No</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Blood Group</th>
                                <th>Date of Birth</th>
                                <th>Address</th>
                                <th>Emergency Contact</th>
                                <th>Department</th>
                                <th>Qualification</th>
                                <th>Experience (Years)</th>
                                <th>College</th>
                                <th>License No (BMDC)</th>
                                <th>Shift</th>
                                <th>Fees (BDT)</th>
                                <th>Application Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($doctors as $doctor): ?>
                            <tr>
                                <td><?php echo $doctor['doctor_ID']; ?></td>
                                <td><?php echo $doctor['doc_name']; ?></td>
                                <td><?php echo $doctor['doc_email']; ?></td>
                                <td class="password-field"><?php echo str_repeat('*', strlen($doctor['doc_pass'])); ?></td>
                                <td><?php echo $doctor['doc_ContactNO']; ?></td>
                                <td><?php echo $doctor['doc_age']; ?></td>
                                <td><?php echo $doctor['doc_gender']; ?></td>
                                <td><?php echo $doctor['doc_bloodGrp']; ?></td>
                                <td><?php echo $doctor['doc_dob']; ?></td>
                                <td class="address-field"><?php echo $doctor['doc_address']; ?></td>
                                <td><?php echo $doctor['doc_emgContactNo']; ?></td>
                                <td><?php echo $doctor['doc_dept']; ?></td>
                                <td><?php echo $doctor['doc_quali']; ?></td>
                                <td><?php echo $doctor['doc_exp']; ?></td>
                                <td><?php echo $doctor['doc_college']; ?></td>
                                <td><?php echo $doctor['doc_licenceNo']; ?></td>
                                <td>
                                    <span class="shift-<?php echo strtolower($doctor['doc_shift']); ?>">
                                        <?php echo $doctor['doc_shift']; ?>
                                    </span>
                                </td>
                                <td>৳<?php echo $doctor['doc_fees']; ?></td>
                                <td><?php echo isset($doctor['application_date']) ? $doctor['application_date'] : 'N/A'; ?></td>
                                <td>
                                    <span class="status-<?php echo strtolower($doctor['status']); ?>">
                                        <?php echo $doctor['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="editdoctorApp.php?id=<?php echo $doctor['doctor_ID']; ?>" class="card-btn edit-btn">Edit</a>
                                    <a href="deletedoctorApp.php?id=<?php echo $doctor['doctor_ID']; ?>" 
                                       class="card-btn delete-btn"
                                       onclick="return confirm('Are you sure you want to delete this doctor?');">
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
                    <?php if(!empty($search)): ?>
                        <p>No doctors found matching "<?php echo htmlspecialchars($search); ?>"</p>
                        <a href="doclist.php" class="card-btn">Show All Doctors</a>
                    <?php else: ?>
                        <p>No approved doctors found in the system.</p>
                        <a href="doctorApp.php" class="card-btn">Review Applications</a>
                    <?php endif; ?>
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