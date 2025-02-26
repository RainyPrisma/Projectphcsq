<?php
session_start();
require_once('../Assets/src/backendreq.php');
// ดึงข้อมูลประวัติการสั่งซื้อทั้งหมด (เริ่มต้น)
$order_sql = "SELECT id, order_id, username, email, item, total_price, 	order_reference, created_at 
              FROM orderhistory
              ORDER BY created_at DESC";
$orders_result = $conn->query($order_sql);

// ถ้ามีการค้นหาประวัติการสั่งซื้อ
if (isset($_GET['search_order']) && !empty($_GET['search_order'])) {
    $search_term = "%" . $_GET['search_order'] . "%";
    $order_sql = "SELECT id, order_id, username, email, item, total_price, 	order_reference, created_at 
                  FROM orderhistory 
                  WHERE username LIKE ? OR email LIKE ? OR 	order_reference LIKE ?
                  ORDER BY created_at DESC";
    $stmt = $conn->prepare($order_sql);
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
    $stmt->execute();
    $orders_result = $stmt->get_result();
    $stmt->close();
}

// ถ้ามีการกรองตามวันที่
if ((isset($_GET['date_from']) && !empty($_GET['date_from'])) || 
    (isset($_GET['date_to']) && !empty($_GET['date_to']))) {
    
    $conditions = [];
    $params = [];
    $types = "";
    
    if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
        $conditions[] = "created_at >= ?";
        $params[] = $_GET['date_from'] . " 00:00:00";
        $types .= "s";
    }
    
    if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
        $conditions[] = "created_at <= ?";
        $params[] = $_GET['date_to'] . " 23:59:59";
        $types .= "s";
    }
    
    $where_clause = "";
    if (!empty($conditions)) {
        $where_clause = " WHERE " . implode(" AND ", $conditions);
    }
    
    $order_sql = "SELECT id, order_id, username, email, item, total_price, 	order_reference, created_at 
                  FROM orderhistory
                  $where_clause
                  ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($order_sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $orders_result = $stmt->get_result();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการสั่งซื้อ</title>
    <link href="../Assets/CSS/management.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="#">StorageManagement</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="management.php">จัดการสินค้า</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="login_logs.php">ประวัติการเข้าสู่ระบบ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white active" href="order_history.php">ประวัติการสั่งซื้อ</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-white me-3">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= htmlspecialchars($_SESSION['user_email']) ?>
                    </span>
                    <a href="../Frontend/index.php" class="btn btn-success me-3">
                        <i class="bi bi-house-door me-1"></i>
                        หน้าหลัก
                    </a>
                    <a href="../Frontend/logout.php" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right me-1"></i>
                        ออกจากระบบ
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">ประวัติการสั่งซื้อของลูกค้า</h1>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <p>
                <i class="bi bi-clock me-1"></i> 
                วันที่: <?= date('d/m/Y H:i:s') ?>
            </p>
            <p>
                <i class="bi bi-receipt me-1"></i>
                จำนวนคำสั่งซื้อทั้งหมด: 
                <span class="badge bg-primary"><?= $orders_result ? $orders_result->num_rows : 0 ?></span>
            </p>
        </div>
        
        <div class="filter-form mb-4">
            <form id="filter-form" class="row g-3" method="GET" action="">
                <div class="col-md-4">
                    <label class="form-label">ค้นหาตามชื่อ/อีเมล/รหัสคำสั่งซื้อ</label>
                    <input type="text" name="search_order" id="search_order" class="form-control"
                           value="<?= isset($_GET['search_order']) ? htmlspecialchars($_GET['search_order']) : '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">ตั้งแต่วันที่</label>
                    <input type="date" name="date_from" id="date_from" class="form-control"
                           value="<?= isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">ถึงวันที่</label>
                    <input type="date" name="date_to" id="date_to" class="form-control"
                           value="<?= isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : '' ?>">
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-filter me-1"></i>กรอง
                    </button>
                    <a href="order_history.php" class="btn btn-secondary">รีเซ็ต</a>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if ($orders_result && $orders_result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>รหัสคำสั่งซื้อ</th>
                                    <th>ชื่อลูกค้า</th>
                                    <th>อีเมล</th>
                                    <th>รายการสินค้า</th>
                                    <th>ยอดรวม</th>
                                    <th>วันที่สั่งซื้อ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = $orders_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($order['order_reference']) ?></td>
                                        <td><?= htmlspecialchars($order['username']) ?></td>
                                        <td><?= htmlspecialchars($order['email']) ?></td>
                                        <td><?= htmlspecialchars($order['item']) ?></td>
                                        <td><?= number_format($order['total_price'], 2) ?> ฿</td>
                                        <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-1"></i>
                        ไม่พบข้อมูลประวัติการสั่งซื้อ
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('mousedown', function() {
                    this.style.transform = 'scale(0.95)';
                });
                
                button.addEventListener('mouseup', function() {
                    this.style.transform = 'scale(1)';
                });
                
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>