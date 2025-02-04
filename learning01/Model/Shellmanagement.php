<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ตรวจสอบสถานะการล็อกอินและบทบาท
if (!isset($_SESSION['user_email']) || $_SESSION['role'] !== 'admin') {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit;
}

// ตรวจสอบ Session Timeout
$session_timeout = 1800; // 30 นาที
if (!isset($_SESSION['last_activity']) || (time() - $_SESSION['last_activity']) > $session_timeout) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
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
    $stmt = $conn->prepare("DELETE FROM shellproduct WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "ลบสินค้าสำเร็จ!";
    } else {
        $message = "เกิดข้อผิดพลาด!";
    }
    $stmt->close();
}

// อัปเดตสินค้า
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image_url = $_POST['image_url'];

    $stmt = $conn->prepare("UPDATE shellproduct SET name = ?, price = ?, image_url = ? WHERE id = ?");
    $stmt->bind_param("sdsi", $name, $price, $image_url, $id);

    if ($stmt->execute()) {
        $message = "อัปเดตข้อมูลสินค้าสำเร็จ!";
    } else {
        $message = "เกิดข้อผิดพลาด!";
    }
    $stmt->close();
}

// ดึงข้อมูลสินค้าทั้งหมดพร้อมการค้นหา
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM shellproduct WHERE name LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $search . "%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการข้อมูลสินค้า (สำหรับผู้ดูแลระบบ)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- แก้ path CSS เป็น -->
    <link href="../View/management.css?v=1.3" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid px-4">
            <span class="navbar-brand">StorageManagement</span>
            <div>
                <span class="text-white me-3">ผู้ใช้: <?= htmlspecialchars($_SESSION['user_email']) ?></span>
                <a href="../index.php" class="home-button">กลับหน้าหลัก</a>
                <div class="dropdown d-inline-block">
                    <button class="home-button dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        เลือกข้อมูลจัดการ
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="management.php">Special & Seasonal</a></li>
                        <li><a class="dropdown-item" href="Fishmanagement.php?type=fish">Fish</a></li>
                        <li><a class="dropdown-item" href="Occtsmanagement.php?type=octs">Octs</a></li>
                        <li><a class="dropdown-item" href="Shellmanagement.php?type=shell">Shell</a></li>
                    </ul>
                </div>
                <a href="../logout.php" class="btn btn-danger">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>จัดการข้อมูลสินค้าหอย</h2>

        <?php if ($message): ?>
            <div class="alert alert-success">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- เพิ่มฟอร์มค้นหา -->
        <div class="search-container my-4">
                <form method="GET">
                    <div class="input-group">
                        <input type="search" class="form-control" placeholder="🔍 ค้นหาสินค้า..." 
                            name="search" value="<?= htmlspecialchars($search) ?>">
                        <button class="btn search-btn" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                        <?php if ($search): ?>
                            <a href="?type=shell" class="btn clear-btn">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ชื่อสินค้า</th>
                        <th>ราคา</th>
                        <th>รูปภาพ</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= number_format($row['price'], 2) ?> บาท</td>
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
                                            <div class="mb-3">
                                                <label class="form-label">ชื่อสินค้า</label>
                                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">ราคา</label>
                                                <input type="number" step="0.01" class="form-control" name="price" value="<?= $row['price'] ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">URL รูปภาพ</label>
                                                <input type="text" class="form-control" name="image_url" value="<?= htmlspecialchars($row['image_url']) ?>" required>
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php $conn->close(); ?>
</body>
</html>