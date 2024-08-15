<?php
// Assuming you have established a database connection

// Retrieve username and selected vaccine from the request
$username = $_POST['username'];
$vaccine = $_POST['vaccine'];

// Query to fetch Aadhaar number from the citizen table based on username
$getUserQuery = "SELECT adhaar_no FROM citizen WHERE username = '$username'";
$userResult = mysqli_query($connection, $getUserQuery);

if ($userResult) {
    $row = mysqli_fetch_assoc($userResult);
    $aadhaar = $row['adhaar_no'];

    // Update slot table with the selected vaccine for the retrieved Aadhaar number
    $updateSlotQuery = "UPDATE slot SET vaccine = '$vaccine' WHERE adhaar_no = '$aadhaar'";
    $updateResult = mysqli_query($connection, $updateSlotQuery);

    if ($updateResult) {
        echo "Vaccine selection updated successfully.";
    } else {
        echo "Error updating vaccine selection.";
    }
} else {
    echo "Error fetching Aadhaar number.";
}
?>
