<?php
session_start();

// ใส่โค้ดนี้ที่ส่วนบนของไฟล์ หลัง session_start();
$session_timeout = 30 * 60; // 30 นาที (เป็นวินาที)

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ตรวจสอบสถานะการล็อกอินและบทบาท
if (!isset($_SESSION['user_email']) || $_SESSION['role'] !== 'admin') {
    session_unset();
    session_destroy();
    header("Location: ../Frontend/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกการเข้าสู่ระบบ</title>
    <!-- Bootstrap CSS -->
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
                        <a class="nav-link text-white active" href="login_logs.php">ประวัติการเข้าสู่ระบบ</a>
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
        <h1 class="mb-4">บันทึกการเข้าสู่ระบบ</h1>
        
        <!-- ฟอร์มกรอง -->
        <div class="filter-form mb-4">
            <form id="filter-form" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">ชื่อผู้ใช้</label>
                    <input type="text" name="username" id="username" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">อีเมล</label>
                    <input type="text" name="email" id="email" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">สถานะ</label>
                    <select name="is_active" id="is_active" class="form-select">
                        <option value="">ทั้งหมด</option>
                        <option value="1">กำลังใช้งาน</option>
                        <option value="0">ออกจากระบบแล้ว</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">ตั้งแต่วันที่</label>
                    <input type="date" name="date_from" id="date_from" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">ถึงวันที่</label>
                    <input type="date" name="date_to" id="date_to" class="form-control">
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-filter me-1"></i>กรอง
                    </button>
                    <button type="button" id="reset-btn" class="btn btn-secondary">รีเซ็ต</button>
                </div>
            </form>
        </div>
        
        <!-- ตารางข้อมูล -->
        <div id="data-container">
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">กำลังโหลด...</span>
                </div>
            </div>
        </div>
        
        <!-- การแบ่งหน้า -->
        <div id="pagination-container" class="mt-3">
            <!-- Pagination will be populated by JavaScript -->
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript สำหรับดึงข้อมูล -->
    <script src="../Assets/JS/login_logs.js"></script>
</body>
</html>