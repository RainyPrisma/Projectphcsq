<?php
session_start();
include 'config.php';
require_once dirname(__DIR__) . '../Assets/src/UserCookieManager.php';
use src\UserCookieManager;

header('Content-Type: application/json');

// Check Admin rights
if (!isset($_SESSION['user_email'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_email = $_SESSION['user_email'];
$sql = "SELECT * FROM users WHERE email = ? AND role = 'admin'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(['error' => 'Not an admin']);
    exit();
}

// Handle different API requests
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'dashboard_stats':
        $total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
        $total_orders = $conn->query("SELECT COUNT(*) as count FROM orderhistory")->fetch_assoc()['count'];
        $total_products = $conn->query("SELECT COUNT(*) as count FROM productlist")->fetch_assoc()['count'];
        $total_revenue = $conn->query("SELECT SUM(total_price) as revenue FROM orderhistory")->fetch_assoc()['revenue'];

        echo json_encode([
            'total_users' => $total_users,
            'total_orders' => $total_orders,
            'total_products' => $total_products,
            'total_revenue' => $total_revenue
        ]);
        break;

    case 'recent_logins':
        $recent_logins = $conn->query("SELECT * FROM login_logs ORDER BY login_time DESC LIMIT 5");
        $logins = [];
        while ($login = $recent_logins->fetch_assoc()) {
            $logins[] = $login;
        }
        echo json_encode($logins);
        break;

    case 'product_categories':
        $categories = $conn->query("SELECT p.nameType, COUNT(pl.id) as count 
            FROM product p 
            LEFT JOIN productlist pl ON p.id = pl.product_id 
            GROUP BY p.nameType");
        $category_data = [];
        while ($category = $categories->fetch_assoc()) {
            $category_data[] = $category;
        }
        echo json_encode($category_data);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
$conn->close();
?>