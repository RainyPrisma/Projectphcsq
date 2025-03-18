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
                <!-- Notification Dropdown -->
                <div class="dropdown me-3">
                    <a href="#" class="text-decoration-none text-white position-relative" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell-fill fs-5"></i>
                        <span id="unread_count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationDropdown" style="width: 300px;">
                        <li><h6 class="dropdown-header">การแจ้งเตือน</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <div id="notification_list"></div>
                        <li><a class="dropdown-item text-center text-primary" href="../Notification/notification.php">ดูการแจ้งเตือนทั้งหมด</a></li>
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
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h2 class="fw-bold">ยินดีต้อนรับ คุณ <span id="username"></span> <i class="bi bi-emoji-smile text-warning"></i></h2>
                    <p class="mb-0"><i class="bi bi-clock-history me-1"></i>เข้าสู่ระบบล่าสุด: <span id="last_login"></span></p>
                </div>
                <div class="col-md-4 d-flex align-items-center justify-content-end">
                    <div class="weather-widget p-3 text-center" id="weatherWidget">
                        <div class="weather-icon">
                            <i class="bi bi-cloud-sun-fill"></i>
                        </div>
                        <h5 class="mb-0">สภาพทะเลวันนี้</h5>
                        <p class="mb-1" id="weatherStatus">โหลดข้อมูล...</p>
                        <small>อัพเดทล่าสุด: <span id="weatherUpdateTime"><?php echo date('H:i น.'); ?></span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <!-- Carousel Section -->
    <div id="promoCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://www.malandaseafood.com.au/wp-content/uploads/2020/06/slider3.jpg" class="d-block w-100" alt="Slide 1">
                <div class="carousel-caption d-none d-md-block">
                    <h5>โปรโมชั่นพิเศษ!</h5>
                    <p>ลด 20% สำหรับสินค้าทะเลสดทุกชนิด</p>
                    <a href="../Product/gallery.php" class="btn btn-danger">ช้อปเลย</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://www.coastalseafoods.com/Themes/Default/Content/Images/fortune-fish-gourmet-seafood.jpg" class="d-block w-100" alt="Slide 2">
                <div class="carousel-caption d-none d-md-block">
                    <h5>สินค้าใหม่</h5>
                    <p>กุ้งสดคุณภาพสูงจากท่าเรือ</p>
                    <a href="../Product/gallery.php" class="btn btn-success">ดูรายการ</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://www.nbfoodexportdirectory.ca/new-brunswick-seafood-directory/images/seafood-banner-new.jpg" class="d-block w-100" alt="Slide 3">
                <div class="carousel-caption d-none d-md-block">
                    <h5>ปลาทะเลสด</h5>
                    <p>ส่งตรงถึงคุณภายใน 24 ชม.</p>
                    <a href="../Product/gallery.php" class="btn btn-primary">เลือกซื้อ</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#promoCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#promoCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
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
                            <a href="../Product/gallery.php" class="btn btn-danger">
                                <i class="bi bi-basket me-1"></i> ช้อปเลย
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 d-none d-md-block">
                        <img src="https://cloudfront-eu-central-1.images.arcpublishing.com/williamreed/X77LETLKUBIY7NSZZOJW4NLDQM.jpg" class="w-100 h-100" style="object-fit: cover;" alt="Promo">
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
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
                        <p class="card-text fs-3 fw-bold" id="total_orders">0 ครั้ง</p>
                        <div class="progress mt-2" style="height: 10px;">
                            <div class="progress-bar bg-primary" role="progressbar" id="total_orders_progress" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
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
                        <p class="card-text fs-3 fw-bold" id="total_spending">฿0</p>
                        <div class="progress mt-2" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" id="total_spending_progress" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="10000"></div>
                        </div>
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
                        <p class="card-text fs-5" id="most_purchased_item">-</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders and Recent Activities Side by Side -->
        <div class="row mb-4">
        <!-- Recent Orders (Left Side) -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="m-0">คำสั่งซื้อล่าสุด</h3>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <li class="timeline-item">
                                    <div class="timeline-marker bg-warning">
                                        <i class="bi bi-cart-fill"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h5 class="mb-1">คำสั่งซื้อ</h5>
                                        <p class="mb-0"><?php echo htmlspecialchars("คุณ {$order['username']} ซื้อสินค้า: {$order['item']} (เลขที่ออเดอร์: {$order['order_reference']})"); ?></p>
                                        <p class="text-muted small mb-0"><?php echo date('d ม.ค. Y H:i น.', strtotime($order['created_at'])); ?></p>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="timeline-item">
                                <div class="timeline-marker bg-secondary">
                                    <i class="bi bi-info-circle"></i>
                                </div>
                                <div class="timeline-content">
                                    <h5 class="mb-1">ไม่มีคำสั่งซื้อ</h5>
                                    <p class="mb-0">ยังไม่มีคำสั่งซื้อล่าสุด</p>
                                    <p class="text-muted small mb-0"><?php echo date('d ม.ค. Y H:i น.'); ?></p>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-clock-history me-1"></i> ดูทั้งหมด
                        </a>
                    </div>
                </div>
            </div>
        </div>

    <!-- Recent Activities (Right Side) -->
        <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="m-0">กิจกรรมล่าสุด</h3>
                    </div>
                    <div class="card-body">
                        <ul class="timeline">
                            <?php if (!empty($activities)): ?>
                                <?php foreach ($activities as $activity): ?>
                                    <li class="timeline-item">
                                        <div class="timeline-marker 
                                            <?php 
                                                if ($activity['activity_type'] === 'login') echo 'bg-primary'; 
                                                elseif ($activity['activity_type'] === 'review') echo 'bg-success'; 
                                                elseif ($activity['activity_type'] === 'purchase') echo 'bg-warning'; 
                                            ?>">
                                            <i class="bi bi-<?php 
                                                if ($activity['activity_type'] === 'login') echo 'box-arrow-in-right'; 
                                                elseif ($activity['activity_type'] === 'review') echo 'star-fill'; 
                                                elseif ($activity['activity_type'] === 'purchase') echo 'cart-fill'; 
                                            ?>"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h5 class="mb-1">
                                                <?php 
                                                    if ($activity['activity_type'] === 'login') echo 'เข้าสู่ระบบ'; 
                                                    elseif ($activity['activity_type'] === 'review') echo 'รีวิวสินค้า'; 
                                                    elseif ($activity['activity_type'] === 'purchase') echo 'การซื้อสินค้า'; 
                                                ?>
                                            </h5>
                                            <p class="mb-0"><?php echo htmlspecialchars($activity['description']); ?></p>
                                            <p class="text-muted small mb-0"><?php echo date('d ม.ค. Y H:i น.', strtotime($activity['activity_time'])); ?></p>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="timeline-item">
                                    <div class="timeline-marker bg-secondary">
                                        <i class="bi bi-info-circle"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h5 class="mb-1">ไม่มีกิจกรรม</h5>
                                        <p class="mb-0">ยังไม่มีกิจกรรมล่าสุด</p>
                                        <p class="text-muted small mb-0"><?php echo date('d ม.ค. Y H:i น.'); ?></p>
                                    </div>
                                </li>
                            <?php endif; ?>
                        </ul>
                        <div class="text-center mt-3">
                            <a href="#" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-clock-history me-1"></i> ดูทั้งหมด
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <!-- Latest Order 
            <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="m-0">คำสั่งซื้อล่าสุด</h3>
                        </div>
                        <div class="card-body" id="latest_order_container">
                            <div class="text-center py-4">
                                <i class="bi bi-bag-x fs-1 text-muted mb-3"></i>
                                <p class="text-muted">ยังไม่มีคำสั่งซื้อ</p>
                                <a href="../Product/gallery.php" class="btn btn-ocean">
                                    <i class="bi bi-cart-plus me-1"></i> เริ่มการช้อปปิ้ง
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                            -->

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
                                <small class="text-muted">20 รายการ</small>
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
                                <small class="text-muted">30 รายการ</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="../Product/Gallery.php" class="card category-card text-center text-decoration-none h-100">
                            <div class="card-body">
                                <div class="category-icon mb-3">
                                    <i class="bi bi-palette text-danger"></i>
                                </div>
                                <h6 class="card-title text-dark mb-0">ปู (เร็วๆนี้)</h6>
                                <small class="text-muted">15 รายการ</small>
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
                                <small class="text-muted">25 รายการ</small>
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
                                <small class="text-muted">40 รายการ</small>
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
                                <small class="text-muted">10 รายการ</small>
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
    <script src="../Assets/JS/notifications.js"></script>
    <script src="../API/dashboard.js"></script>
    <script src="../Assets/JS/weather.js"></script>
</body>
</html>