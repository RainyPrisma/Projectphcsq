<?php
session_start();
require_once dirname(__DIR__) . '/Assets/src/UserCookieManager.php';
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
    $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
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

// ดึงข้อมูลสถิติ
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

// 3. สินค้าที่ซื้อบ่อยที่สุด
$sql_most_purchased = "SELECT item FROM orderhistory WHERE email = ? GROUP BY item ORDER BY COUNT(*) DESC LIMIT 1";
$stmt_most_purchased = $conn->prepare($sql_most_purchased);
$stmt_most_purchased->bind_param("s", $email);
$stmt_most_purchased->execute();
$most_purchased_result = $stmt_most_purchased->get_result()->fetch_assoc();
$most_purchased_item = 'ยังไม่มี';
if ($most_purchased_result) {
    $item_text = $most_purchased_result['item'];
    $items = explode(',', $item_text);
    $most_purchased_item = trim($items[0]);
}

// 4. คำสั่งซื้อล่าสุด
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

// 6. ดึงข้อมูลสินค้าที่คนซื้อมากที่สุด (จากทุกผู้ใช้)
$sql_popular_products = "
    SELECT pus.สินค้า AS product_name, SUM(pus.จำนวน) AS total_quantity
    FROM purchases_by_user_split pus
    GROUP BY pus.สินค้า
    ORDER BY total_quantity DESC
    LIMIT 6
";
$popular_products_result = $conn->query($sql_popular_products);
$popular_products = [];
if ($popular_products_result) {
    while ($row = $popular_products_result->fetch_assoc()) {
        $popular_products[] = [
            'product_name' => $row['product_name'],
            'total_quantity' => $row['total_quantity']
        ];
    }
}

// 7. การแจ้งเตือน
$query = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_data['id']);
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
$stmt_unread->bind_param("i", $user_data['id']);
$stmt_unread->execute();
$unread_result = $stmt_unread->get_result();
$unread_count = $unread_result->fetch_assoc()['unread_count'];

// ตรวจสอบสถานะจากการดำเนินการ
$status_message = '';
$status_type = '';
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'marked': $status_message = "ทำเครื่องหมายว่าอ่านแล้วสำเร็จ"; $status_type = "success"; break;
        case 'no_change': $status_message = "แจ้งเตือนนี้ถูกอ่านแล้ว หรือไม่พบแจ้งเตือน"; $status_type = "info"; break;
        case 'missing_id': $status_message = "ไม่พบรหัสแจ้งเตือน"; $status_type = "warning"; break;
        case 'cleared': $status_message = "เคลียร์การแจ้งเตือนทั้งหมดสำเร็จ"; $status_type = "success"; break;
        case 'no_notifications': $status_message = "ไม่มีแจ้งเตือนให้เคลียร์"; $status_type = "info"; break;
        case 'error': $status_message = "เกิดข้อผิดพลาด กรุณาลองใหม่"; $status_type = "danger"; break;
    }
}

// ดึงข้อมูลคำสั่งซื้อล่าสุด
$order_query = "SELECT username, item, created_at, order_reference 
                FROM orderhistory 
                WHERE email = ? 
                ORDER BY created_at DESC 
                LIMIT 5";
$stmt_order = $conn->prepare($order_query);
$stmt_order->bind_param("s", $email);
$stmt_order->execute();
$order_result = $stmt_order->get_result();
$orders = [];
if ($order_result) {
    while ($row = $order_result->fetch_assoc()) {
        $orders[] = $row;
    }
    $order_result->free();
}

// ดึงข้อมูลจาก View recent_activities
$query = "SELECT activity_type, id, description, activity_time 
          FROM recent_activities 
          WHERE email = ? 
          ORDER BY activity_time DESC 
          LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$activities = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    $result->free();
}

$stmt->close();
$stmt_unread->close();
$stmt_order->close();
$conn->close();
?>