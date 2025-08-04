<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'functions.php';

$order_id = isset($_GET['order_id']) && is_numeric($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($order_id > 0) {
    generateInvoicePDF($order_id);
} else {
    echo "<script>alert('Invalid order ID!'); window.location.href='orders.php';</script>";
}
?>