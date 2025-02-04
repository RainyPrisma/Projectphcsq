<?php
// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$host = "localhost";
$username = "root";
$password = "1234";
$dbname = "management01";

// สร้างการเชื่อมต่อ
$conn = new mysqli($host, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับค่าจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบว่ามีค่าที่ส่งมาครบหรือไม่
    if (isset($_POST['username']) && isset($_POST['email']) && 
        isset($_POST['password']) && isset($_POST['phone_number'])) {

        $user = $_POST['username'];
        $email = $_POST['email'];
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $phone_number = $_POST['phone_number'];

        // เตรียมคำสั่ง SQL ด้วย prepared statement
        $stmt = $conn->prepare("INSERT INTO users (username, email, phone_number, password) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        // ผูกค่าตัวแปรกับ prepared statement
        // "ssss" หมายถึงสี่ตัวแปรชนิด string
        $stmt->bind_param("ssss", $user, $email, $phone_number, $pass);
        // ตรวจสอบว่าอีเมลไม่มีอักษรพิเศษที่ห้ามใช้
        if (preg_match('/[!#$%^&*(),?":{}|<>\/]/', $email)) {
        die("<script>alert('อีเมลห้ามมีอักษรพิเศษที่ไม่ถูกต้อง'); window.history.back();</script>");
        }

        // รันคำสั่ง SQL
        if ($stmt->execute()) {
            echo "<script>
                   alert('ลงทะเบียนสำเร็จ! กรุณาเข้าสู่ระบบ');
                   window.location.href = 'login.php';
                 </script>";
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        // ปิด statement
        $stmt->close();
    } else {
        echo "กรุณากรอกข้อมูลให้ครบทุกช่อง";
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
