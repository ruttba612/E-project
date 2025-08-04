<?php
include 'includes/db.php';

function addProduct($name, $category_id, $price, $stock, $description, $status, $image = null) {
    global $conn;
    $name = $conn->real_escape_string($name);
    $category_id = (int)$category_id;
    $price = (float)$price;
    $stock = (int)$stock;
    $description = $conn->real_escape_string($description);
    $status = $conn->real_escape_string($status);
    $image = $image ? $conn->real_escape_string($image) : null;
    $sql = "INSERT INTO products (name, category_id, price, stock, description, status, image) 
            VALUES ('$name', $category_id, $price, $stock, '$description', '$status', " . ($image ? "'$image'" : "NULL") . ")";
    if ($conn->query($sql) === TRUE) {
        error_log("Product added: $name");
    } else {
        error_log("Error adding product: " . $conn->error);
    }
}

function editProduct($id, $name, $category_id, $price, $stock, $description, $status, $image = null) {
    global $conn;
    $id = (int)$id;
    $name = $conn->real_escape_string($name);
    $category_id = (int)$category_id;
    $price = (float)$price;
    $stock = (int)$stock;
    $description = $conn->real_escape_string($description);
    $status = $conn->real_escape_string($status);
    $image = $image ? $conn->real_escape_string($image) : null;
    $sql = "UPDATE products SET 
            name = '$name', 
            category_id = $category_id, 
            price = $price, 
            stock = $stock, 
            description = '$description', 
            status = '$status'";
    if ($image) {
        $sql .= ", image = '$image'";
    }
    $sql .= " WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        error_log("Product updated: ID $id");
    } else {
        error_log("Error updating product: " . $conn->error);
    }
}

function deleteProduct($id) {
    global $conn;
    $id = (int)$id;
    $sql = "DELETE FROM products WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        error_log("Product deleted: ID $id");
    } else {
        error_log("Error deleting product: " . $conn->error);
    }
}

function addCategory($name, $description, $image = null) {
    global $conn;
    $name = $conn->real_escape_string($name);
    $description = $conn->real_escape_string($description);
    $image = $image ? $conn->real_escape_string($image) : null;
    $sql = "INSERT INTO categories (name, description, image) 
            VALUES ('$name', '$description', " . ($image ? "'$image'" : "NULL") . ")";
    if ($conn->query($sql) === TRUE) {
        error_log("Category added: $name");
    } else {
        error_log("Error adding category: " . $conn->error);
    }
}

function editCategory($id, $name, $description, $image = null) {
    global $conn;
    $id = (int)$id;
    $name = $conn->real_escape_string($name);
    $description = $conn->real_escape_string($description);
    $image = $image ? $conn->real_escape_string($image) : null;
    $sql = "UPDATE categories SET 
            name = '$name', 
            description = '$description'";
    if ($image) {
        $sql .= ", image = '$image'";
    }
    $sql .= " WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        error_log("Category updated: ID $id");
    } else {
        error_log("Error updating category: " . $conn->error);
    }
}

function deleteCategory($id) {
    global $conn;
    $id = (int)$id;
    $sql = "DELETE FROM categories WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        error_log("Category deleted: ID $id");
    } else {
        error_log("Error deleting category: " . $conn->error);
    }
}

function addBanner($title, $status, $image = null) {
    global $conn;
    $title = $conn->real_escape_string($title);
    $status = $conn->real_escape_string($status);
    $image = $image ? $conn->real_escape_string($image) : null;
    $sql = "INSERT INTO banners (title, status, image) 
            VALUES ('$title', '$status', " . ($image ? "'$image'" : "NULL") . ")";
    if ($conn->query($sql) === TRUE) {
        error_log("Banner added: $title");
    } else {
        error_log("Error adding banner: " . $conn->error);
    }
}

function editBanner($id, $title, $status, $image = null) {
    global $conn;
    $id = (int)$id;
    $title = $conn->real_escape_string($title);
    $status = $conn->real_escape_string($status);
    $image = $image ? $conn->real_escape_string($image) : null;
    $sql = "UPDATE banners SET 
            title = '$title', 
            status = '$status'";
    if ($image) {
        $sql .= ", image = '$image'";
    }
    $sql .= " WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        error_log("Banner updated: ID $id");
    } else {
        error_log("Error updating banner: " . $conn->error);
    }
}

function deleteBanner($id) {
    global $conn;
    $id = (int)$id;
    $sql = "DELETE FROM banners WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        error_log("Banner deleted: ID $id");
    } else {
        error_log("Error deleting banner: " . $conn->error);
    }
}

