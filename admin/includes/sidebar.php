<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', 'Lora', sans-serif;
    background-color: #FFF5F5;
    color: #2C2C2C;
    line-height: 1.6;
    overflow-x: hidden;
}

#wrapper {
    display: flex;
    min-height: 100vh;
}

#content-wrapper {
    flex-grow: 1;
    margin-left: 260px;
    padding-top: 70px;
    padding-bottom: 80px; /* To account for fixed footer */
    transition: margin-left 0.3s ease;
}

.sidebar {
    background: linear-gradient(180deg, #FFE4E1 0%, #FFCCD5 100%);
    width: 260px;
    min-width: 260px;
    max-width: 100vw;
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    max-height: 100vh;
    z-index: 1001;
    box-shadow: 3px 0 15px rgba(0, 0, 0, 0.08);
    transition: width 0.3s ease;
    overflow-x: visible;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.sidebar.toggled {
    width: 80px;
    min-width: 80px;
}

body.sidebar-toggled #content-wrapper {
    margin-left: 80px;
}

.sidebar-brand {
    padding: 12px;
    background: #FFF5F5;
    color: #2C2C2C;
    font-weight: 600;
    font-size: 1.4rem;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.sidebar-brand-icon i {
    font-size: 1.5rem;
    margin-right: 8px;
    color: #de5c74ff;
    transition: transform 0.3s ease;
}

.sidebar.toggled .sidebar-brand-text {
    display: none;
}

.sidebar.toggled .sidebar-brand-icon i {
    margin-right: 0;
    transform: scale(1.2);
}

.sidebar-divider {
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    margin: 6px 10px;
    opacity: 0.5;
}

.sidebar-heading {
    font-size: 0.8rem;
    color: #2C2C2C;
    padding: 6px 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.7px;
    background: rgba(0, 0, 0, 0.02);
    margin: 4px 0;
}

.sidebar.toggled .sidebar-heading {
    display: none;
}

.nav-item .nav-link {
    color: #2C2C2C;
    font-weight: 500;
    padding: 8px 12px;
    border-radius: 6px;
    margin: 3px 6px;
    display: flex;
    align-items: center;
    position: relative;
    transition: all 0.3s ease;
}

.nav-item .nav-link i {
    font-size: 1.1rem;
    margin-right: 8px;
    color: #FFCCD5;
    transition: color 0.3s ease, transform 0.3s ease;
}

.nav-item .nav-link:hover {
    background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
    color: #2C2C2C;
    transform: translateX(5px) scale(1.02);
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

.nav-item .nav-link:hover i {
    color: #D4AF37;
    transform: scale(1.2);
}

.nav-item.active .nav-link {
    background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
    color: #2C2C2C;
    font-weight: 600;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

.nav-item.active .nav-link i {
    color: #D4AF37;
    transform: scale(1.2);
}

.sidebar.toggled .nav-link span {
    display: none;
}

.sidebar.toggled .nav-link i {
    margin-right: 0;
    font-size: 1.3rem;
}

.sidebar.toggled .nav-link:hover:after {
    content: attr(data-tooltip);
    position: absolute;
    left: 90px;
    top: 50%;
    transform: translateY(-50%);
    background: #2C2C2C;
    color: #FFF5F5;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.8rem;
    white-space: nowrap;
    z-index: 1002;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.nav-item .badge {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background-color: #D4AF37;
    color: #FFF5F5;
    font-size: 0.65rem;
    padding: 3px 6px;
    border-radius: 10px;
    transition: opacity 0.3s ease;
}

.sidebar.toggled .nav-item .badge {
    opacity: 0;
}

.sidebar.toggled .nav-item:hover .badge {
    opacity: 1;
}

.sidebar-toggle {
    background-color: #FFCCD5;
    color: #2C2C2C;
    border: none;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    margin: 10px auto;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.sidebar-toggle:hover {
    background-color: #D4AF37;
    color: #FFF5F5;
    transform: scale(1.15);
}

@media (max-width: 768px) {
    .sidebar {
        width: 80px;
        min-width: 80px;
        overflow-y: auto;
        max-height: 100vh;
    }
    #content-wrapper {
        margin-left: 80px;
        padding-bottom: 60px;
    }
    .sidebar.toggled {
        width: 260px;
        min-width: 260px;
    }
    body.sidebar-toggled #content-wrapper {
        margin-left: 260px;
    }
    .sidebar-brand-text,
    .nav-link span,
    .sidebar-heading {
        display: none;
    }
    .sidebar.toggled .sidebar-brand-text,
    .sidebar.toggled .nav-link span,
    .sidebar.toggled .sidebar-heading {
        display: block;
    }
    .nav-item .badge {
        opacity: 0;
    }
    .sidebar.toggled .nav-item .badge {
        opacity: 1;
    }
}
</style>
<body>
        <!-- Sidebar -->
<ul class="navbar-nav sidebar accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon"><i class="fas fa-gem"></i></div>
        <div class="sidebar-brand-text mx-3">Auranest</div>
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php" data-tooltip="Dashboard">
            <i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span><span class="badge badge-warning ms-2">3</span>
        </a>
    </li>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Core Management</div>
    <li class="nav-item"><a class="nav-link" href="products.php" data-tooltip="Products"><i class="fas fa-fw fa-tshirt"></i><span>Products</span><span class="badge badge-warning ms-2">12</span></a></li>
    <li class="nav-item"><a class="nav-link" href="categories.php" data-tooltip="Categories"><i class="fas fa-fw fa-sitemap"></i><span>Categories</span></a></li>
    <li class="nav-item"><a class="nav-link" href="orders.php" data-tooltip="Orders"><i class="fas fa-fw fa-shopping-cart"></i><span>Orders</span><span class="badge badge-warning ms-2">5</span></a></li>
    <li class="nav-item"><a class="nav-link" href="customers.php" data-tooltip="Customers"><i class="fas fa-fw fa-users"></i><span>Customers</span></a></li>
    <li class="nav-item"><a class="nav-link" href="brands.php" data-tooltip="Brands"><i class="fas fa-fw fa-copyright"></i><span>feedbacks</span></a></li>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Analytics & Marketing</div>
    <li class="nav-item"><a class="nav-link" href="reports.php" data-tooltip="Reports"><i class="fas fa-fw fa-chart-line"></i><span>Reports</span></a></li>
    <li class="nav-item"><a class="nav-link" href="promotions.php" data-tooltip="Promotions"><i class="fas fa-fw fa-bullhorn"></i><span>Promotions</span><span class="badge badge-warning ms-2">2</span></a></li>
    <li class="nav-item"><a class="nav-link" href="cms.php" data-tooltip="CMS Pages"><i class="fas fa-fw fa-pencil-alt"></i><span>CMS Pages</span></a></li>
    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-angle-left"></i></button>
    </div>
</ul>
</body>
</html>