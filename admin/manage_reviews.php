<?php
session_start();
$pdo = new PDO("mysql:host=127.0.0.1;dbname=auranest_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Handle review status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'], $_POST['status'])) {
    $reviewId = $_POST['review_id'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE reviews SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$status, $reviewId]);
    $success = "Review status updated successfully!";
}

// Fetch all reviews
$stmt = $pdo->query("
    SELECT r.id, r.message, r.rating, r.status, r.created_at, c.name, o.order_id
    FROM reviews r
    JOIN customers c ON r.user_id = c.id
    JOIN orders o ON r.order_id = o.id
    ORDER BY r.created_at DESC
");
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to display stars
// function displayStars($rating) {
//     $fullStars = floor($rating);
//     $halfStar = $rating - $fullStars >= 0.5 ? 1 : 0;
//     $emptyStars = 5 - $fullStars - $halfStar;
//     return str_repeat('<i class="fas fa-star"></i>', $fullStars) .
//            ($halfStar ? '<i class="fas fa-star-half-alt"></i>' : '') .
//            str_repeat('<i class="far fa-star"></i>', $emptyStars);
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - Auranest Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8e1e9, #f5c6cb);
            font-family: 'Roboto', sans-serif;
            color: #333;
        }
        .container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 20px;
        }
        .dashboard-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(8px);
        }
        .dashboard-heading {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 5vw, 2.5rem);
            color: #f5c6cb;
            text-align: center;
            margin-bottom: 30px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
        }
        .table thead {
            background: #f5c6cb;
            color: #fff;
        }
        .table th, .table td {
            padding: 15px;
            vertical-align: middle;
            border: none;
        }
        .table tbody tr {
            transition: background 0.3s ease;
        }
        .table tbody tr:hover {
            background: #fef7f9;
        }
        .btn-soft-pink {
            background: #f5c6cb;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }
        .btn-soft-pink:hover {
            background: #f0a8b0;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .form-select {
            background: #f8e1e9;
            border: 1px solid #f5c6cb;
            color: #333;
            border-radius: 8px;
        }
        .form-select:focus {
            border-color: #f0a8b0;
            box-shadow: 0 0 5px rgba(245, 198, 203, 0.5);
        }
        .review-stars {
            color: #f5c6cb;
            font-size: 14px;
            display: flex;
            gap: 2px;
        }
        .status-pending { color: #f0a8b0; font-weight: 500; }
        .status-approved { color: #28a745; font-weight: 500; }
        .status-rejected { color: #dc3545; font-weight: 500; }
        .alert {
            background: #fef7f9;
            color: #333;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .container { margin: 20px; padding: 15px; }
            .dashboard-heading { font-size: 1.8rem; }
            .table th, .table td { font-size: 0.9rem; padding: 10px; }
            .btn-soft-pink { padding: 6px 15px; }
        }
        @media (max-width: 576px) {
            .dashboard-heading { font-size: 1.5rem; }
            .table { font-size: 0.85rem; }
            .review-stars { font-size: 12px; }
            .form-select, .btn-soft-pink { font-size: 0.9rem; }
        }
    </style>
</head>
<body>
   <?php
session_start();
$pdo = new PDO("mysql:host=127.0.0.1;dbname=auranest_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Handle review status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'], $_POST['status'])) {
    $reviewId = $_POST['review_id'];
    $status = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE reviews SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $reviewId]);
        $success = "Review status updated successfully!";
    } catch (PDOException $e) {
        $error = "Error updating review: " . $e->getMessage();
    }
}

// Fetch all reviews
try {
    $stmt = $pdo->query("
        SELECT r.id, r.message, r.rating, r.status, r.created_at, c.name, o.order_id
        FROM reviews r
        LEFT JOIN customers c ON r.user_id = c.id
        LEFT JOIN orders o ON r.order_id = o.id
        ORDER BY r.created_at DESC
    ");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching reviews: " . $e->getMessage();
    $reviews = [];
}

// Function to display stars
function displayStars($rating) {
    $fullStars = floor($rating);
    $halfStar = $rating - $fullStars >= 0.5 ? 1 : 0;
    $emptyStars = 5 - $fullStars - $halfStar;
    return str_repeat('<i class="fas fa-star"></i>', $fullStars) .
           ($halfStar ? '<i class="fas fa-star-half-alt"></i>' : '') .
           str_repeat('<i class="far fa-star"></i>', $emptyStars);
}
?>

<?php include 'includes/header.php'; ?>

<div class="main-content">
  
    <div class="container">
          <?php include 'includes/sidebar.php'; ?>
        <div class="dashboard-card" style="margin-left: 150px;">
            
            <h2 class="dashboard-heading">Manage Customer Reviews</h2>
            <?php if (isset($success)): ?>
                <div class="alert"><?php echo htmlspecialchars($success); ?></div>
            <?php elseif (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (empty($reviews)): ?>
                <div class="alert alert-info">No reviews found.</div>
            <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Review</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($review['id'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($review['order_id'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($review['name'] ?? 'Unknown'); ?></td>
                        <td><?php echo htmlspecialchars($review['message'] ?? 'No message'); ?></td>
                        <td class="review-stars"><?php echo displayStars($review['rating'] ?? 0); ?></td>
                        <td class="status-<?php echo strtolower($review['status'] ?? 'pending'); ?>">
                            <?php echo htmlspecialchars($review['status'] ?? 'Pending'); ?>
                        </td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="review_id" value="<?php echo $review['id'] ?? ''; ?>">
                                <select name="status" class="form-select">
                                    <option value="pending" <?php echo ($review['status'] ?? '') == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="approved" <?php echo ($review['status'] ?? '') == 'approved' ? 'selected' : ''; ?>>Approved</option>
                                    <option value="rejected" <?php echo ($review['status'] ?? '') == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                </select>
                                <button type="submit" class="btn btn-soft-pink mt-2">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
</html>