<?php
session_start();

// Check if session is not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

// Check if vaccine selection form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve selected vaccine
    if (isset($_POST['vaccine'])) {
        $selected_vaccine = $_POST['vaccine'];
        
        // Connect to the database
        $conn = new mysqli("localhost", "root", "", "vaccination_program_db");

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch adhaar_no from citizens table
        $username = $_SESSION['username'];
        $sql_adhaar_no = "SELECT adhaar_no FROM citizens WHERE username = '$username'";
        $result_adhaar_no = $conn->query($sql_adhaar_no);

        if ($result_adhaar_no->num_rows > 0) {
            $row_adhaar_no = $result_adhaar_no->fetch_assoc();
            $adhaar_no = $row_adhaar_no["adhaar_no"];
            
            // Update vaccine selection in the slot table
            $sql_update_vaccine = "UPDATE slot SET vaccine = '$selected_vaccine' WHERE adhaar_no = '$adhaar_no'";
            
            if ($conn->query($sql_update_vaccine) === TRUE) {
                // Redirect user to user_home.html after updating the vaccine
                header("Location: user_home.php");
                exit();
            } else {
                echo "Error updating record: " . $conn->error;
            }
        } else {
            echo "Please book a slot, you haven't booked a slot";
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Vaccine</title>
    <link rel="stylesheet" href="select_vaccine.css">
    <script src="https://kit.fontawesome.com/5200cd7749.js" crossorigin="anonymous"></script>
</head>
<body>
    <div id="heading">
        <i class="fa-solid fa-house-chimney" id="top_home" onclick="window.location.href='user_home.php'"></i>
        <h2>Select Vaccine of your Choice</h2>
    </div>
    <br><br><br>
    <div class="image-container">
        <form id="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <button class="image-button" value="covaxin" name="vaccine" type="submit">
                <img id="covaxin" src="covaxin1.jpeg" alt="Image 1">
               <div> Covaxin</div>
            </button>
            <button class="image-button" value="covishield" name="vaccine" type="submit">
                <img id="covishield" src="covishield.jpeg" alt="Image 2">
                <div> CoviShield</div>
            </button>
            <button class="image-button" value="sputnik" name="vaccine" type="submit">
                <img id="sputnik" src="sputnik.jpeg" alt="Image 3">
               <div> Sputnik-V</div>
            </button>
        </form>
        <br>
    </div>
    <br><br>
    <button onclick="window.location.href='user_home.php'" type="submit" id="home" style="padding: 0.5rem; background-color: yellow; border-radius:0.5rem;">Back to home <i class="fa-solid fa-house-chimney"></i></button>

</body>
</html>
