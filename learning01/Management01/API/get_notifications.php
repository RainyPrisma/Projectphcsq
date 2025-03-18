<?php
session_start();
// ตรวจสอบ session
if (!isset($_SESSION['user_email'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "1234", "management01");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// ดึง user_id จาก email
$email = $_SESSION['user_email'];
$sql = "SELECT id FROM users WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user_id = $result->fetch_assoc()['id'];

// ดึงข้อมูลการแจ้งเตือน
$query = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

// นับจำนวนการแจ้งเตือนที่ยังไม่ได้อ่าน
$query_unread = "SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0";
$stmt_unread = $conn->prepare($query_unread);
$stmt_unread->bind_param("i", $user_id);
$stmt_unread->execute();
$unread_result = $stmt_unread->get_result();
$unread_count = $unread_result->fetch_assoc()['unread_count'];

$conn->close();

// ส่งข้อมูลกลับเป็น JSON
header('Content-Type: application/json');
echo json_encode([
    'notifications' => $notifications,
    'unread_count' => $unread_count
]);