<?php
require_once('../Assets/src/backendreq.php');
if (!$conn) {
    die("Database connection failed");
}

$message = '';
$message_type = '';

if (isset($_GET['id'])) {
    $coupon_id = $_GET['id'];
    $delete_sql = "DELETE FROM coupons WHERE coupon_id = ?";
    $stmt = $conn->prepare($delete_sql);
    if ($stmt) {
        $stmt->bind_param("i", $coupon_id);
        if ($stmt->execute()) {
            $message = "ลบคูปองเรียบร้อยแล้ว";
            $message_type = "success";
        } else {
            $message = "เกิดข้อผิดพลาดในการลบ: " . $stmt->error;
            $message_type = "danger";
        }
        $stmt->close();
    } else {
        $message = "Prepare failed: " . $conn->error;
        $message_type = "danger";
    }
    header("Location: coupon_management.php?message=" . urlencode($message) . "&type=" . urlencode($message_type));
    exit();
}
?>