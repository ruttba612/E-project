<?php
session_start();
include 'db.php';

$product_id = $_POST['product_id'];
$quantity = $_POST['quantity'];

$product = $conn->query("SELECT * FROM products WHERE id = $product_id")->fetch_assoc();

if (!$product) {
    die("Product not found");
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = [
        'id' => $product['id'],
        'name' => $product['name'],
        'price' => $product['price'],
        'quantity' => $quantity
    ];
}

header('Location: cart.php');
exit;
?>