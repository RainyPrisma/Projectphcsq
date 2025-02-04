<?php 
session_start(); // เริ่มต้น session
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_or_phone = $_POST['email_or_phone']; // รับค่า email หรือ phone
    $password = $_POST['password'];

    // ตรวจสอบผู้ใช้ในฐานข้อมูล
    $sql = "SELECT * FROM users WHERE email = ? OR phone_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email_or_phone, $email_or_phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // ตรวจสอบรหัสผ่าน
        if (password_verify($password, $user['password'])) {
            // เก็บข้อมูลใน Session
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['phone_number'] = $user['phone_number'];
            $_SESSION['role'] = $user['role']; // เก็บบทบาทผู้ใช้
                header("Location: Account.php"); // ถ้าไม่ใช่แอดมินให้ไปหน้า Account
                exit;
            }
        } else {
            echo "<script>alert('รหัสผ่านไม่ถูกต้อง');</script>";
        }
    } else {
        echo "<script>alert('ไม่พบผู้ใช้งาน');</script>";
    }
?>
