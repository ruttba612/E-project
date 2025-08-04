<?php
// Database connection
$pdo = new PDO("mysql:host=127.0.0.1;dbname=auranest_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch approved reviews
$stmt = $pdo->query("
    SELECT r.message, r.rating, c.name
    FROM reviews r
    JOIN customers c ON r.user_id = c.id
    WHERE r.status = 'approved'
    ORDER BY r.created_at DESC
    LIMIT 9
");
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to display stars based on rating
function displayStars($rating) {
    $fullStars = floor($rating);
    $halfStar = $rating - $fullStars >= 0.5 ? 1 : 0;
    $emptyStars = 5 - $fullStars - $halfStar;
    return str_repeat('<i class="fas fa-star"></i>', $fullStars) .
           ($halfStar ? '<i class="fas fa-star-half-alt"></i>' : '') .
           str_repeat('<i class="far fa-star"></i>', $emptyStars);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auranest - For The Real Beauty</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/ScrollTrigger.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/locomotive-scroll/dist/locomotive-scroll.min.css">
<script src="https://cdn.jsdelivr.net/npm/locomotive-scroll/dist/locomotive-scroll.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@400;700;900&family=Great+Vibes&display=swap" rel="stylesheet">

</head>
<style>/* style.css */

/* style.css */

/* Universal Styles & Base Reset */
:root {
    --primary-pink: #F8E5EB; /* Pastel Pink */
    --secondary-rose-gold: #E8B4B8; /* Muted Rose Gold */
    --accent-gold: #FFD700; /* Golden for accents */
    --text-dark: #333;
    --text-light: #fff;
    --blur-strength: 10px; /* For navbar blur */
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
     font-family: 'Poppins', sans-serif; 
    line-height: 1.6;
    color: var(--text-dark);
    background-color: #fff;
    overflow-x: hidden; /* **CRUCIAL FIX FOR DOUBLE SCROLLBAR** - Hides horizontal scroll */
    min-height: 100vh; /* Ensure body takes full height for scroll animations */
}

a {
    text-decoration: none;
    color: inherit;
}

ul {
    list-style: none;
}

button {
    cursor: pointer;
    border: none;
    background: none;
    font-family: 'Poppins', sans-serif;
}

img, video {
    display: block; /* Remove extra space below images/videos */
    max-width: 100%; /* Ensure responsiveness */
    height: auto;
}

/* Custom Golden Glitter Cursor Trail */
.glitter-dot {
    position: fixed;
    width: 6px;
    height: 6px;
    background-color: rgba(255, 215, 0, 0.6); /* Semi-transparent gold */
    border-radius: 50%;
    pointer-events: none; /* Don't interfere with clicks */
    z-index: 9999; /* Always on top */
    opacity: 0; /* Start hidden */
    transform: scale(0); /* Start small */
    animation: glitterFadeOut 0.8s forwards; /* Animation for fading out */
}

@keyframes glitterFadeOut {
    0% { opacity: 1; transform: scale(1); }
    100% { opacity: 0; transform: scale(0.5) translate(10px, 10px); } /* Fade out and move slightly */
}


/* --- 1. Top Info Bar --- */
.top-info-bar {
    background-color: var(--secondary-rose-gold);
    color: var(--text-light);
    padding: 10px 0;
    overflow: hidden; /* Crucial for marquee effect */
    white-space: nowrap; /* Keep content in one line */
    position: relative;
    z-index: 1001; /* Higher than navbar */
    font-size: 0.9em;
    font-weight: 300;
}

.info-content {
    display: inline-block; /* Allows content to flow horizontally */
    padding-left: 100%; /* Start off-screen to the right */
    animation: marquee 30s linear infinite; /* Marquee animation */
}

.info-content span {
    margin-right: 50px; /* Space between messages */
}

@keyframes marquee {
    0% { transform: translateX(0%); }
    100% { transform: translateX(-100%); }
}


/* --- 2. Main Navigation Bar (Navbar) --- 
/* .main-navbar {
    position: fixed; /* Fixed position 
    width: 100%m
    /* top: 0; /* Removed for dynamic top adjustment in JS 
    left: 0;
    z-index: 1000; /* Below info bar, above main content 
    background-color: rgba(255, 255, 255, 0.3); /* Initial transparent background 
    backdrop-filter: blur(var(--blur-strength)); /* Blur effect 
    -webkit-backdrop-filter: blur(var(--blur-strength)); /* For Safari 
    transition: background-color 0.3s ease, backdrop-filter 0.3s ease, box-shadow 0.3s ease, padding 0.3s ease; /* Smooth transitions 
    padding: 15px 0; /* Default padding 
    box-shadow: 0 2px 10px rgba(0,0,0,0.05); /* Subtle shadow 
}

.navbar-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    transition: justify-content 0.3s ease, flex-direction 0.3s ease;
}

.navbar-logo {
    font-family: 'Playfair Display', serif;
    font-size: 1.8em;
    font-weight: 700;
    color: var(--text-dark);
    letter-spacing: 1px;
    transition: color 0.3s ease;
    flex-shrink: 0;
}

.navbar-logo:hover {
    color: var(--secondary-rose-gold);
}

.navbar-links ul {
    display: flex;
    gap: 30px;
}

.navbar-links ul li a {
    font-weight: 400;
    padding: 5px 0;
    position: relative;
    transition: color 0.3s ease;
}

.navbar-links ul li a::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background-color: var(--secondary-rose-gold);
    transition: width 0.3s ease;
}

.navbar-links ul li a:hover {
    color: var(--secondary-rose-gold);
}

.navbar-links ul li a:hover::after {
    width: 100%;
}

/* Dropdown Styles (Categories & User) 
.dropdown-category, .user-icon {
    position: relative;
}

.dropdown-content 
    display: none; /* Hidden by default 
    position: absolute;
    background-color: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.1);
    z-index: 1;
    border-radius: 5px;
    overflow: hidden;
    padding: 10px 0;
    top: 100%; /* Position below the parent link/icon 
    left: 50%;
    transform: translateX(-50%);
    margin-top: 10px;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

.dropdown-content a {
    color: var(--text-dark);
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    transition: background-color 0.2s ease, color 0.2s ease;
}

.dropdown-content a:hover {
    background-color: var(--primary-pink);
    color: var(--text-dark);
}

.dropdown-category:hover .dropdown-content,
.user-icon:hover .dropdown-content {
    display: block; /* Show dropdown on hover 
    opacity: 1;
    visibility: visible;
}

/* Navbar Right Icons 
.navbar-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

.navbar-right i {
    font-size: 1.2em;
    cursor: pointer;
    color: var(--text-dark);
    transition: color 0.3s ease, transform 0.3s ease;
}

.navbar-right i:hover {
    color: var(--secondary-rose-gold);
    transform: scale(1.1);
}

.cart-icon {
    position: relative;
}

.cart-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: var(--accent-gold);
    color: var(--text-dark);
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 0.7em;
    font-weight: 600;
}

/* Search Bar Styling 
.search-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon {
    cursor: pointer;
    padding: 5px;
    transition: color 0.3s ease, text-shadow 0.3s ease;
}

.search-icon:hover {
    color: var(--accent-gold);
    text-shadow: 0 0 8px var(--accent-gold); /* Glow effect 
}

.search-input {
    border: none;
    outline: none;
    background: transparent;
    border-bottom: 1px solid var(--text-dark);
    padding: 5px 0;
    width: 0; /* Hidden by default 
    opacity: 0
    transition: width 0.3s ease, opacity 0.3s ease, border-color 0.3s ease;
    margin-left: 10px;
    font-size: 1em;
    color: var(--text-dark);
    pointer-events: none; /* Disable pointer events when hidden 
}

.search-input.active {
    width: 200px; /* Expand on active 
    opacity: 1;
    pointer-events: auto; /* Enable pointer events when active 
    border-color: var(--secondary-rose-gold); /* Highlight border 
}


/* --- Navbar Dynamic States (Managed by JS) --- */

/* Navbar in Hero Section (transparent background) 
.main-navbar.in-hero {
    background-color: rgba(255, 255, 255, 0); /* Fully transparent 
    backdrop-filter: blur(0px); /* No blur 
    -webkit-backdrop-filter: blur(0px);
    box-shadow: none; /* No shadow 
    /* padding-top: 50px; /* Handled by JS 
    padding-bottom: 20px;
}

/* Navbar when hovered in Hero Section (blur background returns) 
.main-navbar.in-hero.hovered {
    background-color: rgba(255, 255, 255, 0.3); /* Slight transparency with blur 
    backdrop-filter: blur(var(--blur-strength)); /* Apply blur 
    -webkit-backdrop-filter: blur(var(--blur-strength));
    box-shadow: 0 2px 10px rgba(0,0,0,0.05); /* Restore shadow 
}

/* Navbar when scrolled down (permanent blur) 
.main-navbar.scrolled-down {
    background-color: rgba(255, 255, 255, 0.9); /* Less transparent 
    backdrop-filter: blur(8px); /* Slightly less blur than initial 
    -webkit-backdrop-filter: blur(8px);
    box-shadow: 0 2px 10px rgba(0,0,0,0.15); /* Clearer shadow 
    /* padding-top: 15px; /* Handled by JS 
    padding-bottom: 15px;
}

/* Navbar links/logo position change on scroll 
.main-navbar.scrolled-down .navbar-container {
    justify-content: flex-start; /* Align everything to left 
    gap: 30px; /* Space between logo and other elements 
}

.main-navbar.scrolled-down .navbar-center {
    order: -1; /* Move logo/links to the very left 
    display: flex;
    align-items: center;
    gap: 30px; /* Space between logo and links 
    transition: all 0.3s ease; /* Smooth transition for alignment 
}

.main-navbar.scrolled-down .navbar-logo {
    font-size: 1.5em; /* Smaller logo when scrolled 
}

.main-navbar.scrolled-down .navbar-links {
    margin-left: 0; /* Remove any default margins if present 
}

.main-navbar.scrolled-down .navbar-right {
    margin-left: auto; /* Push right elements to the far right 
}

/* Ensure logo and links are visible even when navbar is transparent in hero 
.main-navbar.in-hero .navbar-logo,
.main-navbar.in-hero .navbar-links ul li a,
.main-navbar.in-hero .navbar-right i,
.main-navbar.in-hero .search-wrapper .search-icon {
    color: var(--text-light); /* Make text/icons white or light color 
    text-shadow: 0 0 5px rgba(0,0,0,0.5); /* Add subtle shadow for visibility 
}

/* Adjust search input border color when in hero and transparent 
.main-navbar.in-hero .search-input {
    border-bottom-color: var(--text-light);
}
.main-navbar.in-hero .search-input.active {
    border-bottom-color: var(--accent-gold); /* Glow effect color 
}


/* Adjust hover for links/icons when in hero section 
.main-navbar.in-hero .navbar-links ul li a:hover,
.main-navbar.in-hero .navbar-right i:hover,
.main-navbar.in-hero .search-icon:hover {
    color: var(--accent-gold); /* Golden hover effect 
    text-shadow: none; /* Remove shadow on hover 
}

/* Make sure dropdowns are visible when navbar is transparent 
.main-navbar.in-hero .dropdown-content {
    background-color: rgba(255, 255, 255, 0.95); /* Keep dropdown background solid 
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    color: var(--text-dark); /* Ensure text is readable 
} */


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

/* --- Hero Section --- */
.hero-section {
    position: relative;
    height: 100vh; /* Full viewport height */
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: var(--text-light);
    text-align: center;
}

/* Slider Container */
.hero-slider-container {
    position: absolute; /* Changed to absolute to allow text to overlay */
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden; /* Crucial for hiding parts of slides as they move */
}

.hero-slide {
    position: absolute; /* Each slide takes full container space */
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover; /* Cover the container without distortion */
    opacity: 0; /* Hidden by default */
    transform: translateX(100%); /* Start off-screen to the right */
    transition: transform 0.8s ease-out, opacity 0.8s ease-out; /* Smooth transition */
}

.hero-slide.active {
    opacity: 1; /* Make active slide visible */
    transform: translateX(0%); /* Bring active slide into view */
}

/* Content over slider */
.hero-section h1,
.hero-section p {
    z-index: 2; /* Above image */
    position: relative; /* Ensure z-index works */
}

.hero-section h1 {
    font-family: 'Playfair Display', serif;
    font-size: 4.5em;
    margin-bottom: 10px;
    text-shadow: 2px 2px 6px rgba(0,0,0,0.7);
}

.hero-section p {
    font-size: 1.5em;
    font-weight: 300;
    text-shadow: 1px 1px 4px rgba(0,0,0,0.7);
}

/* Slider Dots */
.slider-dots {
    position: absolute;
    bottom: 30px; /* Position at the bottom of the hero section */
    display: flex;
    gap: 10px;
    z-index: 3; /* Above slides and text */
}

.slider-dots .dot {
    width: 12px;
    height: 12px;
    background-color: rgba(255, 255, 255, 0.5); /* Semi-transparent white */
    border-radius: 50%;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
    border: 1px solid rgba(0,0,0,0.1); /* Subtle border */
}

.slider-dots .dot.active {
    background-color: var(--accent-gold); /* Active dot is golden */
    transform: scale(1.2); /* Slightly larger */
    border-color: var(--accent-gold);
}


/* Add padding-top to main content to account for fixed navbar and top bar */
main {
    padding-top: var(--top-bar-height, 0px); /* Default 0, set by JS */
}

/* --- "How to Use" Section Styles (Add if not already present or modify) --- */
.how-to-use-section {
    padding: 80px 20px;
    background-color: var(--primary-pink);
    text-align: center;
}

.how-to-use-section h2 {
    font-family: 'Playfair Display', serif;
    font-size: 3em;
    margin-bottom: 50px;
    color: var(--text-dark);
}

.shop-by-categories {
    text-align: center;
    background-color: var(--white);
    padding-bottom: 6rem;
}

.shop-by-categories h2 {
    font-size: 2.5rem;
    margin-bottom: 3rem;
    color: var(--rose-gold);
}

.category-carousel-container {
    overflow: hidden; /* Hide excess content */
    margin-bottom: 2rem;
    position: relative;
    padding: 1rem 0; /* Vertical padding */
}

.category-row {
    display: flex;
    gap: 1.5rem;
    padding: 0 5%; /* Horizontal padding to match sections */
    animation: none; /* Reset animation, will be applied by JS for seamless loop */
    width: fit-content; /* Allow content to dictate width */
}

.category-card {
    min-width: 280px; /* Slightly larger fixed width */
    height: 320px; /* Slightly taller */
    flex-shrink: 0;
    background-size: cover;
    background-position: center;
    border-radius: var(--border-radius-sm);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    display: flex;
    justify-content: center;
    align-items: flex-end;
    padding: 1.5rem;
    color: var(--white);
    font-weight: 600;
    font-size: 1.3rem;
    text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.category-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to top, rgba(0,0,0,0.7), rgba(0,0,0,0));
    z-index: 0;
    transition: background 0.3s ease;
}

.category-card h3 {
    position: relative;
    z-index: 1;
    margin: 0;
    text-align: center;
    color: inherit; /* Inherit color from parent */
}

.category-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}

.category-card:hover::before {
    background: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0.1));
}

