<?php

include 'db.php';

// Fetch active categories for dropdown
$result = $conn->query("SELECT name, slug FROM categories WHERE status = 'active'");
$categories = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@400;700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>
    <style>
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
    <script>
       / nav

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
  }  </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>