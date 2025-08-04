<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$greeting = date('H') < 12 ? "Good Morning, $admin_name" : (date('H') < 17 ? "Good Afternoon, $admin_name" : "Good Evening, $admin_name");

// Fetch all feedback from feedback1 table
$feedback_query = "SELECT * FROM feedback1 ORDER BY id DESC";
$feedback_result = $conn->query($feedback_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Management - Auranest Admin</title>
    <meta name="description" content="Manage user feedback for Auranest.">
    <meta name="keywords" content="feedback, auranest, admin">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
        .card-body {
            padding: 20px;
        }
        table {
            background: #FFF5F5;
        }
        th, td {
            border: 1px solid #FFCCD5;
            vertical-align: middle;
        }
        .not-found {
            color: #FF6F61;
            text-align: center;
            font-weight: 500;
        }
        @media (max-width: 576px) {
            h1 { font-size: 1.5rem; }
            .table { font-size: 0.9rem; }
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
                    <h1 class="mt-4 mb-4">Feedback Management</h1>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold">User Feedback</h6>
                        </div>
                        <div class="card-body">
                            <?php if ($feedback_result && $feedback_result->num_rows > 0): ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Message</th>
                                            <th>Messaged at</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($feedback = $feedback_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($feedback['id']) ?></td>
                                                <td><?= htmlspecialchars($feedback['username']) ?></td>
                                                <td><?= htmlspecialchars($feedback['email']) ?></td>
                                                <td><?= htmlspecialchars($feedback['message']) ?></td>
                                                <td><?= htmlspecialchars($feedback['created_at']) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="not-found">No feedback found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
