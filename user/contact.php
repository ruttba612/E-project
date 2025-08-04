<?php
// Turn on error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if form submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    // Include DB connection file
    include 'db.php';

    // Sanitize and validate input
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    



    // Create SQL query
    $sql = "INSERT INTO feedback1 (username, email, message) VALUES ('$name', '$email', '$message')";

    // Execute query and redirect accordingly
    if (mysqli_query($conn, $sql)) {
    echo "<script>
        alert('Message sent successfully!');
        window.history.back(); // Optional: goes back to the previous page
    </script>";
} else {
    echo "<script>
        alert('Something went wrong. Please try again.');
        window.history.back(); // Optional: goes back to the previous page
    </script>";
}


    // Close connection
    mysqli_close($conn);

} else {
    // If not POST request
    header("Location: beauty.html");
}
?>


