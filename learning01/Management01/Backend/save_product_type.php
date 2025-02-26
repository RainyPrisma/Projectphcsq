<?php
include '../Database/config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => '', 'id' => null];

try {
    if (!isset($_POST['nameType']) || empty(trim($_POST['nameType']))) {
        throw new Exception('กรุณากรอกชื่อประเภทสินค้า');
    }

    $nameType = trim($_POST['nameType']);
    
    // ตรวจสอบว่ามีชื่อประเภทนี้อยู่แล้วหรือไม่
    $check_sql = "SELECT id FROM product WHERE nameType = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $nameType);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        throw new Exception('มีประเภทสินค้านี้อยู่แล้ว');
    }
    
    // บันทึกข้อมูลใหม่
    $insert_sql = "INSERT INTO product (nameType) VALUES (?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("s", $nameType);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['id'] = $conn->insert_id;
        $response['message'] = 'บันทึกข้อมูลสำเร็จ';
    } else {
        throw new Exception('เกิดข้อผิดพลาดในการบันทึกข้อมูล');
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>