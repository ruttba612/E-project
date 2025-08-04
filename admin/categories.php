<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$greeting = date('H') < 12 ? "Good Morning, $admin_name" : (date('H') < 17 ? "Good Afternoon, $admin_name" : "Good Evening, $admin_name");

// Handle category management (add, edit, delete)
$error = null;
$success = null;

// Add category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $slug = strtolower(str_replace(' ', '-', $name));
    $status = $_POST['status'];

    if (empty($name)) {
        $error = "Category name is required.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM categories WHERE slug = ?");
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "Category slug already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO categories (name, description, slug, status, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
            $stmt->bind_param("ssss", $name, $description, $slug, $status);
            if ($stmt->execute()) {
                $success = "Category added successfully.";
            } else {
                $error = "Failed to add category.";
            }
        }
        $stmt->close();
    }
}

// Edit category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_category'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $slug = strtolower(str_replace(' ', '-', $name));
    $status = $_POST['status'];

    if (empty($name)) {
        $error = "Category name is required.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM categories WHERE slug = ? AND id != ?");
        $stmt->bind_param("si", $slug, $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "Category slug already exists.";
        } else {
            $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ?, slug = ?, status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("ssssi", $name, $description, $slug, $status, $id);
            if ($stmt->execute()) {
                $success = "Category updated successfully.";
            } else {
                $error = "Failed to update category.";
            }
        }
        $stmt->close();
    }
}

// Delete category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category'])) {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = "Category deleted successfully. Associated products have been unassigned.";
    } else {
        $error = "Failed to delete category.";
    }
    $stmt->close();
}

// Fetch categories (Jewelry and Beauty Essentials)
$categories_query = "SELECT id, name, description, slug, status FROM categories WHERE id IN (2, 3) ORDER BY name";
$categories_result = $conn->query($categories_query);

// Fetch products for a specific category (if slug is provided)
$slug = $_GET['slug'] ?? '';
$category = null;
$products = [];

