<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('fpdf/fpdf.php');
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db.php';
include 'functions.php';

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$greeting = date('H') < 12 ? "Good Morning, $admin_name" : (date('H') < 17 ? "Good Afternoon, $admin_name" : "Good Evening, $admin_name");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        $name = $_POST['name'] ?? '';
        $category_id = $_POST['category_id'] ?? 0;
        $price = $_POST['price'] ?? 0;
        $stock = $_POST['stock'] ?? 0;
        $description = $_POST['description'] ?? '';
        $status = $_POST['status'] ?? 'active';
        $image = $_FILES['image']['name'] ?? '';
        $target_dir = "Uploads/";
        $target_file = $target_dir . basename($image);
        $valid_categories = [2, 3];
        if (in_array($category_id, $valid_categories)) {
            if ($image && move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                addProduct($name, $category_id, $price, $stock, $description, $status, $image);
            } else {
                addProduct($name, $category_id, $price, $stock, $description, $status, null);
            }
            header("Location: products.php");
            exit();
        }
    } elseif (isset($_POST['edit_product'])) {
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $category_id = $_POST['category_id'] ?? 0;
        $price = $_POST['price'] ?? 0;
        $stock = $_POST['stock'] ?? 0;
        $description = $_POST['description'] ?? '';
        $status = $_POST['status'] ?? 'active';
        $image = $_FILES['image']['name'] ?? '';
        $target_dir = "Uploads/";
        $target_file = $target_dir . basename($image);
        $valid_categories = [2, 3];
        if (in_array($category_id, $valid_categories)) {
            if ($image && move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                editProduct($id, $name, $category_id, $price, $stock, $description, $status, $image);
            } else {
                editProduct($id, $name, $category_id, $price, $stock, $description, $status, null);
            }
            header("Location: products.php");
            exit();
        }
    } elseif (isset($_POST['delete_product'])) {
        $id = $_POST['id'] ?? 0;
        deleteProduct($id);
        header("Location: products.php");
        exit();
    }
}

