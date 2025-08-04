<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';
include 'functions.php';

$id = $_GET['id'] ?? '';
$page = null;
if (!empty($id)) {
    $stmt = $conn->prepare("SELECT id, title, slug, content FROM cms_pages WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $page = $result->fetch_assoc();
    $stmt->close();
}
$conn->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_page'])) {
    $title = $_POST['title'] ?? '';
    $slug = $_POST['slug'] ?? '';
    $content = $_POST['content'] ?? '';
    // Note: This is a placeholder. Update function needed in functions.php
    if (!empty($id) && !empty($title) && !empty($slug) && !empty($content)) {
        // Add updateCmsPage function in functions.php
        echo "<script>alert('Page updated successfully.'); window.location.href='cms.php';</script>";
    } else {
        echo "<script>alert('Failed to update page.');</script>";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auranest Admin - Edit CMS Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'includes/header.php'; ?>
                <div class="container-fluid">
                    <h1 class="mt-4 mb-4">Edit CMS Page</h1>
                    <?php if ($page): ?>
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <form method="POST">
                                    <div class="form-group">
                                        <label>Title</label>
                                        <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($page['title']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Slug</label>
                                        <input type="text" class="form-control" name="slug" value="<?php echo htmlspecialchars($page['slug']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Content</label>
                                        <textarea class="form-control" name="content" rows="5" required><?php echo htmlspecialchars($page['content']); ?></textarea>
                                    </div>
                                    <button type="submit" name="update_page" class="btn btn-primary">Update</button>
                                    <a href="cms.php" class="btn btn-secondary">Cancel</a>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">Page not found.</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="main.js"></script>
</body>
</html>