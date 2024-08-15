<?php
session_start();

if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $conn = new mysqli("localhost", "root", "", "vaccination_program_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT adhaar_no FROM citizens WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $adhaar_no = $row['adhaar_no'];

        $stmt->close();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ((!empty($_POST['slot1_date']) && !empty($_POST['slot1_time']))|| ( !empty($_POST['slot2_date']) && !empty($_POST['slot2_time']))) {
                $slot1_date = $_POST['slot1_date'];
                $slot1_time = $_POST['slot1_time'];
                $slot2_date = $_POST['slot2_date'];
                $slot2_time = $_POST['slot2_time'];

                // Check if the slot booking dates are already fully booked
                $stmt_check_slots = $conn->prepare("SELECT COUNT(*) AS count FROM slot WHERE (slot1_date = ? OR slot2_date = ?) AND adhaar_no != ?");
                $stmt_check_slots->bind_param("sss", $slot1_date, $slot2_date, $adhaar_no);
                $stmt_check_slots->execute();
                $result_check_slots = $stmt_check_slots->get_result();
                $row_check_slots = $result_check_slots->fetch_assoc();
                $slot_count = $row_check_slots['count'];
                $stmt_check_slots->close();

                if($slot_count >= 2) {
                    header('Location:login_modify_time2.html');
                } else {
                    // Update slots in the database
                    $stmt = $conn->prepare("UPDATE slot SET slot1_date = ?, slot1_time = ?, slot2_date = ?, slot2_time = ? WHERE adhaar_no = ?");
                    $stmt->bind_param("sssss", $slot1_date, $slot1_time, $slot2_date, $slot2_time, $adhaar_no);

                    if ($stmt->execute()) {
                        header('Location:success4.html');
                    } else {
                        echo "Error: " . $stmt->error;
                    }

                    $stmt->close();
                }
            } else {
                echo "All fields are required.";
            }
        } 
    } else {
        echo "Aadhaar number not found for the provided username.";
    }

    $conn->close();
} else {
    echo "Username not found.";
}
?>
