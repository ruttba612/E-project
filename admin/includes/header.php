<?php
$profile_pic = isset($profile_pic) ? $profile_pic : 'assets/logo.png';
$greeting = isset($greeting) ? $greeting : 'Hello, Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auranest Admin</title>
    <style>
        .topbar {
            background: linear-gradient(90deg, #FFE4E1, #FFCCD5);
            position: fixed;
            width: calc(100% - 260px);
            top: 0;
            left: 260px;
            height: 60px;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            transition: width 0.3s ease, left 0.3s ease;
            overflow: visible; /* Ensure dropdowns are not clipped */
        }
        body.sidebar-toggled .topbar {
            width: calc(100% - 80px);
            left: 80px;
        }
        .topbar .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2C2C2C;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            transition: color 0.3s ease;
            margin-right: auto;
        }
        .topbar .navbar-brand:hover {
            color: #D4AF37;
        }
        .topbar .navbar-nav {
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
        }
        .topbar .nav-item {
            margin: 0 5px;
        }
        .topbar .nav-link {
            color: #2C2C2C !important;
            padding: 0 10px;
            height: 60px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        .topbar .nav-link:hover {
            color: #D4AF37 !important;
            background: rgba(255, 204, 213, 0.3);
            border-radius: 8px;
        }
        .topbar .dropdown-menu {
            background: #FFF5F5;
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-top: 10px;
            min-width: 220px;
            position: absolute;
            right: 0; /* Align dropdown to right */
            left: auto;
            transform: translateX(0); /* Prevent shifting */
        }
        .topbar .dropdown-header {
            background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
            color: #2C2C2C;
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 8px 8px 0 0;
        }
        .topbar .dropdown-item {
            color: #2C2C2C;
            padding: 8px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        .topbar .dropdown-item:hover {
            background: linear-gradient(90deg, #FFCCD5, #FFE4E1);
            color: #2C2C2C;
        }
        .topbar .dropdown-item i {
            margin-right: 10px;
            color: #2C2C2C;
        }
        .topbar .badge-counter {
            position: absolute;
            top: 8px;
            right: 5px;
            background-color: #D4AF37;
            color: #FFF5F5;
            font-size: 0.7rem;
            padding: 4px 7px;
            border-radius: 10px;
            transform: scale(0.75);
        }
        .topbar .img-profile {
            height: 34px;
            width: 34px;
            border-radius: 50%;
            object-fit: cover;
            margin-left: 10px;
            border: 2px solid #FFCCD5;
            transition: transform 0.3s ease;
        }
        .topbar .img-profile:hover {
            transform: scale(1.1);
        }
        .topbar-divider {
            border-right: 1px solid rgba(0, 0, 0, 0.1);
            height: 40px;
            margin: 0 10px;
        }
        .icon-circle {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            position: absolute;
            bottom: 0;
            right: 0;
            border: 1px solid #FFF5F5;
        }
        @media (max-width: 768px) {
            .topbar {
                width: calc(100% - 80px);
                left: 80px;
                padding: 0 10px;
            }
            body.sidebar-toggled .topbar {
                width: calc(100% - 80px);
                left: 80px;
            }
            .topbar .navbar-brand {
                font-size: 1.3rem;
            }
            .topbar .nav-link {
                padding: 0 8px;
                font-size: 0.9rem;
            }
            .topbar .img-profile {
                height: 30px;
                width: 30px;
            }
            .topbar .badge-counter {
                transform: scale(0.65);
                right: 2px;
                top: 6px;
            }
            .topbar-divider {
                margin: 0 5px;
            }
            .topbar .dropdown-menu {
                right: 10px; /* Adjust for mobile */
                min-width: 180px;
            }
        }
        @media (max-width: 576px) {
            .topbar {
                width: 100%;
                left: 0;
                padding: 0 5px;
                justify-content: space-between;
            }
            body.sidebar-toggled .topbar {
                width: 100%;
                left: 0;
            }
            .topbar .navbar-brand {
                font-size: 1.2rem;
                margin-right: 5px;
            }
            .topbar .nav-link {
                padding: 0 5px;
                font-size: 0.85rem;
            }
            .topbar .navbar-nav {
                flex-direction: row;
                flex-wrap: nowrap;
            }
            .topbar .nav-item {
                margin: 0 2px;
            }
            .topbar .dropdown-menu {
                right: 5px;
                min-width: 160px;
            }
        }
    </style>
</head>
<body>
<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-2">
        <i class="fa fa-bars"></i>
    </button>
    <a class="navbar-brand d-none d-sm-inline" href="index.php">Auranest Admin</a>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <span class="badge badge-danger badge-counter">0+</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <li><h6 class="dropdown-header">Alerts Center</h6></li>
                <li><a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="mr-3">
                        <div class="icon-circle bg-warning">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500"><?php echo date('M d, Y'); ?></div>
                        <span class="font-weight-bold">No low stock items!</span>
                    </div>
                </a></li>
                <li><a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a></li>
            </ul>
        </li>
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-envelope fa-fw"></i>
                <span class="badge badge-danger badge-counter">0</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                <li><h6 class="dropdown-header">Message Center</h6></li>
                <li><a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="dropdown-list-image mr-3">
                        <img class="rounded-circle" src="assets/logo.png" alt="User Profile">
                        <div class="status-indicator bg-success"></div>
                    </div>
                    <div class="font-weight-bold">
                        <div class="text-truncate">No new messages</div>
                    </div>
                </a></li>
                <li><a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a></li>
            </ul>
        </li>
        <div class="topbar-divider d-none d-sm-block"></div>
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-dark small"><?php echo htmlspecialchars($greeting); ?></span>
                <img class="img-profile rounded-circle" src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile">
            </a>
            <ul class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="settings.php"><i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>My Profile</a></li>
                <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>
</body>
</html>