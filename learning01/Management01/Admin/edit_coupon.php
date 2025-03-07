<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ตั้งค่า log
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/log/php_errors.log');

// เชื่อมต่อฐานข้อมูล
require_once('../Assets/src/backendreq.php');
if (!$conn) {
    die("Database connection failed: " . $conn->connect_error);
}

date_default_timezone_set('Asia/Bangkok');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid coupon ID");
}

$coupon_id = intval($_GET['id']);
$query = "SELECT * FROM coupons WHERE coupon_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $coupon_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Coupon not found");
}

$coupon = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขคูปอง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../Assets/CSS/management.css" rel="stylesheet">
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
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-white me-3">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= htmlspecialchars($_SESSION['user_email'] ?? 'Guest') ?>
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
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-pencil-square me-2"></i>แก้ไขคูปอง
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="editCouponForm">
                            <input type="hidden" name="coupon_id" value="<?= $coupon['coupon_id'] ?>">
                            <input type="hidden" name="update_coupon" value="1">
                            <div class="mb-3">
                                <label class="form-label">รหัสคูปอง</label>
                                <input type="text" class="form-control" name="coupon_code" value="<?= htmlspecialchars($coupon['coupon_code']) ?>" required>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">มูลค่าส่วนลด</label>
                                    <input type="number" class="form-control" name="discount_amount" step="0.01" min="0" value="<?= htmlspecialchars($coupon['discount_amount']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ประเภทส่วนลด</label>
                                    <select class="form-select" name="discount_type" required>
                                        <option value="percentage" <?= $coupon['discount_type'] == 'percentage' ? 'selected' : '' ?>>เปอร์เซ็นต์ (%)</option>
                                        <option value="fixed" <?= $coupon['discount_type'] == 'fixed' ? 'selected' : '' ?>>จำนวนเงิน (฿)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">วันที่เริ่มใช้งาน</label>
                                    <input type="datetime-local" class="form-control" name="valid_from" value="<?= date('Y-m-d\TH:i', strtotime($coupon['valid_from'])) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">วันที่สิ้นสุด</label>
                                    <input type="datetime-local" class="form-control" name="valid_until" value="<?= date('Y-m-d\TH:i', strtotime($coupon['valid_until'])) ?>" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">ยอดสั่งซื้อขั้นต่ำ (฿)</label>
                                    <input type="number" class="form-control" name="min_purchase" value="<?= htmlspecialchars($coupon['min_purchase']) ?>" step="0.01" min="0" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">จำกัดการใช้งาน</label>
                                    <input type="number" class="form-control" name="usage_limit" value="<?= htmlspecialchars($coupon['usage_limit']) ?>" min="0" required>
                                    <div class="form-text">0 = ไม่จำกัดจำนวนครั้ง</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= $coupon['is_active'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_active">เปิดใช้งานคูปองทันที</label>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" id="editSubmitButton" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>
                                    บันทึกการแก้ไข
                                </button>
                                <a href="coupon_management.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    กลับ
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('editCouponForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            const submitButton = document.getElementById('editSubmitButton');
            
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> กำลังบันทึก...`;

            setTimeout(() => {
                fetch('coupon_management.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'coupon_management.php?success=1';
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
                    submitButton.disabled = false;
                    submitButton.innerHTML = `<i class="bi bi-save me-1"></i>บันทึกการแก้ไข`;
                });
            }, 1500);
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>