.guide-content {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 40px;
    max-width: 1200px;
    margin: 0 auto;
}

.guide-points {
    flex: 1;
    min-width: 300px;
    text-align: left;
    list-style: none;
    padding: 0;
}

.guide-points li {
    background-color: #fff;
    padding: 20px 30px;
    margin-bottom: 20px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-left: 5px solid transparent; /* For active state highlight */
}

.guide-points li.active {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    border-left-color: var(--secondary-rose-gold);
}

.guide-points li h3 {
    font-size: 1.5em;
    margin-bottom: 10px;
    color: var(--secondary-rose-gold);
}

.guide-points li p {
    font-size: 0.95em;
    color: var(--text-dark);
}

.guide-image-container {
    flex: 1;
    min-width: 400px;
    max-width: 500px;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    display: flex; /* For centering image */
    justify-content: center;
    align-items: center;
    background-color: #f0f0f0; /* Placeholder background */
}

.guide-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    border-radius: 15px; /* Match container */
}

/* --- Image Comparison Section --- */
.image-comparison-section {
    padding: 80px 20px;
    background-color: #f9f9f9;
    text-align: center;
}

.image-comparison-section h2 {
    font-family: 'Playfair Display', serif;
    font-size: 3em;
    margin-bottom: 50px;
    color: var(--text-dark);
}

.image-comparison {
    position: relative;
    width: 100%;
    max-width: 800px; /* Max width for the comparison */
    height: 450px; /* Fixed height for consistency */
    margin: 0 auto;
    overflow: hidden;
    border-radius: 10px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.image-comparison img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    pointer-events: none; /* Images shouldn't interfere with handle drag */
}

.before-image {
    clip-path: inset(0 50% 0 0); /* Initially show left half */
    z-index: 2;
}

.after-image {
    clip-path: inset(0 0 0 50%); /* Initially show right half */
    z-index: 1;
}

.resize-handle {
    position: absolute;
    left: 50%; /* Start in the middle */
    top: 0;
    bottom: 0;
    width: 4px; /* Thin line */
    background-color: var(--accent-gold);
    transform: translateX(-50%); /* Center the handle */
    cursor: ew-resize;
    z-index: 3;
}

.resize-handle::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 30px; /* Larger clickable area */
    height: 30px;
    background-color: var(--accent-gold);
    border: 2px solid #fff;
    border-radius: 50%;
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
}

