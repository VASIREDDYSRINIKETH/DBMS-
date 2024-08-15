<?php
$conn = new mysqli("localhost", "root", "", "supervisor");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $upassword = $_POST['upassword'];

    $sql = "SELECT * FROM sup WHERE username = '$username' AND upassword = '$upassword'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        header("Location: loading2.php");
        exit();
    } else {
        echo '<script type="text/javascript">';
            echo 'alert("Invalid username or password")';
            echo '</script>';
            header("Location: Supervisor2.html");
    }
}

$conn->close();
?>