function updateSiteSettings($key, $value) {
    global $conn;
    $key = $conn->real_escape_string($key);
    $value = $conn->real_escape_string($value);
    $sql = "INSERT INTO site_settings (setting_key, setting_value) 
            VALUES ('$key', '$value') 
            ON DUPLICATE KEY UPDATE setting_value = '$value'";
    if ($conn->query($sql) === TRUE) {
        error_log("Setting updated: $key");
    } else {
        error_log("Error updating setting: " . $conn->error);
    }
}

function getSiteSetting($key) {
    global $conn;
    $key = $conn->real_escape_string($key);
    $result = $conn->query("SELECT setting_value FROM site_settings WHERE setting_key = '$key'");
    if ($result && $row = $result->fetch_assoc()) {
        return $row['setting_value'];
    }
    return '';
}
function clearCache() {
    $files = glob('cache/*');
    foreach ($files as $file) {
        if (is_file($file)) unlink($file);
    }
}


function generateInvoicePDF($order_id) {
    global $conn;
    include('fpdf/fpdf.php');

    // Validate order_id
    $order_id = (int)$order_id;
    if ($order_id <= 0) {
        error_log("Invalid order_id: $order_id");
        echo "<script>alert('Invalid order ID!'); window.location.href='orders.php';</script>";
        exit();
    }

    // Fetch order details
    $order_query = $conn->query("SELECT o.order_id, o.total_amount, o.status, o.order_date, c.name, c.email, c.address 
                                 FROM orders o 
                                 JOIN customers c ON o.customer_id = c.id 
                                 WHERE o.order_id = $order_id");
    if (!$order_query || $order_query->num_rows === 0) {
        error_log("Failed to fetch order for invoice, order_id: $order_id");
        echo "<script>alert('Order not found!'); window.location.href='orders.php';</script>";
        exit();
    }
    $order = $order_query->fetch_assoc();

    // Fetch order items
    $items_query = $conn->query("SELECT oi.quantity, oi.price, p.name 
                                 FROM order_items oi 
                                 JOIN products p ON oi.product_id = p.id 
                                 WHERE oi.order_id = $order_id");
    if (!$items_query || $items_query->num_rows === 0) {
        error_log("Failed to fetch order items for order_id: $order_id");
        echo "<script>alert('Order items not found!'); window.location.href='orders.php';</script>";
        exit();
    }
    $items = [];
    $subtotal = 0;
    while ($item = $items_query->fetch_assoc()) {
        $items[] = $item;
        $subtotal += $item['quantity'] * $item['price'];
    }
    $tax_rate = 0.05; // 5% tax
    $tax = $subtotal * $tax_rate;
    $grand_total = $subtotal + $tax;

    // Create PDF
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $font_available = file_exists('fpdf/font/Lora-Regular.php') && file_exists('fpdf/font/Lora-Bold.php');
    if ($font_available) {
        $pdf->AddFont('Lora', '', 'Lora-Regular.php');
        $pdf->AddFont('Lora', 'B', 'Lora-Bold.php');
    } else {
        error_log("Lora fonts not found, using Arial");
    }

    // Header
    $pdf->SetFillColor(255, 204, 213); // #FFCCD5
    $pdf->Rect(0, 0, 210, 30, 'F');
    $pdf->SetFont($font_available ? 'Lora' : 'Arial', 'B', 20);
    $pdf->SetTextColor(44, 44, 44); // #2C2C2C
    $pdf->Cell(0, 10, 'Auranest', 0, 1, 'C');
    $pdf->SetFont($font_available ? 'Lora' : 'Arial', '', 12);
    $pdf->Cell(0, 5, 'Invoice # ' . $order['order_id'], 0, 1, 'C');
    $pdf->Cell(0, 5, 'Date: ' . date('F j, Y', strtotime($order['order_date'])), 0, 1, 'C');
    $pdf->Ln(10);

    // Customer Information
    $pdf->SetFont($font_available ? 'Lora' : 'Arial', 'B', 12);
    $pdf->Cell(0, 8, 'Customer Details', 0, 1, 'L');
    $pdf->SetFont($font_available ? 'Lora' : 'Arial', '', 10);
    $pdf->Cell(0, 6, 'Name: ' . ($order['name'] ?? 'N/A'), 0, 1, 'L');
    $pdf->Cell(0, 6, 'Email: ' . ($order['email'] ?? 'Not provided'), 0, 1, 'L');
    $pdf->Cell(0, 6, 'Address: ' . ($order['address'] ?? 'Not provided'), 0, 1, 'L');
    $pdf->Ln(5);

    // Order Items Table
    $pdf->SetFont($font_available ? 'Lora' : 'Arial', 'B', 10);
    $pdf->SetFillColor(255, 228, 225); // #FFE4E1
    $pdf->Cell(80, 8, 'Product', 1, 0, 'L', 1);
    $pdf->Cell(30, 8, 'Quantity', 1, 0, 'C', 1);
    $pdf->Cell(40, 8, 'Unit Price', 1, 0, 'C', 1);
    $pdf->Cell(40, 8, 'Total', 1, 1, 'C', 1);
    $pdf->SetFont($font_available ? 'Lora' : 'Arial', '', 10);
    foreach ($items as $item) {
        $total = $item['quantity'] * $item['price'];
        $pdf->Cell(80, 8, $item['name'], 1, 0, 'L');
        $pdf->Cell(30, 8, $item['quantity'], 1, 0, 'C');
        $pdf->Cell(40, 8, '$' . number_format($item['price'], 2), 1, 0, 'C');
        $pdf->Cell(40, 8, '$' . number_format($total, 2), 1, 1, 'C');
    }
    $pdf->Ln(5);

    // Totals
    $pdf->SetFont($font_available ? 'Lora' : 'Arial', 'B', 10);
    $pdf->Cell(150, 8, 'Subtotal:', 0, 0, 'R');
    $pdf->Cell(40, 8, '$' . number_format($subtotal, 2), 0, 1, 'R');
    $pdf->Cell(150, 8, 'Tax (5%):', 0, 0, 'R');
    $pdf->Cell(40, 8, '$' . number_format($tax, 2), 0, 1, 'R');
    $pdf->Cell(150, 8, 'Grand Total:', 0, 0, 'R');
    $pdf->Cell(40, 8, '$' . number_format($grand_total, 2), 0, 1, 'R');

    // Footer
    $pdf->Ln(10);
    $pdf->SetFont($font_available ? 'Lora' : 'Arial', '', 10);
    $pdf->Cell(0, 6, 'Thank you for shopping with Auranest!', 0, 1, 'C');
    $pdf->Cell(0, 6, 'Contact: contact@auranest.com | www.auranest.com', 0, 1, 'C');

    // Output PDF inline
    $pdf->Output('I', 'invoice_order_' . $order['order_id'] . '.pdf');
}

function cancelOrder($order_id) {
    global $conn;
    $order_id = (int)$order_id;
    if ($order_id <= 0) {
        error_log("Invalid order_id for cancel: $order_id");
        return false;
    }
    $result = $conn->query("UPDATE orders SET status = 'cancelled' WHERE order_id = $order_id AND status != 'cancelled'");
    return $result && $conn->affected_rows > 0;
}


function blockCustomer($customer_id, $block_status) {
    global $conn;
    $stmt = $conn->prepare("UPDATE customers SET status = ? WHERE id = ?");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("ii", $block_status, $customer_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function respondFeedback($feedback_id, $response, $status) {
    global $conn;
    if (!$conn || $conn->connect_error) {
        error_log("No database connection in respondFeedback: " . ($conn ? $conn->connect_error : "No connection object"));
        return false;
    }
    if (!$feedback_id || !$response || !in_array($status, ['pending', 'responded', 'closed'])) {
        error_log("Invalid input in respondFeedback: ID=$feedback_id, Response=$response, Status=$status");
        return false;
    }
    $stmt = $conn->prepare("UPDATE feedback SET response = ?, status = ? WHERE id = ?");
    if (!$stmt) {
        error_log("Prepare failed in respondFeedback: " . $conn->error);
        return false;
    }
    $stmt->bind_param("ssi", $response, $status, $feedback_id);
    $result = $stmt->execute();
    if (!$result) {
        error_log("Execute failed in respondFeedback: ID=$feedback_id, Response=$response, Status=$status, Error=" . $stmt->error);
    } else {
        error_log("respondFeedback successful: ID=$feedback_id, Response=$response, Status=$status");
    }
    $stmt->close();
    return $result;
}

function deleteFeedback($feedback_id) {
    global $conn;
    if (!$conn || $conn->connect_error) {
        error_log("No database connection in deleteFeedback: " . ($conn ? $conn->connect_error : "No connection object"));
        return false;
    }
    if (!$feedback_id) {
        error_log("Invalid input in deleteFeedback: ID=$feedback_id");
        return false;
    }
    $stmt = $conn->prepare("DELETE FROM feedback WHERE id = ?");
    if (!$stmt) {
        error_log("Prepare failed in deleteFeedback: " . $conn->error);
        return false;
    }
    $stmt->bind_param("i", $feedback_id);
    $result = $stmt->execute();
    if (!$result) {
        error_log("Execute failed in deleteFeedback: ID=$feedback_id, Error=" . $stmt->error);
    } else {
        error_log("deleteFeedback successful: ID=$feedback_id");
    }
    $stmt->close();
    return $result;
}


?>