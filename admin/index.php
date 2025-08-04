<?php
require('fpdf/fpdf.php');
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Admin info
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$greeting = date('H') < 12 ? "Good Morning, $admin_name" : (date('H') < 17 ? "Good Afternoon, $admin_name" : "Good Evening, $admin_name");
$profile_pic = $_SESSION['profile_pic'] ?? 'https://via.placeholder.com/60/FFCCD5/FFF5F5?text=A';

// Database Connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=auranest_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    $db_error = "Failed to connect to database. Please check your database setup.";
}

// Fetch Stats
$total_sales = 0;
$total_orders = 0;
$new_customers = 0;
$low_stock_items = 0;

try {
    $total_sales = $pdo->query("SELECT SUM(amount) as total FROM sales")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $total_orders = $pdo->query("SELECT COUNT(*) as count FROM orders")->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    $new_customers = $pdo->query("SELECT COUNT(*) as count FROM clients WHERE spending > 0")->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    $low_stock_items = $pdo->query("SELECT COUNT(*) as count FROM products WHERE stock < 10")->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
} catch (PDOException $e) {
    error_log("Stats query failed: " . $e->getMessage());
    $total_sales = 25450; // Fallback data
    $total_orders = 150;
    $new_customers = 25;
    $low_stock_items = 7;
}

// Fetch Chart Data
$bestselling_products = [];
try {
    $bestselling_products = $pdo->query("SELECT name, price * stock as sales FROM products ORDER BY sales DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Bestselling products query failed: " . $e->getMessage());
    $bestselling_products = [
        ['name' => 'Blue Evening Dress', 'sales' => 5000],
        ['name' => 'Diamond Stud Earrings', 'sales' => 4500],
        ['name' => 'Hydrating Face Serum', 'sales' => 3800],
        ['name' => 'Men\'s Leather Wallet', 'sales' => 3000],
        ['name' => 'Velvet Lipstick (Red)', 'sales' => 2500],
    ];
}
$product_labels = array_column($bestselling_products, 'name');
$product_data = array_column($bestselling_products, 'sales');

$sales_by_category = [];
try {
    $sales_by_category = $pdo->query("SELECT category_name, sales FROM categories ORDER BY sales DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Sales by category query failed: " . $e->getMessage());
    $sales_by_category = [
        ['category_name' => 'Fashion', 'sales' => 12000],
        ['category_name' => 'Beauty', 'sales' => 8000],
        ['category_name' => 'Jewelry', 'sales' => 5450],
        ['category_name' => 'Accessories', 'sales' => 3000],
    ];
}
$category_labels = array_column($sales_by_category, 'category_name');
$category_data = array_column($sales_by_category, 'sales');

$sales_overview = [];
try {
    $sales_overview = $pdo->query("SELECT DATE_FORMAT(sale_date, '%b %d') as date, amount as sales FROM sales ORDER BY sale_date DESC LIMIT 7")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Sales overview query failed: " . $e->getMessage());
    $sales_overview = [
        ['date' => 'Jul 20', 'sales' => 5000],
        ['date' => 'Jul 21', 'sales' => 7000],
        ['date' => 'Jul 22', 'sales' => 9000],
        ['date' => 'Jul 23', 'sales' => 11000],
        ['date' => 'Jul 24', 'sales' => 13000],
        ['date' => 'Jul 25', 'sales' => 15000],
        ['date' => 'Jul 26', 'sales' => 17000],
    ];
}
$sales_dates = array_column($sales_overview, 'date');
$sales_values = array_column($sales_overview, 'sales');

