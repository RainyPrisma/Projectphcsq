<?php
include '../Backend/dashboardreq.php';

header('Content-Type: application/json');

// สร้าง array ข้อมูลที่จะส่งกลับ
$response = [
    'username' => htmlspecialchars($user_data['username']),
    'last_login' => date('d ม.ค. Y H:i น.', strtotime($cookieData['last_login'])),
    'total_orders' => $total_orders,
    'total_spending' => number_format($total_spending, 0),
    'most_purchased_item' => htmlspecialchars($most_purchased_item),
    'latest_order' => $latest_order ? [
        'order_reference' => htmlspecialchars($latest_order['order_reference']),
        'total_price' => number_format($latest_order['total_price'], 0),
        'created_at' => date('d ม.ค. Y', strtotime($latest_order['created_at']))
    ] : null,
    'notifications' => $notifications,
    'unread_count' => $unread_count
];

echo json_encode($response);
?>