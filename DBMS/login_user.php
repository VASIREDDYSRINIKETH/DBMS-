<?php
session_start();

if(isset($_SESSION['username'])) {
    // Get the username
    $username = $_SESSION['username'];

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if all required fields are filled
        if (!empty($_POST['slot1_date']) && !empty($_POST['slot1_time'])) {
            // Get form data
            $slot1_date = $_POST['slot1_date'];
            $slot1_time = $_POST['slot1_time'];
            $slot2_time = $_POST['slot1_time'];

            // Calculate slot2 as 2 months after slot1
            $slot2_date = date('Y-m-d', strtotime($slot1_date . ' +2 months'));

            // Database connection
            $conn = new mysqli("localhost", "root", "", "vaccination_program_db");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Prepared statement to prevent SQL injection
            $stmt = $conn->prepare("INSERT INTO slot (username,slot1_date, slot1_time, slot2_date, slot2_time) VALUES (?, ?, ?, ?,?)");
            $stmt->bind_param("sssss", $username,$slot1_date, $slot1_time, $slot2_date, $slot2_time);

            // Execute the statement
            if ($stmt->execute()) {
                echo "Slot information added successfully.";
            } else {
                echo "Error: " . $stmt->error;
            }

            // Close statement and connection
            $stmt->close();
            $conn->close();
        } else {
            // Handle case where required fields are not filled
            echo "All fields are required.";
        }
    } else {
        // Handle case where form is not submitted
        echo "Form not submitted.";
    }
} else {
    echo "Username not found.";
}
?>
