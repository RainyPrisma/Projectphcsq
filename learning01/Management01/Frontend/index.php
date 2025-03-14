<?php
session_start();
require_once dirname(__DIR__) . '../Assets/src/UserCookieManager.php';
use src\UserCookieManager;

$cookieManager = new UserCookieManager();

// ตั้งค่า timezone
date_default_timezone_set('Asia/Bangkok');

// Redirect if not logged in or not admin (initial check)
if (!isset($_SESSION['user_email'])) {
    header('Location: ../Frontend/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Marine Seafood Hub</title>
    <link rel="icon" href="https://customseafoods.com/cdn/shop/files/CS_Logo_2_1000.webp?v=1683664967" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/CSS/index.css">
    <script src="../Assets/JS/userdatafetch.js"></script>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-ocean sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../Frontend/index.php">
                <img src="https://customseafoods.com/cdn/shop/files/CS_Logo_2_1000.webp?v=1683664967" alt="Logo" class="me-2">
                <span>Marine Seafood Hub</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../Frontend/index.php">
                            <i class="bi bi-house-fill"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../Product/gallery.php">
                            <i class="bi bi-collection-fill"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../Admin/management.php">
                            <i class="bi bi-gear-fill"></i> Management
                        </a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="../Users/account.php" class="btn btn-outline-light me-2">
                        <i class="bi bi-person-circle"></i> Account
                    </a>
                    <a href="../Frontend/logout.php" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="container py-4">
        <h1 class="bi bi-speedometer2"> Admin Dashboard</h1>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4" id="stats-container">
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-people-fill fs-1 text-primary me-3"></i>
                            <h3>Total Users</h3>
                        </div>
                        <p class="card-text fs-3" id="total-users">Loading...</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-cart-fill fs-1 text-success me-3"></i>
                            <h3>Total Orders</h3>
                        </div>
                        <p class="card-text fs-3" id="total-orders">Loading...</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-box-seam fs-1 text-info me-3"></i>
                            <h3>Total Products</h3>
                        </div>
                        <p class="card-text fs-3" id="total-products">Loading...</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-currency-dollar fs-1 text-warning me-3"></i>
                            <h3>Total Revenue</h3>
                        </div>
                        <p class="card-text fs-3" id="total-revenue">Loading...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="wave-divider"></div>

        <!-- Recent Activity -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title mb-3">
                            <i class="bi bi-clock-history text-primary"></i> Recent Login Activity
                        </h2>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Login Time</th>
                                        <th>IP Address</th>
                                    </tr>
                                </thead>
                                <tbody id="recent-logins">
                                    <tr><td colspan="4">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions (unchanged) -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title mb-3">
                            <i class="bi bi-gear-fill text-success"></i> Quick Actions
                        </h2>
                        <div class="d-grid gap-2">
                            <a href="../Admin/add_product.php" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add New Product
                            </a>
                            <a href="../Admin/login_logs.php" class="btn btn-info">
                                <i class="bi bi-people"></i> Manage Users
                            </a>
                            <a href="../Admin/order_history.php" class="btn btn-warning">
                                <i class="bi bi-cart"></i> View Orders
                            </a>
                            <a href="../Admin/coupon_management.php" class="btn btn-success">
                                <i class="bi bi-ticket-perforated"></i> Manage Coupons
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Categories -->
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title mb-3">
                            <i class="bi bi-bar-chart-fill text-info"></i> Product Categories
                        </h2>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Product Count</th>
                                    </tr>
                                </thead>
                                <tbody id="product-categories">
                                    <tr><td colspan="2">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>