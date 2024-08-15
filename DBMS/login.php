<?php
session_start();

// Destroy existing session
session_destroy();

// Start a new session
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "vaccination_program_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // SQL query to check if the username exists and password matches
    $sql = "SELECT * FROM citizens WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Username and password are correct
        $_SESSION['username'] = $username;
        header("Location: loading.php");
        exit();
    } else {
        header("Location: login2.html");
    }
}

$conn->close();
?>
