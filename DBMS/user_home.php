
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

// Define $check1 variable
$check1 = 0;

// Connect to your database (Replace dbname, username, password with your actual credentials)
$conn = new mysqli("localhost", "root", "", "vaccination_program_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user details from citizens table
$username = $_SESSION['username'];
$sql_user_details = "SELECT adhaar_no, First_name, Last_name, gender, age, city, pin_code FROM citizens WHERE username = '$username'";
$result_user_details = $conn->query($sql_user_details);

if ($result_user_details->num_rows > 0) {
    $row_user_details = $result_user_details->fetch_assoc();
    $adhaar_no = $row_user_details["adhaar_no"];
    $first_name = $row_user_details["First_name"];
    $last_name = $row_user_details["Last_name"];
    $gender = $row_user_details["gender"];
    $age = $row_user_details["age"];
    $city = $row_user_details["city"];
    $pin_code = $row_user_details["pin_code"];
} else {
    // Display error message and contact supervisor
    echo "User details not found. Please contact the supervisor.";
}

if (!empty($adhaar_no)) {
  $sql_slot_details = "SELECT slot1_date, slot1_time,slot2_date, slot2_time,vaccine FROM slot WHERE adhaar_no = '$adhaar_no'";
  $result_slot_details = $conn->query($sql_slot_details);

  if ($result_slot_details->num_rows > 0) {
      $check1 = 1; // Set $check1 to 1 if a slot is booked
      $row_slot_details = $result_slot_details->fetch_assoc();
      $slot1_date = $row_slot_details["slot1_date"];
      $slot1_time = $row_slot_details["slot1_time"];
      $slot2_date = $row_slot_details["slot2_date"];
      $slot2_time = $row_slot_details["slot2_time"];
      $v=$row_slot_details["vaccine"];
      $slot_message1 = "Session1: $slot1_date, $slot1_time ";
      $slot_message2= "Session2 : $slot2_date, $slot2_time";
      $vaccine="Vaccine type: $v ";
  } else {
      $slot_message1 = "No slot booked";
      $slot_message2="";
  }
}
// Check if the user has a slot booked
if (!empty($adhaar_no)) {
    $sql_check1 = "SELECT check1 FROM citizens WHERE adhaar_no = '$adhaar_no'";
    $result_check1 = $conn->query($sql_check1);

    if ($result_check1->num_rows > 0) {
        $row_check1 = $result_check1->fetch_assoc();
        $check1 = $row_check1["check1"];
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>COVID Vaccination Drive</title>
  <script src="https://kit.fontawesome.com/5200cd7749.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="user_home.css">
  <style>
    /* CSS for the profile widget */
    .profile-widget {
      display: none;
      position: fixed;
      top: 50%;
      left: 0; /* Set to left of the screen */
      transform: translate(0, -50%);
      background-color: #f9f9f9;
      padding: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      z-index: 1000;
    }

    /* CSS for the close button */
    .close-button {
      position: absolute;
      top: 5px;
      right: 10px;
      background-color: transparent;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: #aaa;
    }

    .close-button:hover {
      color: #000;
    }
  </style>
</head>
<body>
  <header>
    <img src="ashoka.jpeg" alt="Ashoka Emblem" width="100" height="65" id="lions">
    <h1>COVID Vaccination Drive</h1>
    
  </header>
  <h2>
    <button id="profile" class="home-btn" type="submit"><i class="fa-regular fa-user"></i></button>
    <button class="home-btn" onclick="window.location.href='login_slotbooking.html'" type="submit">Book a Slot</button>
    <button class="home-btn" onclick="window.location.href='login_modify_time.html'" type="submit">Modify Slot</button>
    <button class="home-btn" onclick="window.location.href='select_vaccine.php'" type="submit">Select Vaccine</button>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    User Home Page
    <i class="fa-solid fa-house-chimney" id="top_home" onclick="window.location.href='webpage.html'" style="float: right; padding-right : 1rem; " ></i>
  </h2>
  <div style="font-size: 20px; ">
    <?php
    if ($check1 == 1) {
        echo "<p style='color: red;'>You haven't Booked your slot or haven't came at alloted date please book your slot again !! </p>";
    }
    ?>
  </div>
  <!-- Profile Widget -->
  <div id="profile-widget" class="profile-widget">
    <!-- Close button -->
    <button id="close-profile-widget" class="close-button">&times;</button>
    <!-- Display user details here -->
    <p>User Details:</p>
    <p>First Name: <?php echo $first_name; ?></p>
    <p>Last Name: <?php echo $last_name; ?></p>
    <p>Gender: <?php echo $gender; ?></p>
    <p>Age: <?php echo $age; ?></p>
    <p>City: <?php echo $city; ?></p>
    <p>Pin Code: <?php echo $pin_code; ?></p>
    <!-- Slot details -->
    <?php if (!empty($slot_message1)) {
    echo "<p>$slot_message1</p>";
} ?>
<?php if (!empty($slot_message2)) {
    echo "<p>$slot_message2</p>";
} ?>
<?php if (!empty($v)) {
    echo "<p>$vaccine</p>";
} ?>

  </div>

  <main>
    <p>Simple and Easy Process to Book Your Slots</p>
    <ol class="steps">
      <li class="step">
        <span class="step-number">1</span>
        <span class="step-description">Select your Vaccine</span>
      </li>
      <li class="step">
        <span class="step-number">2</span>
        <span class="step-description">Book a Slot and Select Hospital of your Choice</span>
      </li>
      <li class="step">
        <span class="step-number">3</span>
        <span class="step-description">Modify the Slot to your Preferred Date and Time if Necessary</span>
      </li>
      <li class="step">
        <span class="step-number">4</span>
        <span class="step-description">Get Vaccinated and Defeat COVID!!</span>
      </li>
    </ol>
  </main>
  <footer>
    <p id="contact">Contact details:</p>
    <p id="phone">For any queries, contact <a href="tel:+919090909090" id="ph_no">+91 9090909090</a></p>
    <p id="mail">Email: <a href="mailto:vaccinationorg@gmail.com" id="email_id">vaccinationorg@gmail.com</a></p>
    <p>&copy; COVID Vaccination Drive. All rights reserved.</p>
  </footer>

  <script>
    // JavaScript to toggle the visibility of the profile widget
    document.getElementById('profile').addEventListener('click', function() {
      var widget = document.getElementById('profile-widget');
      if (widget.style.display === 'block') {
        widget.style.display = 'none';
      } else {
        widget.style.display = 'block';
      }
    });

    // JavaScript to close the profile widget when close button is clicked
    document.getElementById('close-profile-widget').addEventListener('click', function() {
      var widget = document.getElementById('profile-widget');
      widget.style.display = 'none';
    });
  </script>
</body>
</html>
