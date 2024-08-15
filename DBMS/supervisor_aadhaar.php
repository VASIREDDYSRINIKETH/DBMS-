<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor-Aadhaar</title>
    <link rel="stylesheet" href="supervisor_aadhaar.css">
    <link rel="stylesheet" href="table_style.css"> <!-- Add this line to link the new CSS file -->
    <script src="https://kit.fontawesome.com/5200cd7749.js" crossorigin="anonymous"></script>
    <script>
        function searchAadhaar() {
            var adhaar_no = document.getElementById('aadhaar').value;

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        document.getElementById('results').innerHTML = xhr.responseText;
                        document.getElementById('heading').style.display = 'none';
                        document.getElementById('aadhaar_cont').style.display = 'none';
                        // Remove the "Back to home" button
                        var homeButton = document.getElementById('home');
                        if (homeButton) {
                            homeButton.parentNode.removeChild(homeButton);
                        }
                    } else {
                        alert('There was a problem with the request.');
                    }
                }
            };
            xhr.open('POST', '', true); // Empty string indicates the same file
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.send('adhaar_no=' + adhaar_no);
        }
    </script>
</head>
<body>

    <div id="heading">
        <i class="fa-solid fa-house-chimney" id="top_home" onclick="window.location.href='supervisor.html'"></i>
        <h1 >Search by Aadhaar</h1>
    </div>
    <div id="aadhaar_cont">
        <form method="POST" action="">
            <label for="enter_aadhaar" id="enter_aadhaar">Aadhaar no: </label>
            <input type="text" name="adhaar_no" id="aadhaar" placeholder="Enter Aadhaar here">
            <button type="button" id="search" onclick="searchAadhaar()">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
    </div>
    <br>
    <div id="results">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['adhaar_no'])) {
            $conn = new mysqli("localhost", "root", "", "vaccination_program_db");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $adhaar_no = $_POST['adhaar_no'];
            $sql = "SELECT c.first_name, c.last_name, c.gender, c.adhaar_no, s.slot1_date, s.slot1_time, s.slot2_date, s.slot2_time,s.vaccine
                FROM citizens AS c
                JOIN slot AS s ON c.adhaar_no = s.adhaar_no
                WHERE c.adhaar_no = '$adhaar_no';";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<tr><th>First Name</th><th>Last Name</th><th>Aadhaar No</th><th>Slot 1 Date</th><th>Slot 1 Time</th><th>Slot 2 Date</th><th>Slot 2 Time</th><th>Vaccine-type</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['first_name'] . "</td>";
                    echo "<td>" . $row['last_name'] . "</td>";
                    echo "<td>" . $row['adhaar_no'] . "</td>";
                    echo "<td>" . $row['slot1_date'] . "</td>";
                    echo "<td>" . $row['slot1_time'] . "</td>";
                    echo "<td>" . $row['slot2_date'] . "</td>";
                    echo "<td>" . $row['slot2_time'] . "</td>";
                    echo "<td>" . $row['vaccine'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No user found.";
            }
            $conn->close();
        }
        ?>
    </div>
    <button onclick="window.location.href='supervisor1.html'" type="submit" id="home">Back to home <i class="fa-solid fa-house-chimney"></i></button>
    <script>
        // Hide the heading and search bar after the search is performed
        document.getElementById('heading').style.display = 'block';
        document.getElementById('aadhaar_cont').style.display = 'block';
    </script>
    <br>
</body>
</html>