/* --- "Makeup for the Real Beauty" Section --- */
.real-beauty-section {
    position: relative;
    height: 100vh; /* This section will be full viewport height when pinned */
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden; /* Hide overflowing media for parallax */
    background: linear-gradient(to bottom right, #FFDDE1, #DDA0DD); /* Soft gradient background */
    color: var(--text-dark);
}

.real-beauty-section .container {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 0 50px;
}

.real-beauty-section .center-text {
    position: relative; /* Needs to be relative for z-index to work */
    text-align: center;
    z-index: 2; /* Ensure text is above images */
    padding: 20px;
    background: rgba(255, 255, 255, 0.7); /* Semi-transparent background for text */
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    max-width: 600px;
    opacity: 0; /* Start hidden for animation */
    transform: translateY(50px); /* Start below for animation */
}

.real-beauty-section .center-text h2 {
    font-family: 'Playfair Display', serif;
    font-size: 3.5em;
    margin-bottom: 15px;
    color: var(--secondary-rose-gold);
}
.makeup-real-beauty .center-text {
    position: relative;
    top: -50px; /* Moves it slightly up initially */
    text-align: center;
    z-index: 2;
    padding: 20px;
}

.real-beauty-section .center-text p {
    font-size: 1.2em;
    line-height: 1.8;
}

.real-beauty-section .left-media,
.real-beauty-section .right-media {
    position: absolute; /* Absolute position for parallax */
    width: 35%; /* Adjust size as needed */
    height: 70%; /* Adjust size as needed */
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #ccc; /* Placeholder */
    opacity: 0; /* Start hidden for animation */
}

.real-beauty-section .left-media {
    left: 5%; /* Position to the left */
    top: 15%; /* Adjust vertical alignment */
}

.real-beauty-section .right-media {
    right: 5%; /* Position to the right */
    bottom: 15%; /* Adjust vertical alignment */
}

.real-beauty-section .left-media img,
.real-beauty-section .right-media img,
.real-beauty-section .left-media video,
.real-beauty-section .right-media video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.makeup-real-beauty .center-text h2 {
    font-size: 3rem;
    font-weight: 700;
    background: linear-gradient(90deg, #725006ff, #18010cff, #70610aff);
    background-size: 200%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: shimmer 3s infinite linear;
}

@keyframes shimmer {
    0% { background-position: 0% 50%; }
    100% { background-position: 200% 50%; }
}

.shop-now-btn {
    background-color: #fff;
    color: #333;
    border: 2px solid #FFD700;
    padding: 12px 28px;
    font-size: 18px;
    border-radius: 8px;
    font-weight: bold;
    transition: all 0.3s ease;
    cursor: pointer;
}

/* --- New Arrivals Section --- */
.new-arrivals-section {
    padding: 80px 20px;
    background-color: #fff;
    text-align: center;
}

.new-arrivals-section h2 {
    font-family: 'Playfair Display', serif;
    font-size: 3em;
    margin-bottom: 50px;
    color: var(--text-dark);
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

.product-card {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    overflow: hidden;
    text-align: left;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.product-card img {
    width: 100%;
    height: 250px; /* Fixed height for product images */
    object-fit: cover;
    border-bottom: 1px solid #eee;
}

.product-info {
    padding: 20px;
}

.product-info h3 {
    font-size: 1.3em;
    margin-bottom: 10px;
    color: var(--secondary-rose-gold);
}

.product-info p {
    font-size: 1em;
    color: var(--text-dark);
    margin-bottom: 15px;
}

.product-price {
    font-size: 1.2em;
    font-weight: 600;
    color: var(--accent-gold);
    margin-bottom: 15px;
    display: block;
}

.add-to-cart-btn {
    background-color: var(--secondary-rose-gold);
    color: var(--text-light);
    padding: 10px 20px;
    border-radius: 5px;
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.add-to-cart-btn:hover {
    background-color: #b87cbb; /* Darker rose gold */
}

/* --- Testimonials Section --- */
.testimonials-section {
    background-color: var(--primary-pink);
    padding: 80px 20px;
    text-align: center;
}

.testimonials-section h2 {
    font-family: 'Playfair Display', serif;
    font-size: 3em;
    margin-bottom: 50px;
    color: var(--text-dark);
}

.testimonial-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

.testimonial-card {
    background-color: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    text-align: center;
    transition: transform 0.3s ease;
}

.testimonial-card:hover {
    transform: translateY(-5px);
}

.testimonial-card p {
    font-style: italic;
    font-size: 1.1em;
    margin-bottom: 20px;
    color: var(--text-dark);
}

.testimonial-card .author {
    font-weight: 600;
    color: var(--secondary-rose-gold);
    font-size: 1.1em;
}

/* --- Call to Action (CTA) Section --- */
.cta-section {
    background: url('pictures/Luminous Foundation 04 Mineral-based foundation….jpeg') no-repeat center center/cover;
    color: var(--text-light);
    padding: 100px 20px;
    text-align: center;
    position: relative;
    z-index: 0; /* Ensure overlay works */
}

.cta-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4); /* Dark overlay */
    z-index: -1;
}

.cta-section h2 {
    font-family: 'Playfair Display', serif;
    font-size: 3.5em;
    margin-bottom: 20px;
    text-shadow: 2px 2px 6px rgba(0,0,0,0.7);
}

.cta-section p {
    font-size: 1.3em;
    margin-bottom: 30px;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
    text-shadow: 1px 1px 4px rgba(0,0,0,0.7);
}

.cta-button {
    background-color: var(--accent-gold);
    color: var(--text-dark);
    padding: 15px 30px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.1em;
    transition: background-color 0.3s ease, transform 0.3s ease;
    display: inline-block; /* Allows padding and margin */
}

.cta-button:hover {
    background-color: #e6c200; /* Darker gold */
    transform: translateY(-3px);
}


/* --- Footer --- */
footer {
    background-color: var(--text-dark);
    color: var(--text-light);
    padding: 60px 20px;
    font-size: 0.9em;
}
.site-footer .footer-logo {
 
    /* font-family: 'Playfair Display', serif; */
    font-size: 2em;
    font-weight: 700;
    letter-spacing: 1px;
    flex-basis: 100%; /* Take full width on smaller screens */
    text-align: center;
    margin-bottom: 20px;
     font-size: 28px;
      font-weight: bold;
      color: #E8B4B8;
      font-family: cinzel Decorative ;
}

.footer-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 40px;
    max-width: 1200px;
    margin: 0 auto;
}

.footer-column h3 {
    font-size: 1.4em;
    margin-bottom: 20px;
    color: var(--secondary-rose-gold);
}

.footer-column p {
    margin-bottom: 10px;
    line-height: 1.8;
}

.footer-column ul {
    list-style: none;
}

.footer-column ul li {
    margin-bottom: 10px;
}

.footer-column ul li a {
    color: var(--text-light);
    transition: color 0.3s ease;
}

.footer-column ul li a:hover {
    color: var(--accent-gold);
}

.social-icons {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

.social-icons a {
    font-size: 1.5em;
    color: var(--text-light);
    transition: color 0.3s ease, transform 0.3s ease;
}

.social-icons a:hover {
    color: var(--accent-gold);
    transform: translateY(-3px);
}

.footer-bottom {
    text-align: center;
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,0.1);
    font-size: 0.85em;
    color: rgba(255,255,255,0.7);
}


/* --- Responsive Design (Media Queries) --- */

@media (max-width: 992px) {
    .navbar-links {
        display: none; /* Hide main links on smaller screens */
    }

    .navbar-container {
        justify-content: space-between; /* Re-adjust alignment */
    }

    .navbar-logo {
        font-size: 1.6em;
    }

    .main-navbar.scrolled-down .navbar-container {
        justify-content: space-between; /* Maintain space-between on scroll */
    }

    .main-navbar.scrolled-down .navbar-center {
        order: 0; /* Reset order */
        gap: 0; /* Remove gap */
    }

    .main-navbar.scrolled-down .navbar-logo {
        font-size: 1.6em; /* Keep size consistent */
    }

    .main-navbar.scrolled-down .navbar-right {
        margin-left: 0; /* Reset margin */
    }

    .hero-section h1 {
        font-size: 3.5em;
    }

    .hero-section p {
        font-size: 1.2em;
    }

    .real-beauty-section .container {
        flex-direction: column; /* Stack elements vertically */
        padding: 20px;
    }

    .real-beauty-section .left-media,
    .real-beauty-section .right-media {
        position: static; /* Remove absolute positioning */
        width: 80%; /* Adjust width for mobile */
        height: 250px; /* Adjust height */
        margin-bottom: 20px;
        top: auto;
        left: auto;
        right: auto;
        bottom: auto;
        opacity: 1; /* Keep visible on smaller screens if no animation */
        transform: none; /* Remove parallax transform */
    }
    .real-beauty-section .center-text {
        order: -1; /* Place text above images */
        opacity: 1; /* Keep visible on smaller screens */
        transform: none; /* Remove animation transform */
        margin-bottom: 30px;
    }

    .real-beauty-section .center-text h2 {
        font-size: 2.5em;
    }

    .how-to-use-section .guide-content {
        flex-direction: column;
    }
    .guide-points {
        min-width: unset;
        width: 100%;
    }
    .guide-image-container {
        min-width: unset;
        width: 100%;
        height: 300px;
    }
}

@media (max-width: 768px) {
    .top-info-bar {
        font-size: 0.8em;
    }
    .navbar-logo {
        font-size: 1.4em;
    }
    .hero-section h1 {
        font-size: 2.8em;
    }
    .hero-section p {
        font-size: 1em;
    }
    .cta-section h2 {
        font-size: 2.5em;
    }
    .cta-section p {
        font-size: 1em;
    }
    .footer-container {
        grid-template-columns: 1fr; /* Single column layout for footer */
        text-align: center;
    }
    .footer-column ul {
        padding-left: 0;
    }
    .social-icons {
        justify-content: center;
    }

    .image-comparison {
        height: 300px; /* Adjust height for smaller screens */
    }
}

@media (max-width: 480px) {
    .navbar-container {
        padding: 0 10px;
    }
    .navbar-right {
        gap: 15px;
    }
    .search-input.active {
        width: 150px;
    }
    .hero-section h1 {
        font-size: 2em;
    }
    .hero-section p {
        font-size: 0.9em;
    }
    .cta-section h2 {
        font-size: 2em;
    }
    .cta-button {
        padding: 12px 25px;
        font-size: 1em;
    }
}  
main {
    padding-top: var(--top-bar-height, 0px); /* Default 0, set by JS */
}


/* --- Sticky Text Section --- */
.sticky-text-section {
    background-color: var(--primary-pink);
    color: var(--text-dark);
    padding: 15px 0;
    text-align: center;
    font-size: 1.1em;
    font-weight: 600;
    overflow: hidden;
    white-space: nowrap;
    border-top: 1px solid rgba(0,0,0,0.1);
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

.sticky-text-content {
    display: inline-block;
    padding-left: 100%;
    animation: marqueeText 20s linear infinite; /* Shorter duration for this one */
}

@keyframes marqueeText {
    0% { transform: translateX(0%); }
    100% { transform: translateX(-100%); }
}

/* --- Shop by Categories --- */
.shop-by-categories {
    padding: 80px 20px;
    text-align: center;
    background-color: #fcfcfc;
}

.shop-by-categories h2 {
    font-family: 'Playfair Display', serif;
    font-size: 2.5em;
    margin-bottom: 40px;
    color: var(--text-dark);
}

.categories-carousel {
    display: flex;
    gap: 30px;
    overflow-x: auto; /* Enable horizontal scrolling */
    padding-bottom: 20px; /* Space for scrollbar */
    -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
    scrollbar-width: thin; /* Firefox */
    scrollbar-color: var(--secondary-rose-gold) lightgrey; /* Firefox */
}

/* Hide scrollbar for Webkit browsers */
.categories-carousel::-webkit-scrollbar {
    height: 8px;
}

.categories-carousel::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.categories-carousel::-webkit-scrollbar-thumb {
    background: var(--secondary-rose-gold);
    border-radius: 10px;
}

.categories-carousel::-webkit-scrollbar-thumb:hover {
    background: var(--accent-gold);
}


.category-card {
    flex: 0 0 auto; /* Prevent cards from shrinking */
    width: 180px; /* Fixed width for cards, small enough for 4+ */
    height: 180px;
    background-color: var(--primary-pink);
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    font-size: 1.1em;
    font-weight: 600;
    color: var(--text-dark);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
    cursor: pointer;
    padding: 10px;
}

.category-card img {
    width: 100px; /* Category icon size */
    height: 100px;
    object-fit: contain;
    margin-bottom: 10px;
    border-radius: 5px;
}

.category-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    background-color: var(--secondary-rose-gold); /* Example hover effect */
    color: var(--text-light);
}

.category-card:hover span {
    color: var(--text-light);
}

/* --- "Makeup for the Real Beauty" Section --- */
.makeup-real-beauty {
    padding: 100px 20px;
    background-color: #fff;
    position: relative;
    overflow: hidden; /* Crucial for controlling inner element overflow */
}

.makeup-real-beauty .content-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 4%; /* Use percentage for responsive gaps */
    max-width: 1400px;
    margin: 0 auto;
    position: relative; /* For GSAP positioning */
    z-index: 2; /* Above any background elements */
}

.makeup-real-beauty .left-media,
.makeup-real-beauty .right-media {
    display: flex;
    flex-direction: column;
    gap: 20px;
    position: relative; /* For GSAP */
}

.makeup-real-beauty .left-media {
    align-items: flex-end; /* Align to the right within its container */
    flex-basis: 30%; /* Control width */
    max-width: 450px;
}

.makeup-real-beauty .right-media {
    align-items: flex-start; /* Align to the left within its container */
    flex-basis: 25%; /* Control width */
    max-width: 350px;
}

.makeup-real-beauty img,
.makeup-real-beauty video {
    border-radius: 10px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    object-fit: cover;
    width: 100%; /* Make them fill their flex basis */
}

.makeup-real-beauty .img-upper {
    height: 500px;
}

.makeup-real-beauty .video-lower {
    height: 250px; /* Slightly larger video */
    margin-top: -80px; /* Overlap with upper image, adjusted */
    margin-right: -50px; /* Pull video slightly out, adjusted */
    width: 80%; /* Adjust width relative to container */
    align-self: flex-end; /* Align to right within column */
}

.makeup-real-beauty .img-right-upper {
    height: 450px;
}

.makeup-real-beauty .img-right-lower {
    height: 450px;
    margin-top: -30px; /* Overlap */
    margin-left: 30px; /* Staggered effect */
}

.makeup-real-beauty .center-text {
    text-align: center;
    flex-shrink: 0;
    flex-basis: 30%; /* Control width */
    max-width: 400px;
    z-index: 3; /* Ensure text is above media elements during scroll */
}

.makeup-real-beauty .center-text h2 {
    font-family: 'Playfair Display', serif;
    font-size: 3.5em;
    color: var(--text-dark);
    margin-bottom: 30px;
    line-height: 1.2;
}

.makeup-real-beauty .shop-now-btn {
    background-color: var(--accent-gold);
    color: var(--text-dark);
    padding: 15px 35px;
    border-radius: 50px;
    font-size: 1.1em;
    font-weight: 600;
    transition: transform 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 5px 15px rgba(255,215,0,0.3);
}

.makeup-real-beauty .shop-now-btn:hover {
    transform: translateY(-5px) scale(1.05);
    background-color: #e6c200; /* Slightly darker gold */
    box-shadow: 0 8px 20px rgba(255,215,0,0.5);
}

/* --- Responsive Video Section --- */
.responsive-video-section {
    padding: 80px 20px;
    display: flex;
    justify-content: center;
    background-color: #fefefe;
}

.responsive-video-section .responsive-video {
    width: calc(100% - 80px); /* 40px margin on each side (total 80px) */
    max-width: 1200px; /* Max width to control size on large screens */
    height: auto; /* Maintain aspect ratio (16:9) relative to max-width */
    object-fit: cover; /* Cover the area, useful if aspect ratio varies slightly */
    border-radius: 15px; /* Rounded borders */
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    margin: 0px; /* Apply margin on all sides */

     width: 100%; /* Make it take full width of its container */
    max-width: 1200px; /* Cap its max width for larger screens */
    height: auto;
    max-height: 600px; /* Prevent it from becoming too tall */
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    display: block;
    margin: 0 auto;
}

/* --- Sale of the Month Section --- */
.sale-of-the-month {
    padding: 80px 20px;
    text-align: center;
    background-color: var(--primary-pink);
}

.sale-of-the-month h2 {
    font-family: 'Playfair Display', serif;
    font-size: 2.8em;
    margin-bottom: 40px;
    color: var(--text-dark);
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
}

.sale-of-the-month .timer {
    background-color: var(--accent-gold);
    color: var(--text-dark);
    padding: 8px 15px;
    border-radius: 5px;
    font-family: 'Poppins', sans-serif;
    font-size: 0.7em; /* Relative to H2 */
    font-weight: 700;
    letter-spacing: 1px;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); /* Adjusted for 4 cards */
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

.product-card {
    background-color: var(--text-light);
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    overflow: hidden;
    text-align: left;
    padding-bottom: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.15);
}

