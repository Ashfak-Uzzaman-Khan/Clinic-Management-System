<?php
// Define variables and initialize with empty values
$name = $email = $password = $contact = $age = $gender = $blood = $dob = $address = $emergency = "";
$department = $experience = $license = $qualification = $mbbs = $shift = $fees = "";
$nameErr = $emailErr = $passwordErr = $contactErr = $ageErr = $genderErr = $bloodErr = $dobErr = $addressErr = $emergencyErr = "";
$departmentErr = $experienceErr = $licenseErr = $qualificationErr = $mbbsErr = $shiftErr = $feesErr = "";
$success_message = "";

// Validate form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Name
    if (empty($_POST["name"])) { $nameErr = "Name is required"; } 
    else { $name = test_input($_POST["name"]); }

    // Email
    if (empty($_POST["email"])) { $emailErr = "Email is required"; } 
    else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_contains($email, "@") || !str_contains($email, ".")) {
            $emailErr = "Invalid email format";
        }
    }

    // Password
    if (empty($_POST["password"])) { $passwordErr = "Password is required"; } 
    else {
        $password = test_input($_POST["password"]);
        if (strlen($password) < 3) { $passwordErr = "Password must be at least 3 characters"; }
    }

    // Contact
    if (empty($_POST["contact"])) { $contactErr = "Contact number is required"; } 
    else {
        $contact = test_input($_POST["contact"]);
        if (!preg_match("/^01[0-9]{9}$/", $contact)) {
            $contactErr = "Invalid contact number (must start with 01 and be 11 digits)";
        }
    }

    // Age
    if (empty($_POST["age"])) { $ageErr = "Age is required"; } 
    else {
        $age = test_input($_POST["age"]);
        if (!ctype_digit($age)) { $ageErr = "Age must be numeric"; }
    }

    // Gender
    if (empty($_POST["gender"])) { $genderErr = "Gender is required"; } 
    else { $gender = test_input($_POST["gender"]); }

    // Blood group
    if (empty($_POST["blood"])) { $bloodErr = "Blood group is required"; } 
    else { $blood = test_input($_POST["blood"]); }

    // DOB
    if (empty($_POST["dob"])) { $dobErr = "Date of Birth is required"; } 
    else { $dob = test_input($_POST["dob"]); }

    // Address
    if (empty($_POST["address"])) { $addressErr = "Address is required"; } 
    else { $address = test_input($_POST["address"]); }

    // Emergency
    if (empty($_POST["emergency"])) { $emergencyErr = "Emergency contact is required"; } 
    else {
        $emergency = test_input($_POST["emergency"]);
        if (!preg_match("/^01[0-9]{9}$/", $emergency)) {
            $emergencyErr = "Invalid emergency number (must start with 01 and be 11 digits)";
        }
    }

    // Department
    if (empty($_POST["department"])) { $departmentErr = "Please select a department"; } 
    else { $department = test_input($_POST["department"]); }

    // Experience
    if (empty($_POST["experience"])) { $experienceErr = "Experience is required"; } 
    else {
        $experience = test_input($_POST["experience"]);
        if (!ctype_digit($experience)) { $experienceErr = "Experience must be numeric"; }
    }

    // License
    if (empty($_POST["license"])) { $licenseErr = "License number is required"; } 
    else { $license = test_input($_POST["license"]); }

    // Qualification
    if (empty($_POST["qualification"])) { $qualificationErr = "Qualification is required"; } 
    else { $qualification = test_input($_POST["qualification"]); }

    // MBBS College
    if (empty($_POST["mbbs"])) { $mbbsErr = "MBBS College is required"; } 
    else { $mbbs = test_input($_POST["mbbs"]); }

    // Shift
    if (empty($_POST["shift"])) { $shiftErr = "Please select a shift"; } 
    else { $shift = test_input($_POST["shift"]); }

    // Consultation Fees
    if (empty($_POST["fees"])) { $feesErr = "Consultation fees is required"; } 
    else {
        $fees = test_input($_POST["fees"]);
        if (!ctype_digit($fees)) { $feesErr = "Fees must be numeric"; }
    }

    // If no validation errors, insert into database
    if (empty($nameErr) && empty($emailErr) && empty($passwordErr) && empty($contactErr) && 
        empty($ageErr) && empty($genderErr) && empty($bloodErr) && empty($dobErr) && 
        empty($addressErr) && empty($emergencyErr) && empty($departmentErr) && 
        empty($experienceErr) && empty($licenseErr) && empty($qualificationErr) && 
        empty($mbbsErr) && empty($shiftErr) && empty($feesErr)) {
        
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
        $check_email_sql = "SELECT doctor_ID FROM doctorlog WHERE doc_email = ?";
        $check_stmt = $conn->prepare($check_email_sql);
        if ($check_stmt) {
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $emailErr = "Email already exists! Please use a different email.";
                $check_stmt->close();
            } else {
                $check_stmt->close();
                
                // Insert into database - USING SIMPLIFIED COLUMN NAMES
                $sql = "INSERT INTO doctorlog (doc_name, doc_email, doc_pass, doc_ContactNO, doc_age, doc_gender, doc_bloodGrp, doc_dob, doc_address, doc_emgContactNo, doc_dept, doc_quali, doc_exp, doc_college, doc_licenceNo, doc_shift, doc_fees) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("ssssisssssssisssi", $name, $email, $password, $contact, $age, $gender, $blood, $dob, $address, $emergency, $department, $qualification, $experience, $mbbs, $license, $shift, $fees);
                    
                    if ($stmt->execute()) {
                        $success_message = "Doctor application submitted successfully! We will review your application and contact you soon.";
                        // Clear form fields
                        $name = $email = $password = $contact = $age = $gender = $blood = $dob = $address = $emergency = "";
                        $department = $experience = $license = $qualification = $mbbs = $shift = $fees = "";
                    } else {
                        $success_message = "Error submitting application: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $success_message = "Error preparing statement: " . $conn->error;
                }
            }
        } else {
            $success_message = "Error checking email: " . $conn->error;
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
    <title>Doctor Application</title>
    <link rel="stylesheet" href="apply.css">
</head>
<body>

<!-- Back to Home Box -->
<a href="index.html" class="back-home">Go Home</a> 

<!-- Background Video -->
<video autoplay muted loop id="bgvideo">
    <source src="images/bgvideo.mp4" type="video/mp4">
    Your browser does not support HTML5 video.
</video>

<div class="form-container">
    <h2>Doctor Application Form</h2>
    
    <!-- Success Message -->
    <?php if (!empty($success_message)): ?>
        <div class="success-message" style="color: green; background: #e6ffe6; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

        <!-- Personal Info -->
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

        <!-- Professional Info -->
        <label>Department:</label>
        <select name="department">
            <option value="">Select</option>
            <option value="Neurology" <?php if ($department=="Neurology") echo "selected"; ?>>Neurology</option>
            <option value="Orthopedic" <?php if ($department=="Orthopedic") echo "selected"; ?>>Orthopedic</option>
            <option value="Cardiology" <?php if ($department=="Cardiology") echo "selected"; ?>>Cardiology</option>
            <option value="Gynecology" <?php if ($department=="Gynecology") echo "selected"; ?>>Gynecology</option>
            <option value="Surgon" <?php if ($department=="Surgon") echo "selected"; ?>>Surgon</option>
        </select>
        <span class="error"><?php echo $departmentErr; ?></span><br>

        <label>Years of Experience:</label>
        <input type="text" name="experience" value="<?php echo $experience; ?>">
        <span class="error"><?php echo $experienceErr; ?></span><br>

        <label>License Number (BMDC):</label>
        <input type="text" name="license" value="<?php echo $license; ?>">
        <span class="error"><?php echo $licenseErr; ?></span><br>

        <label>Qualifications:</label>
        <input type="text" name="qualification" value="<?php echo $qualification; ?>">
        <span class="error"><?php echo $qualificationErr; ?></span><br>

        <label>MBBS College:</label>
        <input type="text" name="mbbs" value="<?php echo $mbbs; ?>">
        <span class="error"><?php echo $mbbsErr; ?></span><br>

        <label style="color:red;">Working Information:</label>
        <input type="text" value="Will work according to department needs" disabled class="highlight"><br>

        <label>Shift:</label>
        <select name="shift">
            <option value="">Select</option>
            <option value="Morning" <?php if ($shift=="Morning") echo "selected"; ?>>Morning</option>
            <option value="Evening" <?php if ($shift=="Evening") echo "selected"; ?>>Evening</option>
            <option value="Night" <?php if ($shift=="Night") echo "selected"; ?>>Night</option>
        </select>
        <span class="error"><?php echo $shiftErr; ?></span><br>

        <label>Consultation Fees (BDT):</label>
        <input type="text" name="fees" value="<?php echo $fees; ?>">
        <span class="error"><?php echo $feesErr; ?></span><br>

        <button type="submit">Submit Application</button>
    </form>
</div>

</body>
</html>