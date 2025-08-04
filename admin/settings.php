<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$greeting = date('H') < 12 ? "Good Morning, $admin_name" : (date('H') < 17 ? "Good Afternoon, $admin_name" : "Good Evening, $admin_name");

// Handle add admin form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $status = $_POST['status'];

    if (empty($name) || empty($email) || empty($_POST['password'])) {
        $error = "All fields are required.";
    } else {
        // Check if email already exists
        $check_query = "SELECT id FROM admins WHERE email = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            $error = "Email already exists.";
        } else {
            // Insert new admin
            $insert_query = "INSERT INTO admins (name, email, password, status, created_at) VALUES (?, ?, ?, ?, NOW())";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("ssss", $name, $email, $password, $status);
            if ($insert_stmt->execute()) {
                $success = "Admin added successfully.";
            } else {
                $error = "Failed to add admin.";
            }
            $insert_stmt->close();
        }
        $check_stmt->close();
    }
}

// Fetch all admins for display
$admins_query = "SELECT id, name, email, status, created_at FROM admins ORDER BY created_at DESC";
$admins_result = $conn->query($admins_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auranest Admin - Settings</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Inter', 'Lora', sans-serif;
            background-color: #FFF5F5;
            color: #2C2C2C;
            padding-top: 80px;
        }
        .container-fluid {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            font-size: 2rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
        }
        .card {
            border: none;
            border-radius: 12px;
            background: #FFF5F5;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        .card-header {
            background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
            color: #2C2C2C;
            padding: 15px;
            border-radius: 12px 12px 0 0;
            font-weight: 600;
            text-align: center;
        }
        .btn-custom {
            padding: 6px 12px;
            border-radius: 8px;
            margin: 0 4px;
        }
        .btn-submit {
            background: #D4AF37;
            color: #FFF5F5;
            border: 1px solid #B89B2E;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #FFCCD5;
        }
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 250px;
            background: #FFF5F5;
            border: 1px solid #FFCCD5;
            border-radius: 8px;
            padding: 15px;
            z-index: 2000;
            display: none;
        }
        .toast.show {
            display: block;
            animation: fadeIn 0.5s, fadeOut 0.5s 2.5s;
        }
        .toast-success {
            border-left: 4px solid #D4AF37;
        }
        .toast-error {
            border-left: 4px solid #FF6F61;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-10px); }
        }
        @media (max-width: 576px) {
            h1 { font-size: 1.5rem; }
        }
        table {
            background: #FFF5F5;
        }
        th, td {
            border: 1px solid #FFCCD5;
        }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'includes/header.php'; ?>
                <div class="container-fluid">
                    <h1 class="mt-4 mb-4">Settings</h1>
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold">Add New Admin</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <button type="submit" name="add_admin" class="btn btn-custom btn-submit">Add Admin</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold">Admin List</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Status</th>
                                                <th>Created At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($admins_result && $admins_result->num_rows > 0) {
                                                while ($admin = $admins_result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td>" . htmlspecialchars($admin['name']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($admin['status']) . "</td>";
                                                    echo "<td>" . date('Y-m-d H:i', strtotime($admin['created_at'])) . "</td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='4'>No admins found.</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
    <div class="toast <?php echo isset($success) ? 'toast-success' : (isset($error) ? 'toast-error' : ''); ?>" id="toastNotification">
        <div class="toast-body">
            <?php
            if (isset($success)) echo htmlspecialchars($success);
            elseif (isset($error)) echo htmlspecialchars($error);
            else echo "Action completed!";
            ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js" integrity="sha384-LtrjvnR4Twt/qOuYxE721u19sVFLVSA4hf/rRt6PrZTmiPltdZcI7q7PXQBYTKyf" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            <?php if (isset($success) || isset($error)): ?>
                $('#toastNotification').addClass('show');
                setTimeout(() => $('#toastNotification').removeClass('show'), 3000);
            <?php endif; ?>
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>