<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- The title can be made dynamic -->
    <title>AIUB Student Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./assets/style.css">
</head>
<body>
    <!-- The login overlay is now on its own page (login.php) -->

    <!-- Header Section -->
    <header>
        <div class="header-container">
            <div class="logo-container">
                <a href="index.php" class="logo">AIUB<span>Portal</span></a>
            </div>
            <div class="nav-container">
                <ul class="nav-menu">
                    <li><a href="index.php?page=dashboard" class="<?php echo ($page === 'dashboard' ? 'active' : ''); ?>"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="index.php?page=registered-courses" class="<?php echo ($page === 'registered-courses' ? 'active' : ''); ?>"><i class="fas fa-book"></i> Courses</a></li>
                    <li><a href="index.php?page=academic-results" class="<?php echo ($page === 'academic-results' ? 'active' : ''); ?>"><i class="fas fa-chart-line"></i> Results</a></li>
                    <li><a href="index.php?page=financial-statements" class="<?php echo ($page === 'financial-statements' ? 'active' : ''); ?>"><i class="fas fa-file-invoice-dollar"></i> Finance</a></li>
                </ul>
                <div class="user-controls">
                    <!-- Notification logic can be added later -->
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <div class="notification-badge">3</div>
                        <!-- ... (notification dropdown) ... -->
                    </div>
                    <div class="user-profile">
                        <a href="index.php?page=profile" aria-label="User Profile"><i class="fas fa-user"></i></a>
                    </div>
                    <a href="logout.php" id="logout-btn" title="Logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </header>