<?php
session_start();
include 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php?redirect=checkout");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_SESSION['customer_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $payment_method = $_POST['payment_method'];
    $total_amount = 0;

    // Calculate total from cart
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }
    }

    // Generate unique order_id
    $result = $conn->query("SELECT COUNT(*) as count FROM orders");
    $order_count = $result->fetch_assoc()['count'] + 1;
    $order_id = '#AUN' . str_pad($order_count, 3, '0', STR_PAD_LEFT);

    // Insert into orders table
  $stmt = $conn->prepare("INSERT INTO orders (order_id, customer_id, total_amount, status, order_date, updated_at, name, email, phone, address, city, payment_method) VALUES (?, ?, ?, 'pending', NOW(), NOW(), ?, ?, ?, ?, ?, ?)");


    $stmt->bind_param("sisssssss", $order_id, $customer_id, $total_amount, $name, $email, $phone, $address, $city, $payment_method);
    $stmt->execute();
    $new_order_id = $conn->insert_id;
    $stmt->close();

    // Insert cart items into order_items
    if (!empty($_SESSION['cart'])) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $item) {
            $stmt->bind_param("iiid", $new_order_id, $item['id'], $item['quantity'], $item['price']);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Update total_spent in customers table
    $stmt = $conn->prepare("UPDATE customers SET total_spent = total_spent + ? WHERE id = ?");
    $stmt->bind_param("di", $total_amount, $customer_id);
    $stmt->execute();
    $stmt->close();

    // Clear cart
    $_SESSION['cart'] = [];

    echo "<script>alert('Order placed successfully!'); window.location.href='../index.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Auranest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f8e1e9, #f5c6cb);
            font-family: 'Roboto', sans-serif;
            min-height: 100vh;
        }
        /* navbar */


.navbar,
.menu {
    position: relative;
    z-index: 1000;
    background-color: white; /* Optional: ensures text is readable over banner */
}

/* FIX 2: Style the dropdown properly */
.dropdown {
    position: relative;
}

.dropdown-content {
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    min-width: 200px;
    display: none;
    flex-direction: column;
    z-index: 2000; /* HIGH value to appear above everything */
}

.dropdown-content.show {
    display: flex;
}

/* Optional: Smooth animation on dropdown */
.dropdown-content a {
    padding: 10px 15px;
    text-decoration: none;
    color: #333;
    background-color: white;
    transition: background 0.3s;
}

.dropdown-content a:hover {
    background-color: #f2f2f2;
}
 .navbar {
      backdrop-filter: blur(12px);
      background-color: rgba(255, 255, 255, 0.5);
      padding: 40px 20px;
      position: sticky;
      top: 0;
      z-index: 999;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      /* border-bottom: 1px solid #ddd; */
    }

    .navbar .left,
    .navbar .center,
    .navbar .right {
      display: flex;
      align-items: center;
    }

    .navbar .center {
      flex: 1;
      justify-content: center;
    }

    .navbar input[type="text"] {
      padding: 6px 10px;
      border-radius: 20px;
       border: 1px solid #ccc; 
      outline: none;
      width: 180px;
    }

    .logo {
      font-size: 28px;
      font-weight: bold;
      color: #E8B4B8;
      font-family: Cinzel Decorative ;
    }

    .navbar .right i {
      font-size: 20px;
      margin-left: 20px;
      cursor: pointer;
      color: #333;
      
    }

    /* Below logo menu */
    .menu {
      background-color: rgba(255, 255, 255, 0.4);
      backdrop-filter: blur(10px);
      /* padding: 0 0; */
      display: flex;
     
      justify-content: center;
      gap: 40px;
      /* border-bottom: 1px solid #ddd; */
    }

    .menu a {
      text-decoration: none;
      color: #333;
      font-weight: 500;
      position: relative;
      font-family: Arial, Helvetica, sans-serif;
      font-weight: lighter;
    }

    .dropdown {
      position: relative;
    }

    .dropdown-content {
      display: none;
      position: absolute;
       top: 100%;
      left: 0; 
      background-color: white;
      min-width: 160px;
      box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
      border-radius: 5px;
      z-index: 1000;
      flex-direction: column;
    }

    .dropdown-content a {
      padding: 10px 15px;
      display: block;
      color: #333;
    }
    

     .dropdown:hover .dropdown-content {
      display: block;
    } 

     .dropdown-content a:hover {
      background-color: #f0f0f0;
    } 

    .dropdown-content.show {
    display: flex;
}
  /* === SIDE SEARCH PANEL === */



