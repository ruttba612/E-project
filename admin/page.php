<?php
include 'db.php';
$slug = $_GET['slug'] ?? '';
$page = null;
if (!empty($slug)) {
    $stmt = $conn->prepare("SELECT title, content, meta_title, meta_description, meta_keywords FROM cms_pages WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $page = $result->fetch_assoc();
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page ? htmlspecialchars($page['meta_title'] ?: $page['title']) : 'Page Not Found'; ?></title>
    <meta name="description" content="<?php echo $page ? htmlspecialchars($page['meta_description']) : ''; ?>">
    <meta name="keywords" content="<?php echo $page ? htmlspecialchars($page['meta_keywords']) : ''; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Inter', 'Lora', sans-serif;
            background-color: #FFF5F5;
            color: #2C2C2C;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        h1 {
            font-size: 2rem;
            font-weight: 600;
            color: #2C2C2C;
            margin-bottom: 20px;
        }
        .content {
            background: #FFE4E1;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #FFCCD5;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($page) : ?>
            <h1><?php echo htmlspecialchars($page['title']); ?></h1>
            <div class="content"><?php echo $page['content']; ?></div>
        <?php else : ?>
            <h1>Page Not Found</h1>
            <p>The requested page does not exist or is not published.</p>
        <?php endif; ?>
    </div>
</body>
</html>