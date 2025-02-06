<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

// ดึงข้อมูลจาก view_account
$sql = "SELECT * FROM view_account WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['user_email']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $email = $row['email'];
    $username = $row['username'];
    $full_name = $row['full_name'];
    $phone_number = $row['phone_number'];
    $address = $row['address'];
    $city = $row['city'];
    $state = $row['state'];
    $zip_code = $row['zip_code'];
    $country = $row['country'];
    $gender = $row['gender'];
} else {
    echo "<script>alert('ไม่พบข้อมูลผู้ใช้');</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip_code = $_POST['zip_code'];
    $country = $_POST['country'];
    $gender = $_POST['gender'];

    // ตรวจสอบ username
    $check_user_sql = "SELECT * FROM users WHERE username = ? AND email != ?";
    $check_user_stmt = $conn->prepare($check_user_sql);
    $check_user_stmt->bind_param("ss", $username, $_SESSION['user_email']);
    $check_user_stmt->execute();
    $user_result = $check_user_stmt->get_result();

    if ($user_result->num_rows > 0) {
        echo "<script>alert('Username นี้ถูกใช้งานแล้ว กรุณาเลือก username อื่น');</script>";
        exit;
    }

    // เริ่ม transaction
    $conn->begin_transaction();

    try {
        // 1. อัพเดต username ชั่วคราวในตาราง users
        $temp_username = $username . '_temp';
        $update_user_sql = "UPDATE users SET username = ? WHERE email = ?";
        $update_user_stmt = $conn->prepare($update_user_sql);
        $update_user_stmt->bind_param("ss", $temp_username, $_SESSION['user_email']);
        if (!$update_user_stmt->execute()) {
            throw new Exception("ไม่สามารถอัพเดต username ชั่วคราวในตาราง users ได้");
        }

        // 2. อัพเดต username ชั่วคราวในตาราง user_details
        $update_details_temp_sql = "UPDATE user_details SET username = ? WHERE email = ?";
        $update_details_temp_stmt = $conn->prepare($update_details_temp_sql);
        $update_details_temp_stmt->bind_param("ss", $temp_username, $_SESSION['user_email']);
        if (!$update_details_temp_stmt->execute()) {
            throw new Exception("ไม่สามารถอัพเดต username ชั่วคราวในตาราง user_details ได้");
        }

        // 3. อัพเดต username จริงในตาราง users
        $update_user_final_sql = "UPDATE users SET username = ? WHERE email = ?";
        $update_user_final_stmt = $conn->prepare($update_user_final_sql);
        $update_user_final_stmt->bind_param("ss", $username, $_SESSION['user_email']);
        if (!$update_user_final_stmt->execute()) {
            throw new Exception("ไม่สามารถอัพเดต username ในตาราง users ได้");
        }

        // 4. ถ้ามีการเปลี่ยน email ให้อัพเดต email ก่อน
        if ($email !== $_SESSION['user_email']) {
            $update_email_sql = "UPDATE user_details SET email = ? WHERE email = ?";
            $update_email_stmt = $conn->prepare($update_email_sql);
            $update_email_stmt->bind_param("ss", $email, $_SESSION['user_email']);
            if (!$update_email_stmt->execute() || $update_email_stmt->affected_rows === 0) {
                throw new Exception("ไม่สามารถอัพเดต email ได้");
            }
        }

        // 5. อัพเดตข้อมูลอื่นๆ ใน user_details
        $update_details_sql = "UPDATE user_details SET 
            username = ?,
            full_name = ?, 
            phone_number = ?, 
            address = ?, 
            city = ?, 
            state = ?, 
            zip_code = ?, 
            country = ?, 
            gender = ? 
            WHERE email = ?";
        $update_details_stmt = $conn->prepare($update_details_sql);
        $update_details_stmt->bind_param("ssssssssss", 
        $username, $full_name, $phone_number, 
        $address, $city, $state, $zip_code, 
        $country, $gender, $_SESSION['user_email']
        );
        if (!$update_details_stmt->execute() || $update_details_stmt->affected_rows === 0) {
            throw new Exception("ไม่สามารถอัพเดตข้อมูลใน user_details ได้");
        }

        // ถ้าทุกอย่างผ่าน ให้ commit การเปลี่ยนแปลง
        $conn->commit();

        // อัพเดต session
        $_SESSION['user_email'] = $email;
        $_SESSION['username'] = $username;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['phone_number'] = $phone_number;
        $_SESSION['address'] = $address;
        $_SESSION['city'] = $city;
        $_SESSION['state'] = $state;
        $_SESSION['zip_code'] = $zip_code;
        $_SESSION['country'] = $country;
        $_SESSION['gender'] = $gender;

        echo "<script>alert('ข้อมูลถูกบันทึกเรียบร้อย');</script>";
    } catch (Exception $e) {
        // ถ้าเกิดข้อผิดพลาด ให้ rollback การเปลี่ยนแปลงทั้งหมด
        $conn->rollback();
        echo "<script>alert('ไม่สามารถบันทึกข้อมูลได้: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="View/account.css">
</head>
<body>
    <div class="account-container">
        <a href="index.php" class="back-button">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Home
        </a>

        <div class="account-header">
            <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
            <p class="text-muted">Manage your account details below</p>
        </div>

        <form action="" method="POST">
            <div class="form-section">
                <h4>Personal Information</h4>
                
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($full_name); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($phone_number); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-control" required>
                        <option value="Male" <?php if ($gender == 'Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if ($gender == 'Female') echo 'selected'; ?>>Female</option>
                        <option value="Other" <?php if ($gender == 'Other') echo 'selected'; ?>>Other</option>
                    </select>
                </div>
            </div>

            <div class="form-section">
                <h4>Contact Information</h4>
                
                <div class="mb-3 full-width">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($address); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($city); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control" value="<?php echo htmlspecialchars($state); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">ZIP Code</label>
                    <input type="text" name="zip_code" class="form-control" value="<?php echo htmlspecialchars($zip_code); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" class="form-control" value="<?php echo htmlspecialchars($country); ?>" required>
                </div>

                <button type="submit" class="btn btn-success">Save Changes</button>
            </div>
        </form>
    </div>
</body>
</html>