.search-panel {
  position: fixed;
  top: 80px; /* ✅ yahan px likhna zaroori tha */
  right: 0;
  width: 300px;
  height: calc(100vh - 110px);
  background-color: white;
  box-shadow: -2px 0 10px rgba(0, 0, 0, 0.2);
  padding: 30px 20px; /* ✅ restored */
  z-index: 2000;
  transform: translateX(100%);
  transition: transform 0.3s ease-in-out;
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.search-panel.active {
  transform: translateX(0);
}


.search-panel input {
  padding: 10px 15px;
  border-radius: 30px;
  border: 1px solid #ccc;
  font-size: 16px;
}

.search-panel button {
  padding: 10px;
  border: none;
  border-radius: 30px;
  background-color: #e8b4b8;
  color: white;
  font-weight: bold;
  cursor: pointer;
}

.search-panel .close-btn {
  font-size: 26px;
  position: absolute;
  top: 30px;
  right: 28px;
  cursor: pointer;
}

@media (max-width: 768px) {
  .navbar {
    flex-direction: column;
    gap: 10px;
    align-items: flex-start;
  }

  .navbar .center {
    justify-content: flex-start;
    width: 100%;
  }

  .navbar .left,
  .navbar .right {
    width: 100%;
    justify-content: space-between;
  }

  .menu {
    flex-direction: column;
    gap: 15px;
    align-items: center;
    position: relative;
    z-index: 999;

  }

  .dropdown-content {
    position: static;
    box-shadow: none;
    display: none;
  }

  .dropdown.open .dropdown-content {
    display: block;
  }

  .search-panel {
    width: 100vw;
    height: calc(100vh - 70px);
    top: 70px;
  }
}

        .container {
            max-width: 500px;
            margin: 50px auto;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 40px;
            backdrop-filter: blur(8px);
        }
        h2 {
            font-family: 'Playfair Display', serif;
            color: #f5c6cb;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
            display: block;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            font-size: 16px;
            background: #fef7f9;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #f0a8b0;
            box-shadow: 0 0 5px rgba(245, 198, 203, 0.5);
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: #f5c6cb;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #f0a8b0;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        @media (max-width: 480px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
            h2 {
                font-size: 24px;
            }
            .form-group input, .form-group select, .form-group textarea, .btn {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
        <!-- Top Navbar -->
<div class="navbar">
  <div class="left">
    <i class="fa-solid fa-magnifying-glass search-icon" onclick="toggleSearchPanel()"></i>
  </div>
  
  <div class="center">
    <div class="logo">Auranest</div>
  </div>
  <div class="user-dropdown">
    <i class="fa-solid fa-user user-icon"></i>
    <div class="dropdown-menu">
      <a href="login.php">Login</a>
      <a href="registration.php">Sign Up</a>
      <a href="my_profile.php">My Profile</a>
    </div>
  </div>
  <div class="right">
   <a href="cart.php"> <i class="fa-solid fa-cart-shopping"></i></a>
  </div>
</div>

<div id="search-panel" class="search-panel">
  <span class="close-btn" onclick="toggleSearchPanel()">×</span>
  <input type="text" placeholder="Search Products...">
  <button type="submit">Search</button>
</div>

<!-- Pages under logo -->
<div class="menu">
  <a href="index.php">Home</a>
  <div class="dropdown">
    <a href="#" onclick="toggleDropdown(event)">Categories <i class="fa-solid fa-caret-down fa-2xs"></i></a>
    <div class="dropdown-content">
      <a href="jewllery.html">Jeweleries</a>
      <a href="beauty.html">Beauty Essentials</a>
    </div>
  </div>
  <a href="blog.html">About Us</a>
  <a href="about.html">Blog</a>
  <a href="beauty.html#contact">Contact Us</a>
</div>


    <div class="container">
        <h2>Checkout Form</h2>
        <form action="checkout.php" method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>
            </div>
            <div class="form-group">
                <label for="address">Delivery Address</label>
                <textarea id="address" name="address" placeholder="Enter your delivery address" required></textarea>
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" placeholder="Enter your city" required>
            </div>
            <div class="form-group">
                <label for="payment_method">Payment Method</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="">Select Payment Method</option>
                    <option value="Cash on Delivery">Cash on Delivery</option>
                    <option value="Card">Card</option>
                </select>
            </div>
            <button type="submit" class="btn">Place Order</button>
        </form>
    </div>
    <script>
        /* ================================
       Navbar Scroll Behavior
    =================================== */
    const heroSection = document.getElementById('heroSection');
    if (mainNavbar && heroSection) {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    mainNavbar.classList.add('in-hero');
                    gsap.to(mainNavbar, { boxShadow: 'none', duration: 0.3 });
                } else {
                    mainNavbar.classList.remove('in-hero');
                    gsap.to(mainNavbar, { boxShadow: '0 2px 10px rgba(0,0,0,0.15)', duration: 0.3 });
                }
            });
        }, { threshold: 0 });
        observer.observe(heroSection);
    }

        gsap.from(".container", { opacity: 0, y: 50, duration: 1, ease: "power2.out" });
        gsap.from(".form-group", { opacity: 0, x: -20, duration: 0.8, stagger: 0.2, ease: "power2.out" });
        gsap.from(".btn", { opacity: 0, scale: 0.9, duration: 0.5, ease: "elastic.out(1, 0.3)" });
    </script>
</body>
</html>