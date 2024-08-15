<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor-Timestamp</title>
    <link rel="stylesheet" href="supervisor_date_time.css">
    <script src="https://kit.fontawesome.com/5200cd7749.js" crossorigin="anonymous"></script>
</head>
<body>
    <div id="heading">
        <i class="fa-solid fa-house-chimney" id="top_home" onclick="window.location.href='supervisor.html'"></i>
        <h1>
            Supervisor/Search by Date and Time
        </h1>
    </div>
    <div id="timestamp_cont">
        <a> Select the session:</a>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="radio" name="slot" value="slot1">
                Session1 &nbsp;&nbsp;
            </input>
            <input type="radio" name="slot" value="slot2">
                Session2
            </input> <br> <br>
            <div id="date_cont">
                <label for="date_select" id="date_select">Date: </label>
                <input type="date" name="date_select" id="date" placeholder="Date">
            </div>
            <br>
            <div id="time_cont">
                <label for="slot_select" id="slot_select">Slot: </label>
                <select id="slot" name="slot_select">
                    <option value="8AM-12PM">8AM to 12PM</option>
                    <option value="2PM-6PM">2PM to 6PM</option>
                </select>
            </div>
            <br>
            <button type="submit" id="search" name="submit">Search <i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </div>
<br>
    <div id="results">
    <?php
// Check if form is submitted
if(isset($_POST['submit'])) {
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

    // Get selected slot, date, and time
    $slot = $_POST['slot'];
    $date = $_POST['date_select'];
    $time = $_POST['slot_select'];

    // Construct SQL query based on slot selection
    if ($slot == "slot1") {
        $sql = "SELECT s.adhaar_no, c.first_name, c.last_name, c.age, c.city, c.pin_code, s.check_box,s.vaccine FROM slot as s
            JOIN citizens as c ON s.adhaar_no=c.adhaar_no
            WHERE s.slot1_date = '$date' AND s.slot1_time = '$time'";
    } else {
        $sql = "SELECT s.adhaar_no, c.first_name, c.last_name, c.age, c.city, c.pin_code, s.check_box FROM slot as s
            JOIN citizens as c ON s.adhaar_no=c.adhaar_no
            WHERE s.slot2_date = '$date' AND s.slot2_time = '$time'";
    }

    // Execute query
    $result = $conn->query($sql);

    if ($result->num_rows > 0 ) {
        echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>";
        echo "<table class='centered-table' border='1'>";
        echo "<tr>";
        echo "<th>Adhaar No</th>";
        echo "<th>First Name</th>";
        echo "<th>Last Name</th>";
        echo "<th>Age</th>";
        echo "<th>City</th>";
        echo "<th>Pincode</th>";
        echo "<th>Vaccine-type</th>";
        echo "<th>Check</th>";
        echo "</tr>";
    
        // Fetch all rows and display them horizontally
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td class='center'>".$row["adhaar_no"]."</td>";
            echo "<td class='center'>".$row["first_name"]."</td>";
            echo "<td class='center'>".$row["last_name"]."</td>";
            echo "<td class='center'>".$row["age"]."</td>";
            echo "<td class='center'>".$row["city"]."</td>";
            echo "<td class='center'>".$row["pin_code"]."</td>";
            echo "<td class='center'>".$row["vaccine"]."</td>";
            echo "<td class='center'><input type='checkbox' name='check_list[]' value='".$row["adhaar_no"]."'></td>";
            echo " ";
            echo "</tr>";
        }
        echo "</table>";
        echo "<input type='hidden' name='slot' value='$slot'>";
        echo "<input type='hidden' name='date' value='$date'>";
        echo "<input type='hidden' name='time' value='$time'>";
        echo "<button type='submit' id='save_changes' name='save_changes'>Save Changes</button>";
        echo "</form>";
    } else {
        echo "No results found";
    }
    
    // Close connection
    $conn->close();
}

// Update check_box values in the database
if(isset($_POST['save_changes'])) {
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

    // Get selected slot, date, and time
    $slot = $_POST['slot'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Update check_box values based on the selected checkboxes
    if(!empty($_POST['check_list'])) {
        foreach($_POST['check_list'] as $adhaar_no) {
            $update_sql = "UPDATE slot SET check_box = ";
            if($slot == 'slot1') {
                $update_sql .= "1 ";
            } else {
                $update_sql .= "2 ";
            }
            $update_sql .= "WHERE adhaar_no = '$adhaar_no' AND ";
            if($slot == 'slot1') {
                $update_sql .= "slot1_date = '$date' AND slot1_time = '$time'";
            } else {
                $update_sql .= "slot2_date = '$date' AND slot2_time = '$time'";
            }
            $conn->query($update_sql);
        }
        echo "Changes saved successfully.";
    } else {
        echo "No checkboxes selected.";
    }

    // Close connection
    $conn->close();
}
?>

    </div>
<br>
    <button onclick="window.location.href='supervisor1.html'" type="submit" id="home">Back to home <i class="fa-solid fa-house-chimney"></i></button>
</body>
</html>
