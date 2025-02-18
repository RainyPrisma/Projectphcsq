<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ตรวจสอบสถานะการล็อกอินและบทบาท
if (!isset($_SESSION['user_email']) || $_SESSION['role'] !== 'admin') {
    session_unset();
    session_destroy();
    header("Location: ../Frontend/login.php");
    exit;
}

// ตรวจสอบ Session Timeout
$session_timeout = 18000; // 10 นาที
if (!isset($_SESSION['last_activity']) || (time() - $_SESSION['last_activity']) > $session_timeout) {
    session_unset();
    session_destroy();
    header("Location: ../Frontend/login.php");
    exit;
}

$_SESSION['last_activity'] = time();

// การเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "management01";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

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
    $image_url = $_POST['image_url'];
    $orderdate = $_POST['orderdate'];

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
        $image_url,
        $orderdate,
        $id
    );

    if ($stmt->execute()) {
        $message = "อัปเดตข้อมูลสินค้าสำเร็จ!";
    // เพิ่มหลังจากบรรทัด $message = "";
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

        <?php if ($message): ?>
            <div class="alert alert-success">
                <?= $message ?>
            </div>
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
                                    <form method="post">
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
                                                <label class="form-label">URL รูปภาพ</label>
                                                <input type="text" class="form-control" name="image_url" 
                                                       value="<?= htmlspecialchars($row['image_url']) ?>" required>
                                                <div class="mt-2">
                                                    <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="Preview" style="max-width: 100px;">
                                                </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../Assets/JS/manage_search.js"></script>
    <?php $conn->close(); ?>
</body>
</html>