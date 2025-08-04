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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_promotion'])) {
        $title = $_POST['title'] ?? '';
        $discount_type = $_POST['discount_type'] ?? 'percentage';
        $discount_amount = (float)($_POST['discount_amount'] ?? 0);
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $categories = isset($_POST['categories']) ? implode(',', $_POST['categories']) : '';
        $products = isset($_POST['products']) ? implode(',', $_POST['products']) : '';
        $description = $_POST['description'] ?? '';
        $status = $_POST['status'] ?? 'active';
        $site_wide = isset($_POST['site_wide']) ? 1 : 0;
        $user_group = $_POST['user_group'] ?? 'all';
        $promo_code = strtoupper(substr(str_replace(' ', '', $title), 0, 6) . rand(1000, 9999));

        // Handle banner upload
        $banner = '';
        if (isset($_FILES['banner']['name']) && $_FILES['banner']['name']) {
            $upload_dir = 'uploads/promotions/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $banner = $upload_dir . time() . '_' . basename($_FILES['banner']['name']);
            move_uploaded_file($_FILES['banner']['tmp_name'], $banner);
        }

        $today = date('Y-m-d');
        $status = ($end_date < $today) ? 'expired' : $status;

        $stmt = $conn->prepare("INSERT INTO promotions (title, discount_type, discount_amount, start_date, end_date, categories, products, description, status, site_wide, user_group, promo_code, banner) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            $error = "Failed to prepare query";
        } else {
            $stmt->bind_param("ssdsissssisss", $title, $discount_type, $discount_amount, $start_date, $end_date, $categories, $products, $description, $status, $site_wide, $user_group, $promo_code, $banner);
            if ($stmt->execute()) {
                $success = "Promotion added successfully!";
            } else {
                error_log("Execute failed: " . $stmt->error);
                $error = "Failed to add promotion";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['delete_promotion'])) {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM promotions WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $success = "Promotion deleted successfully!";
        } else {
            $error = "Failed to delete promotion";
        }
        $stmt->close();
    } elseif (isset($_POST['duplicate_promotion'])) {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $conn->prepare("INSERT INTO promotions (title, discount_type, discount_amount, start_date, end_date, categories, products, description, status, site_wide, user_group, promo_code, banner)
                                SELECT title, discount_type, discount_amount, ?, ?, categories, products, description, status, site_wide, user_group, ?, banner
                                FROM promotions WHERE id = ?");
        $new_start = date('Y-m-d');
        $new_end = date('Y-m-d', strtotime('+30 days'));
        $new_promo_code = strtoupper('COPY' . rand(1000, 9999));
        $stmt->bind_param("sssi", $new_start, $new_end, $new_promo_code, $id);
        if ($stmt->execute()) {
            $success = "Promotion duplicated successfully!";
        } else {
            $error = "Failed to duplicate promotion";
        }
        $stmt->close();
    }
}

// Fetch promotions
$filter = $_POST['filter'] ?? 'all';
$sort = $_POST['sort'] ?? 'start_date DESC';
$today = date('Y-m-d');
$where = '';
if ($filter === 'active') {
    $where = "WHERE status = 'active' AND end_date >= '$today'";
} elseif ($filter === 'expired') {
    $where = "WHERE status = 'expired' OR end_date < '$today'";
} elseif ($filter === 'upcoming') {
    $where = "WHERE start_date > '$today'";
}
$query = "SELECT * FROM promotions $where ORDER BY $sort";
$result = $conn->query($query);

