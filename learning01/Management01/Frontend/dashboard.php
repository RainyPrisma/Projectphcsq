<?php
include '../Backend/dashboardreq.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Marine Seafood Hub</title>
    <link rel="stylesheet" href="../Assets/CSS/ref.css">
    <link rel="stylesheet" href="../Assets/CSS/account.css">
    <link rel="stylesheet" href="../Assets/CSS/dashboard.css">
    <link rel="icon" href="https://customseafoods.com/cdn/shop/files/CS_Logo_2_1000.webp?v=1683664967" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
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
                    <a href="logout.php" class="btn btn-ocean"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h2 class="fw-bold">ยินดีต้อนรับ คุณ <?php echo htmlspecialchars($user_data['username']); ?></h2>
            <p>Last Login: <?php echo date('d ม.ค. Y H:i น.', strtotime($cookieData['last_login'])); ?></p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
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
            <div class="card">
                <div class="card-header">
                    <h3 class="m-0">คำสั่งซื้อล่าสุด</h3>
                </div>
                <div class="card-body">
                    <?php if ($latest_order): ?>
                        <p class="fw-bold">Order #<?php echo htmlspecialchars($latest_order['order_id']); ?> - ฿<?php echo number_format($latest_order['total_price'], 0); ?></p>
                        <p class="text-success">สถานะ: <?php echo htmlspecialchars($latest_order['order_reference'] ?? 'กำลังดำเนินการ'); ?> 
                            (คาดถึง: <?php echo date('d ม.ค. Y', strtotime($latest_order['created_at'] . ' + 2 days')); ?>)</p>
                        <a href="../Users/ordercus_history.php" class="btn btn-ocean btn-sm">ดูรายละเอียด</a>
                    <?php else: ?>
                        <p class="text-muted">ยังไม่มีคำสั่งซื้อ</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Order History -->
        <div class="mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="m-0">ประวัติการสั่งซื้อ</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php while ($order = $order_history_result->fetch_assoc()): ?>
                            <li class="list-group-item">
                                #<?php echo $order['order_id']; ?> - 
                                <?php echo date('d ม.ค. Y', strtotime($order['created_at'])); ?> - 
                                ฿<?php echo number_format($order['total_price'], 0); ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                    <a href="../Users/ordercus_history.php" class="btn btn-ocean mt-3">ดูทั้งหมด</a>
                </div>
            </div>
        </div>

        <!-- Recommended Products -->
        <div class="mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="m-0">สินค้าแนะนำ</h3>
                    <span class="badge bg-warning text-dark">คัดสรรพิเศษสำหรับคุณ</span>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <?php while ($product = $recommended_result->fetch_assoc()): ?>
                            <div class="col-md-6">
                                <div class="card product-card shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="position-relative">
                                            <img src="<?php echo htmlspecialchars($product['image_url'] ?? 'https://via.placeholder.com/100'); ?>" 
                                                alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                class="me-3 rounded" style="width: 100px; height: 100px; object-fit: cover;">
                                            <?php if (rand(0, 1)): // สมมติว่ามีส่วนลดสำหรับบางรายการ ?>
                                                <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger">
                                                    -<?php echo rand(5, 30); ?>%
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="w-100">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                                <i class="bi bi-bookmark<?php echo rand(0, 1) ? '-fill text-warning' : ''; ?>"></i>
                                            </div>
                                            
                                            <!-- Rating -->
                                            <div class="mb-1">
                                                <?php
                                                $rating = rand(3, 5);
                                                for ($i = 1; $i <= 5; $i++) {
                                                    if ($i <= $rating) {
                                                        echo '<i class="bi bi-star-fill text-warning"></i>';
                                                    } else {
                                                        echo '<i class="bi bi-star text-warning"></i>';
                                                    }
                                                }
                                                ?>
                                                <small class="text-muted ms-1">(<?php echo rand(10, 100); ?>)</small>
                                            </div>
                                            
                                            <!-- Price -->
                                            <div class="mb-2">
                                                <?php if (rand(0, 1)): // สมมติว่ามีส่วนลดสำหรับบางรายการ ?>
                                                    <span class="text-decoration-line-through text-muted me-2">
                                                        ฿<?php echo number_format($product['price'] * (1 + rand(10, 30) / 100), 0); ?>
                                                    </span>
                                                <?php endif; ?>
                                                <span class="fw-bold text-danger fs-5">฿<?php echo number_format($product['price'], 0); ?></span>
                                                <small class="ms-1 text-success"><?php echo rand(0, 1) ? 'มีสต็อก' : 'สั่งล่วงหน้า'; ?></small>
                                            </div>
                                            
                                            <!-- Tags -->
                                            <div class="mb-2">
                                                <?php 
                                                $tags = ['สด', 'ประมงพื้นบ้าน', 'ออแกนิค', 'นำเข้า'];
                                                $random_tags = array_rand(array_flip($tags), rand(1, 2));
                                                if (!is_array($random_tags)) $random_tags = [$random_tags];
                                                foreach ($random_tags as $tag): 
                                                ?>
                                                    <span class="badge bg-info text-dark me-1"><?php echo $tag; ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                            
                                            <!-- Buttons -->
                                            <div class="d-flex mt-auto">
                                                <a href="../Product/gallery.php" class="btn btn-ocean btn-sm">
                                                    <i class="bi bi-cart-plus"></i> สั่งซื้อ
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="../Product/gallery.php" class="btn btn-outline-ocean">ดูสินค้าทั้งหมด <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

    <!-- Wave Decoration -->
    <div class="wave-decoration"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// ปิดการเชื่อมต่อ
$conn->close();
?>