
<?php
// includes/db.php
$host = 'localhost';
$db = 'auranest_db';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error); // Log error to file
    die("Error: Unable to connect to the database. Please check your configuration.");
}
?>
