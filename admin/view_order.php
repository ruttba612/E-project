<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';
$order_id = $_GET['order_id'] ?? '';
// Debugging: Log received order_id
error_log("Received order_id: $order_id");
$order = null;
$items = [];
$total_items = 0;

if (!empty($order_id)) {
    // Fetch order details
    $stmt = $conn->prepare("SELECT o.order_id, c.name, o.total_amount, o.order_date, o.status FROM orders o JOIN customers c ON o.customer_id = c.id WHERE o.order_id = ? LIMIT 1");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();

    // Fetch order items
    if ($order) {
        $stmt = $conn->prepare("SELECT p.name AS product, oi.quantity, oi.price FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
        $stmt->bind_param("s", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $items[] = [
                'product' => htmlspecialchars($row['product']),
                'quantity' => (int)$row['quantity'],
                'price' => (float)$row['price']
            ];
            $total_items += $row['quantity'] * $row['price'];
        }
        $stmt->close();
    } else {
        error_log("No order found for order_id: $order_id");
    }
} else {
    error_log("order_id is empty or not provided");
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auranest Admin - View Order</title>
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
        .order-details {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .order-details p {
            margin: 0 0 10px;
            font-size: 1rem;
        }
        .order-details i {
            margin-right: 8px;
            color: #D4AF37;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 0.9rem;
        }
        .badge-pending {
            background-color: #FFC107;
            color: #2C2C2C;
        }
        .badge-shipped {
            background-color: #28A745;
            color: #FFF5F5;
        }
        .badge-delivered {
            background-color: #007BFF;
            color: #FFF5F5;
        }
        .table {
            background: #FFF5F5;
            border-radius: 8px;
            overflow: hidden;
        }
        .table th {
            background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
            font-weight: 600;
            text-align: center;
            padding: 12px;
        }
        .table td {
            background: #FFF5F5;
            text-align: center;
            padding: 12px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        .table tr:nth-child(even) td {
            background: #FFE4E1;
        }
        .table tr:hover td {
            background: #FFCCD5;
            transition: background 0.3s ease;
        }
        .btn-custom {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
        }
        .btn-back {
            background: #D4AF37;
            color: #FFF5F5;
            border: 1px solid #B89B2E;
        }
        .btn-back:hover {
            background: #B89B2E;
            border-color: #A68B27;
        }
        .alert {
            border-radius: 8px;
            background: #FFE4E1;
            color: #2C2C2C;
            border: 1px solid #FF6F61;
            padding: 15px;
        }
        .alert i {
            margin-right: 8px;
            color: #FF6F61;
        }
        @media (max-width: 576px) {
            .order-details {
                flex-direction: column;
            }
            .order-details p {
                font-size: 0.9rem;
            }
            .table th, .table td {
                font-size: 0.85rem;
                padding: 8px;
            }
            .btn-back {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body id="page-top">
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h3 class="m-0 font-weight-bold">Order Details - <?php echo htmlspecialchars($order_id); ?></h3>
            </div>
            <div class="card-body">
                <?php if ($order): ?>
                    <div class="order-details">
                        <div class="col-md-6">
                            <p><i class="fas fa-id-card"></i><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
                            <p><i class="fas fa-user"></i><strong>Customer:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
                            <p><i class="fas fa-dollar-sign"></i><strong>Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><i class="fas fa-calendar-alt"></i><strong>Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                            <p><i class="fas fa-info-circle"></i><strong>Status:</strong> 
                                <span class="badge badge-<?php echo $order['status'] == 'Shipped' ? 'shipped' : ($order['status'] == 'Pending' ? 'pending' : 'delivered'); ?>">
                                    <?php echo htmlspecialchars($order['status']); ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <hr>
                    <h5 class="mb-3">Order Items</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($items)): ?>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><?php echo $item['product']; ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                                            <td>$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="3"><strong>Total</strong></td>
                                        <td><strong>$<?php echo number_format($total_items, 2); ?></strong></td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No items found for this order.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> Order not found. Please ensure the order ID '<?php echo htmlspecialchars($order_id); ?>' exists in the database and matches with a customer.
                    </div>
                <?php endif; ?>
                <a href="orders.php" class="btn btn-custom btn-back mt-3"><i class="fas fa-arrow-left mr-2"></i>Back to Orders</a>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>