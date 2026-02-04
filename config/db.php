<?php
$servername = "localhost";
$username = "root"; // your XAMPP MySQL username
$password = "";     // your XAMPP MySQL password
$dbname = "bachat_buddy"; // your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<?php
// config/db.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bachat_buddy";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

// Set charset
$conn->set_charset("utf8mb4");
?>