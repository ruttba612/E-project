<?php
session_start();
include 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];
$stmt = $conn->prepare("SELECT name, email, total_spent FROM customers WHERE id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result(); // Get mysqli_result object
$customer = $result->fetch_assoc(); // Use fetch_assoc on result
$stmt->close();

$stmt = $conn->prepare("SELECT o.id, o.order_id, o.total_amount, o.status, o.order_date, GROUP_CONCAT(p.name, ' (Qty: ', oi.quantity, ')') as items
                        FROM orders o
                        LEFT JOIN order_items oi ON o.id = oi.order_id
                        LEFT JOIN products p ON oi.product_id = p.id
                        WHERE o.customer_id = ?
                        GROUP BY o.id
                        ORDER BY o.order_date DESC");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); // Use get_result for orders too
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Auranest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@400;700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>
    <style>
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
             * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #E8B4B8 0%, #F5E2E4 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow-x: hidden;
        }

        .container {
            background: white;
            border-radius: 25px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 900px;
            padding: 50px;
            text-align: center;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            margin-bottom: 40px;
        }

        .logo {
            font-size: 36px;
            font-weight: 700;
            color: #E8B4B8;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        .header p {
            color: #555;
            font-size: 16px;
        }

        .button-group {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .btn {
            background: #E8B4B8;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
            min-width: 220px;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            background: #D89AA0;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(232, 180, 184, 0.4);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 1000;
            animation: fadeIn 0.3s ease-in-out;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 700px;
            position: relative;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.5s ease-in-out;
        }

        @keyframes slideIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 28px;
            cursor: pointer;
            color: #E8B4B8;
            transition: color 0.3s ease;
        }

        .close-btn:hover {
            color: #D89AA0;
        }

        h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .order-item, .spending-item, .review-item {
            background: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .order-item:hover, .spending-item:hover, .review-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .order-item p, .spending-item p, .review-item p {
            color: #555;
            font-size: 15px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }

        label {
            display: block;
            color: #333;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #E8B4B8;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #D89AA0;
            box-shadow: 0 0 8px rgba(232, 180, 184, 0.3);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        .submit-btn {
            background: #E8B4B8;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            width: 100%;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .submit-btn:hover {
            background: #D89AA0;
            transform: translateY(-2px);
        }

        .success-message {
            display: none;
            color: #27ae60;
            text-align: center;
            margin-top: 20px;
            font-size: 15px;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .container {
                padding: 30px;
            }

            .logo {
                font-size: 28px;
            }

            .btn {
                min-width: 100%;
            }

            .modal-content {
                margin: 15px;
                padding: 25px;
            }

            h2 {
                font-size: 20px;
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
   <a href="add_to_card/index.php"> <i class="fa-solid fa-cart-shopping"></i></a>
  </div>
</div>

<div id="search-panel" class="search-panel">
  <span class="close-btn" onclick="toggleSearchPanel()">×</span>

  <form method="GET" action="search.php">
    <input type="text" name="search" placeholder="Search Products..." required>
    <button type="submit" style= width:230px;margin-top:10px;>Search</button>
  </form>
</div>


<!-- Pages under logo -->
<div class="menu">
  <a href="#">Home</a>
  <div class="dropdown">
    <a href="#" onclick="toggleDropdown(event)">Categories <i class="fa-solid fa-caret-down fa-2xs"></i></a>
    <div class="dropdown-content">
      <a href="jewllery.html">Jeweleries</a>
      <a href="beauty.html">Beauty Essentials</a>
    </div>
  </div>

  
  <a href="about.html">About Us</a>
  <a href="blog.html">Blog</a>
  <a href="beauty.html#contact">Contact Us</a>
</div>

       <div class="container">
        <div class="header">
            <h1 class="logo">AURANEST</h1>
            <p>Your Personal Order Dashboard</p>
        </div>
        <div class="button-group">
            <button class="btn" onclick="openModal('orderModal')">Track Your Orders</button>
            <button class="btn" onclick="openModal('spendingModal')">Spending Summary</button>
            <button class="btn" onclick="openModal('reviewModal')">Write a Review</button>
        </div>
    </div>

    <!-- Order Tracking Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('orderModal')">&times;</span>
            <h2>Your Orders</h2>
            <div class="order-list">
                <div class="order-item">
                    <p><strong>Order #AURA1234</strong> - Placed on 01/08/2025</p>
                    <p>Status: Shipped</p>
                    <p>Estimated Delivery: 05/08/2025</p>
                    <p>Items: 3 Products</p>
                </div>
                <div class="order-item">
                    <p><strong>Order #AURA1235</strong> - Placed on 30/07/2025</p>
                    <p>Status: Delivered</p>
                    <p>Delivered on: 02/08/2025</p>
                    <p>Items: 2 Products</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Spending Summary Modal -->
    <div id="spendingModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('spendingModal')">&times;</span>
            <h2>Spending Summary</h2>
            <div class="spending-list">
                <div class="spending-item">
                    <p><strong>Total Spent (2025):</strong> $1,500.00</p>
                </div>
                <div class="spending-item">
                    <p><strong>Last 30 Days:</strong> $450.00</p>
                </div>
                <div class="spending-item">
                    <p><strong>Average Order Value:</strong> $150.00</p>
                </div>
                <div class="spending-item">
                    <p><strong>Total Orders:</strong> 10</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('reviewModal')">&times;</span>
            <h2>Write a Review</h2>
            <form id="reviewForm">
                <div class="form-group">
                    <label for="orderId">Select Order</label>
                    <select id="orderId">
                        <option value="AURA1234">Order #AURA1234</option>
                        <option value="AURA1235">Order #AURA1235</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="rating">Rating</label>
                    <select id="rating">
                        <option value="5">★★★★★ 5 Stars</option>
                        <option value="4">★★★★ 4 Stars</option>
                        <option value="3">★★★ 3 Stars</option>
                        <option value="2">★★ 2 Stars</option>
                        <option value="1">★ 1 Star</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="reviewText">Your Review</label>
                    <textarea id="reviewText" placeholder="Share your experience with us..."></textarea>
                </div>
                <button type="submit" class="submit-btn">Submit Review</button>
                <div class="success-message" id="reviewSuccess">Thank you! Your review has been submitted.</div>
            </form>
        </div>
    </div>

    <script>
        // nav
 function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            if (modalId === 'reviewModal') {
                document.getElementById('reviewForm').reset();
                document.getElementById('reviewSuccess').style.display = 'none';
            }
        }

        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const successMessage = document.getElementById('reviewSuccess');
            successMessage.style.display = 'block';
            setTimeout(() => {
                closeModal('reviewModal');
            }, 2000);
            // Add actual review submission logic here
        });

        window.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                closeModal(e.target.id);
            }
        });
    function toggleDropdown(e) {
    e.preventDefault();
    const dropdown = e.target.closest('.dropdown');
    dropdown.querySelector('.dropdown-content').classList.toggle('show');
  }

  // Close dropdown if clicked outside
  window.onclick = function(e) {
    if (!e.target.matches('.dropdown > a') && !e.target.closest('.dropdown-content')) {
      const dropdowns = document.querySelectorAll('.dropdown-content');
      dropdowns.forEach(dropdown => {
        dropdown.classList.remove('show');
      });
    }
  }

  function toggleSearchPanel() {
    document.getElementById("search-panel").classList.toggle("active");
  }
        gsap.from(".container", { opacity: 0, y: 50, duration: 1, ease: "power2.out" });
        gsap.from(".profile-info p", { opacity: 0, x: -20, duration: 0.8, stagger: 0.2, ease: "power2.out" });
        gsap.from(".orders-table tr", { opacity: 0, y: 20, duration: 0.8, stagger: 0.1, ease: "power2.out" });
    </script>
</body>
</html>