.product-card img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    border-bottom: 1px solid #eee;
    margin-bottom: 15px;
}

.product-card h3 {
    font-size: 1.3em;
    margin: 0 15px 8px;
    color: var(--text-dark);
}

.product-card .description {
    font-size: 0.9em;
    color: #666;
    margin: 0 15px 10px;
    height: 40px; /* Limit description height */
    overflow: hidden;
}

.product-card .price {
    margin: 0 15px 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.product-card .old-price {
    text-decoration: line-through;
    color: #999;
    font-size: 0.9em;
}

.product-card .new-price {
    color: var(--secondary-rose-gold);
    font-size: 1.2em;
    font-weight: 700;
}

.product-card .discount-tag {
    background-color: var(--accent-gold);
    color: var(--text-dark);
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 0.75em;
    font-weight: 600;
    margin-left: 15px;
    display: inline-block;
    margin-bottom: 10px;
}

.product-card .stars {
    margin: 0 15px 10px;
    color: #ccc; /* Default star color */
}

.product-card .stars .gold {
    color: var(--accent-gold);
}

.product-card .card-actions {
    display: flex;
    justify-content: space-around;
    margin-top: 15px;
    padding: 0 15px;
}

.product-card .add-to-cart,
.product-card .buy-now {
    flex: 1;
    padding: 10px 0;
    margin: 0 5px;
    border-radius: 5px;
    font-weight: 600;
    transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease;
}

.product-card .add-to-cart {
    background-color: var(--primary-pink);
    color: var(--text-dark);
}

.product-card .add-to-cart:hover {
    background-color: var(--secondary-rose-gold);
    color: var(--text-light);
    transform: translateY(-2px);
}

.product-card .buy-now {
    background-color: var(--accent-gold);
    color: var(--text-dark);
}

.product-card .buy-now:hover {
    background-color: #e6c200;
    transform: translateY(-2px);
}


/* --- How to Use Palettes Section --- */
.how-to-use-section {
    padding: 80px 20px;
    background-color: #fefefe;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 50px;
    max-width: 1200px;
    margin: 0 auto;
}

.how-to-use-section .left-content {
    flex: 1;
    text-align: right;
    padding-right: 30px;
}

.how-to-use-section .left-content h2 {
    font-family: 'Playfair Display', serif;
    font-size: 2.8em;
    color: var(--text-dark);
    margin-bottom: 30px;
    line-height: 1.2;
}

.how-to-use-section .right-guide {
    flex: 1.2;
    display: flex;
    gap: 30px;
    align-items: flex-start;
}

.how-to-use-section .guide-points {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.how-to-use-section .guide-points li {
    font-size: 1.3em;
    font-weight: 600;
    color: #999; /* Light by default */
    cursor: pointer;
    transition: color 0.3s ease, font-size 0.3s ease;
}

.how-to-use-section .guide-points li.active {
    color: var(--secondary-rose-gold); /* Highlighted */
    font-size: 1.5em;
    text-shadow: 0 0 5px rgba(221,160,221,0.5); /* Subtle glow */
}

.how-to-use-section .guide-image-container {
    flex-shrink: 0;
    width: 400px;
    height: 500px;
    background-color: #eee; /* Placeholder background */
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.how-to-use-section .guide-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: opacity 0.5s ease; /* Smooth image change */
}

/* --- Our Best Sellers Section --- */
.best-sellers {
    padding: 80px 20px;
    text-align: center;
    background-color: var(--primary-pink);
}

.best-sellers h2 {
    font-family: 'Playfair Display', serif;
    font-size: 2.8em;
    margin-bottom: 40px;
    color: var(--text-dark);
}

/* Uses same .product-grid and .product-card styles as Sale section */

/* --- Customer Reviews Section --- */
/* --- Community Reviews Section --- */
.community-reviews {
    position: relative;
    padding: 80px 20px;
    background: linear-gradient(135deg, #fff7fa, #fbe9f0);
    text-align: center;
    overflow: hidden;
}

.community-heading {
    font-size: clamp(2rem, 5vw, 3rem);
    font-family: 'Playfair Display', serif;
    color: #d19a9a; /* Rose gold shade */
    font-weight: 600;
    margin-bottom: 40px;
    position: relative;
    z-index: 2;
}

.floating-reviews {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    position: relative;
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
}

.review-bubble {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: clamp(10px, 2vw, 15px);
    font-size: clamp(0.9rem, 2.5vw, 1rem);
    max-width: clamp(150px, 30vw, 200px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    color: #444;
    font-style: italic;
    text-align: center;
    flex: 1 1 auto;
    transition: transform 0.3s ease;
}

/* Star Ratings */
.review-stars {
    color: #b76e79; /* Rose gold for stars */
    font-size: clamp(12px, 2vw, 14px);
    margin-bottom: 8px;
    display: flex;
    justify-content: center;
    gap: 2px;
}

/* Floating animation for larger screens */
@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
}

@media (min-width: 768px) {
    .review-bubble {
        animation: float 6s ease-in-out infinite;
    }
    .review-bubble:nth-child(1) { animation-delay: 0s; }
    .review-bubble:nth-child(2) { animation-delay: 0.5s; }
    .review-bubble:nth-child(3) { animation-delay: 1s; }
    .review-bubble:nth-child(4) { animation-delay: 1.5s; }
    .review-bubble:nth-child(5) { animation-delay: 2s; }
    .review-bubble:nth-child(6) { animation-delay: 2.5s; }
    .review-bubble:nth-child(7) { animation-delay: 3s; }
    .review-bubble:nth-child(8) { animation-delay: 3.5s; }
    .review-bubble:nth-child(9) { animation-delay: 4s; }
}

/* Responsive adjustments */
@media (max-width: 767px) {
    .community-reviews {
        padding: 50px 15px;
    }

    .floating-reviews {
        flex-direction: column;
        align-items: center;
    }

    .review-bubble {
        max-width: 90%;
        margin-bottom: 15px;
        animation: none; /* Disable float on mobile for clarity */
    }

    .review-stars {
        font-size: 12px;
    }
}

@media (max-width: 576px) {
    .community-heading {
        font-size: 1.8rem;
    }

    .review-bubble {
        font-size: 0.85rem;
        padding: 10px;
    }
}

/* --- Before & After Section --- */
.before-after-section {
    padding: 80px 20px;
    text-align: center;
    background-color: #fff;
    position: relative;
}

.before-after-section h2 {
    font-family: 'Playfair Display', serif;
    font-size: 2.8em;
    margin-bottom: 40px;
    color: var(--text-dark);
}

.image-comparison {
    position: relative;
    width: 700px; /* Adjusted fixed width */
    height: 450px; /* Adjusted fixed height */
    margin: 0 auto;
    overflow: hidden;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    cursor: ew-resize; /* Cursor on entire comparison area */
}

.image-comparison img {
    position: absolute;
    width: 100%;
    height: 100%;
    object-fit: cover;
    pointer-events: none; /* Crucial: Images should not interfere with drag */
}

.image-comparison .before-image {
    clip-path: inset(0 50% 0 0); /* Show left half initially */
}

.image-comparison .after-image {
    clip-path: inset(0 0 0 50%); /* Show right half initially */
}

.image-comparison .resize-handle {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 50%;
    width: 4px;
    background-color: var(--accent-gold);
    cursor: ew-resize;
    z-index: 10;
    transform: translateX(-50%);
    display: flex; /* For circles */
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
}

.image-comparison .resize-handle::before,
.image-comparison .resize-handle::after {
    content: '';
    width: 20px;
    height: 20px;
    background-color: var(--accent-gold);
    border-radius: 50%;
    box-shadow: 0 0 10px rgba(255,215,0,0.5);
    flex-shrink: 0;
}

.image-comparison .label {
    position: absolute;
    top: 20px;
    font-weight: 600;
    font-size: 1.1em;
    color: var(--text-light);
    background-color: rgba(0,0,0,0.5);
    padding: 5px 10px;
    border-radius: 5px;
    z-index: 5;
}

.image-comparison .before-label {
    left: 20px;
}

.image-comparison .after-label {
    right: 20px;
}


/* --- Footer --- */
.site-footer {
    background-color: var(--text-dark);
    color: var(--text-light);
    padding: 50px 20px 20px;
    font-size: 0.95em;
}

.site-footer .footer-content {
    display: flex;
    justify-content: space-around;
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto 30px;
    flex-wrap: wrap;
}

.site-footer .footer-logo {
    font-family: 'Playfair Display', serif;
    font-size: 2em;
    font-weight: 700;
    letter-spacing: 1px;
    flex-basis: 100%; /* Take full width on smaller screens */
    text-align: center;
    margin-bottom: 20px;
}

.site-footer h3 {
    font-size: 1.2em;
    margin-bottom: 15px;
    color: var(--secondary-rose-gold);
}

.site-footer ul li {
    margin-bottom: 8px;
}

.site-footer ul li a {
    transition: color 0.3s ease;
}

.site-footer ul li a:hover {
    color: var(--accent-gold);
}

.footer-social a {
    margin-right: 15px;
    font-size: 1.5em;
    transition: color 0.3s ease;
}

.footer-social a:hover {
    color: var(--accent-gold);
}

.site-footer .footer-bottom {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,0.1);
    font-size: 0.85em;
    color: #aaa;
}

/* --- Chatbot Icon (Sticky) --- */
.chatbot-icon {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background-color: var(--accent-gold);
    color: var(--text-dark);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 1.8em;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    cursor: pointer;
    transition: transform 0.3s ease, background-color 0.3s ease;
    z-index: 1000;
}

.chatbot-icon:hover {
    transform: scale(1.1);
    background-color: #e6c200;
}

/* --- Scroll Reveal Animations --- */
.hidden-for-reveal {
    opacity: 0;
    transform: translateY(50px);
    transition: opacity 0.8s ease-out, transform 0.8s ease-out;
}

.revealed {
    opacity: 1;
    transform: translateY(0);
}

/* Ensure the main-navbar is not hidden by this reveal effect */
.main-navbar.hidden-for-reveal {
    opacity: 1;
    transform: translateY(0);
}

/* --- Responsive Design --- */
@media (max-width: 1200px) {
    .navbar-container {
        max-width: 960px;
    }
    .makeup-real-beauty .center-text h2 {
        font-size: 3em;
    }
}

@media (max-width: 992px) {
    .navbar-links { /* Hide main nav links on smaller screens */
        display: none;
    }
    .navbar-container {
        justify-content: space-between; /* Keep other elements spaced */
    }
    .main-navbar.scrolled-down .navbar-container {
        justify-content: space-between; /* Adjust for smaller scrolled state */
        gap: 15px;
    }
    .main-navbar.scrolled-down .navbar-center {
        order: 0; /* Reset order */
        gap: 15px;
    }
    .main-navbar.scrolled-down .navbar-logo {
        font-size: 1.8em; /* Keep logo size consistent */
    }
    .main-navbar.scrolled-down .navbar-right {
        margin-left: 0;
    }

    .makeup-real-beauty .content-wrapper {
        flex-direction: column;
        gap: 30px;
        align-items: center;
    }
    .makeup-real-beauty .left-media,
    .makeup-real-beauty .right-media {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        width: 100%;
        max-width: 600px; /* Control max width on smaller screens */
    }
    .makeup-real-beauty .img-upper,
    .makeup-real-beauty .video-lower,
    .makeup-real-beauty .img-right-upper,
    .makeup-real-beauty .img-right-lower {
        width: 48%; /* Two items per row */
        height: auto;
        margin: 0; /* Reset margins for better flow */
    }
    .makeup-real-beauty .video-lower {
        height: 200px;
    }
    .how-to-use-section {
        flex-direction: column;
        text-align: center;
    }
    .how-to-use-section .left-content {
        padding-right: 0;
        text-align: center;
    }
    .how-to-use-section .right-guide {
        flex-direction: column;
        align-items: center;
    }
    .responsive-video-section .responsive-video {
        width: calc(100% - 40px); /* Smaller margin for smaller screens */
        margin: 20px;
        height: 400px; /* Adjust height */
    }
    .reviews-container {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }
}

@media (max-width: 768px) {
    .hero-section h1 {
        font-size: 3em;
    }
    .hero-section p {
        font-size: 1.2em;
    }
    .main-navbar {
        padding: 10px 0;
    }
    .top-info-bar {
        /* display: none; /* Removed: Let JS handle hiding if needed */
    }
    .search-input.active {
        width: 150px;
    }
    .sale-of-the-month h2, .best-sellers h2, .how-to-use-section h2, .before-after-section h2, .customer-reviews h2 {
        font-size: 2.2em;
    }
    .product-grid {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); /* Mobile pe 2-3 cards */
    }
    .image-comparison {
        width: 100%;
        height: 350px;
    }
    .responsive-video-section .responsive-video {
        height: 300px; /* Further adjust height */
    }
}

@media (max-width: 480px) {
    .navbar-container {
        flex-direction: row; /* Keep elements in row */
        justify-content: space-around; /* Space them out */
    }
    .navbar-left, .navbar-center, .navbar-right {
        flex-basis: auto; /* Allow content to dictate size */
        margin: 0;
    }
    .navbar-logo {
        font-size: 1.3em;
    }
    .navbar-right {
        gap: 10px;
    }
    .hero-section h1 {
        font-size: 2em;
    }
    .hero-section p {
        font-size: 1em;
    }
    .makeup-real-beauty .center-text h2 {
        font-size: 2.5em;
    }
    .makeup-real-beauty .img-upper,
    .makeup-real-beauty .video-lower,
    .makeup-real-beauty .img-right-upper,
    .makeup-real-beauty .img-right-lower {
        width: 90%; /* Single column for media */
    }
    .image-comparison {
        height: 250px;
    }
   
}

.top-brands-section {
    background-color: var(--white);
    padding: 4rem 0;
    text-align: center;
    overflow: hidden;
}

.top-brands-section h2 {
    font-size: 2.5rem;
    color: var(--rose-gold);
    margin-bottom: 3rem;
}

.brands-marquee-container {
    overflow: hidden;
    /* position: relative; */
    width: 100%;
}



.brands-marquee img {
    height: 100px;
    width: auto;
   
    filter: grayscale(0%) opacity(1);
    border-radius: 20px
}

.brands-marquee img:hover {
     filter: grayscale(12%) opacity(0.9);
}


/* .brands-marquee {
    display: flex;
    gap: 4rem;
    width: max-content;
    align-items: center;
} */
.brands-marquee-container {
    overflow: hidden;
    width: 100%;
    display: flex; /* Keep both marquees on one line */
}

.brands-marquee {
    display: flex;
    gap: 4rem;
    align-items: center;
    flex-shrink: 0; /* Prevent wrapping */
}

 .user-dropdown {
      position: relative;
      display: inline-block;
    }

    /* Button/Icon */
    .user-icon {
      font-size: 20px;
      cursor: pointer;
      padding: 8px;
      border-radius: 50%;
      transition: background 0.3s;
      background: #f8f8f8;
    }

    .user-icon:hover {
      background-color: #e4e4e4;
    }

    /* Dropdown menu */
    .dropdown-menu {
      display: none;
      position: absolute;
      top: 100%; /* Below the icon */
      right: 0;
      background-color: white;
      min-width: 160px;
      box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
      border-radius: 6px;
      z-index: 100;
    }

    .dropdown-menu a {
      color: #333;
      padding: 10px 15px;
      text-decoration: none;
      display: block;
      font-size: 14px;
    }

    .dropdown-menu a:hover {
      background-color: #f2f2f2;
    }

    /* Show dropdown on hover of whole container */
    .user-dropdown:hover .dropdown-menu {
      display: block;
    }


</style>
<body>

    <div class="top-info-bar">
        <div class="info-content">
            <span>Free Shipping on All Orders!</span>
            <span>Shop Our New Collection Now!</span>
            <span>Limited Time Offers - Don't Miss Out!</span>
        </div>
    </div>

    


   
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
      <!-- <a href="my_profile.php">My Profile</a> -->
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

    <main>
       <section id="heroSection" class="hero-section">
    <div class="hero-slider-container">
        <img src="uploads/banner_home2.jpg" alt="Beauty Banner 1" class="hero-slide active">
        <img src="uploads/banner home.jpg" alt="Beauty Banner 2" class="hero-slide">
        <img src="uploads/banner_home2.jpg" alt="Beauty Banner 3" class="hero-slide">
        </div>

    <div class="slider-dots">
        <span class="dot active" data-slide="0"></span>
        <span class="dot" data-slide="1"></span>
        <span class="dot" data-slide="2"></span>
        </div>

    <h1>Your Beauty, Redefined</h1>
    <p>Discover the essence of elegance and self-expression.</p>
</section>

        <section class="sticky-text-section">
            <p class="sticky-text-content">✨ Elevate Your Look | Unleash Your Radiance | Auranest Beauty ✨</p>
        </section>

        <section class="shop-by-categories">
            <h2>Shop By Categories</h2>
            <div class="category-carousel-container">
                <div class="category-row category-cosmetics">
                    <div class="category-card" style="background-image: url('uploads/lipstick.jpg');"><h3>Lipsticks</h3></div>
                    <div class="category-card" style="background-image: url('uploads/foundation.jpg');"><h3>Foundations</h3></div>
                    <div class="category-card" style="background-image: url('uploads/eyeshadow copy.jpg');"><h3>Eyeshadows</h3></div>
                    <div class="category-card" style="background-image: url('uploads/serum.jpg');"><h3>Skincare</h3></div>
                    <div class="category-card" style="background-image: url('uploads/blush.jpg');"><h3>Blush</h3></div>
                    <div class="category-card" style="background-image: url('uploads/perfume.jpg');"><h3>Perfumes</h3></div>
                    <div class="category-card" style="background-image: url('uploads/lipstick.jpg');"><h3>Lipsticks</h3></div>
                    <div class="category-card" style="background-image: url('uploads/foundation.jpg');"><h3>Foundations</h3></div>
                    <div class="category-card" style="background-image: url('uploads/eyeshadow copy.jpg');"><h3>Eyeshadows</h3></div>
                    <div class="category-card" style="background-image: url('uploads/serum.jpg');"><h3>Skincare</h3></div>
                    <div class="category-card" style="background-image: url('uploads/blush.jpg');"><h3>Blush</h3></div>
                    <div class="category-card" style="background-image: url('uploads/perfume.jpg');"><h3>Perfumes</h3></div>
                </div>
            </div>
            <div class="category-carousel-container">
                <div class="category-row category-jewellery">
                    <div class="category-card" style="background-image: url('uploads/WhatsApp Image 2025-07-18 at 4.35.14 PM.jpeg');"><h3>Necklaces</h3></div>
                    <div class="category-card" style="background-image: url('uploads/WhatsApp Image 2025-07-18 at 4.36.19 PM.jpeg');"><h3>Rings</h3></div>
                    <div class="category-card" style="background-image: url('uploads/makeup img 17.jpg');"><h3>Earrings</h3></div>
                    <div class="category-card" style="background-image: url('uploads/WhatsApp Image 2025-07-18 at 4.42.43 PM.jpeg');"><h3>Bracelets</h3></div>
                    <div class="category-card" style="background-image: url('uploads/makeup img 19.jpg');"><h3>Pendants</h3></div>
                    <div class="category-card" style="background-image: url('uploads/makeup img6.jpg');"><h3>Brooches</h3></div>
                    <div class="category-card" style="background-image: url('uploads/WhatsApp Image 2025-07-18 at 4.35.14 PM.jpeg');"><h3>Necklaces</h3></div>
                    <div class="category-card" style="background-image: url('uploads/WhatsApp Image 2025-07-18 at 4.36.19 PM.jpeg');"><h3>Rings</h3></div>
                    <div class="category-card" style="background-image: url('uploads/makeup img 17.jpg');"><h3>Earrings</h3></div>
                    <div class="category-card" style="background-image: url('uploads/WhatsApp Image 2025-07-18 at 4.42.43 PM.jpeg');"><h3>Bracelets</h3></div>
                    <div class="category-card" style="background-image: url('uploads/makeup img 19.jpg');"><h3>Pendants</h3></div>
                    <div class="category-card" style="background-image: url('uploads/makeup img6.jpg');"><h3>Brooches</h3></div>
                </div>
            </div>
        </section>

       <section class="makeup-real-beauty" id="realBeautySection">
    <div class="content-wrapper">
        <div class="left-media">
            <img src="uploads/makeup img 25.jpg" alt="Beauty Image 1" class="img-upper">
            <video muted autoplay loop playsinline class="video-lower">
                <source src="uploads/video4.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        <div class="center-text">
            <h2>Makeup for the Real Beauty</h2>
            <button class="shop-now-btn">Shop Now</button>
        </div>
        <div class="right-media">
            <img src="uploads/skincare1.jpg" alt="Beauty Image 2" class="img-right-upper">
            <img src="uploads/cosmetic.jpg" alt="Beauty Image 3" class="img-right-lower">
        </div>
    </div>
</section>


        <section class="responsive-video-section">
            <video controls autoplay muted loop playsinline class="responsive-video">
                <source src="uploads/video.mp4">
                Your browser does not support the video tag.
            </video>
        </section>


        <?php
include 'db.php'; // DB connection

$sql = "SELECT name, description, price, image FROM products";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    echo '<div class="product-grid">';
    while ($row = mysqli_fetch_assoc($result)) {
        $name = $row['name'];
        $description = $row['description'];
        $price = $row['price'];
        $image = $row['image']; // Make sure your image path is correct

       echo '
<form method="POST" action="cart.php">
    <input type="hidden" name="product_name" value="' . htmlspecialchars($name) . '">
    <input type="hidden" name="product_price" value="' . htmlspecialchars($price) . '">
    <input type="hidden" name="product_image" value="' . htmlspecialchars($image) . '">
    
    <div class="product-card">
        <img src="uploads/' . htmlspecialchars($image) . '" alt="' . htmlspecialchars($name) . '">
        <h3>' . htmlspecialchars($name) . '</h3>
        <p class="description">' . htmlspecialchars($description) . '</p>
        <div class="price">
            <span class="old-price">$' . number_format($price * 2, 2) . '</span>
            <span class="new-price">$' . number_format($price, 2) . '</span>
        </div>
        <span class="discount-tag">50% OFF</span>
        <div class="stars">
            <i class="fas fa-star gold"></i><i class="fas fa-star gold"></i>
            <i class="fas fa-star gold"></i><i class="fas fa-star gold"></i>
            <i class="fas fa-star gold"></i>
        </div>
        <div class="card-actions">
            <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
            <button type="button" class="buy-now">Buy Now</button>
        </div>
    </div>
</form>';

    }
    echo '</div>';
} else {
    echo "<p>No products found.</p>";
}
?>

        <!-- <?php
         include "db.php";
         
         
        ?>

        <section class="sale-of-the-month">
            <h2>Sale of the Month <span class="timer" id="saleTimer"></span></h2>
            <div class="product-grid">
                <div class="product-card">
                    <img src="eyeshadow.jpg" alt="Product 1">
                    <h3>Auranest Palette Pro</h3>
                    <p class="description">Velvety smooth eyeshadows for a flawless finish.</p>
                    <div class="price">
                        <span class="old-price">$50.00</span>
                        <span class="new-price">$25.00</span>
                    </div>
                    <span class="discount-tag">50% OFF</span>
                    <div class="stars">
                        <i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i>
                    </div>
                    <div class="card-actions">
                        <button class="add-to-cart">Add to Cart</button>
                        <button class="buy-now">Buy Now</button>
                    </div>
                </div>
                
                </div>
        </section> -->

        <section class="how-to-use-section">
            <div class="left-content">
                <h2>How to Use Auranest Palettes</h2>
                <button class="shop-now-btn">Shop Now</button>
            </div>
            <div class="right-guide">
                <ul class="guide-points">
                    <li data-image="uploads/makeup img8.jpg">Concelar</li>
                    <li data-image="uploads/makeup img7.jpg">Blender</li>
                    <li data-image="uploads/makeup img1.jpg">Lipstick</li>
                    <li data-image="uploads/makeup img4.jpg">Blush</li>
                    <li data-image="uploads/shade6.jpg">Powder</li>
                </ul>
                <div class="guide-image-container">
                    <img src="pictures/powder.webp" alt="Guide Image" id="guideImage">
                </div>
            </div>
        </section>

        <section class="best-sellers">
            <h2>Loved by our Auranest Community</h2>
            <div class="product-grid">
                <div class="product-card">
                    <img src="uploads/foundation.jpg" alt="Best Seller 1">
                    <h3>Luminous Foundation</h3>
                    <p class="description">Flawless coverage for a natural look.</p>
                    <div class="price">
                        <span class="new-price">$45.00</span>
                    </div>
                    <div class="stars">
                        <i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i>
                    </div>
                    <div class="card-actions">
                        <button class="add-to-cart">Add to Cart</button>
                        <button class="buy-now">Buy Now</button>
                    </div>
                </div>
                 <div class="product-card">
                    <img src="uploads/toner.jpg" alt="Best Seller 2">
                    <h3>Hydrating Face Mist</h3>
                    <p class="description">Refresh your skin throughout the day.</p>
                    <div class="price">
                        <span class="new-price">$22.00</span>
                    </div>
                    <div class="stars">
                        <i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star"></i>
                    </div>
                    <div class="card-actions">
                        <button class="add-to-cart">Add to Cart</button>
                        <button class="buy-now">Buy Now</button>
                    </div>
                </div>
                <div class="product-card">
                    <img src="uploads/liner.jpg" alt="Best Seller 3">
                    <h3>Defining Eyeliner</h3>
                    <p class="description">Achieve sharp and precise lines.</p>
                    <div class="price">
                        <span class="new-price">$18.00</span>
                    </div>
                    <div class="stars">
                        <i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i>
                    </div>
                    <div class="card-actions">
                        <button class="add-to-cart">Add to Cart</button>
                        <button class="buy-now">Buy Now</button>
                    </div>
                </div>
                <div class="product-card">
                    <img src="uploads/makeup img13.jpg" alt="Best Seller 4">
                    <h3>Velvet Blusher</h3>
                    <p class="description">A soft, natural flush for your cheeks.</p>
                    <div class="price">
                        <span class="new-price">$28.00</span>
                    </div>
                    <div class="stars">
                        <i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <div class="card-actions">
                        <button class="add-to-cart">Add to Cart</button>
                        <button class="buy-now">Buy Now</button>
                    </div>
                </div>
            </div>
        </section>

        <section class="before-after-section">
            <h2>See the Auranest Difference</h2>
            <div class="image-comparison" id="imageComparison">
                <img src="uploads/Before.webp" alt="Before Look" class="before-image">
                <img src="uploads/After.webp" alt="After Look" class="after-image">
                <div class="resize-handle"></div>
                <span class="label before-label">Before</span>
                <span class="label after-label">After</span>
            </div>
        </section>

         <section class="top-brands-section">
            <h2>Our Partner Brands</h2>
            <div class="brands-marquee-container">
                <div class="brands-marquee">
                    <img src="uploads/makeup img3.jpg" alt="L'Oréal">
                    <img src="uploads/makeup img5.jpg" alt="Dior">
                    <img src="uploads/makeup img16.jpg" alt="Chanel">
                    <img src="uploads/makeup img 21.jpg" alt="Estée Lauder">
                    <img src="uploads/MAC.png" alt="MAC">
                    <img src="uploads/tiffany.jpg" alt="Tiffany & Co.">
                    <img src="uploads/makeup img14.jpg" alt="Cartier">
                    <img src="uploads/misss rose.png" alt="Miss Rose">
                    <img src="uploads/makeup img 10.jpg" alt="Guerlain">
                    <img src="uploads/Swarovski.webp" alt="Swarovski">
                    <img src="uploads/makeup img 9.jpg" alt="Fenty Beauty">
                    <img src="uploads/nars.jpg" alt="NARS Cosmetics">
                     <img src="uploads/makeup img3.jpg" alt="L'Oréal">
                    <img src="uploads/makeup img5.jpg" alt="Dior">
                    <img src="uploads/makeup img16.jpg" alt="Chanel">
                    <img src="uploads/makeup img 21.jpg" alt="Estée Lauder">
                    <img src="uploads/MAC.png" alt="MAC">
                    <img src="uploads/tiffany.jpg" alt="Tiffany & Co.">
                    <img src="uploads/makeup img14.jpg" alt="Cartier">
                    <img src="uploads/misss rose.png" alt="Miss Rose">
                    <img src="uploads/makeup img 10.jpg" alt="Guerlain">
                    <img src="uploads/Swarovski.webp" alt="Swarovski">
                    <img src="uploads/makeup img 9.jpg" alt="Fenty Beauty">
                    <img src="uploads/nars.jpg" alt="NARS Cosmetics">
                </div>
            </div>
        </section>
