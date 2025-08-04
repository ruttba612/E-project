<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
if (file_exists('functions.php')) {
    include 'functions.php';
} else {
    die("functions.php file not found. Please place it in the admin folder.");
}
include 'db.php';
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$greeting = date('H') < 12 ? "Good Morning, $admin_name" : (date('H') < 17 ? "Good Afternoon, $admin_name" : "Good Evening, $admin_name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST data: " . print_r($_POST, true));
    if (isset($_POST['generate_invoice']) && !empty($_POST['order_id']) && is_numeric($_POST['order_id'])) {
        $order_id = (int)$_POST['order_id'];
        if ($order_id > 0) {
            error_log("Generating invoice for order ID: $order_id");
            generateInvoicePDF($order_id);
            exit();
        } else {
            echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('Invalid order ID!', true); });</script>";
        }
    } elseif (isset($_POST['cancel_order']) && !empty($_POST['order_id']) && is_numeric($_POST['order_id'])) {
        $order_id = (int)$_POST['order_id'];
        if ($order_id > 0 && cancelOrder($order_id)) {
            echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('Order cancelled successfully!'); window.location.href='orders.php'; });</script>";
        } else {
            echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('Failed to cancel order!', true); });</script>";
        }
    } else {
        echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('Invalid request!', true); });</script>";
    }
}

