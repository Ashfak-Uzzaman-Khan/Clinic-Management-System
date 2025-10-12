<?php
session_start();

// Check if patient is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'patient') {
    header("Location: ../log.php");
    exit();
}

$patient_id = $_SESSION['user_id'];
$patient_name = $_SESSION['user_name'];
$selected_doctor_id = isset($_POST['doctor_id']) ? intval($_POST['doctor_id']) : 0;

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all approved doctors
$doctors_sql = "SELECT doctor_ID, doc_name, doc_dept FROM doctorlog WHERE status = 'approved'";
$doctors_result = $conn->query($doctors_sql);
$doctors = [];
if ($doctors_result->num_rows > 0) {
    while($row = $doctors_result->fetch_assoc()) {
        $doctors[] = $row;
    }
}

// Handle doctor selection
if (isset($_POST['select_doctor'])) {
    $selected_doctor_id = intval($_POST['doctor_id']);
}

// Handle message sending
if (isset($_POST['send']) && !empty($_POST['message']) && $selected_doctor_id > 0) {
    $msg = $conn->real_escape_string($_POST['message']);
    $sql = "INSERT INTO chat (sender_id, receiver_id, sender_type, receiver_type, message) 
            VALUES ('$patient_id', '$selected_doctor_id', 'patient', 'doctor', '$msg')";
    
    if (!$conn->query($sql)) {
        $error = "Failed to send message: " . $conn->error;
    } else {
        // Refresh messages after sending
        header("Location: patient_chat.php?doctor_id=" . $selected_doctor_id);
        exit();
    }
}

// Fetch chat messages if doctor is selected
$messages = [];
$current_doctor_name = "";
if ($selected_doctor_id > 0) {
    // Get doctor name
    $doctor_sql = "SELECT doc_name FROM doctorlog WHERE doctor_ID = '$selected_doctor_id'";
    $doctor_result = $conn->query($doctor_sql);
    if ($doctor_result->num_rows > 0) {
        $doctor_data = $doctor_result->fetch_assoc();
        $current_doctor_name = $doctor_data['doc_name'];
    }
    
    // Fetch messages
    $sql = "SELECT c.*, 
                   CASE 
                       WHEN c.sender_type = 'patient' THEN p.patient_name 
                       WHEN c.sender_type = 'doctor' THEN d.doc_name 
                   END as sender_name,
                   CASE 
                       WHEN c.receiver_type = 'patient' THEN p2.patient_name 
                       WHEN c.receiver_type = 'doctor' THEN d2.doc_name 
                   END as receiver_name
            FROM chat c
            LEFT JOIN patientlog p ON c.sender_id = p.patient_ID AND c.sender_type = 'patient'
            LEFT JOIN doctorlog d ON c.sender_id = d.doctor_ID AND c.sender_type = 'doctor'
            LEFT JOIN patientlog p2 ON c.receiver_id = p2.patient_ID AND c.receiver_type = 'patient'
            LEFT JOIN doctorlog d2 ON c.receiver_id = d2.doctor_ID AND c.receiver_type = 'doctor'
            WHERE (c.sender_id='$patient_id' AND c.receiver_id='$selected_doctor_id' AND c.sender_type='patient' AND c.receiver_type='doctor')
               OR (c.sender_id='$selected_doctor_id' AND c.receiver_id='$patient_id' AND c.sender_type='doctor' AND c.receiver_type='patient')
            ORDER BY c.timestamp ASC";
            
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Communication - Patient</title>
    <link rel="stylesheet" href="patient_chat.css">
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
            <li><a href="patient_chat.php"><img src="../images/message.webp" class="sidebar-icon">Doctor Chat</a></li>
            <li><a href="#"><img src="../images/setting.png" class="sidebar-icon">Setting</a></li>
            <li><a href="../log.php"><img src="../images/logout.png" class="sidebar-icon">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="form-container">
            <h2 class="welcome-text">Doctor Communication - <?php echo $patient_name; ?></h2>

            <!-- Error Message -->
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Doctor Selection Card -->
            <div class="card card-form">
                <h3>Select Doctor to Chat With</h3>
                <form method="post" class="doctor-selection-form">
                    <div class="form-group">
                        <label>Choose Doctor:</label>
                        <select name="doctor_id" required>
                            <option value="">Select a Doctor</option>
                            <?php foreach($doctors as $doctor): ?>
                                <option value="<?php echo $doctor['doctor_ID']; ?>" 
                                    <?php echo $selected_doctor_id == $doctor['doctor_ID'] ? 'selected' : ''; ?>>
                                    Dr. <?php echo $doctor['doc_name']; ?> - <?php echo $doctor['doc_dept']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="select_doctor" class="card-btn">Select Doctor</button>
                </form>
            </div>

            <?php if ($selected_doctor_id > 0 && !empty($current_doctor_name)): ?>
            <!-- Chat Container -->
            <div class="card card-form">
                <h3>Chat with Dr. <?php echo $current_doctor_name; ?></h3>
                
                <div class="chat-messages" id="chatMessages">
                    <?php if (count($messages) > 0): ?>
                        <?php foreach($messages as $row): ?>
                            <div class="message <?php echo $row['sender_type'] == 'patient' ? 'patient-message' : 'doctor-message'; ?>">
                                <div class="message-sender">
                                    <?php echo $row['sender_type'] == 'patient' ? 'You' : 'Dr. ' . $row['sender_name']; ?>:
                                </div>
                                <div class="message-content">
                                    <?php echo htmlspecialchars($row['message']); ?>
                                </div>
                                <div class="message-time">
                                    <?php echo date('M j, h:i A', strtotime($row['timestamp'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-messages">
                            No messages yet. Start the conversation!
                        </div>
                    <?php endif; ?>
                </div>

                <form method="post" class="chat-form">
                    <input type="hidden" name="doctor_id" value="<?php echo $selected_doctor_id; ?>">
                    <div class="message-input-group">
                        <input type="text" name="message" placeholder="Type your message..." required>
                        <button type="submit" name="send" class="send-btn">Send</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Auto-scroll to bottom of chat messages
window.onload = function() {
    var chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
};
</script>
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