$top_clients = [];
try {
    $top_clients = $pdo->query("SELECT name, spending FROM clients ORDER BY spending DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Top clients query failed: " . $e->getMessage());
    $top_clients = [
        ['name' => 'Sara Ali', 'spending' => 5000],
        ['name' => 'Ali Khan', 'spending' => 4500],
        ['name' => 'Fatima Ahmed', 'spending' => 4000],
        ['name' => 'Zara Abbas', 'spending' => 3500],
        ['name' => 'Hamza Malik', 'spending' => 3000],
        ['name' => 'Ayesha Siddiqui', 'spending' => 2500],
        ['name' => 'Omar Farooq', 'spending' => 2000],
        ['name' => 'Hina Butt', 'spending' => 1500],
        ['name' => 'Usman Raja', 'spending' => 1000],
        ['name' => 'Nida Shah', 'spending' => 500],
    ];
}
$client_names = array_column($top_clients, 'name');
$client_spending = array_column($top_clients, 'spending');

$user_growth = [];
try {
    $user_growth = $pdo->query("SELECT month, user_count FROM users ORDER BY FIELD(month, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun')")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("User growth query failed: " . $e->getMessage());
    $user_growth = [
        ['month' => 'Jan', 'user_count' => 1200],
        ['month' => 'Feb', 'user_count' => 1500],
        ['month' => 'Mar', 'user_count' => 1800],
        ['month' => 'Apr', 'user_count' => 1700],
        ['month' => 'May', 'user_count' => 2000],
        ['month' => 'Jun', 'user_count' => 2300],
    ];
}
$user_months = array_column($user_growth, 'month');
$user_counts = array_column($user_growth, 'user_count');

// Fetch Recent Orders
$recent_orders = [];
try {
    $recent_orders = $pdo->query("SELECT order_id, customer_name, amount, status, order_date FROM orders ORDER BY order_date DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Recent orders query failed: " . $e->getMessage());
    $recent_orders = [
        ['order_id' => '#AUN001', 'customer_name' => 'Ali Khan', 'amount' => 125.00, 'status' => 'Pending', 'order_date' => '2025-07-26'],
        ['order_id' => '#AUN002', 'customer_name' => 'Fatima Ahmed', 'amount' => 250.50, 'status' => 'Shipped', 'order_date' => '2025-07-25'],
        ['order_id' => '#AUN003', 'customer_name' => 'Zara Abbas', 'amount' => 75.00, 'status' => 'Processing', 'order_date' => '2025-07-24'],
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auranest Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
       * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', 'Lora', sans-serif;
    background-color: #FFF5F5;
    color: #2C2C2C;
    line-height: 1.6;
    overflow-x: hidden;
}

#wrapper {
    display: flex;
    min-height: 100vh;
}

#content-wrapper {
    flex-grow: 1;
    margin-left: 260px;
    padding-top: 70px;
    padding-bottom: 80px; /* Increased to account for fixed footer */
    transition: margin-left 0.3s ease;
}

.sidebar {
    background: linear-gradient(180deg, #FFE4E1 0%, #FFCCD5 100%);
    width: 260px;
    min-width: 260px;
    max-width: 100vw;
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    max-height: 100vh; /* Ensure sidebar fits within viewport */
    z-index: 1001;
    box-shadow: 3px 0 15px rgba(0, 0, 0, 0.08);
    transition: width 0.3s ease;
    overflow-x: visible;
    overflow-y: auto; /* Allow scrolling if content exceeds height */
    display: flex;
    flex-direction: column;
    flex: 1; /* Distribute space efficiently */
}

.sidebar.toggled {
    width: 80px;
    min-width: 80px;
}

body.sidebar-toggled #content-wrapper {
    margin-left: 80px;
}

