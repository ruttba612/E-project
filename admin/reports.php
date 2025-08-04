<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';
include 'functions.php';
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$greeting = date('H') < 12 ? "Good Morning, $admin_name" : (date('H') < 17 ? "Good Afternoon, $admin_name" : "Good Evening, $admin_name");

// Handle AJAX requests for chart data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fetch_data'])) {
    header('Content-Type: application/json');
    $start_date = $_POST['start_date'] ?? '2025-01-01';
    $end_date = $_POST['end_date'] ?? '2025-12-31';
    $category_id = (int)($_POST['category_id'] ?? 0);

    // Debug: Log POST data
    error_log("POST data for fetch_data: " . print_r($_POST, true));

    // Monthly Sales
    $sales_query = "SELECT MONTH(o.created_at) AS month, SUM(o.total_amount) AS total
                    FROM orders o
                    WHERE o.created_at BETWEEN ? AND ?
                    " . ($category_id ? "AND EXISTS (SELECT 1 FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = o.id AND p.category_id = ?)" : "") . "
                    GROUP BY MONTH(o.created_at)";
    $stmt = $conn->prepare($sales_query);
    if (!$stmt) {
        error_log("Prepare failed for sales_query: " . $conn->error);
        echo json_encode(['error' => 'Database error']);
        exit();
    }
    if ($category_id) {
        $stmt->bind_param("ssi", $start_date, $end_date, $category_id);
    } else {
        $stmt->bind_param("ss", $start_date, $end_date);
    }
    $stmt->execute();
    $sales_data = array_fill(1, 12, 0);
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $sales_data[$row['month']] = (float)$row['total'];
    }
    $stmt->close();

    // Orders by Status
    $status_query = "SELECT status, COUNT(*) AS count
                     FROM orders
                     WHERE created_at BETWEEN ? AND ?
                     GROUP BY status";
    $stmt = $conn->prepare($status_query);
    if (!$stmt) {
        error_log("Prepare failed for status_query: " . $conn->error);
        echo json_encode(['error' => 'Database error']);
        exit();
    }
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $status_data = [];
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $status_data[$row['status']] = (int)$row['count'];
    }
    $stmt->close();

    // Top Selling Products
    $products_query = "SELECT p.name, SUM(oi.quantity) AS total_quantity
                      FROM order_items oi
                      JOIN products p ON oi.product_id = p.id
                      JOIN orders o ON oi.order_id = o.id
                      WHERE o.created_at BETWEEN ? AND ?
                      " . ($category_id ? "AND p.category_id = ?" : "") . "
                      GROUP BY p.id, p.name
                      ORDER BY total_quantity DESC
                      LIMIT 5";
    $stmt = $conn->prepare($products_query);
    if (!$stmt) {
        error_log("Prepare failed for products_query: " . $conn->error);
        echo json_encode(['error' => 'Database error']);
        exit();
    }
    if ($category_id) {
        $stmt->bind_param("ssi", $start_date, $end_date, $category_id);
    } else {
        $stmt->bind_param("ss", $start_date, $end_date);
    }
    $stmt->execute();
    $products_data = ['labels' => [], 'quantities' => []];
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products_data['labels'][] = $row['name'];
        $products_data['quantities'][] = (int)$row['total_quantity'];
    }
    $stmt->close();

    // Categories for dropdown
    $categories = [];
    $result = $conn->query("SELECT id, name FROM categories ORDER BY name");
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    echo json_encode([
        'sales' => array_values($sales_data),
        'status' => $status_data,
        'products' => $products_data,
        'categories' => $categories
    ]);
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auranest Admin - Reports</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
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
        .btn-filter {
            background: #D4AF37;
            color: #FFF5F5;
            border: 1px solid #B89B2E;
        }
        .btn-reset {
            background: #FF6F61;
            color: #FFF5F5;
            border: 1px solid #E65A4E;
        }
        .filter-section {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .filter-section input, .filter-section select {
            max-width: 200px;
            border-radius: 8px;
            border: 1px solid #FFCCD5;
            height: 38px;
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
            .filter-section { flex-direction: column; gap: 10px; }
            .filter-section input, .filter-section select { max-width: 100%; }
        }
        canvas {
            max-height: 400px;
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
                    <h1 class="mt-4 mb-4">Reports & Analytics</h1>
                    <div class="filter-section">
                        <input type="text" id="dateRangePicker" class="form-control" placeholder="Select Date Range">
                        <select id="categoryFilter" class="form-control">
                            <option value="0">All Categories</option>
                            <?php
                            $result = $conn->query("SELECT id, name FROM categories ORDER BY name");
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                            }
                            $conn->close();
                            ?>
                        </select>
                        <button id="applyFilter" class="btn btn-custom btn-filter">Apply Filter</button>
                        <button id="resetFilter" class="btn btn-custom btn-reset">Reset</button>
                    </div>
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold">Monthly Sales</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-bar pt-4 pb-2">
                                        <canvas id="monthlySalesChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold">Orders by Status</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
                                        <canvas id="ordersByStatusChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold">Top Selling Products</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-bar pt-4 pb-2">
                                        <canvas id="topProductsChart"></canvas>
                                    </div>
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
    <div class="toast" id="toastNotification">
        <div class="toast-body">Action successful!</div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js" integrity="sha384-LtrjvnR4Twt/qOuYxE721u19sVFLVSA4hf/rRt6PrZTmiPltdZcI7q7PXQBYTKyf" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="reports.js"></script>
</body>
</html>