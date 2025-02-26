<?php
session_start();
require_once('../Assets/src/backendreq.php');
// ฟังก์ชันสำหรับเพิ่มประเภทสินค้าใหม่
if (isset($_POST['new_product_type'])) {
    $nameType = trim($_POST['nameType']);
    
    // ตรวจสอบว่ามีชื่อประเภทนี้แล้วหรือไม่
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM product WHERE nameType = ?");
    $check_stmt->bind_param("s", $nameType);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    
    if ($count > 0) {
        echo json_encode(['success' => false, 'message' => 'มีประเภทสินค้านี้อยู่แล้ว']);
        exit;
    }
    
    // เพิ่มประเภทสินค้าใหม่
    $insert_stmt = $conn->prepare("INSERT INTO product (nameType) VALUES (?)");
    $insert_stmt->bind_param("s", $nameType);
    
    if ($insert_stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการเพิ่มประเภทสินค้า']);
    }
    exit;
}
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
    $price = str_replace(',', '', $_POST['price']); // ลบ comma ออกจากราคา
    $quantity = $_POST['quantity'];
    $image_url = $_POST['image_url'];
    $orderdate = $_POST['orderdate'];

    $stmt = $conn->prepare("INSERT INTO productlist (product_id, name, detail, price, quantity, image_url, orderdate) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        http_response_code(500);
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

    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        http_response_code(200);
        header("Location: management.php?success=1");
        exit;
    } else {
        http_response_code(500);
        die('Execute failed: ' . $conn->error);
    }
}
?>