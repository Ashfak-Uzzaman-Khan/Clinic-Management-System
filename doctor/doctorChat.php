<?php
session_start();

// Check if doctor is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'doctor') {
    header("Location: ../log.php");
    exit();
}

$doctor_id = $_SESSION['user_id'];
$selected_patient_id = isset($_POST['patient_id']) ? intval($_POST['patient_id']) : (isset($_GET['patient_id']) ? intval($_GET['patient_id']) : 0);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch ALL patients (not just those who have chatted)
$patients_sql = "SELECT patient_ID, patient_name FROM patientlog ORDER BY patient_name";
$patients_result = $conn->query($patients_sql);
$patients = [];
if ($patients_result->num_rows > 0) {
    while($row = $patients_result->fetch_assoc()) {
        $patients[] = $row;
    }
}

// Handle patient selection
if (isset($_POST['select_patient'])) {
    $selected_patient_id = intval($_POST['patient_id']);
}

// Handle message sending - FIXED: No redirect, just process and continue
if (isset($_POST['send']) && !empty($_POST['message']) && $selected_patient_id > 0) {
    $msg = $conn->real_escape_string($_POST['message']);
    $sql = "INSERT INTO chat (sender_id, receiver_id, message, sender_type, receiver_type) 
            VALUES ('$doctor_id', '$selected_patient_id', '$msg', 'doctor', 'patient')";
    
    if (!$conn->query($sql)) {
        $error = "Failed to send message: " . $conn->error;
    } else {
        $success_message = "Message sent successfully!";
        // No redirect - just continue to show the page with the new message
    }
}

// Fetch chat messages if patient is selected
$messages = [];
$current_patient = null;

if ($selected_patient_id > 0) {
    // Get current patient details
    $patient_sql = "SELECT patient_name FROM patientlog WHERE patient_ID = '$selected_patient_id'";
    $patient_result = $conn->query($patient_sql);
    if ($patient_result->num_rows > 0) {
        $current_patient = $patient_result->fetch_assoc();
    }
    
    // Fetch chat messages
    $sql = "SELECT * FROM chat 
            WHERE (sender_id='$doctor_id' AND receiver_id='$selected_patient_id' AND sender_type='doctor' AND receiver_type='patient')
               OR (sender_id='$selected_patient_id' AND receiver_id='$doctor_id' AND sender_type='patient' AND receiver_type='doctor')
            ORDER BY timestamp ASC";
            
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
    <title>Patient Communication - Doctor</title>
    <link rel="stylesheet" href="doctorChat.css">
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
            <li><a href="doctor.php">Dashboard</a></li>
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
            <img src="../images/logo.png" alt="Doctor" class="sidebar-logo">
            <h3>Doctor Panel</h3>
            <hr style="border:none; height:3px; background-color:white; width:80%; margin-top:10px;">
        </div>
        <ul class="sidebar-menu">
            <li><a href="manageSlot.php"><img src="../images/apointment.avif" class="sidebar-icon">Manage Slots</a></li>
            <li><a href="reportsPrescriptions.php"><img src="../images/report.jpg" class="sidebar-icon">Reports & Prescriptions</a></li>
            <li><a href="doctor_chat.php"><img src="../images/message.webp" class="sidebar-icon">Patient Communication</a></li>
            <li><a href="settings.php"><img src="../images/settings.png" class="sidebar-icon">Settings</a></li>
            <li><a href="myProfile.php"><img src="../images/settings.png" class="sidebar-icon">Profile</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="form-container">
            <h2 class="welcome-text">Patient Communication - Dr. <?php echo $_SESSION['user_name']; ?></h2>

            <!-- Success Message -->
            <?php if (isset($success_message)): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Patient Selection Card -->
            <div class="card card-form">
                <h3>Select Patient to Chat With</h3>
                <form method="post" class="patient-selection-form">
                    <div class="form-group">
                        <label>Choose Patient:</label>
                        <select name="patient_id" required>
                            <option value="">Select a Patient</option>
                            <?php foreach($patients as $patient): ?>
                                <option value="<?php echo $patient['patient_ID']; ?>" 
                                    <?php echo $selected_patient_id == $patient['patient_ID'] ? 'selected' : ''; ?>>
                                    <?php echo $patient['patient_name']; ?> (ID: <?php echo $patient['patient_ID']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="select_patient" class="card-btn">Select Patient</button>
                </form>
                
                <?php if (count($patients) === 0): ?>
                    <div class="no-patients" style="text-align: center; color: #666; margin-top: 15px;">
                        No patients found in the system.
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($selected_patient_id > 0 && $current_patient): ?>
            <!-- Chat Container -->
            <div class="card card-form">
                <h3>Chat with <?php echo $current_patient['patient_name']; ?></h3>
                
                <div class="chat-messages" id="chatMessages">
                    <?php if (count($messages) > 0): ?>
                        <?php foreach($messages as $row): ?>
                            <div class="message <?php echo $row['sender_type'] == 'doctor' ? 'doctor-message' : 'patient-message'; ?>">
                                <div class="message-sender">
                                    <?php echo $row['sender_type'] == 'doctor' ? 'You' : $current_patient['patient_name']; ?>:
                                </div>
                                <div class="message-content">
                                    <?php echo htmlspecialchars($row['message']); ?>
                                </div>
                                <div class="message-time">
                                    <?php echo date('h:i A', strtotime($row['timestamp'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-messages">
                            No messages yet. Start the conversation!
                        </div>
                    <?php endif; ?>
                </div>

                <form method="post" class="chat-form" id="chatForm">
                    <input type="hidden" name="patient_id" value="<?php echo $selected_patient_id; ?>">
                    <div class="message-input-group">
                        <input type="text" name="message" placeholder="Type your message..." required id="messageInput">
                        <button type="submit" name="send" class="send-btn">Send</button>
                    </div>
                </form>
            </div>
            
            <!-- Auto-scroll to bottom of chat -->
            <script>
                // Scroll to bottom of chat messages
                function scrollToBottom() {
                    var chatMessages = document.getElementById('chatMessages');
                    if (chatMessages) {
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                }
                
                // Scroll when page loads
                window.onload = scrollToBottom;
                
                // Clear message input after successful send
                <?php if (isset($success_message)): ?>
                    document.getElementById('messageInput').value = '';
                <?php endif; ?>
            </script>
            
            <?php elseif ($selected_patient_id > 0): ?>
            <div class="error-message">
                Selected patient not found in the system.
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