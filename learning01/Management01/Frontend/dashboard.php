<?php
include '../Backend/dashboardreq.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Marine Seafood Hub</title>
    <link rel="stylesheet" href="../Assets/CSS/dashboard.css">
    <link rel="icon" href="https://customseafoods.com/cdn/shop/files/CS_Logo_2_1000.webp?v=1683664967" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Fish Animation Background -->
    <div class="fish-container">
        <?php for($i = 0; $i < 5; $i++): ?>
            <div class="fish" style=">
                top: <?php echo rand(10, 90); ?>%;
                animation-duration: <?php echo rand(15, 25); ?>s;
                animation-delay: <?php echo rand(0, 5); ?>s;
                width: <?php echo rand(20, 40); ?>px;
                opacity: <?php echo rand(1, 3) / 10; ?>;
            "></div>
        <?php endfor; ?>
    </div>

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
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-house-fill me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../Product/gallery.php">
                            <i class="bi bi-collection-fill me-1"></i> สินค้า
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../Frontend/contactus.php">
                            <i class="bi bi-telephone-fill me-1"></i> ติดต่อเรา
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://www.thairath.co.th/tags/%E0%B8%AD%E0%B8%B2%E0%B8%AB%E0%B8%B2%E0%B8%A3%E0%B8%97%E0%B8%B0%E0%B9%80%E0%B8%A5">
                            <i class="bi bi-newspaper me-1"></i> ข่าวสาร
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <div class="dropdown me-3">
                        <a href="#" class="text-decoration-none text-white position-relative" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell-fill fs-5"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                3
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationDropdown" style="width: 300px;">
                            <li><h6 class="dropdown-header">การแจ้งเตือน</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-box-seam text-primary fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="mb-0 fw-bold">คำสั่งซื้อได้จัดส่งแล้ว</p>
                                        <p class="text-muted small mb-0">สินค้าของคุณกำลังอยู่ระหว่างการจัดส่ง</p>
                                        <p class="text-muted small mb-0">2 ชั่วโมงที่แล้ว</p>
                                    </div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-percent text-success fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="mb-0 fw-bold">โปรโมชั่นพิเศษ</p>
                                        <p class="text-muted small mb-0">รับส่วนลด 15% สำหรับการสั่งซื้อครั้งถัดไป</p>
                                        <p class="text-muted small mb-0">1 วันที่แล้ว</p>
                                    </div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-tag-fill text-warning fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="mb-0 fw-bold">สินค้าใหม่มาแล้ว</p>
                                        <p class="text-muted small mb-0">กุ้งมังกรแช่แข็งคุณภาพพรีเมียม</p>
                                        <p class="text-muted small mb-0">3 วันที่แล้ว</p>
                                    </div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center text-primary" href="#">ดูการแจ้งเตือนทั้งหมด</a></li>
                        </ul>
                    </div>
                    <a href="../Users/account.php" class="btn btn-outline-light me-2 position-relative">
                        <i class="bi bi-person-circle me-1"></i> บัญชีของฉัน
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle">
                            <span class="visually-hidden">Online</span>
                        </span>
                    </a>
                    <a href="logout.php" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right me-1"></i> ออกจากระบบ
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h2 class="fw-bold">ยินดีต้อนรับ คุณ <?php echo htmlspecialchars($user_data['username']); ?> <i class="bi bi-emoji-smile text-warning"></i></h2>
                    <p class="mb-0"><i class="bi bi-clock-history me-1"></i>เข้าสู่ระบบล่าสุด: <?php echo date('d ม.ค. Y H:i น.', strtotime($cookieData['last_login'])); ?></p>
                    <div class="d-flex align-items-center mt-3">
                        <div class="badge bg-primary me-2 p-2">
                            <i class="bi bi-award me-1"></i> สมาชิก <?php echo rand(1, 3) == 1 ? 'VIP' : 'ทั่วไป'; ?>
                        </div>
                        <div class="badge bg-info text-dark me-2 p-2">
                            <i class="bi bi-gem me-1"></i> <?php echo rand(20, 500); ?> พอยท์
                        </div>
                        <div class="badge bg-warning text-dark p-2">
                            <i class="bi bi-truck me-1"></i> ฟรีค่าส่ง <?php echo rand(0, 1) ? '✓' : '✗'; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-center justify-content-end">
                    <div class="weather-widget p-3 text-center">
                        <div class="weather-icon">
                            <i class="bi bi-cloud-sun-fill"></i>
                        </div>
                        <h5 class="mb-0">สภาพทะเลวันนี้</h5>
                        <p class="mb-1">สงบ · คลื่น 0.5 ม.</p>
                        <small>อัพเดทล่าสุด: <?php echo date('H:i น.'); ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Quick Actions -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <a href="../Product/gallery.php" class="card text-center h-100 text-decoration-none">
                    <div class="card-body">
                        <i class="bi bi-cart-plus fs-1 text-primary mb-2"></i>
                        <h5 class="card-title text-dark">สั่งซื้อสินค้า</h5>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="../Users/ordercus_history.php" class="card text-center h-100 text-decoration-none">
                    <div class="card-body">
                        <i class="bi bi-box-seam fs-1 text-success mb-2"></i>
                        <h5 class="card-title text-dark">ติดตามคำสั่งซื้อ</h5>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="#" class="card text-center h-100 text-decoration-none">
                    <div class="card-body">
                        <i class="bi bi-heart fs-1 text-danger mb-2"></i>
                        <h5 class="card-title text-dark">รายการโปรด</h5>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="../Frontend/contactus.php" class="card text-center h-100 text-decoration-none">
                    <div class="card-body">
                        <i class="bi bi-headset fs-1 text-warning mb-2"></i>
                        <h5 class="card-title text-dark">ติดต่อเรา</h5>
                    </div>
                </a>
            </div>
        </div>

        <!-- Promo Banner -->
        <div class="card mb-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-md-8">
                        <div class="p-4">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-danger me-2">ลดพิเศษ</span>
                                <span class="badge bg-warning text-dark">จำกัดเวลา</span>
                            </div>
                            <h3 class="card-title fw-bold">โปรโมชั่นสินค้าทะเลสดทุกชนิด ลด 20%</h3>
                            <p class="card-text">เฉพาะสั่งซื้อภายในสัปดาห์นี้เท่านั้น! สินค้าคุณภาพส่งตรงจากท่าเรือ</p>
                            <div class="d-flex align-items-center">
                                <a href="../Product/gallery.php" class="btn btn-danger">
                                    <i class="bi bi-basket me-1"></i> ช้อปเลย
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 d-none d-md-block">
                        <img src="https://cloudfront-eu-central-1.images.arcpublishing.com/williamreed/X77LETLKUBIY7NSZZOJW4NLDQM.jpg" class="w-100 h-100" style="object-fit: cover;" alt="Promo">
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section with Icons and Animation -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card stat-card text-center h-100">
                    <div class="card-body">
                        <div class="position-relative">
                            <div class="position-absolute top-0 start-100 translate-middle">
                                <i class="bi bi-arrow-up-circle-fill text-success fs-4"></i>
                            </div>
                            <i class="bi bi-cart-fill fs-1 text-primary mb-3"></i>
                        </div>
                        <h5 class="card-title">สั่งซื้อทั้งหมด</h5>
                        <p class="card-text fs-3 fw-bold"><?php echo $total_orders; ?> ครั้ง</p>
                        <div class="progress mt-2" style="height: 10px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo min(100, $total_orders * 5); ?>%" aria-valuenow="<?php echo $total_orders; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="text-muted mt-2 mb-0 small">
                            <i class="bi bi-graph-up-arrow me-1"></i> เพิ่มขึ้น <?php echo rand(5, 20); ?>% จากเดือนที่แล้ว
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card text-center h-100">
                    <div class="card-body">
                        <div class="position-relative">
                            <div class="position-absolute top-0 start-100 translate-middle">
                                <i class="bi bi-arrow-up-circle-fill text-success fs-4"></i>
                            </div>
                            <i class="bi bi-wallet2 fs-1 text-success mb-3"></i>
                        </div>
                        <h5 class="card-title">ยอดใช้จ่ายรวม</h5>
                        <p class="card-text fs-3 fw-bold">฿<?php echo number_format($total_spending, 0); ?></p>
                        <div class="progress mt-2" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo min(100, $total_spending / 100); ?>%" aria-valuenow="<?php echo $total_spending; ?>" aria-valuemin="0" aria-valuemax="10000"></div>
                        </div>
                        <p class="text-muted mt-2 mb-0 small">
                            <i class="bi bi-graph-up-arrow me-1"></i> เพิ่มขึ้น <?php echo rand(3, 15); ?>% จากไตรมาสที่แล้ว
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card text-center h-100">
                    <div class="card-body">
                        <div class="position-relative">
                            <div class="position-absolute top-0 start-100 translate-middle">
                                <i class="bi bi-trophy-fill text-warning fs-4"></i>
                            </div>
                            <i class="bi bi-star-fill fs-1 text-warning mb-3"></i>
                        </div>
                        <h5 class="card-title">สินค้าที่ชอบ</h5>
                        <p class="card-text fs-5"><?php echo htmlspecialchars($most_purchased_item); ?></p>
                        <div class="d-flex justify-content-center mt-2">
                            <div class="me-2">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-half text-warning"></i>
                            </div>
                            <span class="small text-muted">(<?php echo rand(45, 120); ?> รีวิว)</span>
                        </div>
                        <a href="#" class="btn btn-sm btn-outline-warning mt-3">
                            <i class="bi bi-bag-heart-fill me-1"></i> สั่งซื้ออีกครั้ง
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Timeline -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="m-0">กิจกรรมล่าสุด</h3>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-filter me-1"></i> กรอง
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="#">ทั้งหมด</a></li>
                                <li><a class="dropdown-item" href="#">การสั่งซื้อ</a></li>
                                <li><a class="dropdown-item" href="#">การจัดส่ง</a></li>
                                <li><a class="dropdown-item" href="#">การชำระเงิน</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="timeline">
                            <li class="timeline-item">
                                <div class="timeline-marker bg-primary">
                                    <i class="bi bi-cart-check-fill"></i>
                                </div>
                                <div class="timeline-content">
                                    <h5 class="mb-1">สั่งซื้อสำเร็จ</h5>
                                    <p class="mb-0">คำสั่งซื้อ #<?php echo rand(10000, 99999); ?> สำเร็จแล้ว</p>
                                    <p class="text-muted small mb-0"><?php echo date('d ม.ค. Y H:i น.', strtotime('-1 hours')); ?></p>
                                </div>
                            </li>
                            <li class="timeline-item">
                                <div class="timeline-marker bg-success">
                                    <i class="bi bi-credit-card-fill"></i>
                                </div>
                                <div class="timeline-content">
                                    <h5 class="mb-1">ชำระเงินสำเร็จ</h5>
                                    <p class="mb-0">ชำระเงินคำสั่งซื้อ #<?php echo rand(10000, 99999); ?> จำนวน ฿<?php echo number_format(rand(500, 5000), 0); ?></p>
                                    <p class="text-muted small mb-0"><?php echo date('d ม.ค. Y H:i น.', strtotime('-1 day')); ?></p>
                                </div>
                            </li>
                            <li class="timeline-item">
                                <div class="timeline-marker bg-info">
                                    <i class="bi bi-truck"></i>
                                </div>
                                <div class="timeline-content">
                                    <h5 class="mb-1">จัดส่งสินค้า</h5>
                                    <p class="mb-0">คำสั่งซื้อ #<?php echo rand(10000, 99999); ?> อยู่ระหว่างการจัดส่ง</p>
                                    <p class="text-muted small mb-0"><?php echo date('d ม.ค. Y H:i น.', strtotime('-2 days')); ?></p>
                                </div>
                            </li>
                            <li class="timeline-item">
                                <div class="timeline-marker bg-warning">
                                    <i class="bi bi-star-fill"></i>
                                </div>
                                <div class="timeline-content">
                                    <h5 class="mb-1">ให้คะแนนสินค้า</h5>
                                    <p class="mb-0">คุณให้คะแนน <?php echo rand(4, 5); ?> ดาว สำหรับ <?php echo htmlspecialchars($most_purchased_item); ?></p>
                                    <p class="text-muted small mb-0"><?php echo date('d ม.ค. Y H:i น.', strtotime('-3 days')); ?></p>
                                </div>
                            </li>
                        </ul>
                        <div class="text-center mt-3">
                            <a href="#" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-clock-history me-1"></i> ดูทั้งหมด
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latest Order with Enhanced Details -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="m-0">คำสั่งซื้อล่าสุด</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($latest_order): ?>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="badge bg-primary p-2">คำสั่งซื้อ #<?php echo htmlspecialchars($latest_order['order_id']); ?></span>
                                <span class="fw-bold fs-5">฿<?php echo number_format($latest_order['total_price'], 0); ?></span>
                            </div>
                            <div class="mb-3 p-3 border rounded bg-light">
                                <div class="mb-2">
                                    <i class="bi bi-clock me-1 text-secondary"></i>
                                    <span class="text-muted">วันที่สั่งซื้อ:</span>
                                    <span class="fw-medium ms-2"><?php echo date('d ม.ค. Y', strtotime($latest_order['created_at'])); ?></span>
                                </div>
                                <div class="mb-2">
                                    <i class="bi bi-geo-alt me-1 text-secondary"></i>
                                    <span class="text-muted">วิธีจัดส่ง:</span>
                                    <span class="fw-medium ms-2">Kerry Express (ด่วนพิเศษ)</span>
                                </div>
                                <div>
                                    <i class="bi bi-credit-card me-1 text-secondary"></i>
                                    <span class="text-muted">วิธีชำระเงิน:</span>
                                    <span class="fw-medium ms-2">บัตรเครดิต</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <div class="status-circle bg-success">
                                        <i class="bi bi-check-lg text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-success fw-bold">สถานะ: <?php echo htmlspecialchars($latest_order['order_reference'] ?? 'กำลังดำเนินการ'); ?></div>
                                    <div class="small">คาดว่าจะจัดส่งถึง: <?php echo date('d ม.ค. Y', strtotime($latest_order['created_at'] . ' + 2 days')); ?></div>
                                </div>
                            </div>
                            <div class="progress mb-3" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mb-4">
                                <span>รับออเดอร์</span>
                                <span>กำลังเตรียมจัดส่ง</span>
                                <span>จัดส่งแล้ว</span>
                                <span>สำเร็จ</span>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="../Users/ordercus_history.php" class="btn btn-ocean">
                                    <i class="bi bi-eye me-1"></i> ดูรายละเอียด
                                </a>
                                <a href="#" class="btn btn-outline-primary">
                                    <i class="bi bi-geo-alt me-1"></i> ติดตามพัสดุ
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-bag-x fs-1 text-muted mb-3"></i>
                                <p class="text-muted">ยังไม่มีคำสั่งซื้อ</p>
                                <a href="../Product/gallery.php" class="btn btn-ocean">
                                    <i class="bi bi-cart-plus me-1"></i> เริ่มการช้อปปิ้ง
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Popular Categories -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="m-0">หมวดหมู่ยอดนิยม</h3>
                <span class="badge bg-ocean">แนะนำ</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="../Product/Fish.php" class="card category-card text-center text-decoration-none h-100">
                            <div class="card-body">
                                <div class="category-icon mb-3">
                                    <i class="bi bi-water text-primary"></i>
                                </div>
                                <h6 class="card-title text-dark mb-0">ปลาทะเล</h6>
                                <small class="text-muted"><?php echo rand(20, 50); ?> รายการ</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="../Product/Shrimp.php" class="card category-card text-center text-decoration-none h-100">
                            <div class="card-body">
                                <div class="category-icon mb-3">
                                    <i class="bi bi-egg-fried text-warning"></i>
                                </div>
                                <h6 class="card-title text-dark mb-0">กุ้ง</h6>
                                <small class="text-muted"><?php echo rand(20, 50); ?> รายการ</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="../Product/Gallery.php" class="card category-card text-center text-decoration-none h-100">
                            <div class="card-body">
                                <div class="category-icon mb-3">
                                    <i class="bi bi-palette text-danger"></i>
                                </div>
                                <h6 class="card-title text-dark mb-0">ปู(เร็วๆนี้)</h6>
                                <small class="text-muted"><?php echo rand(20, 50); ?> รายการ</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="../Product/Shell.php" class="card category-card text-center text-decoration-none h-100">
                            <div class="card-body">
                                <div class="category-icon mb-3">
                                    <i class="bi bi-eyeglasses text-info"></i>
                                </div>
                                <h6 class="card-title text-dark mb-0">หอย</h6>
                                <small class="text-muted"><?php echo rand(20, 50); ?> รายการ</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="../Product/Occt.php" class="card category-card text-center text-decoration-none h-100">
                            <div class="card-body">
                                <div class="category-icon mb-3">
                                    <i class="bi bi-droplet-fill text-success"></i>
                                </div>
                                <h6 class="card-title text-dark mb-0">หมึก</h6>
                                <small class="text-muted"><?php echo rand(20, 50); ?> รายการ</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="../Product/gallery.php" class="card category-card text-center text-decoration-none h-100">
                            <div class="card-body">
                                <div class="category-icon mb-3">
                                    <i class="bi bi-droplet-fill text-success"></i>
                                </div>
                                <h6 class="card-title text-dark mb-0">อื่นๆ</h6>
                                <small class="text-muted"><?php echo rand(20, 50); ?> รายการ</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wave Decoration -->
    <div class="wave-decoration"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>