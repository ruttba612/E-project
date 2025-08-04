<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db.php';
$customer_id = (int)($_GET['id'] ?? 0);
$customer = null;
if ($customer_id) {
    $stmt = $conn->prepare("SELECT id, name, email, total_spending FROM customers WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();
    $stmt->close();
    if (!$customer) {
        echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('Customer not found!', true); });</script>";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auranest Admin - View Customer</title>
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
        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
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
        .customer-details p {
            font-size: 1.1rem;
            margin-bottom: 15px;
        }
        .customer-details i {
            color: #D4AF37;
            margin-right: 8px;
        }
        .btn-back {
            background: #D4AF37;
            color: #FFF5F5;
            border: 1px solid #B89B2E;
            border-radius: 8px;
            padding: 8px 20px;
        }
        .alert {
            border-radius: 8px;
            background: #FFE4E1;
            border: 1px solid #FF6F61;
            color: #2C2C2C;
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
            .card-header h3 { font-size: 1.5rem; }
            .customer-details p { font-size: 1rem; }
            .btn-back { padding: 6px 15px; }
        }
    </style>
</head>
<body id="page-top">
    <div class="container mt-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h3 class="m-0 font-weight-bold">Customer Details - ID: <?php echo htmlspecialchars($customer_id); ?></h3>
            </div>
            <div class="card-body">
                <?php if ($customer): ?>
                    <div class="customer-details">
                        <p><i class="fas fa-id-card"></i><strong>ID:</strong> <?php echo htmlspecialchars($customer['id']); ?></p>
                        <p><i class="fas fa-user"></i><strong>Name:</strong> <?php echo htmlspecialchars($customer['name']); ?></p>
                        <p><i class="fas fa-envelope"></i><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
                        <p><i class="fas fa-dollar-sign"></i><strong>Total Spending:</strong> $<?php echo number_format($customer['total_spending'], 2); ?></p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Customer not found.
                    </div>
                <?php endif; ?>
                <a href="customers.php" class="btn btn-back mt-3">Back to Customers</a>
            </div>
        </div>
    </div>
    <div class="toast" id="toastNotification">
        <div class="toast-body">Action successful!</div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            function showToast(message, isError = false) {
                const toast = $('#toastNotification');
                toast.find('.toast-body').text(message);
                toast.addClass('show ' + (isError ? 'toast-error' : 'toast-success'));
                setTimeout(() => toast.removeClass('show'), 3000);
            }
        });
    </script>
</body>
</html>