<section class="community-reviews" id="communitySection">
        <h2 class="community-heading">What Our Community Says</h2>
        <div class="floating-reviews">
            <?php foreach ($reviews as $index => $review): ?>
            <div class="review-bubble" style="animation-delay: <?php echo $index * 0.5; ?>s;">
                <div class="review-stars">
                    <?php echo displayStars($review['rating']); ?>
                </div>
                “<?php echo htmlspecialchars($review['message']); ?>” – <?php echo htmlspecialchars($review['name']); ?>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    </main>

    <footer class="site-footer">
        <div class="footer-content">
            <div class="footer-logo">AURANEST</div>
            <div class="footer-links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#">Shop All</a></li>
                    <li><a href="#">FAQs</a></li>
                    <li><a href="#">Shipping & Returns</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="footer-social">
                <h3>Follow Us</h3>
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
                <a href="#"><i class="fab fa-pinterest"></i></a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Auranest. All rights reserved.</p>
        </div>
    </footer>

    <div class="chatbot-icon">
        <i class="fas fa-comments"></i>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/Draggable.min.js"></script>
    
    <script src="script.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', () => {
    console.log("Auranest Script Loaded!");

    gsap.registerPlugin(ScrollTrigger);

    /* ================================
       Dynamic Main Content Padding
    =================================== */
    const mainContent = document.querySelector('main');
    const mainNavbar = document.getElementById('mainNavbar');
    const topInfoBar = document.querySelector('.top-info-bar');

    function setMainContentPadding() {
        if (mainContent && mainNavbar) {
            let totalHeight = mainNavbar.offsetHeight;
            if (topInfoBar && window.getComputedStyle(topInfoBar).display !== 'none') {
                totalHeight += topInfoBar.offsetHeight;
            }
            mainContent.style.paddingTop = `${totalHeight}px`;
        }
    }
    setMainContentPadding();
    window.addEventListener('resize', setMainContentPadding);
 // nav
    /* ================================
       Search Bar Toggle
    =================================== */
    // const searchIcon = document.getElementById('searchIcon');
    // const searchInput = document.getElementById('searchInput');
    // if (searchIcon && searchInput) {
    //     searchIcon.addEventListener('click', (e) => {
    //         e.stopPropagation();
    //         searchInput.classList.toggle('active');
    //         if (searchInput.classList.contains('active')) {
    //             searchInput.focus();
    //         } else {
    //             searchInput.value = '';
    //         }
    //     });
    //     document.addEventListener('click', (e) => {
    //         if (!searchIcon.contains(e.target) && !searchInput.contains(e.target)) {
    //             searchInput.classList.remove('active');
    //             searchInput.value = '';
    //         }
    //     });
    // }

    /* ================================
       Countdown Timer
    =================================== */
    const saleTimerElement = document.getElementById('saleTimer');
    const targetDate = new Date().getTime() + (7 * 24 * 60 * 60 * 1000);
    setInterval(() => {
        const now = new Date().getTime();
        const diff = targetDate - now;
        if (diff < 0) {
            if (saleTimerElement) saleTimerElement.innerHTML = "SALE ENDED!";
            return;
        }
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const secs = Math.floor((diff % (1000 * 60)) / 1000);
        if (saleTimerElement) {
            saleTimerElement.innerHTML = `${days}d ${hours}h ${mins}m ${secs}s`;
        }
    }, 1000);

    /* ================================
       How to Use Guide Image Changer
    =================================== */
    const guidePoints = document.querySelectorAll('.how-to-use-section .guide-points li');
    const guideImage = document.getElementById('guideImage');
    let guideIndex = 0, guideInterval;

    function changeGuideContent(index) {
        if (!guideImage) return;
        guidePoints.forEach(p => p.classList.remove('active'));
        guidePoints[index].classList.add('active');
        gsap.to(guideImage, {
            opacity: 0, duration: 0.3, onComplete: () => {
                guideImage.src = guidePoints[index].dataset.image;
                gsap.to(guideImage, { opacity: 1, duration: 0.3 });
            }
        });
    }
    function startGuideAnimation() {
        clearInterval(guideInterval);
        changeGuideContent(guideIndex);
        guideInterval = setInterval(() => {
            guideIndex = (guideIndex + 1) % guidePoints.length;
            changeGuideContent(guideIndex);
        }, 3000);
    }
    guidePoints.forEach((point, i) => {
        point.addEventListener('click', () => {
            clearInterval(guideInterval);
            guideIndex = i;
            changeGuideContent(guideIndex);
            setTimeout(startGuideAnimation, 5000);
        });
    });
    startGuideAnimation();

    /* ================================
       Before & After Image Comparison
    =================================== */
    const imageComparison = document.getElementById('imageComparison');
    const resizeHandle = imageComparison?.querySelector('.resize-handle');
    const beforeImage = imageComparison?.querySelector('.before-image');
    const afterImage = imageComparison?.querySelector('.after-image');
    let isDragging = false;

    if (resizeHandle && beforeImage && afterImage) {
        resizeHandle.addEventListener('mousedown', () => isDragging = true);
        resizeHandle.addEventListener('touchstart', (e) => { isDragging = true; e.preventDefault(); }, { passive: false });
        document.addEventListener('mouseup', () => isDragging = false);
        document.addEventListener('touchend', () => isDragging = false);

        function handleDrag(x) {
            const rect = imageComparison.getBoundingClientRect();
            x -= rect.left;
            if (x < 0) x = 0; if (x > rect.width) x = rect.width;
            const percent = (x / rect.width) * 100;
            resizeHandle.style.left = `${percent}%`;
            beforeImage.style.clipPath = `inset(0 ${100 - percent}% 0 0)`;
        }
        document.addEventListener('mousemove', (e) => { if (isDragging) handleDrag(e.clientX); });
        document.addEventListener('touchmove', (e) => { if (isDragging) handleDrag(e.touches[0].clientX); }, { passive: false });
    }

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

    /* ================================
       Hero Banner Slider
    =================================== */
    const heroSlides = document.querySelectorAll('.hero-slide');
    const sliderDots = document.querySelectorAll('.slider-dots .dot');
    let currentSlide = 0, autoSlide;

    function showSlide(i) {
        currentSlide = (i + heroSlides.length) % heroSlides.length;
        heroSlides.forEach((slide, idx) => {
            gsap.to(slide, { x: idx === currentSlide ? "0%" : "-100%", opacity: idx === currentSlide ? 1 : 0, duration: 0.8 });
        });
        sliderDots.forEach((dot, idx) => dot.classList.toggle('active', idx === currentSlide));
    }
    function startAutoSlide() {
        clearInterval(autoSlide);
        autoSlide = setInterval(() => showSlide(currentSlide + 1), 5000);
    }
    sliderDots.forEach((dot, i) => dot.addEventListener('click', () => { showSlide(i); startAutoSlide(); }));
    if (heroSlides.length) { showSlide(0); startAutoSlide(); }

    /* ================================
       Shop Categories Marquee (Smooth)
    =================================== */
    const createLoop = (element, speed, dir = 'left') => {
        const width = element.scrollWidth / 2;
        if (dir === 'left') {
            gsap.to(element, { x: -width, duration: speed, ease: "none", repeat: -1, modifiers: { x: gsap.utils.unitize(x => parseFloat(x) % width) } });
        } else {
            gsap.fromTo(element, { x: -width }, { x: 0, duration: speed, ease: "none", repeat: -1, modifiers: { x: gsap.utils.unitize(x => parseFloat(x) % width) } });
        }
    };
    createLoop(document.querySelector('.category-cosmetics'), 40, 'right');
    createLoop(document.querySelector('.category-jewellery'), 40, 'left');

    /* ================================
       Makeup Real Beauty Slip Effect
    =================================== */
    gsap.fromTo("#realBeautySection .center-text",
        { y: -150, scale: 1.05, opacity: 0.95 },
        {
            y: 180,
            scale: 1,
            opacity: 1,
            ease: "power1.inOut",
            scrollTrigger: {
                trigger: "#realBeautySection",
                start: "top bottom",
                end: "bottom top",
                scrub: 1.2
            }
        }
    );

    /* ================================
       Shop Button Hover & Ripple
    =================================== */
    const shopBtn = document.querySelector(".shop-now-btn");
    if (shopBtn) {
        shopBtn.addEventListener("mouseenter", () => gsap.to(shopBtn, { boxShadow: "0 0 20px rgba(218,165,32,0.8)", backgroundColor: "#FFD700", color: "#000", duration: 0.3 }));
        shopBtn.addEventListener("mouseleave", () => gsap.to(shopBtn, { boxShadow: "0 0 0 rgba(0,0,0,0)", backgroundColor: "#fff", color: "#333", duration: 0.3 }));
        shopBtn.addEventListener("click", (e) => {
            const ripple = document.createElement("span");
            ripple.classList.add("ripple");
            shopBtn.appendChild(ripple);
            const rect = shopBtn.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${e.clientX - rect.left - size / 2}px`;
            ripple.style.top = `${e.clientY - rect.top - size / 2}px`;
            setTimeout(() => ripple.remove(), 600);
        });
        gsap.to(shopBtn, { scale: 1.05, duration: 1.2, repeat: -1, yoyo: true, ease: "power1.inOut" });
    }

    /* ================================
       Product Cards Animation (Optimized)
    =================================== */
    gsap.from(".product-card", {
        y: 30,
        opacity: 1,
        duration: 0.8,
        ease: "power2.out",
        stagger: 0.1,
        scrollTrigger: {
            trigger: ".shop-by-categories",
            start: "top 90%",
            toggleActions: "play none none reverse"
        }
    });
// Smooth, subtle animation for product cards
gsap.utils.toArray(".product-card").forEach((card, i) => {
    gsap.from(card, {
        opacity: 0,
        y: 15, // very slight upward shift
        scale: 0.98, // tiny zoom-in feel
        duration: 0.5,
        ease: "power1.out",
        delay: i * 0.05, // small stagger for natural flow
        scrollTrigger: {
            trigger: card,
            start: "top 90%",
            toggleActions: "play none none reverse"
        }
    });
});

    /* ================================
       Top Brands Marquee
    =================================== */
    const marquee = document.querySelector(".brands-marquee");
    const totalWidth = marquee.scrollWidth / 2;
    gsap.to(marquee, {
        x: `-=${totalWidth}`,
        duration: 50,
        ease: "none",
        repeat: -1,
        modifiers: { x: gsap.utils.unitize(x => parseFloat(x) % totalWidth) }
    });
    /* ================================
       reviews section
    =================================== */
gsap.registerPlugin();

function animateReviews() {
    const reviews = document.querySelectorAll(".review-bubble");
    reviews.forEach((review, index) => {
        // Only animate on larger screens
        if (window.innerWidth >= 768) {
            gsap.to(review, {
                y: -10,
                duration: 3,
                ease: "sine.inOut",
                repeat: -1,
                yoyo: true,
                delay: index * 0.5
            });
        }
    });
}

// Run animation on load
animateReviews();

// Re-run on window resize to handle responsiveness
window.addEventListener("resize", animateReviews);
    /* ================================
       Golden Glitter Cursor Trail
    =================================== */
    const style = document.createElement('style');
    style.innerHTML = `
        .glitter-dot {
            position: fixed;
            width: 6px;
            height: 6px;
            background-color: rgba(255, 215, 0, 0.6);
            border-radius: 50%;
            pointer-events: none;
            z-index: 9999;
            opacity: 0;
            transform: scale(0);
            animation: glitterFadeOut 0.8s forwards;
        }
        @keyframes glitterFadeOut {
            0% { opacity: 1; transform: scale(1); }
            100% { opacity: 0; transform: scale(0.5) translate(10px, 10px); }
        }
    `;
    document.head.appendChild(style);
    document.addEventListener('mousemove', (e) => {
        const glitter = document.createElement('div');
        glitter.className = 'glitter-dot';
        glitter.style.left = `${e.clientX}px`;
        glitter.style.top = `${e.clientY}px`;
        document.body.appendChild(glitter);
        glitter.addEventListener('animationend', () => glitter.remove());
    });
});


// nav

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
// End of DOMContentLoaded</script>
</body>
</html>