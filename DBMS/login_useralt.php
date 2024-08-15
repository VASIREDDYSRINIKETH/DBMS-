<?php
session_start();

if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $conn = new mysqli("localhost", "root", "", "vaccination_program_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT adhaar_no, check1 FROM citizens WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $adhaar_no = $row['adhaar_no'];
        $check1 = $row['check1'];

        if($check1 == 0) {
            // User has already booked a slot, redirect them
            header('Location: success3.html');
            exit();
        }

        // Proceed with slot booking
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!empty($_POST['slot1_date']) && !empty($_POST['slot1_time'])) {
                $slot1_date = $_POST['slot1_date'];
                $slot1_time = $_POST['slot1_time'];
                $slot2_time = $_POST['slot1_time'];

                $slot2_date = date('Y-m-d', strtotime($slot1_date . ' +2 months'));

                // Check if the slot booking date is already taken by two members
                $stmt_check_slot = $conn->prepare("SELECT COUNT(*) AS count FROM slot WHERE slot1_date = ?");
                $stmt_check_slot->bind_param("s", $slot1_date);
                $stmt_check_slot->execute();
                $result_check_slot = $stmt_check_slot->get_result();
                $row_check_slot = $result_check_slot->fetch_assoc();
                $slot_count = $row_check_slot['count'];
                $stmt_check_slot->close();

                if($slot_count >= 2) {
                    header('Location: login_slotbooking2.html');
                    exit();
                } else {
                    // Insert new slot into the database
                    $stmt = $conn->prepare("INSERT INTO slot (slot1_date, slot1_time, slot2_date, slot2_time, adhaar_no) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $slot1_date, $slot1_time, $slot2_date, $slot2_time, $adhaar_no);
                    if ($stmt->execute()) {
                        // Update check1 value to 0 for the person
                        $stmt_update = $conn->prepare("UPDATE citizens SET check1 = 0 WHERE adhaar_no = ?");
                        $stmt_update->bind_param("s", $adhaar_no);
                        $stmt_update->execute();
                        $stmt_update->close();

                        header('Location: success2.html');
                        exit();
                    } else {
                        echo "Error: " . $stmt->error;
                    }
                    $stmt->close();
                }
            } else {
                echo "All fields are required.";
            }
        } else {
            echo "Form not submitted.";
        }
    } else {
        echo "Aadhaar number not found for the provided username.";
    }

    // Close the connection
    $conn->close();
} else {
    echo "Username not found.";
}
?>
