<?php
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
if (!isset($_SESSION['user_id'])) {
    // หากยังไม่ได้ล็อกอิน เปลี่ยนเส้นทางไปยังหน้า login.php
    header("Location: login.php");
    exit();
}
?>