// Update expired promotions
$conn->query("UPDATE promotions SET status = 'expired' WHERE end_date < '$today' AND status != 'expired'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auranest Admin - Promotions</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet" integrity="sha384-rz/0M2Y7x2n7kYV0U3nO7TL6O3Sme6T2V6AkjMIQ3eW8W3M2e3e4" crossorigin="anonymous">
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
            color: #2C2C2C;
        }
        .card {
            border: none;
            border-radius: 12px;
            background: #FFF5F5;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
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
            padding: 8px 16px;
            border-radius: 8px;
            margin: 0 5px;
            font-size: 0.9rem;
        }
        .btn-primary {
            background: #D4AF37;
            border: 1px solid #B89B2E;
            color: #FFF5F5;
        }
        .btn-primary:hover {
            background: #B89B2E;
        }
        .btn-danger {
            background: #FF6F61;
            border: 1px solid #E65A4E;
            color: #FFF5F5;
        }
        .btn-danger:hover {
            background: #E65A4E;
        }
        .btn-info {
            background: #FFCCD5;
            border: 1px solid #FFB6C1;
            color: #2C2C2C;
        }
        .btn-info:hover {
            background: #FFB6C1;
        }
        .promo-card {
            background: #FFE4E1;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
            border: 1px solid #FFCCD5;
        }
        .promo-card h5 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2C2C2C;
        }
        .promo-card .timer {
            font-size: 0.9rem;
            color: #FF6F61;
            animation: pulse 2s infinite;
        }
        .promo-card .copy-btn {
            background: #D4AF37;
            color: #FFF5F5;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
        }
        .banner-preview {
            max-height: 200px;
            width: 100%;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .filter-section {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .filter-section select {
            max-width: 200px;
            border-radius: 8px;
            border: 1px solid #FFCCD5;
            height: 38px;
            padding: 0 10px;
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
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        @media (max-width: 576px) {
            h1 { font-size: 1.5rem; }
            .filter-section { flex-direction: column; gap: 10px; }
            .filter-section select { max-width: 100%; }
            .promo-card { width: 100%; }
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
                    <h1 class="mt-4 mb-4">Promotions Management</h1>
                    <?php if (isset($success)) : ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php elseif (isset($error)) : ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <div class="filter-section">
                        <select id="filter" class="form-control">
                            <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Promotions</option>
                            <option value="active" <?php echo $filter === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="expired" <?php echo $filter === 'expired' ? 'selected' : ''; ?>>Expired</option>
                            <option value="upcoming" <?php echo $filter === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                        </select>
                        <select id="sort" class="form-control">
                            <option value="start_date DESC" <?php echo $sort === 'start_date DESC' ? 'selected' : ''; ?>>Date (Newest)</option>
                            <option value="start_date ASC" <?php echo $sort === 'start_date ASC' ? 'selected' : ''; ?>>Date (Oldest)</option>
                            <option value="discount_amount DESC" <?php echo $sort === 'discount_amount DESC' ? 'selected' : ''; ?>>Discount (High to Low)</option>
                            <option value="discount_amount ASC" <?php echo $sort === 'discount_amount ASC' ? 'selected' : ''; ?>>Discount (Low to High)</option>
                        </select>
                    </div>
                    <div class="row">
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="promo-card">
                                    <?php if ($row['banner']) : ?>
                                        <img src="<?php echo htmlspecialchars($row['banner']); ?>" class="banner-preview" alt="Promotion Banner">
                                    <?php endif; ?>
                                    <h5><?php echo htmlspecialchars($row['title']); ?></h5>
                                    <p>Discount: <?php echo htmlspecialchars($row['discount_amount']) . ($row['discount_type'] === 'percentage' ? '%' : ' PKR'); ?></p>
                                    <p>Valid till: <?php echo htmlspecialchars($row['end_date']); ?></p>
                                    <p>Code: <span class="promo-code"><?php echo htmlspecialchars($row['promo_code']); ?></span> <button class="copy-btn" data-code="<?php echo htmlspecialchars($row['promo_code']); ?>">Copy Code</button></p>
                                    <p class="timer" data-end-date="<?php echo htmlspecialchars($row['end_date']); ?>">Calculating...</p>
                                    <p>Status: <?php echo htmlspecialchars($row['status']); ?></p>
                                    <?php if ($row['site_wide']) : ?>
                                        <p><strong>Site-wide Promotion</strong></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold">All Promotions</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="promotionsTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Discount</th>
                                            <th>Validity</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $result->data_seek(0);
                                        while ($row = $result->fetch_assoc()) :
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                                <td><?php echo htmlspecialchars($row['discount_amount']) . ($row['discount_type'] === 'percentage' ? '%' : ' PKR'); ?></td>
                                                <td><?php echo htmlspecialchars($row['start_date']) . ' to ' . htmlspecialchars($row['end_date']); ?></td>
                                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                                <td>
                                                    <form method="POST" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                        <button type="submit" name="duplicate_promotion" class="btn btn-sm btn-info">Duplicate</button>
                                                        <button type="submit" name="delete_promotion" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this promotion?');">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <button class="btn btn-primary mt-3" data-toggle="modal" data-target="#addPromotionModal">Add New Promotion</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
    <div class="modal fade" id="addPromotionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Promotion</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="add_promotion" value="1">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Discount Type</label>
                            <select name="discount_type" class="form-control">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (PKR)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Discount Amount</label>
                            <input type="number" name="discount_amount" class="form-control" min="0" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="text" name="start_date" class="form-control datepicker" required>
                        </div>
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="text" name="end_date" class="form-control datepicker" required>
                        </div>
                        <div class="form-group">
                            <label>Applicable Categories</label>
                            <select name="categories[]" class="form-control" multiple>
                                <?php
                                $result = $conn->query("SELECT id, name FROM categories ORDER BY name");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Applicable Products</label>
                            <select name="products[]" class="form-control" multiple>
                                <?php
                                $result = $conn->query("SELECT id, name FROM products ORDER BY name");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>User Group</label>
                            <select name="user_group" class="form-control">
                                <option value="all">All Users</option>
                                <option value="new_users">New Users</option>
                                <option value="top_customers">Top Customers</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Site-wide Promotion</label>
                            <input type="checkbox" name="site_wide" value="1">
                        </div>
                        <div class="form-group">
                            <label>Banner Image</label>
                            <input type="file" name="banner" class="form-control-file" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Promotion</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="toast" id="toastNotification">
        <div class="toast-body">Action successful!</div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js" integrity="sha384-LtrjvnR4Twt/qOuYxE721u19sVFLVSA4hf/rRt6PrZTmiPltdZcI7q7PXQBYTKyf" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr" integrity="sha256-yW21lWZOo0uV2H90VUk3s0D37Lka0kukI0QngBVo2Og=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            // Initialize Flatpickr
            flatpickr(".datepicker", {
                dateFormat: "Y-m-d"
            });

            // Toast function
            function showToast(message, isError = false) {
                const toast = $('#toastNotification');
                if (!toast.length) {
                    alert(message);
                    return;
                }
                toast.find('.toast-body').text(message);
                toast.addClass('show ' + (isError ? 'toast-error' : 'toast-success'));
                setTimeout(() => {
                    toast.removeClass('show');
                }, 3000);
            }

            // Copy promo code
            $('.copy-btn').on('click', function() {
                const code = $(this).data('code');
                navigator.clipboard.writeText(code).then(() => {
                    showToast('Promo code copied!');
                }).catch(() => {
                    showToast('Failed to copy code', true);
                });
            });

            // Live timer
            $('.timer').each(function() {
                const endDate = $(this).data('end-date');
                const timerElement = $(this);
                function updateTimer() {
                    const now = new Date();
                    const end = new Date(endDate);
                    const diff = end - now;
                    if (diff <= 0) {
                        timerElement.text('Expired');
                        return;
                    }
                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    timerElement.text(`${days} Days ${hours} Hours ${minutes} Minutes Left`);
                }
                updateTimer();
                setInterval(updateTimer, 60000);
            });

            // Filter and sort
            $('#filter, #sort').on('change', function() {
                const filter = $('#filter').val();
                const sort = $('#sort').val();
                $.post('', { filter, sort }, function() {
                    location.reload();
                });
            });

            // Show success/error toasts
            <?php if (isset($success)) : ?>
                showToast('<?php echo addslashes($success); ?>');
            <?php elseif (isset($error)) : ?>
                showToast('<?php echo addslashes($error); ?>', true);
            <?php endif; ?>
        });
    </script>
</body>
</html>