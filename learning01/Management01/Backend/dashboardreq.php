<?php
session_start();
require_once dirname(__DIR__) . '../Assets/src/UserCookieManager.php';
use src\UserCookieManager;

$cookieManager = new UserCookieManager();

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "1234", "management01");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบการlogin
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
} else {
    $user_email = $_SESSION['user_email'];
    $sql = "SELECT * FROM users WHERE email = ? LIMIT 1"; // ดึงข้อมูลผู้ใช้จาก orders
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $cookieData = [
            'username' => $user_data['username'],
            'email' => $user_data['email'],
            'last_login' => date('Y-m-d H:i:s')
        ];
        $cookieManager->setUserCookie($cookieData);
        $_SESSION['user_name'] = $user_data['username'];
    } else {
        header('Location: login.php');
        exit();
    }
}

// ตรวจสอบ Session Timeout
$session_timeout = 1800; // 30 นาที
if (!isset($_SESSION['last_activity']) || (time() - $_SESSION['last_activity']) > $session_timeout) {
    session_unset();
    session_destroy();
    header("Location: ../Frontend/login.php");
    exit;
}
$_SESSION['last_activity'] = time();

// ดึงข้อมูลสถิติ (ใช้ email หรือ username แทน user_id)
$email = $user_data['email'];

// 1. จำนวนคำสั่งซื้อทั้งหมด
$sql_total_orders = "SELECT COUNT(*) as total_orders FROM orderhistory WHERE email = ?";
$stmt_total_orders = $conn->prepare($sql_total_orders);
$stmt_total_orders->bind_param("s", $email);
$stmt_total_orders->execute();
$total_orders_result = $stmt_total_orders->get_result()->fetch_assoc();
$total_orders = $total_orders_result['total_orders'];

// 2. ยอดใช้จ่ายรวม
$sql_total_spending = "SELECT SUM(total_price) as total_spending FROM orderhistory WHERE email = ?";
$stmt_total_spending = $conn->prepare($sql_total_spending);
$stmt_total_spending->bind_param("s", $email);
$stmt_total_spending->execute();
$total_spending_result = $stmt_total_spending->get_result()->fetch_assoc();
$total_spending = $total_spending_result['total_spending'] ?? 0;

// 3. สินค้าที่ซื้อบ่อยที่สุด (ดึงเฉพาะชื่อสินค้าแรกจาก item)
$sql_most_purchased = "SELECT item FROM orderhistory WHERE email = ? GROUP BY item ORDER BY COUNT(*) DESC LIMIT 1";
$stmt_most_purchased = $conn->prepare($sql_most_purchased);
$stmt_most_purchased->bind_param("s", $email);
$stmt_most_purchased->execute();
$most_purchased_result = $stmt_most_purchased->get_result()->fetch_assoc();

// แยกชื่อสินค้าแรกจาก item (สมมติว่า item เป็นข้อความแยกด้วย comma หรือ plain text)
$most_purchased_item = 'ยังไม่มี';
if ($most_purchased_result) {
    $item_text = $most_purchased_result['item'];
    // ถ้า item เป็นข้อความแยกด้วย comma (เช่น "ปูม้า, กุ้งลายเสือ")
    $items = explode(',', $item_text);
    $most_purchased_item = trim($items[0]); // ใช้ชื่อสินค้าแรกและตัดช่องว่าง
}

// 4. คำสั่งซื้อล่าสุด (จาก orderhistory)
$sql_latest_order = "SELECT order_id, total_price, created_at, order_reference FROM orderhistory WHERE email = ? ORDER BY created_at DESC LIMIT 1";
$stmt_latest_order = $conn->prepare($sql_latest_order);
$stmt_latest_order->bind_param("s", $email);
$stmt_latest_order->execute();
$latest_order = $stmt_latest_order->get_result()->fetch_assoc();

// 5. ประวัติการสั่งซื้อ (5 รายการล่าสุด)
$sql_order_history = "SELECT order_id, total_price, created_at, order_reference FROM orderhistory WHERE email = ? ORDER BY created_at DESC LIMIT 5";
$stmt_order_history = $conn->prepare($sql_order_history);
$stmt_order_history->bind_param("s", $email);
$stmt_order_history->execute();
$order_history_result = $stmt_order_history->get_result();

// 6. สินค้าแนะนำ (เลือกสินค้าที่มีการสั่งซื้อบ่อยหรือล่าสุด)
$sql_recommended = "SELECT name, price, image_url FROM productlist WHERE image_url IS NOT NULL ORDER BY quantity DESC, orderdate DESC LIMIT 2";
$recommended_result = $conn->query($sql_recommended);
?>