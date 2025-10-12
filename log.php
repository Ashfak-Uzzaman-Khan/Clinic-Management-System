<?php
session_start();

// -------------------- PHP Validation Section --------------------
$username = $email = $password = "";
$usernameErr = $emailErr = $passwordErr = $loginErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Username validation - NOW ALLOWS NUMBERS
    if (empty($_POST["username"])) {
        $usernameErr = "User Name is required";
    } else {
        $username = test_input($_POST["username"]);
        if (!preg_match("/^[a-zA-Z0-9 ]*$/", $username)) {
            $usernameErr = "Only letters, numbers and white space allowed";
        }
    }

    // Email validation
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
        
        // Basic email format check
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        } 
        // Ensure '@' and '.' are present
        elseif (strpos($email, '@') === false || strpos($email, '.') === false) {
            $emailErr = "Email must contain '@' and '.'";
        }
    }

    // Password validation
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = test_input($_POST["password"]);
        if (strlen($password) < 6) {
            $passwordErr = "Password must be at least 6 characters long";
        }
    }

    // Database authentication if no validation errors
    if (empty($usernameErr) && empty($emailErr) && empty($passwordErr)) {
        
        // Database connection
        $servername = "localhost";
        $db_username = "root";
        $db_password = "";
        $dbname = "hdb";

        $conn = new mysqli($servername, $db_username, $db_password, $dbname);

        if ($conn->connect_error) {
            $loginErr = "Database connection failed. Please try again.";
        } else {
            $user_found = false;

            // Check in adminlog table
            $sql = "SELECT * FROM adminlog WHERE admin_email = ? AND admin_name = ? AND admin_pass = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("sss", $email, $username, $password);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    // Admin login successful
                    $admin_data = $result->fetch_assoc();
                    $_SESSION['user_type'] = 'admin';
                    $_SESSION['user_id'] = $admin_data['admin_ID'];
                    $_SESSION['user_name'] = $admin_data['admin_name'];
                    $_SESSION['user_email'] = $admin_data['admin_email'];
                    $stmt->close();
                    $conn->close();
                    header("Location: admin/admin.php");
                    exit();
                }
                $stmt->close();
            }

            // Check in patientlog table
            if (!$user_found) {
                $sql = "SELECT * FROM patientlog WHERE patient_email = ? AND patient_name = ? AND patient_pass = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("sss", $email, $username, $password);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        // Patient login successful
                        $patient_data = $result->fetch_assoc();
                        $_SESSION['user_type'] = 'patient';
                        $_SESSION['user_id'] = $patient_data['patient_ID'];
                        $_SESSION['user_name'] = $patient_data['patient_name'];
                        $_SESSION['user_email'] = $patient_data['patient_email'];
                        $stmt->close();
                        $conn->close();
                        header("Location: patient/ph.php");
                        exit();
                    }
                    $stmt->close();
                }
            }

            // Check in doctorlog table - FIXED THIS SECTION
            if (!$user_found) {
                // Check for approved doctors only
                $sql = "SELECT * FROM doctorlog WHERE doc_email = ? AND doc_name = ? AND doc_pass = ? AND status = 'approved'";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("sss", $email, $username, $password);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        // Doctor login successful
                        $doctor_data = $result->fetch_assoc();
                        $_SESSION['user_type'] = 'doctor';
                        $_SESSION['user_id'] = $doctor_data['doctor_ID'];
                        $_SESSION['user_name'] = $doctor_data['doc_name'];
                        $_SESSION['user_email'] = $doctor_data['doc_email'];
                        $stmt->close();
                        $conn->close();
                        header("Location: doctor/doctor.php"); // FIXED PATH
                        exit();
                    } else {
                        // Check if doctor exists but not approved
                        $sql_check = "SELECT * FROM doctorlog WHERE doc_email = ? AND doc_name = ? AND doc_pass = ?";
                        $stmt_check = $conn->prepare($sql_check);
                        $stmt_check->bind_param("sss", $email, $username, $password);
                        $stmt_check->execute();
                        $result_check = $stmt_check->get_result();
                        
                        if ($result_check->num_rows > 0) {
                            $loginErr = "Your doctor application is pending approval. Please wait for admin approval.";
                        }
                        $stmt_check->close();
                    }
                    $stmt->close();
                }
            }

            // If no user found in any table
            if (empty($loginErr)) {
                $loginErr = "Invalid username, email or password!";
            }
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Dhaka Centralized Hospital</title>
    <link rel="stylesheet" href="log.css"> <!-- external CSS -->
</head>
<body>
<!-- Back to Home Box -->
<a href="index.html" class="back-home">Go Home</a>

<!-- -------------------- Video Background -------------------- -->
<video autoplay muted loop id="bgvideo">
    <source src="images/bgvideo.mp4" type="video/mp4">
    Your browser does not support HTML5 video.
</video>

<!-- -------------------- Centered Login Form -------------------- -->
<div class="login-container">
    <div class="login-header">
        <img src="images/logo.png" alt="Hospital Logo">
        <h2>Dhaka Centralized Hospital</h2>
    </div>

    <p>Log in using your name and password</p>
    <hr>

    <!-- Display login error -->
    <?php if (!empty($loginErr)): ?>
        <div class="error" style="color: red; background: #ffe6e6; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
            <?php echo $loginErr; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <label>User Name</label>
        <input type="text" name="username" value="<?php echo $username; ?>">
        <span class="error"><?php echo $usernameErr; ?></span>

        <label>Email</label>
        <input type="text" name="email" value="<?php echo $email; ?>">
        <span class="error"><?php echo $emailErr; ?></span>

        <label>Password</label>
        <input type="password" name="password">
        <span class="error"><?php echo $passwordErr; ?></span>

        <input type="submit" value="Log In">
    </form>
    
    <!-- Additional links for different user types -->
    <div style="margin-top: 20px; text-align: center;">
        <p>Don't have an account? 
            <a href="../hms/signup.php" style="color: #004080;">Register as Patient</a> | 
            <a href="../hms/apply.php" style="color: #004080;">Apply as Doctor</a>
        </p>
    </div>
</div>

</body>
</html>