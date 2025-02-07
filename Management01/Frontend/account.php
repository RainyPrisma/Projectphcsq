<?php
require '../Database/config.php';
require_once '../Database/account_handler.php'; 

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

// ดึงข้อมูลผู้ใช้
$handler = new AccountHandler($conn);
$userData = $handler->getUserData($_SESSION['user_email']);

if (!$userData) {
    echo "<script>alert('ไม่พบข้อมูลผู้ใช้');</script>";
    exit;
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
    <link rel="stylesheet" href="../Assets/CSS/account.css">
    <script src="../Assets/JS/account.js"></script>
</head>
<body>
    <div class="account-container">
        <a href="../Frontend/index.php" class="back-button">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Home
        </a>

        <div class="account-header">
            <h1>Welcome, <?php echo htmlspecialchars($userData['username']); ?>!</h1>
            <p class="text-muted">Manage your account details below</p>
        </div>

        <form id="accountForm">
            <div class="form-section">
                <h4>Personal Information</h4>
                
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($userData['username']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($userData['full_name']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($userData['phone_number']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-control" required>
                        <option value="Male" <?php if ($userData['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if ($userData['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                        <option value="Other" <?php if ($userData['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                    </select>
                </div>
            </div>

            <div class="form-section">
                <h4>Contact Information</h4>
                
                <div class="mb-3 full-width">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($userData['address']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($userData['city']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control" value="<?php echo htmlspecialchars($userData['state']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">ZIP Code</label>
                    <input type="text" name="zip_code" class="form-control" value="<?php echo htmlspecialchars($userData['zip_code']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" class="form-control" value="<?php echo htmlspecialchars($userData['country']); ?>" required>
                </div>

                <button type="submit" class="btn btn-success">Save Changes</button>
            </div>
        </form>
    </div>
</body>
</html>