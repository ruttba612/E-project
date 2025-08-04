<?php
session_start();
include("db.php"); // your database connection file

if (isset($_POST['add_to_cart'])) {
    $name = $_POST['product_name'];
    $price = $_POST['product_price'];
    $image = $_POST['product_image'];
    $quantity = 1; // ✅ default quantity

    // ✅ Insert into cart table including quantity
    $stmt = $conn->prepare("INSERT INTO cart (prod_name, prod_price, prod_quantity, prod_img) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $name, $price, $quantity, $image);
    //      s = string (name)
    //      d = double (price)
    //      s = string (image)
    //      s = string (quantity = int but binded as string is also OK)

    if ($stmt->execute()) {
        echo "<script>alert('Product added to cart'); window.location.href='index.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
