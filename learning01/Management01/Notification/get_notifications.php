<?php
session_start();
include '../Database/config.php'; // ไฟล์เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
// ตรวจสอบการlogin
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลการแจ้งเตือน
$query = "SELECT id, title, message, type, is_read, created_at 
          FROM notifications 
          WHERE user_id = ? 
          ORDER BY created_at DESC 
          LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'message' => $row['message'],
        'type' => $row['type'],
        'is_read' => (bool)$row['is_read'],
        'created_at' => date('d ม.ค. Y H:i น.', strtotime($row['created_at']))
    ];
}

// นับจำนวนแจ้งเตือนที่ยังไม่ได้อ่าน
$query_unread = "SELECT COUNT(*) as unread_count 
                 FROM notifications 
                 WHERE user_id = ? AND is_read = 0";
$stmt_unread = $conn->prepare($query_unread);
$stmt_unread->bind_param("i", $user_id);
$stmt_unread->execute();
$unread_result = $stmt_unread->get_result();
$unread_count = $unread_result->fetch_assoc()['unread_count'];

$stmt->close();
$stmt_unread->close();
$conn->close();

// ส่งข้อมูลในรูปแบบ JSON
header('Content-Type: application/json');
echo json_encode([
    'notifications' => $notifications,
    'unread_count' => $unread_count
]);
?>