.sidebar-brand {
    padding: 12px; /* Further reduced padding */
    background: #FFF5F5;
    color: #2C2C2C;
    font-weight: 600;
    font-size: 1.4rem; /* Further reduced font size */
    text-transform: uppercase;
    letter-spacing: 0.8px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.sidebar-brand-icon i {
    font-size: 1.5rem; /* Further reduced icon size */
    margin-right: 8px;
    color: #de5c74ff;
    transition: transform 0.3s ease;
}

.sidebar.toggled .sidebar-brand-text {
    display: none;
}

.sidebar.toggled .sidebar-brand-icon i {
    margin-right: 0;
    transform: scale(1.2);
}

.sidebar-divider {
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    margin: 6px 10px; /* Further reduced margin */
    opacity: 0.5;
}

.sidebar-heading {
    font-size: 0.8rem; /* Further reduced font size */
    color: #2C2C2C;
    padding: 6px 12px; /* Further reduced padding */
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.7px;
    background: rgba(0, 0, 0, 0.02);
    margin: 4px 0; /* Further reduced margin */
}

.sidebar.toggled .sidebar-heading {
    display: none;
}

.nav-item .nav-link {
    color: #2C2C2C;
    font-weight: 500;
    padding: 8px 12px; /* Further reduced padding */
    border-radius: 6px; /* Slightly smaller border-radius */
    margin: 3px 6px; /* Further reduced margin */
    display: flex;
    align-items: center;
    position: relative;
    transition: all 0.3s ease;
}

.nav-item .nav-link i {
    font-size: 1.1rem; /* Further reduced icon size */
    margin-right: 8px;
    color: #FFCCD5;
    transition: color 0.3s ease, transform 0.3s ease;
}

.nav-item .nav-link:hover {
    background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
    color: #2C2C2C;
    transform: translateX(5px) scale(1.02);
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

.nav-item .nav-link:hover i {
    color: #D4AF37;
    transform: scale(1.2);
}

.nav-item.active .nav-link {
    background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
    color: #2C2C2C;
    font-weight: 600;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

.nav-item.active .nav-link i {
    color: #D4AF37;
    transform: scale(1.2);
}

.sidebar.toggled .nav-link span {
    display: none;
}

.sidebar.toggled .nav-link i {
    margin-right: 0;
    font-size: 1.3rem;
}

.sidebar.toggled .nav-link:hover:after {
    content: attr(data-tooltip);
    position: absolute;
    left: 90px;
    top: 50%;
    transform: translateY(-50%);
    background: #2C2C2C;
    color: #FFF5F5;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.8rem; /* Slightly smaller tooltip font */
    white-space: nowrap;
    z-index: 1002;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.nav-item .badge {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background-color: #D4AF37;
    color: #FFF5F5;
    font-size: 0.65rem; /* Further reduced badge size */
    padding: 3px 6px;
    border-radius: 10px;
    transition: opacity 0.3s ease;
}

.sidebar.toggled .nav-item .badge {
    opacity: 0;
}

.sidebar.toggled .nav-item:hover .badge {
    opacity: 1;
}

.sidebar-toggle {
    background-color: #FFCCD5;
    color: #2C2C2C;
    border: none;
    width: 34px; /* Further reduced toggle button size */
    height: 34px;
    border-radius: 50%;
    margin: 10px auto; /* Further reduced margin */
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.sidebar-toggle:hover {
    background-color: #D4AF37;
    color: #FFF5F5;
    transform: scale(1.15);
}

.topbar {
    background: linear-gradient(90deg, #FFE4E1, #FFCCD5);
    position: fixed;
    width: calc(100% - 260px);
    top: 0;
    left: 260px;
    height: 60px;
    z-index: 1000;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 0 20px;
    transition: width 0.3s ease, left 0.3s ease;
}

body.sidebar-toggled .topbar {
    width: calc(100% - 80px);
    left: 80px;
}

.topbar .navbar-brand {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2C2C2C;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    transition: color 0.3s ease;
}

.topbar .navbar-brand:hover {
    color: #D4AF37;
}

.topbar .navbar-nav {
    display: flex;
    align-items: center;
}

.topbar .nav-link {
    color: #2C2C2C !important;
    padding: 0 15px;
    height: 60px;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
}

.topbar .nav-link:hover {
    color: #D4AF37 !important;
    background: rgba(255, 204, 213, 0.3);
    border-radius: 8px;
}

.topbar .dropdown-menu {
    background: #FFF5F5;
    border: none;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    margin-top: 10px;
    min-width: 220px;
}

.topbar .dropdown-header {
    background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
    color: #2C2C2C;
    font-weight: 600;
    padding: 10px 15px;
    border-radius: 8px 8px 0 0;
}

.topbar .dropdown-item {
    color: #2C2C2C;
    padding: 8px 15px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.topbar .dropdown-item:hover {
    background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
    color: #2C2C2C;
}

.topbar .dropdown-item i {
    margin-right: 10px;
    color: #2C2C2C;
}

.topbar .badge-counter {
    position: absolute;
    top: 8px;
    right: 5px;
    background-color: #D4AF37;
    color: #FFF5F5;
    font-size: 0.7rem;
    padding: 4px 7px;
    border-radius: 10px;
    transform: scale(0.75);
}

.topbar .img-profile {
    height: 34px;
    width: 34px;
    border-radius: 50%;
    object-fit: cover;
    margin-left: 12px;
    border: 2px solid #FFCCD5;
    transition: transform 0.3s ease;
}

.topbar .img-profile:hover {
    transform: scale(1.1);
}

.topbar-divider {
    border-right: 1px solid rgba(0, 0, 0, 0.1);
    height: 40px;
    margin: auto 15px;
}

.card {
    border: none;
    border-radius: 12px;
    background: #FFF5F5;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
}

.card-header {
    background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
    color: #2C2C2C;
    border-bottom: none;
    padding: 15px 20px;
    border-radius: 12px 12px 0 0;
    font-weight: 600;
    font-size: 1.1rem;
}

.card-body {
    padding: 20px;
}

.chart-container {
    position: relative;
    height: 400px;
    width: 100%;
    margin: 0 auto;
    padding: 10px;
}

canvas {
    border-radius: 8px;
    width: 100% !important;
    height: 100% !important;
    max-height: 400px;
}

.table-responsive {
    overflow-x: auto; /* Allow horizontal scrolling for table */
    max-width: 100%;
}

.table {
    width: 100%;
    margin-bottom: 0;
    font-size: 0.95rem;
    table-layout: auto; /* Changed to auto for natural column widths */
}

.table th,
.table td {
    padding: 12px;
    vertical-align: middle;
    text-align: center;
    overflow-wrap: break-word; /* Improved text wrapping */
    min-width: 120px; /* Increased min-width for visibility */
}

.table th {
    background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
    color: #2C2C2C;
    font-weight: 600;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.table td {
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.table .btn {
    font-size: 0.85rem;
    padding: 5px 10px;
}

.sticky-footer {
    background: #FFF5F5;
    padding: 20px 0;
    position: fixed; /* Pin footer to bottom */
    bottom: 0;
    width: 100%;
    z-index: 1000;
    box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.05);
}

.scroll-to-top {
    background-color: #FFCCD5;
    color: #2C2C2C;
    border-radius: 50%;
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.scroll-to-top:hover {
    background-color: #D4AF37;
    transform: scale(1.15);
}

.error-message {
    color: #FF6F61;
    font-weight: 500;
    padding: 10px;
    text-align: center;
}

@media (max-width: 768px) {
    .sidebar {
        width: 80px;
        min-width: 80px;
        overflow-y: auto; /* Ensure scrolling in mobile view */
        max-height: 100vh;
    }
    #content-wrapper {
        margin-left: 80px;
        padding-bottom: 60px; /* Adjusted for footer on mobile */
    }
    .topbar {
        width: calc(100% - 80px);
        left: 80px;
    }
    .sidebar.toggled {
        width: 260px;
        min-width: 260px;
    }
    body.sidebar-toggled #content-wrapper {
        margin-left: 260px;
    }
    body.sidebar-toggled .topbar {
        width: calc(100% - 260px);
        left: 260px;
    }
    .sidebar-brand-text,
    .nav-link span,
    .sidebar-heading {
        display: none;
    }
    .sidebar.toggled .sidebar-brand-text,
    .sidebar.toggled .nav-link span,
    .sidebar.toggled .sidebar-heading {
        display: block;
    }
    .nav-item .badge {
        opacity: 0;
    }
    .sidebar.toggled .nav-item .badge {
        opacity: 1;
    }
    .chart-container {
        height: 300px;
    }
    .card-header {
        font-size: 1rem;
    }
    .topbar .navbar-brand {
        font-size: 1.3rem;
    }
    .topbar .nav-link {
        padding: 0 10px;
    }
    .topbar .img-profile {
        height: 30px;
        width: 30px;
    }
    .topbar .badge-counter {
        transform: scale(0.65);
        right: 2px;
        top: 6px;
    }
    .table th,
    .table td {
        font-size: 0.9rem;
        padding: 10px;
        min-width: 100px; /* Adjusted for mobile */
    }
    .sticky-footer {
        padding: 15px 0; /* Slightly less padding on mobile */
    }
}

@media (max-width: 576px) {
    .chart-container {
        height: 250px;
    }
    .card {
        margin-bottom: 15px;
    }
    .topbar .nav-link {
        padding: 0 8px;
    }
    .table th,
    .table td {
        font-size: 0.85rem;
        padding: 8px;
        min-width: 80px; /* Adjusted for smaller screens */
    }
    .sticky-footer {
        padding: 10px 0; /* Further reduced padding */
    }
}  </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav sidebar accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon"><i class="fas fa-gem"></i></div>
                <div class="sidebar-brand-text mx-3">Auranest</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php" data-tooltip="Dashboard">
                    <i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span><span class="badge badge-warning ms-2">3</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Core Management</div>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="products.php" data-tooltip="Products">
                    <i class="fas fa-fw fa-tshirt"></i><span>Products</span><span class="badge badge-warning ms-2">12</span>
                </a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="categories.php" data-tooltip="Categories">
                    <i class="fas fa-fw fa-sitemap"></i><span>Categories</span>
                </a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="orders.php" data-tooltip="Orders">
                    <i class="fas fa-fw fa-shopping-cart"></i><span>Orders</span><span class="badge badge-warning ms-2">5</span>
                </a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="customers.php" data-tooltip="Customers">
                    <i class="fas fa-fw fa-users"></i><span>Customers</span>
                </a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'brands.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="brands.php" data-tooltip="Brands">
                    <i class="fas fa-fw fa-copyright"></i><span>Brands</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Analytics & Marketing</div>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="reports.php" data-tooltip="Reports">
                    <i class="fas fa-fw fa-chart-line"></i><span>Reports</span>
                </a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'promotions.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="promotions.php" data-tooltip="Promotions">
                    <i class="fas fa-fw fa-bullhorn"></i><span>Promotions</span><span class="badge badge-warning ms-2">2</span>
                </a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'cms.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="cms.php" data-tooltip="CMS Pages">
                    <i class="fas fa-fw fa-pencil-alt"></i><span>CMS Pages</span>
                </a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-angle-left"></i></button>
            </div>
        </ul>
        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow">
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
            <a class="navbar-brand d-none d-sm-inline" href="index.php">Auranest Admin</a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown no-arrow mx-1">
                    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fa-fw"></i><span class="badge badge-danger badge-counter">0+</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                        <li><h6 class="dropdown-header">Alerts Center</h6></li>
                        <li><a class="dropdown-item d-flex align-items-center" href="#">
                            <div class="mr-3"><div class="icon-circle bg-warning"><i class="fas fa-exclamation-triangle text-white"></i></div></div>
                            <div><div class="small text-gray-500"><?php echo date('M d, Y'); ?></div><span class="font-weight-bold">No low stock items!</span></div>
                        </a></li>
                        <li><a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown no-arrow mx-1">
                    <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-envelope fa-fw"></i><span class="badge badge-danger badge-counter">0</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                        <li><h6 class="dropdown-header">Message Center</h6></li>
                        <li><a class="dropdown-item d-flex align-items-center" href="#">
                            <div class="dropdown-list-image mr-3"><img class="rounded-circle" src="https://via.placeholder.com/60" alt="..."><div class="status-indicator bg-success"></div></div>
                            <div class="font-weight-bold"><div class="text-truncate">No new messages</div></div>
                        </a></li>
                        <li><a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a></li>
                    </ul>
                </li>
                <div class="topbar-divider d-none d-sm-block"></div>
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="mr-2 d-none d-lg-inline text-dark small"><?php echo $greeting; ?></span>
                        <img class="img-profile rounded-circle" src="<?php echo htmlspecialchars($profile_pic); ?>" style="width: 34px; height: 34px;">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>My Profile</a></li>
                        <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <div class="container-fluid">
                    <?php if (isset($db_error)): ?>
                        <div class="error-message"><?php echo $db_error; ?></div>
                    <?php endif; ?>
                    <h1 class="mt-4 mb-4" style="color: #FFCCD5; font-family: 'Lora', serif; font-weight: 600;">Dashboard Overview</h1>
                    <!-- Stat Cards -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-sm font-weight-bold text-dark text-uppercase mb-1">Total Sales</div>
                                            <div class="h5 mb-0 font-weight-bold text-dark" id="totalSalesCounter"><?php echo number_format($total_sales, 2); ?></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-sm font-weight-bold text-success text-uppercase mb-1">Total Orders</div>
                                            <div class="h5 mb-0 font-weight-bold text-dark" id="totalOrdersCounter"><?php echo $total_orders; ?></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-shopping-basket fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-sm font-weight-bold text-info text-uppercase mb-1">New Customers</div>
                                            <div class="h5 mb-0 font-weight-bold text-dark" id="newCustomersCounter"><?php echo $new_customers; ?></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-user-plus fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-sm font-weight-bold text-warning text-uppercase mb-1">Low Stock Items</div>
                                            <div class="h5 mb-0 font-weight-bold text-dark" id="lowStockCounter"><?php echo $low_stock_items; ?></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Charts -->
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-dark">Sales Over Time</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="salesOverviewChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-dark">Top 5 Bestselling Products</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="topProductsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-dark">Sales by Category</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="salesByCategoryChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-dark">Monthly User Growth</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="userGrowthChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-dark">Top 10 Clients by Spending</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="topClientsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-dark">Recent Orders</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="recentOrdersTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th style="width: 15%; min-width: 100px;">Order ID</th>
                                                    <th style="width: 25%; min-width: 150px;">Customer</th>
                                                    <th style="width: 15%; min-width: 100px;">Amount</th>
                                                    <th style="width: 15%; min-width: 100px;">Status</th>
                                                    <th style="width: 20%; min-width: 120px;">Order Date</th>
                                                    <th style="width: 10%; min-width: 80px;">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recent_orders as $order): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                                        <td>$<?php echo number_format($order['amount'], 2); ?></td>
                                                        <td><span class="badge badge-<?php echo $order['status'] == 'Pending' ? 'warning' : ($order['status'] == 'Shipped' ? 'success' : 'primary'); ?>"><?php echo htmlspecialchars($order['status']); ?></span></td>
                                                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                                        <td><a href="orders.php" class="btn btn-sm btn-info">View</a></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="sticky-footer">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Auranest Admin 2025</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-bs-dismiss="modal"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        const chartData = {
            productLabels: <?php echo json_encode($product_labels); ?>,
            productData: <?php echo json_encode($product_data); ?>,
            categoryLabels: <?php echo json_encode($category_labels); ?>,
            categoryData: <?php echo json_encode($category_data); ?>,
            salesDates: <?php echo json_encode($sales_dates); ?>,
            salesValues: <?php echo json_encode($sales_values); ?>,
            clientNames: <?php echo json_encode($client_names); ?>,
            clientSpending: <?php echo json_encode($client_spending); ?>,
            userMonths: <?php echo json_encode($user_months); ?>,
            userCounts: <?php echo json_encode($user_counts); ?>
        };
        const statsData = {
            totalSales: <?php echo $total_sales; ?>,
            totalOrders: <?php echo $total_orders; ?>,
            newCustomers: <?php echo $new_customers; ?>,
            lowStockItems: <?php echo $low_stock_items; ?>
        };
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="chart.js"></script>
    <script src="main.js"></script>
</body>
</html>