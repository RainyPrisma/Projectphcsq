<?php
include '../Backend/dashboardreq.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marine Seafood Hub</title>
    <link rel="stylesheet" href="../Assets/CSS/dashboard.css">
    <link rel="icon" href="https://customseafoods.com/cdn/shop/files/CS_Logo_2_1000.webp?v=1683664967" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
 
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-ocean sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="https://customseafoods.com/cdn/shop/files/CS_Logo_2_1000.webp?v=1683664967" alt="Logo" width="40" class="me-2">
                <span>Marine Seafood Hub</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="bi bi-house-fill"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="../Product/gallery.php"><i class="bi bi-collection-fill"></i> Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="../Frontend/contactus.php"><i class="bi bi-telephone-fill"></i> Contact Us</a></li>
                </ul>
                <div class="d-flex">
                    <a href="../Users/account.php" class="btn btn-outline-light me-2"><i class="bi bi-person-circle"></i> Account</a>
                    <a href="logout.php" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-4">
        <!-- Welcome Section -->
        <div class="mb-4">
            <h2 class="fw-bold">ยินดีต้อนรับ คุณ <?php echo htmlspecialchars($user_data['username']); ?></h2>
            <p class="text-muted">Last Login: <?php echo date('d ม.ค. Y H:i น.', strtotime($cookieData['last_login'])); ?></p>
        </div>

        <!-- Stats Section -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card stat-card text-center">
                    <div class="card-body">
                        <i class="bi bi-cart-fill fs-1 text-primary mb-2"></i>
                        <h5 class="card-title">สั่งซื้อทั้งหมด</h5>
                        <p class="card-text fs-3 fw-bold"><?php echo $total_orders; ?> ครั้ง</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card text-center">
                    <div class="card-body">
                        <i class="bi bi-wallet2 fs-1 text-success mb-2"></i>
                        <h5 class="card-title">ยอดใช้จ่ายรวม</h5>
                        <p class="card-text fs-3 fw-bold">฿<?php echo number_format($total_spending, 0); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card text-center">
                    <div class="card-body">
                        <i class="bi bi-star-fill fs-1 text-warning mb-2"></i>
                        <h5 class="card-title">สินค้าที่ชอบ</h5>
                        <p class="card-text fs-5"><?php echo htmlspecialchars($most_purchased_item); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Latest Order -->
        <div class="mb-4">
            <h3>คำสั่งซื้อล่าสุด</h3>
            <div class="card card-order">
                <div class="card-body">
                    <?php if ($latest_order): ?>
                        <p class="fw-bold">Order #<?php echo htmlspecialchars($latest_order['order_id']); ?> - ฿<?php echo number_format($latest_order['total_price'], 0); ?></p>
                        <p class="text-success">สถานะ: <?php echo htmlspecialchars($latest_order['order_reference'] ?? 'กำลังดำเนินการ'); ?> 
                            (คาดถึง: <?php echo date('d ม.ค. Y', strtotime($latest_order['created_at'] . ' + 2 days')); ?>)</p>
                        <a href="../Users/ordercus_history.php" class="btn btn-outline-primary btn-sm">ดูรายละเอียด</a>
                    <?php else: ?>
                        <p class="text-muted">ยังไม่มีคำสั่งซื้อ</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Order History -->
        <div class="mb-4">
            <h3>ประวัติการสั่งซื้อ</h3>
            <ul class="list-group">
                <?php while ($order = $order_history_result->fetch_assoc()): ?>
                    <li class="list-group-item">
                        #<?php echo $order['order_id']; ?> - 
                        <?php echo date('d ม.ค. Y', strtotime($order['created_at'])); ?> - 
                        ฿<?php echo number_format($order['total_price'], 0); ?>
                    </li>
                <?php endwhile; ?>
            </ul>
            <a href="../Users/ordercus_history.php" class="btn btn-link mt-2">ดูทั้งหมด</a>
        </div>

        <!-- Recommended Products -->
        <div class="mb-4">
            <h3>สินค้าแนะนำ</h3>
            <div class="row g-4">
                <?php while ($product = $recommended_result->fetch_assoc()): ?>
                    <div class="col-md-6">
                        <div class="card product-card">
                            <div class="card-body d-flex align-items-center">
                                <img src="<?php echo htmlspecialchars($product['image_url'] ?? 'https://via.placeholder.com/100'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="me-3">
                                <div>
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text">฿<?php echo number_format($product['price'], 0); ?></p>
                                    <a href="../Product/gallery.php" class="btn btn-success btn-sm">สั่งซื้อ</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// ปิดการเชื่อมต่อ
$conn->close();
?>