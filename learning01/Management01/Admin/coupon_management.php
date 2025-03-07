<?php
require_once '../Backend/couponreq.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการคูปอง</title>
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
            <!-- แสดงรายการคูปอง -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-ticket-perforated-fill me-2"></i>รายการคูปองทั้งหมด
                        </h5>
                        <a href="management.php" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>
                            กลับ
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (count($coupons) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>รหัสคูปอง</th>
                                            <th>ส่วนลด</th>
                                            <th>วันที่เริ่มใช้</th>
                                            <th>วันที่สิ้นสุด</th>
                                            <th>ยอดขั้นต่ำ</th>
                                            <th>สถานะ</th>
                                            <th>การใช้งาน</th>
                                            <th>จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($coupons as $coupon): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($coupon['coupon_code']) ?></td>
                                                <td>
                                                    <?= htmlspecialchars($coupon['discount_amount']) ?>
                                                    <?= $coupon['discount_type'] == 'percentage' ? '%' : '฿' ?>
                                                </td>
                                                <td><?= date('d/m/Y H:i', strtotime($coupon['valid_from'])) ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($coupon['valid_until'])) ?></td>
                                                <td><?= number_format($coupon['min_purchase'], 2) ?>฿</td>
                                                <td>
                                                    <?php if ($coupon['is_active']): ?>
                                                        <span class="badge bg-success">เปิดใช้งาน</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">ปิดใช้งาน</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?= $coupon['current_usage'] ?>/
                                                    <?= $coupon['usage_limit'] == 0 ? 'ไม่จำกัด' : $coupon['usage_limit'] ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <a href="edit_coupon.php?id=<?= $coupon['coupon_id'] ?>" class="btn btn-sm btn-primary">
                                                            <i class="bi bi-pencil-square"></i> แก้ไข
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger delete-coupon-btn" 
                                                                data-coupon-id="<?= $coupon['coupon_id'] ?>" 
                                                                data-coupon-code="<?= htmlspecialchars($coupon['coupon_code']) ?>">
                                                            <i class="bi bi-trash"></i> ลบ
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">ยังไม่มีคูปองในระบบ</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ฟอร์มเพิ่มคูปอง -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-plus-circle me-2"></i>เพิ่มคูปองใหม่
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="post" id="couponForm">
                            <div class="mb-3">
                                <label class="form-label">รหัสคูปอง</label>
                                <input type="text" class="form-control" name="coupon_code" required placeholder="เช่น SUMMER2025" autocomplete="off">
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">มูลค่าส่วนลด</label>
                                    <input type="number" class="form-control" name="discount_amount" step="0.01" min="0" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ประเภทส่วนลด</label>
                                    <select class="form-select" name="discount_type" required>
                                        <option value="percentage">เปอร์เซ็นต์ (%)</option>
                                        <option value="fixed">จำนวนเงิน (฿)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">วันที่เริ่มใช้งาน</label>
                                    <input type="datetime-local" class="form-control" name="valid_from" value="<?= date('Y-m-d\TH:i') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">วันที่สิ้นสุด</label>
                                    <input type="datetime-local" class="form-control" name="valid_until" value="<?= date('Y-m-d\TH:i', strtotime('+30 days')) ?>" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">ยอดสั่งซื้อขั้นต่ำ (฿)</label>
                                    <input type="number" class="form-control" name="min_purchase" value="0" step="0.01" min="0" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">จำกัดการใช้งาน</label>
                                    <input type="number" class="form-control" name="usage_limit" value="1" min="0" required>
                                    <div class="form-text">0 = ไม่จำกัดจำนวนครั้ง</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                                    <label class="form-check-label" for="is_active">เปิดใช้งานคูปองทันที</label>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" name="add_coupon" id="submitButton" class="btn btn-success">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    เพิ่มคูปอง
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('couponForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            formData.append('add_coupon', '1'); // เพิ่ม flag เพื่อให้ PHP รู้ว่าเป็นการเพิ่มคูปอง
            const submitButton = document.getElementById('submitButton');
            
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> กำลังเพิ่ม...`;

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
                    submitButton.innerHTML = `<i class="bi bi-plus-circle me-1"></i>เพิ่มคูปอง`;
                });
            }, 1500);
        });

        document.querySelectorAll('.delete-coupon-btn').forEach(button => {
            button.addEventListener('click', function() {
                const couponId = this.getAttribute('data-coupon-id');
                const couponCode = this.getAttribute('data-coupon-code');
                if (confirm(`ต้องการลบคูปอง "${couponCode}" ใช่หรือไม่?`)) {
                    const submitButton = this;
                    submitButton.disabled = true;
                    submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> กำลังลบ...`;

                    fetch('coupon_management.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `delete_coupon=1&coupon_id=${couponId}`
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
                        alert(error.message || 'เกิดข้อผิดพลาดในการลบข้อมูล');
                        submitButton.disabled = false;
                        submitButton.innerHTML = `<i class="bi bi-trash"></i> ลบ`;
                    });
                }
            });
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>