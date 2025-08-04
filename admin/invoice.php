<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';
include 'functions.php';

$order_id = isset($_GET['order_id']) && is_numeric($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($order_id <= 0) {
    echo "<script>alert('Invalid order ID!'); window.location.href='orders.php';</script>";
    exit();
}

// Fetch order details
$order_query = $conn->query("SELECT o.order_id, o.total_amount, o.status, o.order_date, c.name, c.email, c.address 
                             FROM orders o 
                             JOIN customers c ON o.customer_id = c.id 
                             WHERE o.order_id = $order_id");
if (!$order_query || $order_query->num_rows === 0) {
    echo "<script>alert('Order not found!'); window.location.href='orders.php';</script>";
    exit();
}
$order = $order_query->fetch_assoc();

// Fetch order items
$items_query = $conn->query("SELECT oi.quantity, oi.price, p.name 
                             FROM order_items oi 
                             JOIN products p ON oi.product_id = p.id 
                             WHERE oi.order_id = $order_id");
if (!$items_query || $items_query->num_rows === 0) {
    echo "<script>alert('Order items not found!'); window.location.href='orders.php';</script>";
    exit();
}
$items = [];
$subtotal = 0;
while ($item = $items_query->fetch_assoc()) {
    $items[] = $item;
    $subtotal += $item['quantity'] * $item['price'];
}
$tax_rate = 0.05; // 5% tax
$tax = $subtotal * $tax_rate;
$grand_total = $subtotal + $tax;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auranest Admin - Invoice #<?php echo htmlspecialchars($order_id); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', 'Lora', sans-serif;
            background-color: #FFF5F5;
            color: #2C2C2C;
            line-height: 1.6;
        }
        .container-fluid {
            padding: 20px;
        }
        h1 {
            font-size: 2rem;
            font-weight: 600;
            color: #2C2C2C;
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-header {
            background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
            padding: 20px;
            border-radius: 12px 12px 0 0;
            text-align: center;
        }
        .invoice-body {
            background: #FFF5F5;
            padding: 20px;
            border: 1px solid #FFCCD5;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        .table {
            width: 100%;
            margin-bottom: 0;
            font-size: 0.95rem;
        }
        .table th, .table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        .table th {
            background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
            font-weight: 600;
        }
        .btn-custom {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            margin: 10px;
        }
        .btn-download {
            background: #28A745;
            color: #FFF5F5;
            border: 1px solid #218838;
        }
        .btn-download:hover {
            background: #218838;
        }
        .btn-print {
            background: #17A2B8;
            color: #FFF5F5;
            border: 1px solid #138496;
        }
        .btn-print:hover {
            background: #138496;
        }
        @media print {
            .no-print {
                display: none;
            }
            .invoice-body {
                border: none;
                box-shadow: none;
            }
        }
        @media (max-width: 576px) {
            h1 { font-size: 1.5rem; }
            .table th, .table td { font-size: 0.85rem; padding: 8px; }
            .btn-custom { font-size: 0.9rem; padding: 8px 16px; }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <h1>Invoice #<?php echo htmlspecialchars($order_id); ?></h1>
        <div class="invoice-header">
            <h2>Auranest</h2>
            <p>Invoice # <?php echo htmlspecialchars($order['order_id']); ?></p>
            <p>Date: <?php echo date('F j, Y', strtotime($order['order_date'])); ?></p>
        </div>
        <div class="invoice-body">
            <h4>Customer Details</h4>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($order['name'] ?? 'N/A'); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ?? 'Not provided'); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address'] ?? 'Not provided'); ?></p>
            <h4 class="mt-4">Order Items</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="text-right mt-3">
                <p><strong>Subtotal:</strong> $<?php echo number_format($subtotal, 2); ?></p>
                <p><strong>Tax (5%):</strong> $<?php echo number_format($tax, 2); ?></p>
                <p><strong>Grand Total:</strong> $<?php echo number_format($grand_total, 2); ?></p>
            </div>
            <div class="text-center mt-4 no-print">
                <button class="btn btn-custom btn-download" onclick="downloadPDF(<?php echo $order_id; ?>)">
                    <i class="fas fa-download"></i> Download PDF
                </button>
                <button class="btn btn-custom btn-print" onclick="window.print()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
            <p class="text-center mt-4">Thank you for shopping with Auranest!</p>
            <p class="text-center">Contact: contact@auranest.com | www.auranest.com</p>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function downloadPDF(orderId) {
            if (!orderId || orderId <= 0) {
                alert('Invalid order ID!');
                return;
            }
            window.location.href = 'generate_pdf.php?order_id=' + orderId;
        }
    </script>
</body>
</html>