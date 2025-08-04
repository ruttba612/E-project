<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$greeting = date('H') < 12 ? "Good Morning, $admin_name" : (date('H') < 17 ? "Good Afternoon, $admin_name" : "Good Evening, $admin_name");

// Fetch current admin details
$admin_query = "SELECT name, email, profile_picture FROM admins WHERE id = ?";
$stmt = $conn->prepare($admin_query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin_result = $stmt->get_result();
$admin = $admin_result->fetch_assoc();
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash(trim($_POST['password']), PASSWORD_DEFAULT) : null;
    $profile_picture = $admin['profile_picture'];

    // Validate inputs
    if (empty($name) || empty($email)) {
        $error = "Name and email are required.";
    } else {
        // Check if email is already used by another admin
        $check_query = "SELECT id FROM admins WHERE email = ? AND id != ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("si", $email, $admin_id);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            $error = "Email is already in use.";
        } else {
            // Handle profile picture upload
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $file_ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
                $allowed_ext = ['jpg', 'jpeg', 'png'];
                if (!in_array(strtolower($file_ext), $allowed_ext)) {
                    $error = "Only JPG and PNG files are allowed.";
                } else {
                    $new_filename = 'profile_' . $admin_id . '_' . time() . '.' . $file_ext;
                    $upload_path = $upload_dir . $new_filename;
                    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                        $profile_picture = $upload_path;
                        // Delete old profile picture if it exists and is not the default
                        if ($admin['profile_picture'] && file_exists($admin['profile_picture']) && $admin['profile_picture'] !== 'default.jpg') {
                            unlink($admin['profile_picture']);
                        }
                    } else {
                        $error = "Failed to upload profile picture.";
                    }
                }
            }

            if (!isset($error)) {
                // Update admin details
                $update_query = "UPDATE admins SET name = ?, email = ?" . ($password ? ", password = ?" : "") . ", profile_picture = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_query);
                if ($password) {
                    $update_stmt->bind_param("ssssi", $name, $email, $password, $profile_picture, $admin_id);
                } else {
                    $update_stmt->bind_param("sssi", $name, $email, $profile_picture, $admin_id);
                }
                if ($update_stmt->execute()) {
                    $success = "Profile updated successfully.";
                    $_SESSION['admin_name'] = $name;
                    $_SESSION['admin_email'] = $email;
                    $_SESSION['profile_picture'] = $profile_picture;
                    $admin['name'] = $name;
                    $admin['email'] = $email;
                    $admin['profile_picture'] = $profile_picture;
                } else {
                    $error = "Failed to update profile.";
                }
                $update_stmt->close();
            }
        }
        $check_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auranest Admin - Profile</title>
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
        .profile-picture {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #FFCCD5;
            margin-bottom: 20px;
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
            .profile-picture { width: 100px; height: 100px; }
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
                    <h1 class="mt-4 mb-4">My Profile</h1>
                    <div class="row justify-content-center">
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold">Profile Details</h6>
                                </div>
                                <div class="card-body text-center">
                                    <img src="<?php echo htmlspecialchars($admin['profile_picture'] ?? 'default.jpg'); ?>" class="profile-picture" alt="Profile Picture">
                                    <form method="POST" action="" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="profile_picture">Profile Picture</label>
                                            <input type="file" class="form-control-file" id="profile_picture" name="profile_picture" accept=".jpg,.jpeg,.png">
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="password">New Password (leave blank to keep current)</label>
                                            <input type="password" class="form-control" id="password" name="password">
                                        </div>
                                        <button type="submit" name="update_profile" class="btn btn-custom btn-submit">Update Profile</button>
                                    </form>
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