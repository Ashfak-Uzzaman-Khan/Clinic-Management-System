<?php
// Connect to DB
include "db_connect.php";

// Check if "id" is passed in the URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // sanitize input (convert to number)

    // SQL to delete record
    $sql = "DELETE FROM reports WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        // Redirect back with success message
        header("Location: reportsPrescriptions.php?msg=deleted");
        exit();
    } else {
        echo " Error deleting record: " . $conn->error;
    }
} else {
    echo " No record ID provided!";
}
?>
