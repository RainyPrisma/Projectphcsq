<?php
session_start();
require_once dirname(__DIR__) . '../Assets/src/UserCookieManager.php';
use src\UserCookieManager;

$cookieManager = new UserCookieManager();

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "1234", "management01");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['user_email'])) {
    header('Location: ../Frontend/login.php');
    exit();
}

$user_email = $_SESSION['user_email'];
$sql = "SELECT * FROM users WHERE email = ? AND role = 'admin'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: ../Frontend/index.php'); // ถ้าไม่ใช่ admin กลับไปหน้า index
    exit();
}

// ดึงข้อมูลสำหรับ Dashboard
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orderhistory")->fetch_assoc()['count'];
$total_products = $conn->query("SELECT COUNT(*) as count FROM productlist")->fetch_assoc()['count'];
$recent_logins = $conn->query("SELECT * FROM login_logs ORDER BY login_time DESC LIMIT 5");
$total_revenue = $conn->query("SELECT SUM(total_price) as revenue FROM orderhistory")->fetch_assoc()['revenue'];

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
</head>
<body class="bg-light">
    <!-- Navbar -->
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
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-people-fill fs-1 text-primary me-3"></i>
                            <h3>Total Users</h3>
                        </div>
                        <p class="card-text fs-3"><?php echo $total_users; ?></p>
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
                        <p class="card-text fs-3"><?php echo $total_orders; ?></p>
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
                        <p class="card-text fs-3"><?php echo $total_products; ?></p>
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
                        <p class="card-text fs-3">$<?php echo number_format($total_revenue, 2); ?></p>
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
                                <tbody>
                                    <?php while ($login = $recent_logins->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($login['username']); ?></td>
                                            <td><?php echo htmlspecialchars($login['email']); ?></td>
                                            <td><?php echo $login['login_time']; ?></td>
                                            <td><?php echo htmlspecialchars($login['ip_address']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title mb-3">
                            <i class="bi bi-gear-fill text-success"></i> Quick Actions
                        </h2>
                        <div class="d-grid gap-2">
                            <a href="..\Admin\add_product.php" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add New Product
                            </a><!--ทำไว้เพื่อน mockup ให้ดู-->
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
                        <?php
                        $categories = $conn->query("SELECT p.nameType, COUNT(pl.id) as count 
                            FROM product p 
                            LEFT JOIN productlist pl ON p.id = pl.product_id 
                            GROUP BY p.nameType");
                        ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Product Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($category = $categories->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($category['nameType']); ?></td>
                                            <td><?php echo $category['count']; ?></td>
                                        </tr>
                                    <?php endwhile; ?>
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