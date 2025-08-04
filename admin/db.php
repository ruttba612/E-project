<?php
$host = 'localhost';
$db = 'auranest_db';
$user = 'root'; // Apna MySQL username, default XAMPP mein 'root'
$pass = '';     // Apna MySQL password, default XAMPP mein khali
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    // die("Connection failed: " . $conn->connect_error); // Comment out for now to debug
    $conn = null; // Set to null if connection fails
}
?>