<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vaccination_program_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if(isset($_POST['save'])) {
    // Retrieve selected checkboxes
    $selected_adhaars = $_POST['save_changes'];

    // Get selected slot
    $selected_slot = isset($_POST['selected_slot']) ? $_POST['selected_slot'] : '';

    // Update corresponding checkboxes in the database
    foreach ($selected_adhaars as $adhaar) {
        // Check which slot's checkbox was checked
        $slot_checked = ($selected_slot == 'slot1') ? 'check_box1' : 'check_box2';

        // Update the checkbox value for the corresponding slot
        $sql = "UPDATE slot SET $slot_checked = 1 WHERE adhaar_no = '$adhaar'";

        // Execute the update query
        if ($conn->query($sql) !== TRUE) {
            echo "Error updating record: " . $conn->error;
        }
    }

    echo "Changes saved successfully!";
}

// Close connection
$conn->close();
?>
