<?php
session_start();
include("db.php");

// ✅ Remove item
if (isset($_POST['remove_item'])) {
    $removeId = $_POST['remove_id'];
    $conn->query("DELETE FROM cart WHERE id = $removeId");
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

// ✅ Update quantity
if (isset($_POST['update_qty'])) {
    $updateId = $_POST['update_id'];
    $qty = intval($_POST['quantity']);
    if ($qty > 0) {
        $conn->query("UPDATE cart SET prod_quantity = $qty WHERE id = $updateId");
    }
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

// ✅ Fetch cart items
$sql = "SELECT * FROM cart";
$result = $conn->query($sql);
$totalAmount = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Cart</title>
    <style>
        body {
            font-family: sans-serif;
            background: #fefefe;
        }
        .cart-heading {
            text-align: center;
            margin: 30px 0;
            font-size: 28px;
            color: #d63384;
        }
        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            box-shadow: 0 0 10px #eee;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #f8c5e0;
            color: #333;
        }
        img {
            width: 80px;
            height: auto;
            border-radius: 8px;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        input[type="number"] {
            width: 50px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .remove-btn {
            background-color: #dc3545;
            color: white;
        }
        .update-btn {
            background-color: #0d6efd;
            color: white;
        }
        .checkout-btn {
            background-color: #dd848cff;
            color: white;
            padding: 12px 25px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s ease;
        }
        .checkout-btn:hover {
            background-color: #e0709fff;
        }
    </style>
</head>
<body>

<h2 class="cart-heading">🛒 Your Shopping Cart</h2>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Image</th>
            <th>Product</th>
            <th>Price × Qty = Total</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sr = 1;
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $itemTotal = $row['prod_price'] * $row['prod_quantity'];
                $totalAmount += $itemTotal;

                echo '
                <tr>
                    <td>' . $sr++ . '</td>
                    <td><img src="../uploads/' . htmlspecialchars($row['prod_img']) . '" alt="Product Image"></td>
                    <td>' . htmlspecialchars($row['prod_name']) . '</td>
                    <td>
                        $' . number_format($row['prod_price'], 2) . ' × 
                        <form method="POST" action="" style="display:inline-block;">
                            <input type="hidden" name="update_id" value="' . $row['id'] . '">
                            <input type="number" name="quantity" value="' . $row['prod_quantity'] . '" min="1">
                            <button type="submit" name="update_qty" class="update-btn">⟳</button>
                        </form>
                        = <b>$' . number_format($itemTotal, 2) . '</b>
                    </td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="remove_id" value="' . $row['id'] . '">
                            <button type="submit" name="remove_item" class="remove-btn">Remove</button>
                        </form>
                    </td>
                </tr>';
            }
        } else {
            echo '<tr><td colspan="5">Your cart is empty.</td></tr>';
        }
        ?>
        <tr class="total-row">
            <td colspan="4">Total Amount</td>
            <td>$<?php echo number_format($totalAmount, 2); ?></td>
        </tr>
    </tbody>
</table>

<!-- ✅ Checkout Button -->
<?php if ($result->num_rows > 0): ?>
    <div style="text-align: center; margin: 30px 0;">
        <form action="checkout.php" method="GET">
            <button type="submit" class="checkout-btn">Proceed to Checkout 🛍️</button>
        </form>
    </div>
<?php endif; ?>

</body>
</html>
