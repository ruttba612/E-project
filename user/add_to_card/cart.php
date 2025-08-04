<?php
session_start();
include("db.php"); // your database connection file

if (isset($_POST['add_to_cart'])) {
    $name = $_POST['product_name'];
    $price = $_POST['product_price'];
    $image = $_POST['product_image'];

    // Insert into cart table
    $stmt = $conn->prepare("INSERT INTO cart (prod_name, prod_price, prod_img) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $name, $price, $image);

    if ($stmt->execute()) {
        echo "<script>alert('Product added to cart'); window.location.href='shop.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
`   