<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
// Check if db.php exists and include it
if (file_exists('db.php')) {
    include 'db.php';
} else {
    die("db.php file not found. Please place it in the admin folder.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    global $conn;
    if ($conn) {
        $stmt = $conn->prepare("SELECT id, name FROM admins WHERE email = ? AND password = ? AND status = 'active' LIMIT 1");
        $stmt->bind_param("ss", $username, $password); // Note: In production, hash password
        $stmt->execute();
        $result = $stmt->get_result();
        var_dump($result->num_rows); // Debug: Check how many rows are returned
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['name'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid credentials";
        }
        $stmt->close();
    } else {
        $error = "Database connection failed";
    }
}
if (isset($conn) && $conn) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auranest Admin - Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Poppins', sans-serif; }
        .login-container { max-width: 400px; margin: 100px auto; padding: 20px; background: #fff; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center mb-4">Auranest Admin Login</h2>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="form-group">
                <label>Username (Email)</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>