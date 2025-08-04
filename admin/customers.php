<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db.php';
include 'functions.php';
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$greeting = date('H') < 12 ? "Good Morning, $admin_name" : (date('H') < 17 ? "Good Afternoon, $admin_name" : "Good Evening, $admin_name");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['block_customer'])) {
    $customer_id = (int)($_POST['customer_id'] ?? 0);
    $block_status = (int)($_POST['block_status'] ?? 0);
    if ($customer_id && blockCustomer($customer_id, $block_status)) {
        $action = $block_status ? 'blocked' : 'unblocked';
        echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('Customer $action successfully!'); });</script>";
    } else {
        echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('Failed to update customer status!', true); });</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auranest Admin - Customers</title>
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
        .table th, .table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        .table th {
            background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
            font-weight: 600;
        }
        .table td {
            background: #FFF5F5;
        }
        .table tr:nth-child(even) td {
            background: #FFE4E1;
        }
        .btn-custom {
            padding: 6px 12px;
            border-radius: 8px;
            margin: 0 4px;
        }
        .btn-view {
            background: #D4AF37;
            color: #FFF5F5;
            border: 1px solid #B89B2E;
        }
        .btn-block {
            background: #FF6F61;
            color: #FFF5F5;
            border: 1px solid #E65A4E;
        }
        .btn-unblock {
            background: #28A745;
            color: #FFF5F5;
            border: 1px solid #218838;
        }
        .modal-content {
            border-radius: 12px;
            background: #FFF5F5;
        }
        .modal-header {
            background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
            border-bottom: none;
            border-radius: 12px 12px 0 0;
        }
        .search-filter {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            justify-content: center;
        }
        .search-filter input {
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
            .table th, .table td { font-size: 0.85rem; padding: 8px; }
            .search-filter { flex-direction: column; gap: 10px; }
            .search-filter input { max-width: 100%; }
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
                    <h1 class="mt-4 mb-4">Customers Management</h1>
                    <div class="search-filter">
                        <input type="text" id="customerSearchInput" class="form-control" placeholder="Search customers by name or email...">
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold">All Customers</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="customersTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Total Spending</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $conn = new mysqli('localhost', 'root', '', 'auranest_db');
                                        if ($conn->connect_error) {
                                            echo "<tr><td colspan='6'>Database error: " . htmlspecialchars($conn->connect_error) . "</td></tr>";
                                            echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('Database connection failed!', true); });</script>";
                                        } else {
                                            $result = $conn->query("SELECT id, name, email, total_spending, status FROM customers");
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                                    echo "<td>$" . number_format($row['total_spending'], 2) . "</td>";
                                                    echo "<td>" . ($row['status'] ? '<span class="text-danger">Blocked</span>' : '<span class="text-success">Active</span>') . "</td>";
                                                    echo "<td>";
                                                    echo "<div class='btn-group' role='group'>";
                                                    echo "<a href='view_customer.php?id=" . urlencode($row['id']) . "' class='btn btn-sm btn-custom btn-view'><i class='fas fa-eye'></i> View</a>";
                                                    echo "<button class='btn btn-sm btn-custom " . ($row['status'] ? 'btn-unblock' : 'btn-block') . "' data-id='" . htmlspecialchars($row['id']) . "' data-status='" . ($row['status'] ? '0' : '1') . "'><i class='fas fa-" . ($row['status'] ? 'lock-open' : 'lock') . "'></i> " . ($row['status'] ? 'Unblock' : 'Block') . "</button>";
                                                    echo "</div>";
                                                    echo "</td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='6' class='text-center'>No customers found.</td></tr>";
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

    <div class="modal fade" id="blockConfirmModal" tabindex="-1" role="dialog" aria-labelledby="blockConfirmModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="blockConfirmModalLabel">Confirm Action</h5>
                    <button class="close" type="button" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    Are you sure you want to <span id="blockActionText"></span> this customer? This action can be reversed.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-custom btn-cancel" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-custom btn-confirm" id="confirmBlock">Yes, <span id="blockButtonText"></span></button>
                </div>
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
            // Toast function
            function showToast(message, isError = false) {
                const toast = $('#toastNotification');
                toast.find('.toast-body').text(message);
                toast.addClass('show ' + (isError ? 'toast-error' : 'toast-success'));
                setTimeout(() => toast.removeClass('show'), 3000);
            }

            // Block/Unblock button handler
            let blockId = null;
            let blockStatus = null;
            $('.btn-block, .btn-unblock').on('click', function() {
                blockId = $(this).data('id');
                blockStatus = $(this).data('status');
                $('#blockActionText').text(blockStatus == 1 ? 'block' : 'unblock');
                $('#blockButtonText').text(blockStatus == 1 ? 'Block' : 'Unblock');
                $('#blockConfirmModal').modal('show');
            });

            $('#confirmBlock').on('click', function() {
                if (blockId && blockStatus !== null) {
                    $('<form>', { method: 'POST' })
                        .append(`<input type="hidden" name="customer_id" value="${blockId}">`)
                        .append(`<input type="hidden" name="block_status" value="${blockStatus}">`)
                        .append(`<input type="hidden" name="block_customer" value="true">`)
                        .appendTo('body')
                        .submit();
                } else {
                    showToast('Invalid action!', true);
                }
            });

            // Search functionality
            $('#customerSearchInput').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('#customersTable tbody tr').each(function() {
                    const name = $(this).find('td').eq(1).text().toLowerCase();
                    const email = $(this).find('td').eq(2).text().toLowerCase();
                    $(this).toggle(name.includes(searchTerm) || email.includes(searchTerm));
                });
            });
        });
    </script>
</body>
</html>