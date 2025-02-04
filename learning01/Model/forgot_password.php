<?php
session_start();
include '../config.php';

// ตรวจสอบว่าเป็น AJAX request หรือไม่
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    // สำหรับ AJAX requests
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $response = array();
        
        try {
            $email = $_POST['email'];
            $oldPassword = $_POST['old_password'];
            $newPassword = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            if ($newPassword !== $confirmPassword) {
                throw new Exception('รหัสผ่านใหม่ไม่ตรงกัน!');
            }

            $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                throw new Exception('ไม่พบอีเมลนี้ในระบบ!');
            }

            $row = $result->fetch_assoc();
            $hashedOldPassword = $row['password'];

            if (!password_verify($oldPassword, $hashedOldPassword)) {
                throw new Exception('รหัสผ่านเก่าไม่ถูกต้อง!');
            }

            $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashedNewPassword, $email);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $response = array('status' => 'success', 'message' => 'อัปเดตรหัสผ่านสำเร็จ!');
            } else {
                throw new Exception('เกิดข้อผิดพลาดในการอัปเดตรหัสผ่าน!');
            }

        } catch (Exception $e) {
            $response = array('status' => 'error', 'message' => $e->getMessage());
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    exit();
}

// ถ้าไม่ใช่ AJAX request ให้แสดงหน้า HTML
include '../View/forgot_password_view.php';
?>