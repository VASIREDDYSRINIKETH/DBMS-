<?php
session_start(); // Start the session

function moveRowToDeleted($aadhaar_no, $conn) {
    // SQL query to move the row to the "deleted" table
    $move_sql = "INSERT INTO deleted SELECT * FROM slot WHERE adhaar_no = '$aadhaar_no'";
    if ($conn->query($move_sql) === TRUE) {
        // SQL query to update the 'check1' attribute in the 'citizens' table
        $update_sql = "UPDATE citizens SET check1 = 1 WHERE adhaar_no = '$aadhaar_no'";
        if ($conn->query($update_sql) === TRUE) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

// Check if delete button is clicked
if (isset($_POST['delete_row'])) {
    // Database connection (Replace with your database credentials)
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

    $aadhaar_no = $_POST['aadhaar_no'];

    // Call the function to move the row to "deleted" table and update "check1" attribute
    if (moveRowToDeleted($aadhaar_no, $conn)) {
        // SQL query to delete the row from the "slot" table
        $delete_sql = "DELETE FROM slot WHERE adhaar_no = '$aadhaar_no'";
        if ($conn->query($delete_sql) === TRUE) {
            // Redirect to the same page with stored search criteria
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error deleting row from 'slot' table: " . $conn->error;
        }
    } else {
        echo "Error moving row to 'deleted' table or updating 'check1' attribute: " . $conn->error;
    }

    $conn->close();
}

// Store search criteria in session variables
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['slot'] = $_POST["slot"];
    $_SESSION['selected_date'] = $_POST["date_select"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor-Aadhaar</title>
    <link rel="stylesheet" href="supervisor_aadhaar.css">
    <style>
        /* Add CSS styles for centering and borders */
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 0 auto; /* Centering the table */
        }
        th, td {
            border: 1px solid black; /* Adding borders */
            padding: 8px;
            text-align: left;
        }
    </style>
    <script src="https://kit.fontawesome.com/5200cd7749.js" crossorigin="anonymous"></script>
</head>
<body>
<div id="heading">
    <i class="fa-solid fa-house-chimney" id="top_home" onclick="window.location.href='supervisor.html'"></i>
    <h1>Search by Crossed Deadline</h1>
</div>
<br>
<div class="bdy">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <a> Select the slot:</a>
        <input type="radio" name="slot" value="slot1">Session1 &nbsp;&nbsp;</input>
        <input type="radio" name="slot" value="slot2">Session2</input> <br> <br>
        <div id="date_cont">
            <label for="date_select" id="date_select">Date: </label>
            <input type="date" name="date_select" id="date" placeholder="Date">
        </div>
        <br>
        <div id="time_cont">
            <label for="slot_select" id="slot_select">Slot: </label>
            <select id="slot" name="slot_select" required>
                <option value="8AM-12PM">8AM to 12PM</option>
                <option value="2PM-6PM">2PM to 6PM</option>
            </select>
        </div>
        <br>
        <button type="submit" id="search" name="submit">Search <i class="fa-solid fa-magnifying-glass"></i></button>
    </form>
    <br>
    <?php
    // PHP code for handling form submission and retrieving Aadhaar numbers

    // Retrieve search criteria from session variables
    if (isset($_SESSION['slot']) && isset($_SESSION['selected_date'])) {
        $slot = $_SESSION['slot'];
        $selected_date = $_SESSION['selected_date'];

        // Database connection and query execution (Replace with your database credentials)
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

        if ($slot == "slot1") {
            $checkbox = "check_box";
            $slot_date = "slot1_date";
            $sql = "SELECT s.adhaar_no,c.first_name,c.last_name,c.age,c.city,c.pin_code FROM slot as s
                JOIN citizens as c ON s.adhaar_no=c.adhaar_no
                WHERE $checkbox = 0 AND $slot_date < '$selected_date'";
        } else {
            $checkbox = "check_box";
            $slot_date = "slot2_date";
            $sql = "SELECT s.adhaar_no,c.first_name,c.last_name,c.age,c.city,c.pin_code FROM slot as s
                JOIN citizens as c ON s.adhaar_no=c.adhaar_no
                WHERE $checkbox <= 1 AND $slot_date < '$selected_date'";
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Aadhaar Number</th><th>First Name</th><th>Last Name</th><th>Age</th><th>City</th><th>Pin Code</th><th>Send Message</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["adhaar_no"] . "</td>";
                echo "<td>" . $row["first_name"] . "</td>";
                echo "<td>" . $row["last_name"] . "</td>";
                echo "<td>" . $row["age"] . "</td>";
                echo "<td>" . $row["city"] . "</td>";
                echo "<td>" . $row["pin_code"] . "</td>";
                // Add Send Message button with form for row deletion
                echo "<td>";
                echo "<form method='post' action=''>";
                echo "<input type='hidden' name='aadhaar_no' value='" . $row["adhaar_no"] . "'>";
                echo "<input type='hidden' name='date_select' value='" . $selected_date . "'>";
                echo "<input type='hidden' name='slot' value='" . $slot . "'>";
                echo "<button type='submit' name='delete_row'>Send Message</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No Aadhaar numbers found for the selected criteria.";
        }

        $conn->close();
    }
    ?>
    <br>
    <button onclick="window.location.href='supervisor1.html'" type="submit" id="home">Back to home <i class="fa-solid fa-house-chimney"></i></button>
</div>
</body>
</html>
