<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$greeting = date('H') < 12 ? "Good Morning, $admin_name" : (date('H') < 17 ? "Good Afternoon, $admin_name" : "Good Evening, $admin_name");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['ajax'])) {
    if (isset($_POST['add_page'])) {
        $title = $_POST['title'] ?? '';
        $slug = $_POST['slug'] ?? '';
        $content = $_POST['content'] ?? '';
        $meta_title = $_POST['meta_title'] ?? '';
        $meta_description = $_POST['meta_description'] ?? '';
        $meta_keywords = $_POST['meta_keywords'] ?? '';
        $status = $_POST['status'] ?? 'draft';

        $stmt = $conn->prepare("INSERT INTO cms_pages (title, slug, content, meta_title, meta_description, meta_keywords, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            $error = "Failed to prepare query";
        } else {
            $stmt->bind_param("sssssss", $title, $slug, $content, $meta_title, $meta_description, $meta_keywords, $status);
            if ($stmt->execute()) {
                $page_id = $conn->insert_id;
                $stmt_version = $conn->prepare("INSERT INTO cms_page_versions (page_id, title, slug, content, meta_title, meta_description, meta_keywords, status, version_timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt_version->bind_param("isssssss", $page_id, $title, $slug, $content, $meta_title, $meta_description, $meta_keywords, $status);
                $stmt_version->execute();
                $stmt_version->close();
                $success = "Page added successfully!";
            } else {
                error_log("Execute failed: " . $stmt->error);
                $error = "Failed to add page";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['update_page'])) {
        $id = (int)($_POST['id'] ?? 0);
        $title = $_POST['title'] ?? '';
        $slug = $_POST['slug'] ?? '';
        $content = $_POST['content'] ?? '';
        $meta_title = $_POST['meta_title'] ?? '';
        $meta_description = $_POST['meta_description'] ?? '';
        $meta_keywords = $_POST['meta_keywords'] ?? '';
        $status = $_POST['status'] ?? 'draft';

        $stmt = $conn->prepare("UPDATE cms_pages SET title = ?, slug = ?, content = ?, meta_title = ?, meta_description = ?, meta_keywords = ?, status = ?, updated_at = NOW() WHERE id = ?");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            $error = "Failed to prepare update query";
        } else {
            $stmt->bind_param("sssssssi", $title, $slug, $content, $meta_title, $meta_description, $meta_keywords, $status, $id);
            if ($stmt->execute()) {
                $stmt_version = $conn->prepare("INSERT INTO cms_page_versions (page_id, title, slug, content, meta_title, meta_description, meta_keywords, status, version_timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt_version->bind_param("isssssss", $id, $title, $slug, $content, $meta_title, $meta_description, $meta_keywords, $status);
                $stmt_version->execute();
                $stmt_version->close();
                $success = "Page updated successfully!";
            } else {
                error_log("Execute failed: " . $stmt->error);
                $error = "Failed to update page";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['delete_page'])) {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $conn->prepare("UPDATE cms_pages SET status = 'trashed' WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $success = "Page moved to trash!";
        } else {
            $error = "Failed to move page to trash";
        }
        $stmt->close();
    } elseif (isset($_POST['restore_page'])) {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $conn->prepare("UPDATE cms_pages SET status = 'draft' WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $success = "Page restored successfully!";
        } else {
            $error = "Failed to restore page";
        }
        $stmt->close();
    } elseif (isset($_POST['permanent_delete'])) {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM cms_pages WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $conn->query("DELETE FROM cms_page_versions WHERE page_id = $id");
            $success = "Page permanently deleted!";
        } else {
            $error = "Failed to delete page";
        }
        $stmt->close();
    } elseif (isset($_POST['restore_version'])) {
        $version_id = (int)($_POST['version_id'] ?? 0);
        $stmt = $conn->prepare("SELECT page_id, title, slug, content, meta_title, meta_description, meta_keywords, status FROM cms_page_versions WHERE id = ?");
        $stmt->bind_param("i", $version_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($version = $result->fetch_assoc()) {
            $stmt_update = $conn->prepare("UPDATE cms_pages SET title = ?, slug = ?, content = ?, meta_title = ?, meta_description = ?, meta_keywords = ?, status = ?, updated_at = NOW() WHERE id = ?");
            $stmt_update->bind_param("sssssssi", $version['title'], $version['slug'], $version['content'], $version['meta_title'], $version['meta_description'], $version['meta_keywords'], $version['status'], $version['page_id']);
            if ($stmt_update->execute()) {
                $success = "Version restored successfully!";
            } else {
                $error = "Failed to restore version";
            }
            $stmt_update->close();
        }
        $stmt->close();
    }
}

// Handle AJAX search
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    $filter = $_POST['filter'] ?? 'all';
    $sort = $_POST['sort'] ?? 'title ASC';
    $search = $_POST['search'] ?? '';

    $query = "SELECT id, title, slug, status, created_at, updated_at FROM cms_pages";
    $params = [];
    $types = '';

    if ($filter !== 'all') {
        $query .= " WHERE status = ?";
        $params[] = $filter;
        $types .= 's';
    }
    if ($search) {
        $query .= ($filter !== 'all' ? ' AND' : ' WHERE') . " (title LIKE ? OR slug LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types .= 'ss';
    }
    $query .= " ORDER BY $sort";

    $stmt = $conn->prepare($query);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $pages = [];
    while ($row = $result->fetch_assoc()) {
        $pages[] = $row;
    }
    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode(['pages' => $pages]);
    exit();
}

// Fetch pages for initial load
$filter = $_POST['filter'] ?? 'all';
$sort = $_POST['sort'] ?? 'title ASC';
$search = $_POST['search'] ?? '';
$query = "SELECT id, title, slug, status, created_at, updated_at FROM cms_pages";
$params = [];
$types = '';

if ($filter !== 'all') {
    $query .= " WHERE status = ?";
    $params[] = $filter;
    $types .= 's';
}
if ($search) {
    $query .= ($filter !== 'all' ? ' AND' : ' WHERE') . " (title LIKE ? OR slug LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}
$query .= " ORDER BY $sort";

$stmt = $conn->prepare($query);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auranest Admin - CMS Pages</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" integrity="sha512-SnH5WK+bZxgPHpS2G0V8z0D4g7g3U0C0z0C0z0C0z0C0z0C0z0C0z0C0z0C0z0C0z0C0z0C0z0C0z0C0z0C0z" crossorigin="anonymous">
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js" integrity="sha512-vwXOpn2uL2B3rF+8pS4JPH9r0MnO+0S+2Q0y6Ff+DKT8r1gq2jE7y0fT+1g0z0C0z0C0z0C0z0C0z0C0z0C0z0C" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: 'Inter', 'Lora', sans-serif;
            background: linear-gradient(to bottom, #FFF5F5, #FFE4E1);
            color: #2C2C2C;
            min-height: 100vh;
        }
        .container-fluid {
            max-width: 1400px;
            padding: 2rem;
        }
        .card {
            background: #FFFFFF;
            border-radius: 1rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            border: none;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
            color: #2C2C2C;
            border-radius: 1rem 1rem 0 0;
            padding: 1.5rem;
            font-weight: 600;
            text-align: center;
        }
        .nav-tabs {
            border-bottom: 2px solid #FFCCD5;
        }
        .nav-tabs .nav-link {
            color: #2C2C2C;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem 0.5rem 0 0;
            transition: background 0.3s ease;
        }
        .nav-tabs .nav-link.active {
            background: #D4AF37;
            color: #FFF5F5;
            border-color: #D4AF37;
        }
        .nav-tabs .nav-link:hover {
            background: #FFB6C1;
        }
        .btn-custom {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
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
        .filter-section {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .filter-section input, .filter-section select {
            max-width: 250px;
            border-radius: 0.5rem;
            border: 1px solid #FFCCD5;
            padding: 0.5rem;
            transition: border-color 0.3s ease;
        }
        .filter-section input:focus, .filter-section select:focus {
            border-color: #D4AF37;
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
        }
        .preview-card {
            background: #FFE4E1;
            border-radius: 0.75rem;
            padding: 1rem;
            border: 1px solid #FFCCD5;
            transition: box-shadow 0.3s ease;
        }
        .preview-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .preview-card h5 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2C2C2C;
        }
        .toast {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            min-width: 300px;
            background: #FFFFFF;
            border: 1px solid #FFCCD5;
            border-radius: 0.5rem;
            padding: 1rem;
            z-index: 2000;
            display: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            .filter-section { flex-direction: column; gap: 0.75rem; }
            .filter-section input, .filter-section select { max-width: 100%; }
            .preview-card { width: 100%; }
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
                    <h1 class="mt-4 mb-4 text-center fw-bold">CMS Pages Management</h1>
                    <ul class="nav nav-tabs mb-4">
                        <li class="nav-item"><a class="nav-link" href="categories.php">Categories</a></li>
                        <li class="nav-item"><a class="nav-link" href="manage_reviews.php">Manage_reviews</a></li>
                        <li class="nav-item"><a class="nav-link" href="settings.php">Settings</a></li>
                        <li class="nav-item"><a class="nav-link active" href="cms.php">CMS Pages</a></li>
                    </ul>
                    <?php if (isset($success)) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php elseif (isset($error)) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-xl-8 col-lg-7 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold">All CMS Pages</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="nav nav-tabs mb-3">
                                        <li class="nav-item"><a class="nav-link <?php echo $filter === 'all' || $filter === 'published' || $filter === 'draft' ? 'active' : ''; ?>" href="#all" data-bs-toggle="tab">All</a></li>
                                        <li class="nav-item"><a class="nav-link <?php echo $filter === 'trashed' ? 'active' : ''; ?>" href="#trash" data-bs-toggle="tab">Trash</a></li>
                                    </ul>
                                    <div class="filter-section">
                                        <input type="text" id="search" class="form-control" placeholder="Search by Title or Slug" value="<?php echo htmlspecialchars($search); ?>">
                                        <select id="filter" class="form-control">
                                            <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All</option>
                                            <option value="published" <?php echo $filter === 'published' ? 'selected' : ''; ?>>Published</option>
                                            <option value="draft" <?php echo $filter === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                            <option value="trashed" <?php echo $filter === 'trashed' ? 'selected' : ''; ?>>Trashed</option>
                                        </select>
                                        <select id="sort" class="form-control">
                                            <option value="title ASC" <?php echo $sort === 'title ASC' ? 'selected' : ''; ?>>Title (A-Z)</option>
                                            <option value="title DESC" <?php echo $sort === 'title DESC' ? 'selected' : ''; ?>>Title (Z-A)</option>
                                            <option value="created_at DESC" <?php echo $sort === 'created_at DESC' ? 'selected' : ''; ?>>Date (Newest)</option>
                                            <option value="created_at ASC" <?php echo $sort === 'created_at ASC' ? 'selected' : ''; ?>>Date (Oldest)</option>
                                            <option value="status ASC" <?php echo $sort === 'status ASC' ? 'selected' : ''; ?>>Status</option>
                                        </select>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="cmsPagesTable">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Title</th>
                                                    <th>Slug</th>
                                                    <th>Status</th>
                                                    <th>Created On</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="pagesTableBody">
                                                <?php while ($row = $result->fetch_assoc()) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['slug']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                                        <td>
                                                            <a href="?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info btn-custom">Edit</a>
                                                            <a href="../page.php?slug=<?php echo htmlspecialchars($row['slug']); ?>" target="_blank" class="btn btn-sm btn-primary btn-custom">View</a>
                                                            <form method="POST" style="display:inline;">
                                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                                <?php if ($row['status'] === 'trashed') : ?>
                                                                    <button type="submit" name="restore_page" class="btn btn-sm btn-success btn-custom">Restore</button>
                                                                    <button type="submit" name="permanent_delete" class="btn btn-sm btn-danger btn-custom" onclick="return confirm('Permanently delete this page?');">Delete</button>
                                                                <?php else : ?>
                                                                    <button type="submit" name="delete_page" class="btn btn-sm btn-danger btn-custom" onclick="return confirm('Move page to trash?');">Trash</button>
                                                                <?php endif; ?>
                                                            </form>
                                                            <button class="btn btn-sm btn-info btn-custom" data-bs-toggle="modal" data-bs-target="#versionModal<?php echo $row['id']; ?>">Versions</button>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-5 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold"><?php echo isset($_GET['id']) ? 'Edit Page' : 'Add New Page'; ?></h6>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $id = $_GET['id'] ?? '';
                                    $page = null;
                                    if (!empty($id)) {
                                        $stmt = $conn->prepare("SELECT id, title, slug, content, meta_title, meta_description, meta_keywords, status FROM cms_pages WHERE id = ? LIMIT 1");
                                        $stmt->bind_param("i", $id);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        $page = $result->fetch_assoc();
                                        $stmt->close();
                                    }
                                    ?>
                                    <form method="POST">
                                        <input type="hidden" name="<?php echo $page ? 'update_page' : 'add_page'; ?>" value="1">
                                        <?php if ($page) : ?>
                                            <input type="hidden" name="id" value="<?php echo $page['id']; ?>">
                                        <?php endif; ?>
                                        <div class="mb-3">
                                            <label class="form-label">Title</label>
                                            <input type="text" name="title" class="form-control" value="<?php echo $page ? htmlspecialchars($page['title']) : ''; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Slug</label>
                                            <input type="text" name="slug" class="form-control" value="<?php echo $page ? htmlspecialchars($page['slug']) : ''; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Content</label>
                                            <textarea name="content" class="form-control ckeditor"><?php echo $page ? htmlspecialchars($page['content']) : ''; ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Meta Title</label>
                                            <input type="text" name="meta_title" class="form-control" value="<?php echo $page ? htmlspecialchars($page['meta_title']) : ''; ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Meta Description</label>
                                            <textarea name="meta_description" class="form-control" rows="3"><?php echo $page ? htmlspecialchars($page['meta_description']) : ''; ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Meta Keywords</label>
                                            <input type="text" name="meta_keywords" class="form-control" value="<?php echo $page ? htmlspecialchars($page['meta_keywords']) : ''; ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-control">
                                                <option value="published" <?php echo $page && $page['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                                                <option value="draft" <?php echo $page && $page['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-custom"><?php echo $page ? 'Update Page' : 'Add Page'; ?></button>
                                        <?php if ($page) : ?>
                                            <a href="cms.php" class="btn btn-secondary btn-custom">Cancel</a>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Page Previews</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php
                                $result->data_seek(0);
                                while ($row = $result->fetch_assoc()) :
                                    if ($row['status'] !== 'trashed') :
                                ?>
                                    <div class="col-xl-4 col-md-6 mb-4">
                                        <div class="preview-card">
                                            <h5><?php echo htmlspecialchars($row['title']); ?></h5>
                                            <p class="mb-2">Slug: <?php echo htmlspecialchars($row['slug']); ?></p>
                                            <p class="mb-2">Status: <?php echo htmlspecialchars($row['status']); ?></p>
                                            <a href="../page.php?slug=<?php echo htmlspecialchars($row['slug']); ?>" target="_blank" class="btn btn-primary btn-custom">Live Preview</a>
                                        </div>
                                    </div>
                                <?php endif; endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
    <?php
    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) :
    ?>
        <div class="modal fade" id="versionModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="versionModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="versionModalLabel<?php echo $row['id']; ?>">Version History - <?php echo htmlspecialchars($row['title']); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Version</th>
                                    <th>Title</th>
                                    <th>Timestamp</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $conn->prepare("SELECT id, title, version_timestamp FROM cms_page_versions WHERE page_id = ? ORDER BY version_timestamp DESC");
                                $stmt->bind_param("i", $row['id']);
                                $stmt->execute();
                                $versions = $stmt->get_result();
                                while ($version = $versions->fetch_assoc()) :
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($version['id']); ?></td>
                                        <td><?php echo htmlspecialchars($version['title']); ?></td>
                                        <td><?php echo htmlspecialchars($version['version_timestamp']); ?></td>
                                        <td>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="version_id" value="<?php echo $version['id']; ?>">
                                                <button type="submit" name="restore_version" class="btn btn-sm btn-success btn-custom">Restore</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; $stmt->close(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
    <div class="toast" id="toastNotification">
        <div class="toast-body">Action successful!</div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            // Initialize CKEditor
            if (typeof CKEDITOR !== 'undefined') {
                CKEDITOR.replace('content', {
                    height: 300
                });
            } else {
                console.error('CKEditor not loaded');
                showToast('Error: CKEditor not loaded!', true);
            }

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

            // Dynamic search, filter, sort
            function updateTable() {
                const filter = $('#filter').val();
                const sort = $('#sort').val();
                const search = $('#search').val();

                $.ajax({
                    url: '',
                    type: 'POST',
                    data: { filter, sort, search, ajax: true },
                    dataType: 'json',
                    success: function(response) {
                        const tbody = $('#pagesTableBody');
                        tbody.empty();
                        response.pages.forEach(page => {
                            const row = `
                                <tr>
                                    <td>${page.id}</td>
                                    <td>${page.title}</td>
                                    <td>${page.slug}</td>
                                    <td>${page.status}</td>
                                    <td>${page.created_at}</td>
                                    <td>
                                        <a href="?id=${page.id}" class="btn btn-sm btn-info btn-custom">Edit</a>
                                        <a href="../page.php?slug=${page.slug}" target="_blank" class="btn btn-sm btn-primary btn-custom">View</a>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="id" value="${page.id}">
                                            ${page.status === 'trashed' ? `
                                                <button type="submit" name="restore_page" class="btn btn-sm btn-success btn-custom">Restore</button>
                                                <button type="submit" name="permanent_delete" class="btn btn-sm btn-danger btn-custom" onclick="return confirm('Permanently delete this page?');">Delete</button>
                                            ` : `
                                                <button type="submit" name="delete_page" class="btn btn-sm btn-danger btn-custom" onclick="return confirm('Move page to trash?');">Trash</button>
                                            `}
                                        </form>
                                        <button class="btn btn-sm btn-info btn-custom" data-bs-toggle="modal" data-bs-target="#versionModal${page.id}">Versions</button>
                                    </td>
                                </tr>`;
                            tbody.append(row);
                        });
                    },
                    error: function() {
                        showToast('Failed to fetch pages', true);
                    }
                });
            }

            // Bind events
            $('#filter, #sort').on('change', updateTable);
            $('#search').on('keyup', function() {
                clearTimeout($.data(this, 'timer'));
                $(this).data('timer', setTimeout(updateTable, 500));
            });

            // Initial toasts
            <?php if (isset($success)) : ?>
                showToast('<?php echo addslashes($success); ?>');
            <?php elseif (isset($error)) : ?>
                showToast('<?php echo addslashes($error); ?>', true);
            <?php endif; ?>
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>