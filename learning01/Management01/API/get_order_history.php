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

$email = $_SESSION['user_email'];
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;

// ดึงประวัติการสั่งซื้อ
$sql_order_history = "SELECT order_id, total_price, created_at, order_reference FROM orderhistory WHERE email = ? ORDER BY created_at DESC LIMIT ?";
$stmt_order_history = $conn->prepare($sql_order_history);
$stmt_order_history->bind_param("si", $email, $limit);
$stmt_order_history->execute();
$result = $stmt_order_history->get_result();

$orders = [];
while($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

$conn->close();

// ส่งข้อมูลกลับเป็น JSON
header('Content-Type: application/json');
echo json_encode($orders);