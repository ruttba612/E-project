<?php
include 'db.php'; // 🧠 Include database connection

$search = $_GET['search'] ?? '';


$sql = "SELECT * FROM search WHERE name LIKE '%$search%' OR category LIKE '%$search%'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Search Results</title>
  <style>
    body { font-family: Arial; padding: 20px; }
    .product { border: 1px solid #ccc; padding: 15px; margin-bottom: 10px; border-radius: 5px; }
    h2 { color: #e8b4b8; }
  </style>
</head>
<body>

<h2>Search Results for "<?php echo htmlspecialchars($search); ?>"</h2>

<?php
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<div class='product'>";
    echo "<strong>Name:</strong> " . $row['name'] . "<br>";
    echo "<strong>Category:</strong> " . $row['category'] . "<br>";
    echo "<strong>Price:</strong> Rs " . $row['price'] . "<br>";
    echo "</div>";
  }
} else {
  echo "<p>No products found.</p>";
}
$conn->close();
?>

</body>
</html>

