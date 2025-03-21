<?php
session_start();
include '../Database/config.php'; // ไฟล์เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
// ตรวจสอบการlogin
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
}

// รับข้อมูลจากฟอร์ม (สมมติว่าส่งมาจากหน้า checkout)
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username']; // เก็บ username ใน session
$email = $_POST['email'] ?? $_SESSION['email']; // อีเมลจากฟอร์มหรือ session
$items = $_POST['items']; // รายการสินค้า (อาจเป็น string หรือ JSON)
$total_price = (float)$_POST['total_price']; // ราคารวม
$discount = isset($_POST['discount']) ? (float)$_POST['discount'] : 0.00; // ส่วนลด (ถ้ามี)

// สร้างรหัสอ้างอิงคำสั่งซื้อ (order_reference)
//$order_reference = "ORD" . time() . rand(100, 999); // ตัวอย่างการสร้างรหัส เช่น ORD1710741234567

// บันทึกคำสั่งซื้อลงตาราง orderhistory
$query = "INSERT INTO orderhistory (order_reference, username, email, item, total_price, discount, created_at) 
          VALUES (?, ?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssssdd", $order_reference, $username, $email, $items, $total_price, $discount);

if ($stmt->execute()) {
    // บันทึกสำเร็จ สร้างการแจ้งเตือน
    $title = "สั่งซื้อสำเร็จ";
    $message = "คำสั่งซื้อ #$order_reference สำเร็จแล้ว";
    $type = "order";

    $query_notify = "INSERT INTO notifications (user_id, title, message, type, created_at) 
                     VALUES (?, ?, ?, ?, NOW())";
    $stmt_notify = $conn->prepare($query_notify);
    $stmt_notify->bind_param("isss", $user_id, $title, $message, $type);
    $stmt_notify->execute();
    $stmt_notify->close();

    // ปิด statement และเปลี่ยนเส้นทาง
    $stmt->close();
    $conn->close();
    header("Location: ../Dashboard/dashboard.php?status=success");
    exit();
} else {
    // บันทึกไม่สำเร็จ
    $stmt->close();
    $conn->close();
    header("Location: ../Product/checkout.php?status=error");
    exit();
}
?>