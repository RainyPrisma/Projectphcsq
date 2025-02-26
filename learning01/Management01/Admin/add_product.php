<?php
include('../Backend/add_productreq.php');
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

                        <form method="post" id="myForm">
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
                            <button type="submit" name="add" id="submitButton" class="btn btn-success">
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
    <script>
        document.getElementById("myForm").addEventListener("submit", function(event) {
            event.preventDefault();
            
            // เก็บข้อมูลฟอร์ม
            const formData = new FormData(this);
            
            // ปิดปุ่มและแสดง loading
            let submitButton = document.getElementById("submitButton");
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> กำลังเพิ่ม...`;
            
            // รอ 1.5 วินาทีแล้วค่อยส่งข้อมูล
            setTimeout(() => {
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        window.location.href = 'management.php?success=1';
                    } else {
                        throw new Error('Something went wrong');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล');
                    submitButton.disabled = false;
                    submitButton.innerHTML = `<i class="bi bi-plus-circle me-1"></i>เพิ่มสินค้า`;
                });
            }, 1500);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>