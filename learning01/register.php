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
        isset($_POST['password']) && isset($_POST['confirm_password']) && 
        isset($_POST['phone_number'])) {

        $user = trim($_POST['username']);
        $email = trim($_POST['email']);
        $phone_number = trim($_POST['phone_number']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // ตรวจสอบความถูกต้องของข้อมูล
        $errors = [];

        // ตรวจสอบชื่อผู้ใช้
        if (empty($user)) {
            $errors[] = 'กรุณากรอกชื่อผู้ใช้';
        } elseif (strlen($user) < 3) {
            $errors[] = 'ชื่อผู้ใช้ต้องมีความยาวอย่างน้อย 3 ตัวอักษร';
        }

        // ตรวจสอบอีเมล
        if (empty($email)) {
            $errors[] = 'กรุณากรอกอีเมล';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'รูปแบบอีเมลไม่ถูกต้อง';
        } elseif (preg_match('/[!#$%^&*(),?":{}|<>\/]/', $email)) {
            $errors[] = 'อีเมลห้ามมีอักษรพิเศษที่ไม่ถูกต้อง';
        }

        // ตรวจสอบรหัสผ่าน
        if (empty($password)) {
            $errors[] = 'กรุณากรอกรหัสผ่าน';
        } elseif (strlen($password) < 8) {
            $errors[] = 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร';
        } elseif ($password !== $confirm_password) {
            $errors[] = 'รหัสผ่านไม่ตรงกัน';
        }

        // ตรวจสอบเบอร์โทรศัพท์
        if (empty($phone_number)) {
            $errors[] = 'กรุณากรอกเบอร์โทรศัพท์';
        } elseif (!preg_match('/^[0-9]{10}$/', $phone_number)) {
            $errors[] = 'เบอร์โทรศัพท์ต้องเป็นตัวเลข 10 หลัก';
        }

        // ตรวจสอบว่ามีอีเมลหรือชื่อผู้ใช้ซ้ำหรือไม่
        $check_stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
        $check_stmt->bind_param("ss", $email, $user);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = 'อีเมลหรือชื่อผู้ใช้นี้มีอยู่แล้ว';
        }
        $check_stmt->close();

        // หากมี errors ให้แสดงข้อผิดพลาด
        if (!empty($errors)) {
            $error_message = implode('\n', $errors);
            die("<script>
                alert('$error_message');
                window.history.back();
            </script>");
        }

        // หากผ่านการตรวจสอบทั้งหมด
        $pass = password_hash($password, PASSWORD_DEFAULT);

        // เตรียมคำสั่ง SQL ด้วย prepared statement
        $stmt = $conn->prepare("INSERT INTO users (username, email, phone_number, password) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        // ผูกค่าตัวแปรกับ prepared statement
        $stmt->bind_param("ssss", $user, $email, $phone_number, $pass);

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
        echo "<script>
            alert('กรุณากรอกข้อมูลให้ครบทุกช่อง');
            window.history.back();
        </script>";
        exit();
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>