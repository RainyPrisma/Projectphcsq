<?php
session_start();
require_once('../Assets/src/backendreq.php');

// ลบสินค้า
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM productlist WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "ลบสินค้าสำเร็จ!";
    } else {
        $message = "เกิดข้อผิดพลาด: " . $stmt->error;
    }
    $stmt->close();
}

// อัปเดตสินค้า
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $detail = $_POST['detail'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $orderdate = $_POST['orderdate'];

    // ดึงข้อมูลรูปภาพเก่าก่อน
    $stmt = $conn->prepare("SELECT image_url FROM productlist WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $old_image = $result->fetch_assoc()['image_url'];
    $stmt->close();

    // การจัดการไฟล์รูปภาพ
    $image_path = $old_image; // ค่าเริ่มต้นคือรูปภาพเก่า
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/Mainpicture/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // เพิ่ม product_id ในชื่อไฟล์
        $image_name = time() . '_product_' . $product_id . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array($imageFileType, $allowed_types)) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image_path = $target_file;
                    // ลบรูปภาพเก่า ถ้ามี
                    if (!empty($old_image) && file_exists($old_image)) {
                        unlink($old_image);
                    }
                } else {
                    $message = "เกิดข้อผิดพลาดในการอัพโหลดไฟล์";
                }
            } else {
                $message = "อนุญาตเฉพาะไฟล์ JPG, JPEG, PNG และ GIF เท่านั้น";
            }
        } else {
            $message = "ไฟล์ที่เลือกไม่ใช่รูปภาพ";
        }
    }

    $stmt = $conn->prepare("UPDATE productlist SET 
        product_id = ?,
        name = ?, 
        detail = ?, 
        price = ?, 
        quantity = ?,
        image_url = ?, 
        orderdate = ? 
        WHERE id = ?");
    
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("issiissi", 
        $product_id,
        $name,
        $detail,
        $price,
        $quantity,
        $image_path,
        $orderdate,
        $id
    );

    if ($stmt->execute()) {
        $message = "อัปเดตข้อมูลสินค้าสำเร็จ!";
        if (isset($_GET['success']) && $_GET['success'] == 1) {
            $message = "เพิ่มสินค้าใหม่สำเร็จ!";
        }
    } else {
        $message = "เกิดข้อผิดพลาด: " . $stmt->error;
    }
    $stmt->close();
}

// ดึงข้อมูลสินค้าทั้งหมดสำหรับการแสดงผลครั้งแรก
$sql = "SELECT pl.*, p.id as product_id 
        FROM productlist pl 
        JOIN product p ON pl.product_id = p.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="https://customseafoods.com/cdn/shop/files/CS_Logo_2_1000.webp?v=1683664967" type="image/png">
    <link href="../Assets/CSS/management.css" rel="stylesheet">
    <script src="../Assets/JS/paginationControl.js"></script>
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
                        <a class="nav-link text-white active" href="management.php">จัดการสินค้า</a>
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
        <h2>จัดการข้อมูลสินค้า</h2>

        <?php if (isset($message)): ?>
            <div id="message" class="alert alert-success message">Welcome !<?= $message ?></div>
        <?php endif; ?>

        <div class="search-container my-4">
            <div class="input-group">
                <input type="search" class="form-control" name="search" 
                       placeholder="🔍 ค้นหาสินค้า..." autocomplete="off">
            </div>
        </div>

        <div class="mb-4">
            <a href="add_product.php" class="btn btn-success">
                <i class="bi bi-plus-circle me-1"></i>
                เพิ่มสินค้าใหม่
            </a>
            <a href="coupon_management.php" class="btn btn-success">
                <i class="bi bi-plus-circle me-1"></i>
                จัดการคูปอง
            </a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>รหัสสินค้า</th>
                        <th>ชื่อสินค้า</th>
                        <th>รายละเอียด</th>
                        <th>ราคา</th>
                        <th>จำนวน</th>
                        <th>วันที่สั่ง</th>
                        <th>รูปภาพ</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['product_id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['detail']) ?></td>
                            <td><?= number_format($row['price'], 2) ?> ฿</td>
                            <td><?= $row['quantity'] ?></td>
                            <td><?= $row['orderdate'] ?></td>
                            <td><img src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" style="max-width: 100px;"></td>
                            <td>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">แก้ไข</button>
                                <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบ?')">ลบ</a>
                            </td>
                        </tr>

                        <!-- Modal แก้ไขสินค้า -->
                        <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">แก้ไขข้อมูลสินค้า</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="post" enctype="multipart/form-data"> <!-- เพิ่ม enctype -->
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                                            <div class="mb-3">
                                                <label class="form-label">ชื่อสินค้า</label>
                                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">รายละเอียด</label>
                                                <textarea class="form-control" name="detail" required><?= htmlspecialchars($row['detail']) ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">ราคา</label>
                                                <input type="number" class="form-control" name="price" value="<?= $row['price'] ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">จำนวน</label>
                                                <input type="number" class="form-control" name="quantity" value="<?= $row['quantity'] ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">วันที่สั่ง</label>
                                                <input type="datetime-local" class="form-control" name="orderdate" 
                                                    value="<?= date('Y-m-d\TH:i', strtotime($row['orderdate'])) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">รูปภาพสินค้า</label>
                                                <input type="file" class="form-control" name="image" accept="image/*">
                                                <?php if (!empty($row['image_url'])): ?>
                                                    <div class="mt-2">
                                                        <p>รูปภาพปัจจุบัน:</p>
                                                        <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="Current Image" style="max-width: 100px;">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                            <button type="submit" name="update" class="btn btn-primary">บันทึก</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="../Assets/JS/fadeMessage.js"></script>                    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../Assets/JS/manage_search.js"></script>
    <?php $conn->close(); ?>
</body>
</html>