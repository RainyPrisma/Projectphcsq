<?php
session_start();
require 'config.php';

// เปลี่ยนการตรวจสอบ session จาก email เป็น user_email
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

// ถ้าผู้ใช้ส่งข้อมูลแก้ไขมา
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $phone_number = $_POST['phone_number'];

    // คำสั่ง SQL สำหรับอัปเดตข้อมูล
    $sql = "UPDATE users SET email = ?, username = ?, phone_number = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    // เปลี่ยนจาก $_SESSION['email'] เป็น $_SESSION['user_email']
    $stmt->bind_param("ssss", $email, $username, $phone_number, $_SESSION['user_email']);
    
    if ($stmt->execute()) {
        // อัปเดตข้อมูลใน Session ให้ใช้ชื่อ key ใหม่
        $_SESSION['user_email'] = $email;
        $_SESSION['username'] = $username;
        $_SESSION['phone_number'] = $phone_number;
        echo "<script>alert('ข้อมูลถูกอัปเดตเรียบร้อย');</script>";
    } else {
        echo "<script>alert('ไม่สามารถอัปเดตข้อมูลได้');</script>";
    }
}

// ดึงข้อมูลผู้ใช้จาก Session ให้ใช้ชื่อ key ใหม่
$email = $_SESSION['user_email'];
$username = $_SESSION['username'];
$phone_number = $_SESSION['phone_number'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Page</title>
    <link rel="icon" href="https://i.pinimg.com/736x/0e/20/49/0e204916ebb9f86ee7f5cfc7433b91c0.jpg" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="View/account.css">
</head>
<body>
    <div class="account-container">
        <div class="account-header">
            <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
            <p class="text-muted">Manage your account details below.</p>
        </div>

        <!-- ส่วนแสดงข้อมูลผู้ใช้ -->
        <div class="mb-4">
            <h4>Account Information</h4>
            <ul class="list-group">
                <li class="list-group-item">
                    <strong>Email:</strong> <?php echo htmlspecialchars($email); ?>
                </li>
                <li class="list-group-item">
                    <strong>Phone Number:</strong> <?php echo htmlspecialchars($phone_number); ?>
                </li>
                <li class="list-group-item">
                    <strong>Username:</strong> <?php echo htmlspecialchars($username); ?>
                </li>
            </ul>
        </div>

        <!-- ฟอร์มแก้ไขข้อมูล -->
        <div class="mb-4">
            <h4>Edit Your Information</h4>
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="phone_number" class="form-label">Phone Number</label>
                    <input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($phone_number); ?>" required>
                </div>
                <button type="submit" class="btn btn-success">Save Changes</button>
            </form>
        </div>

        <!-- ปุ่มกลับไปหน้า Home และ Logout -->
        <div class="d-flex justify-content-between">
            <a href="index.php" class="btn btn-custom">Back to Home</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