// Handle PDF export
if (isset($_GET['export_pdf'])) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Products List', 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    // Set column widths
    $colWidths = [20, 40, 30, 20, 20, 50, 20];
    $pdf->Cell($colWidths[0], 10, 'ID', 1, 0, 'C');
    $pdf->Cell($colWidths[1], 10, 'Name', 1, 0, 'C');
    $pdf->Cell($colWidths[2], 10, 'Category', 1, 0, 'C');
    $pdf->Cell($colWidths[3], 10, 'Price', 1, 0, 'C');
    $pdf->Cell($colWidths[4], 10, 'Stock', 1, 0, 'C');
    $pdf->Cell($colWidths[5], 10, 'Description', 1, 0, 'C');
    $pdf->Cell($colWidths[6], 10, 'Status', 1, 0, 'C');
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 10);
    $result = $conn->query("SELECT p.id, p.name, c.name AS category, p.price, p.stock, p.description, p.status 
                            FROM products p 
                            JOIN categories c ON p.category_id = c.id 
                            WHERE p.category_id IN (2, 3)");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Handle long text in Description
            $desc = strlen($row['description']) > 30 ? substr($row['description'], 0, 27) . '...' : $row['description'];
            $pdf->Cell($colWidths[0], 10, $row['id'], 1, 0, 'C');
            $pdf->Cell($colWidths[1], 10, $row['name'], 1, 0, 'L');
            $pdf->Cell($colWidths[2], 10, $row['category'], 1, 0, 'L');
            $pdf->Cell($colWidths[3], 10, '$' . number_format($row['price'], 2), 1, 0, 'R');
            $pdf->Cell($colWidths[4], 10, $row['stock'], 1, 0, 'C');
            $pdf->Cell($colWidths[5], 10, $desc, 1, 0, 'L');
            $pdf->Cell($colWidths[6], 10, $row['status'], 1, 0, 'C');
            $pdf->Ln();
        }
    }
    $pdf->Output('D', 'products_list.pdf');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auranest Admin - Products</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            font-family: 'Inter', 'Lora', sans-serif;
            background-color: #FFF5F5;
            color: #2C2C2C;
            line-height: 1.6;
            overflow-x: hidden;
        }
        .container-fluid {
            padding: 20px;
        }
        h1 {
            font-size: 2rem;
            font-weight: 600;
            color: #2C2C2C;
            margin-bottom: 20px;
        }
        /* Card Styles */
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
        /* Table Styles */
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
        .table th,
        .table td {
            padding: 12px;
            vertical-align: middle;
            text-align: center;
            overflow-wrap: break-word;
            min-width: 100px;
        }
        .table th {
            background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
            color: #2C2C2C;
            font-weight: 600;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        .table td {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background: #FFF5F5;
        }
        .table tr:nth-child(even) td {
            background: #FFE4E1;
        }
        .table tr:hover td {
            background: rgba(255, 204, 213, 0.3);
            transition: background 0.3s ease;
        }
        .table td img {
            max-width: 50px;
            border-radius: 4px;
            object-fit: cover;
        }
        /* Button Styles */
        .btn-custom {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
            margin-right: 8px;
            margin-bottom: 8px; /* Added for spacing between buttons */
        }
        .btn-edit {
            background: #D4AF37;
            color: #FFF5F5;
            border: 1px solid #B89B2E;
        }
        .btn-edit:hover {
            background: #C19B2F;
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .btn-delete {
            background: #FF6F61;
            color: #FFF5F5;
            border: 1px solid #E65A4E;
        }
        .btn-delete:hover {
            background: #E65A4E;
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .btn-add {
            background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
            color: #2C2C2C;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-add:hover {
            background: linear-gradient(90deg, #FFE4E1, #FFCCD5);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        /* Modal Styles */
        .modal-content {
            border-radius: 12px;
            background: #FFF5F5;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .modal-header {
            background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
            color: #2C2C2C;
            border-bottom: none;
            border-radius: 12px 12px 0 0;
            padding: 15px 20px;
        }
        .modal-title {
            font-size: 1.2rem;
            font-weight: 600;
        }
        .modal-body {
            padding: 20px;
        }
        .form-group label {
            font-weight: 500;
            color: #2C2C2C;
            margin-bottom: 8px;
        }
        .form-control,
        .form-control-file {
            border-radius: 8px;
            border: 1px solid #FFCCD5;
            background: #FFF5F5;
            padding: 8px;
            transition: border-color 0.3s ease;
        }
        .form-control:focus {
            border-color: #D4AF37;
            box-shadow: 0 0 5px rgba(212, 175, 55, 0.3);
            outline: none;
        }
        .form-control-file {
            padding: 6px;
        }
        .modal-footer {
            border-top: none;
            padding: 15px 20px;
        }
        /* Delete Confirmation Modal */
        #deleteConfirmModal .modal-content {
            max-width: 400px;
            margin: 0 auto;
        }
        #deleteConfirmModal .modal-body {
            text-align: center;
            font-size: 1rem;
            color: #2C2C2C;
        }
        #deleteConfirmModal .btn-confirm {
            background: #FF6F61;
            color: #FFF5F5;
            border-radius: 8px;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }
        #deleteConfirmModal .btn-confirm:hover {
            background: #E65A4E;
            transform: scale(1.05);
        }
        #deleteConfirmModal .btn-cancel {
            background: #D4AF37;
            color: #FFF5F5;
            border-radius: 8px;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }
        #deleteConfirmModal .btn-cancel:hover {
            background: #C19B2F;
            transform: scale(1.05);
        }
        /* Search and Filter */
        .search-filter {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .search-filter input,
        .search-filter select {
            max-width: 200px;
            border-radius: 8px;
            border: 1px solid #FFCCD5;
            padding: 8px;
            background: #FFF5F5;
        }
        .search-filter input:focus,
        .search-filter select:focus {
            border-color: #D4AF37;
            box-shadow: 0 0 5px rgba(212, 175, 55, 0.3);
            outline: none;
        }
        /* Toast Notification */
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
            color: #2C2C2C;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-10px); }
        }
        /* Responsive Styles */
        @media (max-width: 768px) {
            h1 {
                font-size: 1.8rem;
            }
            .table th,
            .table td {
                font-size: 0.9rem;
                padding: 10px;
                min-width: 80px;
            }
            .btn-custom {
                padding: 5px 10px;
                font-size: 0.85rem;
                margin-right: 5px;
            }
            .search-filter input,
            .search-filter select {
                max-width: 150px;
            }
        }
        @media (max-width: 576px) {
            h1 {
                font-size: 1.5rem;
            }
            .table th,
            .table td {
                font-size: 0.85rem;
                padding: 8px;
                min-width: 60px;
            }
            .btn-custom {
                padding: 4px 8px;
                font-size: 0.8rem;
                margin-right: 4px;
            }
            .search-filter {
                flex-direction: column;
                gap: 10px;
            }
            .search-filter input,
            .search-filter select {
                max-width: 100%;
            }
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
                    <h1 class="mt-4 mb-4">Products Management</h1>
                    <div class="search-filter">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by name...">
                        <select id="categoryFilter" class="form-control">
                            <option value="">All Categories</option>
                            <?php
                            $conn = new mysqli('localhost', 'root', '', 'auranest_db');
                            if ($conn->connect_error) {
                                echo "<option value=''>Database error: " . htmlspecialchars($conn->connect_error) . "</option>";
                            } else {
                                $result = $conn->query("SELECT id, name FROM categories WHERE id IN (2, 3)");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['id']}'>" . htmlspecialchars($row['name']) . "</option>";
                                }
                                $conn->close();
                            }
                            ?>
                        </select>
                        <button class="btn btn-add" onclick="exportPDF()">Download PDF</button>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold">All Products</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="productsTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Image</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $conn = new mysqli('localhost', 'root', '', 'auranest_db');
                                        if ($conn->connect_error) {
                                            echo "<tr><td colspan='9'>Database error: " . htmlspecialchars($conn->connect_error) . "</td></tr>";
                                        } else {
                                            $result = $conn->query("SELECT p.id, p.name, c.name AS category, p.price, p.stock, p.description, p.status, p.image, p.category_id 
                                                                    FROM products p 
                                                                    JOIN categories c ON p.category_id = c.id 
                                                                    WHERE p.category_id IN (2, 3)");
                                            if ($result) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td>{$row['id']}</td>";
                                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                                                    echo "<td>$" . number_format($row['price'], 2) . "</td>";
                                                    echo "<td>{$row['stock']}</td>";
                                                    echo "<td style='max-width: 150px; overflow-wrap: break-word;'>" . htmlspecialchars($row['description']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                                    echo "<td>" . ($row['image'] && file_exists("Uploads/{$row['image']}") ? "<img src='Uploads/" . htmlspecialchars($row['image']) . "' alt='Product' width='50'>" : "No Image") . "</td>";
                                                    echo "<td>";
                                                    echo "<button class='btn btn-custom btn-edit' data-id='{$row['id']}' data-name='" . htmlspecialchars($row['name']) . "' data-category_id='{$row['category_id']}' data-price='{$row['price']}' data-stock='{$row['stock']}' data-description='" . htmlspecialchars($row['description']) . "' data-status='{$row['status']}'><i class='fas fa-edit'></i> Edit</button>";
                                                    echo "<button class='btn btn-custom btn-delete' data-id='{$row['id']}' data-toggle='modal' data-target='#deleteConfirmModal'><i class='fas fa-trash'></i> Delete</button>";
                                                    echo "</td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='9'>No products found: " . htmlspecialchars($conn->error) . "</td></tr>";
                                            }
                                            $conn->close();
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <button class="btn btn-add mt-3" data-toggle="modal" data-target="#addProductModal">Add New Product</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

   <?php
$conn = new mysqli("localhost", "root", "", "auranest_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Insert/Update Form Submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'] ?? '';
    $name = $conn->real_escape_string($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $description = $conn->real_escape_string($_POST['description']);
    $status = $conn->real_escape_string($_POST['status']);
    $existingImage = $_POST['existing_image'] ?? '';

    // Handle Image Upload
    $uploadDir = "../uploads/";
    $imageName = $_FILES['image']['name'] ?? '';
    $uploadedFile = '';

    if (!empty($imageName)) {
        $uploadedFile = uniqid() . "_" . basename($imageName);
        $targetPath = $uploadDir . $uploadedFile;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
    } else {
        $uploadedFile = $existingImage;
    }

    // Decide Insert or Update
    if (isset($_POST['add_product'])) {
        $sql = "INSERT INTO products (name, category_id, price, stock, description, status, image)
                VALUES ('$name', '$category_id', '$price', '$stock', '$description', '$status', '$uploadedFile')";
        $msg = "Product added successfully!";
    } elseif (isset($_POST['edit_product'])) {
        $sql = "UPDATE products SET 
                name='$name', category_id='$category_id', price='$price', stock='$stock', 
                description='$description', status='$status', image='$uploadedFile' 
                WHERE id='$id'";
        $msg = "Product updated successfully!";
    }

    if (isset($sql) && $conn->query($sql)) {
        echo "<script>alert('$msg'); window.location.href='manage_products.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error: " . $conn->error . "'); window.history.back();</script>";
        exit();
    }
}
?>

<!-- Add/Edit Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Add Product</h5>
        <button class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <form method="POST" enctype="multipart/form-data" id="productForm">
          <input type="hidden" name="id" id="edit-id">

          <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" id="edit-name" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Category</label>
            <select name="category_id" id="edit-category_id" class="form-control" required>
              <?php
              $catConn = new mysqli('localhost', 'root', '', 'auranest_db');
              if ($catConn->connect_error) {
                  echo "<option value=''>Database error</option>";
              } else {
                  $res = $catConn->query("SELECT id, name FROM categories WHERE id IN (2, 3)");
                  while ($row = $res->fetch_assoc()) {
                      echo "<option value='{$row['id']}'>" . htmlspecialchars($row['name']) . "</option>";
                  }
                  $catConn->close();
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label>Price</label>
            <input type="number" name="price" id="edit-price" class="form-control" step="0.01" min="0" required>
          </div>

          <div class="form-group">
            <label>Stock</label>
            <input type="number" name="stock" id="edit-stock" class="form-control" min="0" required>
          </div>

          <div class="form-group">
            <label>Description</label>
            <textarea name="description" id="edit-description" class="form-control" rows="4" required></textarea>
          </div>

          <div class="form-group">
            <label>Status</label>
            <select name="status" id="edit-status" class="form-control" required>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div class="form-group">
            <label>Image</label>
            <input type="file" name="image" id="edit-image" class="form-control-file" accept="image/*">
            <input type="hidden" name="existing_image" id="existing_image">
            <div id="imagePreview" style="margin-top: 10px;"></div>
          </div>

          <button type="submit" name="add_product" class="btn btn-custom btn-add">Add Product</button>
          <button type="submit" name="edit_product" id="edit-submit" class="btn btn-custom btn-add" style="display:none;">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Delete</h5>
        <button class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this product? This action cannot be undone.
      </div>
      <div class="modal-footer">
        <button class="btn btn-cancel" data-dismiss="modal">Cancel</button>
        <button class="btn btn-confirm" id="confirmDelete">Yes, Delete</button>
      </div>
    </div>
  </div>
</div>


    <!-- Toast Notification -->
    <div class="toast" id="toastNotification">
        <div class="toast-body">Action successful!</div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="main.js"></script>
    <script>
        console.log("JavaScript is running!");
        // Edit Button Handler
        document.querySelectorAll('.btn-edit').forEach(button => {
            console.log("Edit button found:", button.getAttribute('data-id'));
            button.addEventListener('click', function() {
                console.log("Edit button clicked, ID:", this.getAttribute('data-id'));
                document.getElementById('edit-id').value = this.getAttribute('data-id');
                document.getElementById('edit-name').value = this.getAttribute('data-name');
                document.getElementById('edit-category_id').value = this.getAttribute('data-category_id');
                document.getElementById('edit-price').value = this.getAttribute('data-price');
                document.getElementById('edit-stock').value = this.getAttribute('data-stock');
                document.getElementById('edit-description').value = this.getAttribute('data-description');
                document.getElementById('edit-status').value = this.getAttribute('data-status');
                document.getElementById('modalTitle').textContent = 'Edit Product';
                document.querySelector('button[name="add_product"]').style.display = 'none';
                document.getElementById('edit-submit').style.display = 'block';
                document.getElementById('imagePreview').innerHTML = '';
                try {
                    $('#addProductModal').modal('show');
                    console.log("Add/Edit modal opened");
                } catch (e) {
                    console.error("Error opening modal:", e.message);
                }
            });
        });

        // Reset Modal on Close
        $('#addProductModal').on('hidden.bs.modal', function() {
            console.log("Add/Edit modal closed");
            document.getElementById('modalTitle').textContent = 'Add Product';
            document.querySelector('button[name="add_product"]').style.display = 'block';
            document.getElementById('edit-submit').style.display = 'none';
            document.getElementById('edit-id').value = '';
            document.getElementById('edit-name').value = '';
            document.getElementById('edit-category_id').value = '2';
            document.getElementById('edit-price').value = '';
            document.getElementById('edit-stock').value = '';
            document.getElementById('edit-description').value = '';
            document.getElementById('edit-status').value = 'active';
            document.getElementById('edit-image').value = '';
            document.getElementById('imagePreview').innerHTML = '';
        });

        // Delete Confirmation Handler
        let deleteProductId = null;
        document.querySelectorAll('.btn-delete').forEach(button => {
            console.log("Delete button found:", button.getAttribute('data-id'));
            button.addEventListener('click', function() {
                deleteProductId = this.getAttribute('data-id');
                console.log("Delete button clicked, ID:", deleteProductId);
                try {
                    $('#deleteConfirmModal').modal('show');
                    console.log("Delete modal opened");
                } catch (e) {
                    console.error("Error opening delete modal:", e.message);
                }
            });
        });

        document.getElementById('confirmDelete').addEventListener('click', function() {
            console.log("Confirm Delete clicked, ID:", deleteProductId);
            if (deleteProductId) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `<input type="hidden" name="id" value="${deleteProductId}">
                                <input type="hidden" name="delete_product" value="true">`;
                document.body.appendChild(form);
                form.submit();
                console.log("Delete form submitted");
            }
        });

        // Image Preview
        document.getElementById('edit-image').addEventListener('change', function(e) {
            console.log("Image input changed");
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '100px';
                    img.style.borderRadius = '4px';
                    img.style.marginTop = '10px';
                    preview.appendChild(img);
                    console.log("Image preview loaded");
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        // Search and Filter
        document.getElementById('searchInput').addEventListener('input', function() {
            console.log("Search input:", this.value);
            const searchTerm = this.value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value;
            const rows = document.querySelectorAll('#productsTable tbody tr');
            rows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const categoryId = row.querySelector('.btn-edit').getAttribute('data-category_id');
                const matchesSearch = name.includes(searchTerm);
                const matchesCategory = !categoryFilter || categoryId === categoryFilter;
                row.style.display = matchesSearch && matchesCategory ? '' : 'none';
            });
        });

        document.getElementById('categoryFilter').addEventListener('change', function() {
            console.log("Category filter changed:", this.value);
            document.getElementById('searchInput').dispatchEvent(new Event('input'));
        });

        // PDF Export
        function exportPDF() {
            console.log("PDF export triggered");
            window.location.href = '?export_pdf=true';
        }

        // Toast Notification
        function showToast(message) {
            console.log("Showing toast:", message);
            const toast = document.getElementById('toastNotification');
            toast.querySelector('.toast-body').textContent = message;
            toast.classList.add('show', 'toast-success');
            setTimeout(() => {
                toast.classList.remove('show');
                console.log("Toast hidden");
            }, 3000);
        }

        // Show toast on successful actions
        <?php if (isset($_POST['add_product']) || isset($_POST['edit_product']) || isset($_POST['delete_product'])): ?>
            showToast('Action completed successfully!');
        <?php endif; ?>
    </script>
</body>
</html>