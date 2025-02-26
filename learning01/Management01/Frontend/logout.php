<?php
// ไฟล์ logout.php
session_start();

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "management01";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// อัพเดทเวลา logout ในฐานข้อมูล
if (isset($_SESSION['user_email'])) {
    $sql = "UPDATE login_logs 
            SET logout_time = NOW(), 
                is_active = 0 
            WHERE email = ? 
            AND is_active = 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['user_email']);
    $stmt->execute();
    $stmt->close();
}

// ทำลาย session
session_unset();
session_destroy();

$conn->close();

// ส่งกลับไปหน้า login
header("Location: login.php");
exit;

// ไฟล์ login.php (เพิ่มส่วนนี้ในส่วนที่ login สำเร็จ)
if ($login_successful) {
    $sql = "INSERT INTO login_logs (username, email, login_time, ip_address, is_active) 
            VALUES (?, ?, NOW(), ?, 1)";
    
    $stmt = $conn->prepare($sql);
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $stmt->bind_param("sss", $username, $email, $ip_address);
    $stmt->execute();
    $stmt->close();
}