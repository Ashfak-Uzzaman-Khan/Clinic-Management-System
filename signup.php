<?php
// Define variables and set to empty
$name = $email = $password = $contact = $age = $gender = $blood = $dob = $address = $emergency = "";
$nameErr = $emailErr = $passwordErr = $contactErr = $ageErr = $genderErr = $bloodErr = $dobErr = $addressErr = $emergencyErr = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Name validation
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = test_input($_POST["name"]);
    }

    // Email validation
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_contains($email, "@") || !str_contains($email, ".")) {
            $emailErr = "Invalid email format (must include @ and .)";
        }
    }

    // Password validation
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = test_input($_POST["password"]);
        if (strlen($password) < 3) {
            $passwordErr = "Password must be at least 3 characters long";
        }
    }

    // Contact Number validation
    if (empty($_POST["contact"])) {
        $contactErr = "Contact number is required";
    } else {
        $contact = test_input($_POST["contact"]);
        if (!preg_match("/^01[0-9]{9}$/", $contact)) {
            $contactErr = "Invalid contact number (must start with 01 and be 11 digits)";
        }
    }

    // Age validation
    if (empty($_POST["age"])) {
        $ageErr = "Age is required";
    } else {
        $age = test_input($_POST["age"]);
        if (!ctype_digit($age)) {
            $ageErr = "Age must be numeric";
        }
    }

    // Gender validation
    if (empty($_POST["gender"])) {
        $genderErr = "Gender is required";
    } else {
        $gender = test_input($_POST["gender"]);
    }

    // Blood group validation
    if (empty($_POST["blood"])) {
        $bloodErr = "Blood group is required";
    } else {
        $blood = test_input($_POST["blood"]);
    }

    // DOB validation
    if (empty($_POST["dob"])) {
        $dobErr = "Date of Birth is required";
    } else {
        $dob = test_input($_POST["dob"]);
    }

    // Address validation
    if (empty($_POST["address"])) {
        $addressErr = "Address is required";
    } else {
        $address = test_input($_POST["address"]);
    }

    // Emergency contact validation
    if (empty($_POST["emergency"])) {
        $emergencyErr = "Emergency contact is required";
    } else {
        $emergency = test_input($_POST["emergency"]);
        if (!preg_match("/^01[0-9]{9}$/", $emergency)) {
            $emergencyErr = "Invalid emergency number (must start with 01 and be 11 digits)";
        }
    }

    // If no validation errors, insert into database
    if (empty($nameErr) && empty($emailErr) && empty($passwordErr) && empty($contactErr) && 
        empty($ageErr) && empty($genderErr) && empty($bloodErr) && empty($dobErr) && 
        empty($addressErr) && empty($emergencyErr)) {
        
        // Database connection
        $servername = "localhost";
        $username = "root";
        $db_password = "";
        $dbname = "hdb";

        $conn = new mysqli($servername, $username, $db_password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if email already exists
        $check_email_sql = "SELECT patient_ID FROM patientlog WHERE patient_email = ?";
        $check_stmt = $conn->prepare($check_email_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $emailErr = "Email already exists! Please use a different email.";
            $check_stmt->close();
        } else {
            $check_stmt->close();
            
            // Insert into database
            $sql = "INSERT INTO patientlog (patient_name, patient_email, patient_pass, patient_contactNo, patient_age, patient_gender, patient_bloodGrp, patient_dob, patient_address, patient_emNo) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssisssss", $name, $email, $password, $contact, $age, $gender, $blood, $dob, $address, $emergency);
            
            if ($stmt->execute()) {
                $success_message = "Patient registration successful! You can now login.";
                // Clear form fields
                $name = $email = $password = $contact = $age = $gender = $blood = $dob = $address = $emergency = "";
            } else {
                $success_message = "Error: " . $sql . "<br>" . $conn->error;
            }
            $stmt->close();
        }
        $conn->close();
    }
}

// Input cleaning function
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Patient Signup</title>
<link rel="stylesheet" href="sign.css"> <!-- external CSS -->
</head>
<body>

<!-- Back to Home Box -->
<a href="index.html" class="back-home">Go Home</a>

<!-- -------------------- Video Background -------------------- -->
<video autoplay muted loop id="bgvideo">
    <source src="images/bgvideo.mp4" type="video/mp4">
    Your browser does not support HTML5 video.
</video>

  <div class="form-container">
    <h2>Patient Signup Form</h2>
    
    <!-- Success Message -->
    <?php if (!empty($success_message)): ?>
        <div class="success-message" style="color: green; background: #e6ffe6; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

      <label>Name:</label>
      <input type="text" name="name" value="<?php echo $name; ?>">
      <span class="error"><?php echo $nameErr; ?></span><br>

      <label>Email:</label>
      <input type="text" name="email" value="<?php echo $email; ?>">
      <span class="error"><?php echo $emailErr; ?></span><br>

      <label>Password:</label>
      <input type="password" name="password">
      <span class="error"><?php echo $passwordErr; ?></span><br>

      <label>Contact Number:</label>
      <input type="text" name="contact" value="<?php echo $contact; ?>">
      <span class="error"><?php echo $contactErr; ?></span><br>

      <label>Age:</label>
      <input type="text" name="age" value="<?php echo $age; ?>">
      <span class="error"><?php echo $ageErr; ?></span><br>

      <label>Gender:</label>
      <select name="gender">
        <option value="">Select</option>
        <option value="Male" <?php if ($gender=="Male") echo "selected"; ?>>Male</option>
        <option value="Female" <?php if ($gender=="Female") echo "selected"; ?>>Female</option>
      </select>
      <span class="error"><?php echo $genderErr; ?></span><br>

      <label>Blood Group:</label>
      <input type="text" name="blood" value="<?php echo $blood; ?>">
      <span class="error"><?php echo $bloodErr; ?></span><br>

      <label>Date of Birth:</label>
      <input type="date" name="dob" value="<?php echo $dob; ?>">
      <span class="error"><?php echo $dobErr; ?></span><br>

      <label>Address:</label>
      <textarea name="address"><?php echo $address; ?></textarea>
      <span class="error"><?php echo $addressErr; ?></span><br>

      <label>Emergency Contact:</label>
      <input type="text" name="emergency" value="<?php echo $emergency; ?>">
      <span class="error"><?php echo $emergencyErr; ?></span><br>

      <button type="submit">Sign Up</button>
    </form>
  </div>
  
</body>
</html>