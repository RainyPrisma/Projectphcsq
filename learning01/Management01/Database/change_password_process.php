<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        echo json_encode(['status' => 'error', 'message' => 'รหัสผ่านใหม่ไม่ตรงกัน!']);
        exit();
    }

    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedOldPassword = $row['password'];

        if (!password_verify($oldPassword, $hashedOldPassword)) {
            echo json_encode(['status' => 'error', 'message' => 'รหัสผ่านเก่าผิด!']);
            exit();
        }

        $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashedNewPassword, $email);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'อัปเดตรหัสผ่านสำเร็จ!']);
            exit();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด! กรุณาลองใหม่']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ไม่พบอีเมลนี้ในระบบ!']);
        exit();
    }
}
?>