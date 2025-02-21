<?php 
session_start();
require '../Database/config.php';
include '../Frontend/modal.php';

// ถ้ามีการล็อกอินแล้ว ให้ redirect ไปหน้า index.php
if(isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_or_phone = $_POST['email_or_phone'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ? OR phone_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email_or_phone, $email_or_phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['phone_number'] = $user['phone_number'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();
            
            // Get client IP address
            function getClientIP() {
                // สำหรับ local development
                if (isset($_SERVER['REMOTE_ADDR'])) {
                    $ip = $_SERVER['REMOTE_ADDR'];
                    
                    // ถ้าเป็น localhost IP ให้เก็บเป็น 'localhost' แทน
                    if ($ip == '::1' || $ip == '127.0.0.1') {
                        return 'localhost';
                    }
                    
                    // ตรวจสอบว่าเป็น IP ที่ถูกต้องหรือไม่
                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        return $ip;
                    }
                }
                
                return 'localhost';
            }

            try {
                $ip_address = getClientIP();
                
                $login_sql = "INSERT INTO login_logs (user_id, username, email, login_time, ip_address) VALUES (?, ?, ?, NOW(), ?)";
                $login_stmt = $conn->prepare($login_sql);
                $login_stmt->bind_param("isss", 
                    $user['id'], 
                    $user['username'], 
                    $user['email'], 
                    $ip_address
                );
                $login_stmt->execute();
                
            } catch (Exception $e) {
                // Log error แต่ยังให้ login ผ่านได้
                error_log("Login logging failed: " . $e->getMessage());
            }
                                    
            // ทุก role ไปที่หน้า index.php
            header("Location: index.php");
            exit();
        } else {
            echo "<script>showModal('แจ้งเตือน', 'รหัสผ่านไม่ถูกต้อง');</script>";
        }
    } else {
        echo "<script>showModal('แจ้งเตือน', 'ไม่พบผู้ใช้งาน');</script>";
    }
}
?>