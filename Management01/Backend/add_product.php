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

// ฟังก์ชันสำหรับลบประเภทสินค้า
if (isset($_POST['delete_product_type'])) {
    $type_id = $_POST['type_id'];
    
    // ตรวจสอบว่ามีสินค้าในประเภทนี้หรือไม่
    $check_products = $conn->prepare("SELECT COUNT(*) as count FROM productlist WHERE product_id = ?");
    $check_products->bind_param("i", $type_id);
    $check_products->execute();
    $result = $check_products->get_result();
    $count = $result->fetch_assoc()['count'];
    
    if ($count > 0) {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบได้เนื่องจากมีสินค้าในประเภทนี้']);
        exit;
    }
    
    // ดำเนินการลบประเภทสินค้า
    $delete_stmt = $conn->prepare("DELETE FROM product WHERE id = ?");
    $delete_stmt->bind_param("i", $type_id);
    
    if ($delete_stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบประเภทสินค้า']);
    }
    exit;
}

// การเพิ่มสินค้าใหม่
if (isset($_POST['add'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $detail = $_POST['detail'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $image_url = $_POST['image_url'];
    $orderdate = $_POST['orderdate'];

    $stmt = $conn->prepare("INSERT INTO productlist (product_id, name, detail, price, quantity, image_url, orderdate) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("issiiss", 
        $product_id,
        $name,
        $detail,
        $price,
        $quantity,
        $image_url,
        $orderdate
    );

    if ($stmt->execute()) {
        header("Location: management.php?success=1");
        exit;
    } else {
        $message = "เกิดข้อผิดพลาด: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มสินค้าใหม่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../Assets/CSS/management.css" rel="stylesheet">
    <script src="../Assets/JS/addproduct.js"></script>
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">เพิ่มสินค้าใหม่</h5>
                        <a href="management.php" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>
                            กลับ
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-danger">
                                <?= $message ?>
                            </div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">ประเภทสินค้า</label>
                                <div class="input-group">
                                    <select class="form-select" name="product_id" id="product_id" required>
                                        <option value="">เลือกประเภทสินค้า</option>
                                        <?php
                                        $product_sql = "SELECT id, nameType FROM product";
                                        $product_result = $conn->query($product_sql);
                                        while ($product = $product_result->fetch_assoc()) {
                                            echo "<option value='" . $product['id'] . "'>" . $product['nameType'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                        + เพิ่มประเภทใหม่
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#manageProductTypesModal">
                                        จัดการประเภท
                                    </button>
                                </div>
                            </div>

                            <!-- Modal สำหรับเพิ่มประเภทสินค้า -->
                            <div class="modal fade" id="addProductModal" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">เพิ่มประเภทสินค้าใหม่</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">ชื่อประเภทสินค้า</label>
                                                <input type="text" class="form-control" id="newProductType">
                                                <div class="invalid-feedback">
                                                    กรุณากรอกชื่อประเภทสินค้า
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                            <button type="button" class="btn btn-primary" onclick="saveNewProduct()">บันทึก</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal จัดการประเภทสินค้า -->
                            <div class="modal fade" id="manageProductTypesModal" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">จัดการประเภทสินค้า</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="list-group">
                                                <?php
                                                $product_result = $conn->query("SELECT id, nameType FROM product");
                                                while ($product = $product_result->fetch_assoc()) {
                                                    echo '<div class="list-group-item d-flex justify-content-between align-items-center">';
                                                    echo htmlspecialchars($product['nameType']);
                                                    echo '<button type="button" class="btn btn-danger btn-sm" onclick="deleteProductType(' . $product['id'] . ')">';
                                                    echo '<i class="bi bi-trash"></i> ลบ</button>';
                                                    echo '</div>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ชื่อสินค้า</label>
                                <input type="text" class="form-control" name="name" autocomplete="off" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">รายละเอียด</label>
                                <textarea class="form-control" name="detail" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ราคา</label>
                                <input type="text" class="form-control" name="price" id="priceInput" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">จำนวน</label>
                                <input type="number" class="form-control" name="quantity" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">วันที่สั่ง</label>
                                <input type="datetime-local" class="form-control" name="orderdate" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">URL รูปภาพ</label>
                                <input type="text" class="form-control" name="image_url" autocomplete="off" required>
                            </div>
                            <div class="text-end">
                                <button type="submit" name="add" class="btn btn-success">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    เพิ่มสินค้า
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // ฟังก์ชันสำหรับลบประเภทสินค้า
    function deleteProductType(typeId) {
        if (confirm('คุณแน่ใจหรือไม่ที่จะลบประเภทสินค้านี้?')) {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'delete_product_type=1&type_id=' + typeId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'เกิดข้อผิดพลาดในการลบประเภทสินค้า');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('เกิดข้อผิดพลาดในการลบประเภทสินค้า');
            });
        }
    }

    // จัดการการแสดงผลราคา
    document.getElementById("priceInput").addEventListener("input", function (e) {
        let value = e.target.value.replace(/[^0-9]/g, "").replace(/^0+/, ""); 
        if (value !== "") {
            e.target.value = Number(value).toLocaleString();
        } else {
            e.target.value = "";
        }
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>