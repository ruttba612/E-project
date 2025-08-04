<?php
include 'db.php';
$cart_id = $_POST['cart_id'];
$quantity = $_POST['quantity'];
$conn->query("UPDATE cart SET quantity = $quantity WHERE id = $cart_id");
header("Location: cart.php");
?>

