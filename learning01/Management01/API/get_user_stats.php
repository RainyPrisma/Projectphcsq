<?php
session_start();
require_once dirname(__DIR__) . '/Assets/src/UserCookieManager.php';
// ตรวจสอบ session และความปลอดภัย
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

// ดึงข้อมูลสถิติต่างๆ
$stats = [
    'total_orders' => 0,
    'total_spending' => 0,
    'most_purchased_item' => 'ยังไม่มี',
    'latest_order' => null
];

// 1. จำนวนคำสั่งซื้อทั้งหมด
$sql_total_orders = "SELECT COUNT(*) as total_orders FROM orderhistory WHERE email = ?";
$stmt_total_orders = $conn->prepare($sql_total_orders);
$stmt_total_orders->bind_param("s", $email);
$stmt_total_orders->execute();
$total_orders_result = $stmt_total_orders->get_result()->fetch_assoc();
$stats['total_orders'] = $total_orders_result['total_orders'];

// 2. ยอดใช้จ่ายรวม
$sql_total_spending = "SELECT SUM(total_price) as total_spending FROM orderhistory WHERE email = ?";
$stmt_total_spending = $conn->prepare($sql_total_spending);
$stmt_total_spending->bind_param("s", $email);
$stmt_total_spending->execute();
$total_spending_result = $stmt_total_spending->get_result()->fetch_assoc();
$stats['total_spending'] = $total_spending_result['total_spending'] ?? 0;

// 3. สินค้าที่ซื้อบ่อยที่สุด
$sql_most_purchased = "SELECT item FROM orderhistory WHERE email = ? GROUP BY item ORDER BY COUNT(*) DESC LIMIT 1";
$stmt_most_purchased = $conn->prepare($sql_most_purchased);
$stmt_most_purchased->bind_param("s", $email);
$stmt_most_purchased->execute();
$most_purchased_result = $stmt_most_purchased->get_result()->fetch_assoc();

if ($most_purchased_result) {
    $item_text = $most_purchased_result['item'];
    $items = explode(',', $item_text);
    $stats['most_purchased_item'] = trim($items[0]);
}

// 4. คำสั่งซื้อล่าสุด
$sql_latest_order = "SELECT order_id, total_price, created_at, order_reference FROM orderhistory WHERE email = ? ORDER BY created_at DESC LIMIT 1";
$stmt_latest_order = $conn->prepare($sql_latest_order);
$stmt_latest_order->bind_param("s", $email);
$stmt_latest_order->execute();
$stats['latest_order'] = $stmt_latest_order->get_result()->fetch_assoc();

$conn->close();

// ส่งข้อมูลกลับเป็น JSON
header('Content-Type: application/json');
echo json_encode($stats);