if (!empty($slug)) {
    $stmt = $conn->prepare("SELECT id, name, description, slug, status FROM categories WHERE slug = ? AND status = 'active' LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        $category = $result->fetch_assoc();
        $stmt->close();

        if ($category) {
            $stmt = $conn->prepare("SELECT name, slug, description, price, stock, image FROM products WHERE category_id = ? AND status = 'active'");
            if ($stmt) {
                $stmt->bind_param("i", $category['id']);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
                $stmt->close();
            } else {
                error_log("Prepare failed for products: " . $conn->error);
                $error = "Failed to fetch products.";
            }
        }
    } else {
        error_log("Prepare failed for category: " . $conn->error);
        $error = "Failed to fetch category.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $category ? htmlspecialchars($category['name']) : 'Manage Categories'; ?> - Auranest Admin</title>
    <meta name="description" content="<?php echo $category ? htmlspecialchars($category['description'] ?: 'Manage ' . $category['name'] . ' category at Auranest.') : 'Manage fashion categories at Auranest.'; ?>">
    <meta name="keywords" content="fashion, auranest, categories, jewelry, beauty essentials">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous">
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
        .product-card {
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .product-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #FFCCD5;
        }
        .btn-custom {
            padding: 6px 12px;
            border-radius: 8px;
        }
        .btn-submit {
            background: #D4AF37;
            color: #FFF5F5;
            border: 1px solid #B89B2E;
        }
        .btn-edit {
            background: #FF6F61;
            color: #FFF5F5;
            border: 1px solid #E65A4E;
        }
        .btn-delete {
            background: #E65A4E;
            color: #FFF5F5;
            border: 1px solid #D43F32;
        }
        .btn-view {
            background: #D4AF37;
            color: #FFF5F5;
            border: 1px solid #B89B2E;
            border-radius: 8px;
            padding: 6px 12px;
            text-decoration: none;
        }
        .btn-view:hover, .btn-submit:hover, .btn-edit:hover, .btn-delete:hover {
            opacity: 0.9;
            color: #FFF5F5;
            text-decoration: none;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #FFCCD5;
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
        .not-found {
            color: #FF6F61;
            text-align: center;
            font-weight: 500;
        }
        table {
            background: #FFF5F5;
        }
        th, td {
            border: 1px solid #FFCCD5;
        }
        @media (max-width: 576px) {
            h1 { font-size: 1.5rem; }
            .product-img { height: 150px; }
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
                    <h1 class="mt-4 mb-4">Manage Categories</h1>
                    <?php if (isset($error) || isset($success)): ?>
                        <div class="toast <?php echo isset($success) ? 'toast-success' : 'toast-error'; ?> show" id="toastNotification">
                            <div class="toast-body"><?php echo htmlspecialchars($success ?? $error); ?></div>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold">Add New Category</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="">
                                        <div class="form-group">
                                            <label for="name">Category Name</label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <button type="submit" name="add_category" class="btn btn-custom btn-submit">Add Category</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold">Category List</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($categories_result && $categories_result->num_rows > 0): ?>
                                                <?php while ($cat = $categories_result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                                        <td><?php echo htmlspecialchars($cat['status']); ?></td>
                                                        <td>
                                                            <button class="btn btn-custom btn-edit" data-toggle="modal" data-target="#editModal<?php echo $cat['id']; ?>">Edit</button>
                                                            <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this category? Associated products will be unassigned.');">
                                                                <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                                                <button type="submit" name="delete_category" class="btn btn-custom btn-delete">Delete</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    <!-- Edit Modal -->
                                                    <div class="modal fade" id="editModal<?php echo $cat['id']; ?>" tabindex="-1" role="dialog">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header" style="background: linear-gradient(90deg, #FFCCD5, #FFE4E1);">
                                                                    <h5 class="modal-title">Edit Category</h5>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form method="POST" action="">
                                                                        <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                                                        <div class="form-group">
                                                                            <label for="name_<?php echo $cat['id']; ?>">Category Name</label>
                                                                            <input type="text" class="form-control" id="name_<?php echo $cat['id']; ?>" name="name" value="<?php echo htmlspecialchars($cat['name']); ?>" required>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="description_<?php echo $cat['id']; ?>">Description</label>
                                                                            <textarea class="form-control" id="description_<?php echo $cat['id']; ?>" name="description" rows="4"><?php echo htmlspecialchars($cat['description']); ?></textarea>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="status_<?php echo $cat['id']; ?>">Status</label>
                                                                            <select class="form-control" id="status_<?php echo $cat['id']; ?>" name="status">
                                                                                <option value="active" <?php echo $cat['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                                                                <option value="inactive" <?php echo $cat['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                                            </select>
                                                                        </div>
                                                                        <button type="submit" name="edit_category" class="btn btn-custom btn-submit">Save Changes</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr><td colspan="3">No categories found.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if ($category): ?>
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold">Products in <?php echo htmlspecialchars($category['name']); ?></h6>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($products)): ?>
                                    <div class="row">
                                        <?php foreach ($products as $product): ?>
                                            <div class="col-lg-4 col-md-6 mb-4">
                                                <div class="card product-card">
                                                    <img src="<?php echo htmlspecialchars($product['image'] ?: 'images/placeholder.jpg'); ?>" class="product-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                                        <p class="card-text"><?php echo htmlspecialchars(strip_tags($product['description'])); ?></p>
                                                        <p class="card-text"><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
                                                        <p class="card-text"><strong>Stock:</strong> <?php echo htmlspecialchars($product['stock']); ?></p>
                                                        <a href="product.php?slug=<?php echo htmlspecialchars($product['slug']); ?>" class="btn btn-view">View Product</a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="not-found">No products found in this category.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js" integrity="sha384-LtrjvnR4Twt/qOuYxE721u19sVFLVSA4hf/rRt6PrZTmiPltdZcI7q7PXQBYTKyf" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            <?php if (isset($error) || isset($success)): ?>
                $('#toastNotification').addClass('show');
                setTimeout(() => $('#toastNotification').removeClass('show'), 3000);
            <?php endif; ?>
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>