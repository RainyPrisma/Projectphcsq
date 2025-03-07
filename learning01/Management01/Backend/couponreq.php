<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ตั้งค่า log ให้ชัดเจน
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/log/php_errors.log');

// เชื่อมต่อฐานข้อมูล
require_once('../Assets/src/backendreq.php');
if (!$conn) {
    die("Database connection failed: " . $conn->connect_error);
}

// ตั้งค่า timezone
date_default_timezone_set('Asia/Bangkok');

$response = ['success' => false, 'message' => '', 'message_type' => 'danger'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // เพิ่มคูปอง
    if (isset($_POST['add_coupon'])) {
        $required_fields = ['coupon_code', 'discount_amount', 'discount_type', 'valid_from', 'valid_until', 'min_purchase', 'usage_limit'];
        $missing_fields = [];
        
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
                $missing_fields[] = $field;
            }
        }
        
        if (!empty($missing_fields)) {
            $response['message'] = "กรุณากรอกข้อมูลให้ครบถ้วน: " . implode(', ', $missing_fields);
            error_log("Missing fields: " . implode(', ', $missing_fields));
        } else {
            try {
                $coupon_code = trim($_POST['coupon_code']);
                $discount_amount = floatval($_POST['discount_amount']);
                $discount_type = $_POST['discount_type'];
                $valid_from_raw = $_POST['valid_from'];
                $valid_until_raw = $_POST['valid_until'];
                $valid_from = date('Y-m-d H:i:s', strtotime($valid_from_raw));
                $valid_until = date('Y-m-d H:i:s', strtotime($valid_until_raw));
                
                if ($valid_from === false || $valid_until === false) {
                    throw new Exception("รูปแบบวันที่ไม่ถูกต้อง");
                }
                
                $min_purchase = floatval($_POST['min_purchase']);
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                $usage_limit = intval($_POST['usage_limit']);
                
                $check_sql = "SELECT coupon_id FROM coupons WHERE coupon_code = ?";
                $check_stmt = $conn->prepare($check_sql);
                if (!$check_stmt) {
                    throw new Exception("Prepare check failed: " . $conn->error);
                }
                
                $check_stmt->bind_param("s", $coupon_code);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                
                if ($check_result->num_rows > 0) {
                    $response['message'] = "รหัสคูปองนี้มีอยู่แล้ว กรุณาใช้รหัสอื่น";
                    error_log("Coupon already exists: $coupon_code");
                } else {
                    $insert_sql = "INSERT INTO coupons (coupon_code, discount_amount, discount_type, valid_from, valid_until, min_purchase, is_active, usage_limit) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($insert_sql);
                    if (!$stmt) {
                        throw new Exception("Prepare insert failed: " . $conn->error);
                    }
                    
                    $stmt->bind_param("sdsssdii", $coupon_code, $discount_amount, $discount_type, $valid_from, $valid_until, $min_purchase, $is_active, $usage_limit);
                    if (!$stmt->execute()) {
                        throw new Exception("Execute failed: " . $stmt->error);
                    }
                    
                    $response['success'] = true;
                    $response['message'] = "เพิ่มคูปอง $coupon_code เรียบร้อยแล้ว";
                    $response['message_type'] = "success";
                    error_log("Insert successful for coupon_code: $coupon_code");
                }
            } catch (Exception $e) {
                $response['message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
                error_log("Error: " . $e->getMessage());
            }
        }
    }

    // อัปเดตคูปอง
    if (isset($_POST['update_coupon']) && isset($_POST['coupon_id'])) {
        try {
            $coupon_id = intval($_POST['coupon_id']);
            $coupon_code = trim($_POST['coupon_code']);
            $discount_amount = floatval($_POST['discount_amount']);
            $discount_type = $_POST['discount_type'];
            $valid_from = date('Y-m-d H:i:s', strtotime($_POST['valid_from']));
            $valid_until = date('Y-m-d H:i:s', strtotime($_POST['valid_until']));
            $min_purchase = floatval($_POST['min_purchase']);
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            $usage_limit = intval($_POST['usage_limit']);

            if ($valid_from === false || $valid_until === false) {
                throw new Exception("รูปแบบวันที่ไม่ถูกต้อง");
            }

            $update_sql = "UPDATE coupons SET coupon_code = ?, discount_amount = ?, discount_type = ?, valid_from = ?, valid_until = ?, min_purchase = ?, is_active = ?, usage_limit = ? WHERE coupon_id = ?";
            $stmt = $conn->prepare($update_sql);
            if (!$stmt) {
                throw new Exception("Prepare update failed: " . $conn->error);
            }

            $stmt->bind_param("sdsssdiii", $coupon_code, $discount_amount, $discount_type, $valid_from, $valid_until, $min_purchase, $is_active, $usage_limit, $coupon_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute update failed: " . $stmt->error);
            }

            $response['success'] = true;
            $response['message'] = "แก้ไขคูปอง $coupon_code เรียบร้อยแล้ว";
            $response['message_type'] = "success";
        } catch (Exception $e) {
            $response['message'] = "เกิดข้อผิดพลาดในการแก้ไข: " . $e->getMessage();
        }
    }

    // ลบคูปอง
    if (isset($_POST['delete_coupon']) && isset($_POST['coupon_id'])) {
        try {
            $coupon_id = intval($_POST['coupon_id']);
            $delete_sql = "DELETE FROM coupons WHERE coupon_id = ?";
            $stmt = $conn->prepare($delete_sql);
            if (!$stmt) {
                throw new Exception("Prepare delete failed: " . $conn->error);
            }

            $stmt->bind_param("i", $coupon_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute delete failed: " . $stmt->error);
            }

            $response['success'] = true;
            $response['message'] = "ลบคูปองเรียบร้อยแล้ว";
            $response['message_type'] = "success";
        } catch (Exception $e) {
            $response['message'] = "เกิดข้อผิดพลาดในการลบ: " . $e->getMessage();
        }
    }

    // ส่งการตอบกลับเป็น JSON เมื่อเรียกผ่าน AJAX
    if (isset($_POST['add_coupon']) || isset($_POST['update_coupon']) || isset($_POST['delete_coupon'])) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}

// ดึงข้อมูลคูปองทั้งหมด
$coupons = [];
$query = "SELECT * FROM coupons ORDER BY created_at DESC";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $coupons[] = $row;
    }
} else {
    error_log("Query failed: " . $conn->error);
}
?>