// Handle PDF export for all orders
if (isset($_GET['export_orders_pdf'])) {
    error_log("Export PDF triggered at " . date('Y-m-d H:i:s'));
    if (!file_exists('fpdf/fpdf.php')) {
        error_log("FPDF file not found at fpdf/fpdf.php");
        echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('PDF library not found!', true); });</script>";
        exit();
    }
    include('fpdf/fpdf.php');
    $pdf = new FPDF();
    $pdf->AddPage();
    $font_available = file_exists('fpdf/font/Lora-Regular.php') && file_exists('fpdf/font/Lora-Bold.php');
    if ($font_available) {
        $pdf->AddFont('Lora', '', 'Lora-Regular.php');
        $pdf->AddFont('Lora', 'B', 'Lora-Bold.php');
    } else {
        error_log("Lora fonts not found, falling back to Arial");
    }
    $pdf->SetFont($font_available ? 'Lora' : 'Arial', 'B', 16);
    $pdf->SetFillColor(255, 204, 213); // #FFCCD5
    $pdf->Rect(0, 0, 210, 30, 'F');
    $pdf->SetTextColor(44, 44, 44); // #2C2C2C
    $pdf->Cell(0, 10, 'Auranest Orders List', 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont($font_available ? 'Lora' : 'Arial', 'B', 12);
    $colWidths = [30, 50, 30, 30, 50];
    $pdf->SetFillColor(255, 228, 225); // #FFE4E1
    $pdf->Cell($colWidths[0], 10, 'Order ID', 1, 0, 'C', 1);
    $pdf->Cell($colWidths[1], 10, 'Customer', 1, 0, 'C', 1);
    $pdf->Cell($colWidths[2], 10, 'Amount', 1, 0, 'C', 1);
    $pdf->Cell($colWidths[3], 10, 'Status', 1, 0, 'C', 1);
    $pdf->Cell($colWidths[4], 10, 'Order Date', 1, 0, 'C', 1);
    $pdf->Ln();
    $pdf->SetFont($font_available ? 'Lora' : 'Arial', '', 10);
    $conn = new mysqli('localhost', 'root', '', 'auranest_db');
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('Database connection failed!', true); });</script>";
        exit();
    }
    $result = $conn->query("SELECT o.order_id, c.name, o.total_amount, o.status, o.order_date FROM orders o JOIN customers c ON o.customer_id = c.id");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell($colWidths[0], 10, $row['order_id'], 1, 0, 'C');
            $pdf->Cell($colWidths[1], 10, $row['name'], 1, 0, 'L');
            $pdf->Cell($colWidths[2], 10, '$' . number_format($row['total_amount'], 2), 1, 0, 'C');
            $pdf->Cell($colWidths[3], 10, $row['status'], 1, 0, 'C');
            $pdf->Cell($colWidths[4], 10, $row['order_date'], 1, 0, 'C');
            $pdf->Ln();
        }
    } else {
        $pdf->Cell(0, 10, 'No orders found', 0, 1, 'C');
        error_log("No orders found in database");
    }
    $conn->close();
    ob_clean(); // Clear any output buffer
    $pdf->Output('D', 'orders_list.pdf');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auranest Admin - Orders</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora&display=swap" rel="stylesheet">
    <link href="assets/bootstrap.min.css" rel="stylesheet" onerror="this.href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css';">
    <link href="assets/fontawesome.all.min.css" rel="stylesheet" onerror="this.href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css';">
    <style>
        body {
            font-family: 'Inter', 'Lora', sans-serif;
            background-color: #FFF5F5;
            color: #2C2C2C;
            line-height: 1.6;
            overflow-x: hidden;
            padding-top: 80px; /* Space for fixed topbar */
        }
        .container-fluid {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            font-size: 2rem;
            font-weight: 600;
            color: #2C2C2C;
            margin-bottom: 20px;
            text-align: center;
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
            text-align: center;
        }
        .card-body {
            padding: 20px;
        }
        .table-responsive {
            overflow-x: auto;
            max-width: 100%;
        }
        .table {
            width: 100%;
            margin-bottom: 0;
            font-size: 0.95rem;
            table-layout: auto;
            border-collapse: separate;
            border-spacing: 0;
        }
        .table th, .table td {
            padding: 12px;
            vertical-align: middle;
            text-align: center;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        .table th {
            background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
            color: #2C2C2C;
            font-weight: 600;
            cursor: pointer;
        }
        .table th:hover {
            background: linear-gradient(90deg, #FFE4E1, #FFCCD5);
        }
        .table td {
            background: #FFF5F5;
        }
        .table tr:nth-child(even) td {
            background: #FFE4E1;
        }
        .table tr:hover td {
            background: rgba(255, 204, 213, 0.3);
        }
        .btn-custom {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            margin: 0 4px;
            transition: all 0.3s ease;
        }
        .btn-view {
            background: #17A2B8;
            color: #FFF5F5;
            border: 1px solid #138496;
        }
        .btn-view:hover {
            background: #138496;
            transform: scale(1.05);
        }
        .btn-invoice {
            background: #28A745;
            color: #FFF5F5;
            border: 1px solid #218838;
        }
        .btn-invoice:hover {
            exfoliation-level-0
            background: #218838;
            transform: scale(1.05);
        }
        .btn-cancel {
            background: #FF6F61;
            color: #FFF5F5;
            border: 1px solid #E65A4E;
        }
        .btn-cancel:hover {
            background: #E65A4E;
            transform: scale(1.05);
        }
        .btn-cancel:disabled {
            background: #CCCCCC;
            border: 1px solid #AAAAAA;
            cursor: not-allowed;
        }
        .btn-add {
            background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
            color: #2C2C2C;
            padding: 10px 20px;
            border-radius: 8px;
        }
        .btn-add:hover {
            background: linear-gradient(90deg, #FFE4E1, #FFCCD5);
            transform: translateY(-2px);
        }
        .modal-content {
            border-radius: 12px;
            background: #FFF5F5;
        }
        .modal-header {
            background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
            color: #2C2C2C;
            border-bottom: none;
            border-radius: 12px 12px 0 0;
        }
        .modal-title {
            font-size: 1.2rem;
            font-weight: 600;
        }
        .modal-body {
            padding: 20px;
        }
        .modal-footer {
            border-top: none;
            padding: 15px 20px;
        }
        .search-filter {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }
        .search-filter input, .search-filter select {
            max-width: 200px;
            border-radius: 8px;
            border: 1px solid #FFCCD5;
            padding: 8px;
            height: 38px;
        }
        .search-filter input:focus, .search-filter select:focus {
            border-color: #D4AF37;
            box-shadow: 0 0 5px rgba(212, 175, 55, 0.3);
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
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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
        @media (max-width: 768px) {
            h1 { font-size: 1.8rem; }
            .table th, .table td { font-size: 0.9rem; padding: 10px; min-width: 80px; }
            .btn-custom { padding: 5px 10px; font-size: 0.85rem; }
            .search-filter input, .search-filter select { max-width: 150px; }
        }
        @media (max-width: 576px) {
            h1 { font-size: 1.5rem; }
            .table th, .table td { font-size: 0.85rem; padding: 8px; min-width: 60px; }
            .btn-custom { padding: 4px 8px; font-size: 0.8rem; }
            .search-filter { flex-direction: column; gap: 10px; align-items: stretch; }
            .search-filter input, .search-filter select { max-width: 100%; }
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
                    <h1 class="mt-4 mb-4">Orders Management</h1>
                    <div class="search-filter">
                        <input type="text" id="orderSearchInput" class="form-control" placeholder="Search by Order ID or Customer...">
                        <select id="statusFilter" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <button class="btn btn-add" onclick="exportOrdersPDF()">Download PDF</button>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold">All Orders</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th data-sort="order_id">Order ID <i class="fas fa-sort"></i></th>
                                            <th data-sort="name">Customer <i class="fas fa-sort"></i></th>
                                            <th data-sort="total_amount">Amount <i class="fas fa-sort"></i></th>
                                            <th data-sort="status">Status <i class="fas fa-sort"></i></th>
                                            <th data-sort="order_date">Order Date <i class="fas fa-sort"></i></th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $conn = new mysqli('localhost', 'root', '', 'auranest_db');
                                        if ($conn->connect_error) {
                                            error_log("Database connection failed: " . $conn->connect_error);
                                            echo "<tr><td colspan='6'>Database error: " . htmlspecialchars($conn->connect_error) . "</td></tr>";
                                            echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('Database connection failed!', true); });</script>";
                                        } else {
                                            $result = $conn->query("SELECT o.order_id, c.name, o.total_amount, o.status, o.order_date FROM orders o JOIN customers c ON o.customer_id = c.id");
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                                    echo "<td>$" . number_format($row['total_amount'], 2) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['order_date']) . "</td>";
                                                    echo "<td>";
                                                    echo "<div class='btn-group' role='group'>";
                                                    echo "<a href='view_order.php?order_id=" . urlencode($row['order_id']) . "' class='btn btn-custom btn-view'><i class='fas fa-eye'></i> View</a>";
                                                    echo "<form method='POST' style='display:inline-block;'>";
                                                    echo "<input type='hidden' name='order_id' value='" . htmlspecialchars($row['order_id']) . "'>";
                                                    echo "<button type='submit' name='generate_invoice' class='btn btn-custom btn-invoice'><i class='fas fa-file-pdf'></i> Invoice</button>";
                                                    echo "</form>";
                                                    echo "<form method='POST' style='display:inline-block;'>";
                                                    echo "<input type='hidden' name='order_id' value='" . htmlspecialchars($row['order_id']) . "'>";
                                                    $disabled = $row['status'] === 'cancelled' ? 'disabled' : '';
                                                    echo "<button type='submit' name='cancel_order' class='btn btn-custom btn-cancel' $disabled onclick='return confirm(\"Are you sure you want to cancel this order?\");'><i class='fas fa-trash'></i> Cancel</button>";
                                                    echo "</form>";
                                                    echo "</div>";
                                                    echo "</td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='6' class='text-center'>No orders found. Please add orders to the database.</td></tr>";
                                            }
                                            $conn->close();
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" onclick="hideModal('logoutModal')"><span>&times;</span></button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-custom btn-cancel" onclick="hideModal('logoutModal')">Cancel</button>
                    <a class="btn btn-custom btn-view" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <div class="toast" id="toastNotification">
        <div class="toast-body">Action successful!</div>
    </div>
    <script>
        // Toast Notification (Defined Early)
        function showToast(message, isError = false) {
            console.log('Showing toast:', message);
            const toast = document.getElementById('toastNotification');
            if (toast) {
                toast.querySelector('.toast-body').textContent = message;
                toast.classList.add('show', isError ? 'toast-error' : 'toast-success');
                setTimeout(() => {
                    toast.classList.remove('show');
                    console.log('Toast hidden');
                }, 3000);
            }
        }

        // Load Assets with Error Handling
        document.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
            link.onerror = function() {
                console.error('Failed to load CSS: ' + link.href);
                showToast('Failed to load CSS: ' + link.href.split('/').pop(), true);
            };
            link.onload = function() {
                console.log('Loaded CSS: ' + link.href);
            };
        });
        document.querySelectorAll('script[src]').forEach(script => {
            script.onerror = function() {
                console.error('Failed to load script: ' + script.src);
                showToast('Failed to load script: ' + script.src.split('/').pop(), true);
            };
            script.onload = function() {
                console.log('Loaded script: ' + script.src);
            };
        });

        console.log("Orders JavaScript initialized at <?php echo date('Y-m-d H:i:s'); ?>");

        // Modal Handling
        function showModal(modalId) {
            console.log('Showing modal:', modalId);
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('show');
                modal.style.display = 'block';
                document.body.classList.add('modal-open');
                const backdrop = document.createElement('div');
                backdrop.classList.add('modal-backdrop', 'fade', 'show');
                document.body.appendChild(backdrop);
            } else {
                showToast('Modal not found!', true);
            }
        }

        function hideModal(modalId) {
            console.log('Hiding modal:', modalId);
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('show');
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            }
        }

        // Table Sorting
        function sortTable(column, order) {
            console.log('Sorting table by:', column, 'Order:', order);
            const table = document.getElementById('ordersTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            rows.sort((a, b) => {
                let aValue = a.cells[column].textContent.trim();
                let bValue = b.cells[column].textContent.trim();
                if (column === 2) { // Amount
                    aValue = parseFloat(aValue.replace('$', '')) || 0;
                    bValue = parseFloat(bValue.replace('$', '')) || 0;
                } else if (column === 4) { // Date
                    aValue = new Date(aValue).getTime();
                    bValue = new Date(bValue).getTime();
                }
                return order === 'asc' ? (aValue > bValue ? 1 : -1) : (aValue < bValue ? 1 : -1);
            });
            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));
        }

        // Search and Filter
        function filterTable() {
            const searchInput = document.getElementById('orderSearchInput');
            const statusFilter = document.getElementById('statusFilter');
            if (!searchInput || !statusFilter) {
                console.error('Search input or status filter not found');
                showToast('Search functionality unavailable!', true);
                return;
            }
            console.log('Filtering table. Search:', searchInput.value, 'Status:', statusFilter.value);
            const searchTerm = searchInput.value.toLowerCase();
            const status = statusFilter.value.toLowerCase();
            const rows = document.querySelectorAll('#ordersTable tbody tr');
            let visibleRows = 0;
            rows.forEach(row => {
                const orderId = row.cells[0].textContent.toLowerCase();
                const customer = row.cells[1].textContent.toLowerCase();
                const rowStatus = row.cells[3].textContent.toLowerCase();
                const matchesSearch = orderId.includes(searchTerm) || customer.includes(searchTerm);
                const matchesStatus = !status || rowStatus === status;
                row.style.display = matchesSearch && matchesStatus ? '' : 'none';
                if (matchesSearch && matchesStatus) visibleRows++;
            });
            console.log('Visible rows:', visibleRows);
        }

        // PDF Export
        function exportOrdersPDF() {
            console.log('Orders PDF export triggered');
            window.location.href = '?export_orders_pdf=true';
        }

        // Document Ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM fully loaded');

            // Initialize Search and Filter
            const searchInput = document.getElementById('orderSearchInput');
            const statusFilter = document.getElementById('statusFilter');
            if (searchInput && statusFilter) {
                searchInput.addEventListener('input', filterTable);
                statusFilter.addEventListener('change', filterTable);
            } else {
                console.error('Search input or status filter not found');
                showToast('Search functionality unavailable!', true);
            }

            // Sorting Handlers
            document.querySelectorAll('#ordersTable th[data-sort]').forEach((th, index) => {
                th.addEventListener('click', function() {
                    const sortKey = this.getAttribute('data-sort');
                    const order = this.classList.contains('sort-asc') ? 'desc' : 'asc';
                    document.querySelectorAll('#ordersTable th').forEach(t => {
                        t.classList.remove('sort-asc', 'sort-desc');
                        t.querySelector('i').className = 'fas fa-sort';
                    });
                    this.classList.add(`sort-${order}`);
                    this.querySelector('i').className = `fas fa-sort-${order === 'asc' ? 'up' : 'down'}`;
                    sortTable(index, order);
                });
            });

            // Form Submission Debug
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    console.log('Form submitted:', this);
                    const orderId = this.querySelector('input[name="order_id"]').value;
                    console.log('Order ID:', orderId);
                });
            });

            // jQuery Check with Fallback
            if (typeof jQuery === 'undefined') {
                console.warn('jQuery not loaded, loading from CDN');
                const script = document.createElement('script');
                script.src = 'https://code.jquery.com/jquery-3.5.1.min.js';
                script.onload = function() {
                    console.log('jQuery loaded from CDN:', jQuery.fn.jquery);
                    initializeJQuery();
                };
                script.onerror = function() {
                    console.error('Failed to load jQuery from CDN');
                    showToast('Failed to load jQuery, some features may be limited!', true);
                };
                document.head.appendChild(script);
            } else {
                console.log('jQuery loaded:', jQuery.fn.jquery);
                initializeJQuery();
            }

            function initializeJQuery() {
                jQuery('#logoutModal').on('hidden.bs.modal', function() {
                    console.log('Logout modal closed (jQuery)');
                });
                jQuery('.dropdown-toggle').on('click', function() {
                    console.log('Dropdown toggled:', this.id);
                    const dropdownMenu = jQuery(this).next('.dropdown-menu');
                    dropdownMenu.css({ 'right': '0', 'left': 'auto' });
                });
            }
        });
    </script>
    <script src="assets/jquery-3.5.1.min.js" onerror="this.src='https://code.jquery.com/jquery-3.5.1.min.js';"></script>
    <script src="assets/bootstrap.bundle.min.js" onerror="this.src='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js';"></script>
</body>
</html>