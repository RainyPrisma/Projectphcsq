<?php
session_start();
require_once 'productreq.php';

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_email'])) {
    header("Location: ../Frontend/login.php");
    exit();
}

// ดึงอีเมลจาก session
$userEmail = $_SESSION['user_email'];

// กำหนดจำนวนการส่งสูงสุดต่อวัน
$max_submissions_per_day = 3;

// ตรวจสอบว่าวันนี้มีการบันทึกการส่งแล้วหรือไม่
$today = date('Y-m-d');
if (!isset($_SESSION['contact_submissions']) || $_SESSION['contact_submissions_date'] != $today) {
    // ถ้ายังไม่มีหรือเป็นวันใหม่ ให้เริ่มนับใหม่
    $_SESSION['contact_submissions'] = 0;
    $_SESSION['contact_submissions_date'] = $today;
}

// ตรวจสอบว่าเกินจำนวนที่กำหนดหรือไม่
if ($_SESSION['contact_submissions'] >= $max_submissions_per_day) {
    $_SESSION['contact_error'] = "คุณได้ส่งข้อความครบตามจำนวนที่กำหนดแล้ว ($max_submissions_per_day ข้อความต่อวัน)";
    header("Location: ../Frontend/contactus.php");
    exit();
}

// ประมวลผลการส่งแบบฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์ม
    $name = $_POST['name'];
    $email = $userEmail; // ใช้อีเมลจาก session เสมอ
    $message = $_POST['message'];
    
    // ตรวจสอบข้อมูล
    if (empty($name) || empty($message)) {
        $_SESSION['contact_error'] = "กรุณากรอกข้อมูลให้ครบถ้วน";
        header("Location: ../Frontend/contactus.php");
        exit();
    }
    
    // เพิ่มข้อมูลลงในฐานข้อมูล
    $insert_query = "INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("sss", $name, $email, $message);
    
    if ($insert_stmt->execute()) {
        // เพิ่มจำนวนการส่งในวันนี้
        $_SESSION['contact_submissions']++;
        $_SESSION['contact_success'] = "ส่งข้อความเรียบร้อยแล้ว! คุณสามารถส่งได้อีก " . ($max_submissions_per_day - $_SESSION['contact_submissions']) . " ครั้งในวันนี้";
    } else {
        // ข้อผิดพลาดจากฐานข้อมูล
        $_SESSION['contact_error'] = "เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง";
    }
    
    // ย้อนกลับไปที่หน้าติดต่อ
    header("Location: ../Frontend/contactus.php");
    exit();
}
?>