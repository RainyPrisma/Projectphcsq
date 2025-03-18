<?php
session_start();
include '../Database/config.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// ตรวจสอบ action ที่ส่งมา (clear_all หรือ mark as read สำหรับรายการเดียว)
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'clear_all') {
    // ลบการแจ้งเตือนทั้งหมดของผู้ใช้
    $query = "DELETE FROM notifications WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $success = $stmt->execute();

    if ($success) {
        $affected_rows = $stmt->affected_rows;
        if ($affected_rows > 0) {
            // ลบสำเร็จ
            header("Location: ../Frontend/dashboard.php?status=cleared");
        } else {
            // ไม่มีแจ้งเตือนให้ลบ
            header("Location: ../Frontend/dashboard.php?status=no_notifications");
        }
    } else {
        header("Location: ../Frontend/dashboard.php?status=error");
    }
    $stmt->close();
} else {
    // ทำเครื่องหมายว่าอ่านแล้วสำหรับการแจ้งเตือนเดียว
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $notification_id = (int)$_GET['id']; // แปลงเป็น integer เพื่อความปลอดภัย
        $query = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ? AND is_read = 0";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ii", $notification_id, $user_id);
        $success = $stmt->execute();

        if ($success) {
            $affected_rows = $stmt->affected_rows;
            if ($affected_rows > 0) {
                // อัพเดทสำเร็จ
                header("Location: ../Frontend/dashboard.php?status=marked");
            } else {
                // ไม่มีแถวที่อัพเดท (อาจอ่านแล้ว หรือ id ไม่ถูกต้อง)
                header("Location: ../Frontend/dashboard.php?status=no_change");
            }
        } else {
            header("Location: ../Frontend/dashboard.php?status=error");
        }
        $stmt->close();
    } else {
        header("Location: ../Frontend/dashboard.php?status=missing_id");
    }
}

$conn->close();
exit();
?>