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
$where_clause = "";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where_clause = "WHERE patient_name LIKE '%$search%' OR patient_email LIKE '%$search%'";
}

// Fetch all patients
$sql = "SELECT * FROM patientlog $where_clause ORDER BY patient_ID DESC";
$result = $conn->query($sql);

$patients = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient List - Admin</title>
    <link rel="stylesheet" href="patientlist.css">
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
            <h2 class="welcome-text">Patient Management</h2>
            
            <!-- Stats Overview -->
            <div class="cards-container">
                <div class="card stats">
                    <img src="../images/patient.png" alt="Patient Icon">
                    <h2><?php echo count($patients); ?></h2>
                    <p>Total Patients</p>
                </div>

                <div class="card stats">
                    <img src="../images/male.png" alt="Male Icon">
                    <h2><?php 
                        $male_count = 0;
                        foreach($patients as $patient) {
                            if($patient['patient_gender'] == 'Male') $male_count++;
                        }
                        echo $male_count;
                    ?></h2>
                    <p>Male Patients</p>
                </div>

                <div class="card stats">
                    <img src="../images/female.png" alt="Female Icon">
                    <h2><?php 
                        $female_count = 0;
                        foreach($patients as $patient) {
                            if($patient['patient_gender'] == 'Female') $female_count++;
                        }
                        echo $female_count;
                    ?></h2>
                    <p>Female Patients</p>
                </div>
            </div>

            <!-- Search Section -->
            <div class="card card-search">
                <h3>Search Patients</h3>
                <form method="GET" action="" class="search-form">
                    <div class="search-group">
                        <input type="text" name="search" placeholder="Search by patient name or email..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="search-btn">Search</button>
                        <?php if(!empty($search)): ?>
                            <a href="patientlist.php" class="clear-btn">Clear</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Divider -->
            <hr class="divider">

            <!-- Patients Table -->
            <div class="card card-table">
                <h3>Patient List</h3>
                
                <?php if (count($patients) > 0): ?>
                <div class="table-container">
                    <table class="patients-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>Contact No</th>
                                <th>Emergency Contact</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Blood Group</th>
                                <th>Date of Birth</th>
                                <th>Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($patients as $patient): ?>
                            <tr>
                                <td><?php echo $patient['patient_ID']; ?></td>
                                <td><?php echo $patient['patient_name']; ?></td>
                                <td><?php echo $patient['patient_email']; ?></td>
                                <td class="password-cell"><?php echo $patient['patient_pass']; ?></td>
                                <td><?php echo $patient['patient_contactNO']; ?></td>
                                <td><?php echo $patient['patient_emNo']; ?></td>
                                <td><?php echo $patient['patient_age']; ?></td>
                                <td>
                                    <span class="gender-<?php echo strtolower($patient['patient_gender']); ?>">
                                        <?php echo $patient['patient_gender']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="blood-group"><?php echo $patient['patient_bloodGrp']; ?></span>
                                </td>
                                <td><?php echo $patient['patient_dob']; ?></td>
                                <td class="address-cell"><?php echo $patient['patient_address']; ?></td>
                                <td class="actions-cell">
                                    <a href="editpatient.php?id=<?php echo $patient['patient_ID']; ?>" class="card-btn edit-btn">Edit</a>
                                    <a href="deletepatient.php?id=<?php echo $patient['patient_ID']; ?>" 
                                       class="card-btn delete-btn"
                                       onclick="return confirm('Are you sure you want to delete this patient?');">
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
                        <p>No patients found matching "<?php echo htmlspecialchars($search); ?>"</p>
                        <a href="patientlist.php" class="card-btn">Show All Patients</a>
                    <?php else: ?>
                        <p>No patients found in the system.</p>
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