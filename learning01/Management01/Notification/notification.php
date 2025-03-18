<?php
session_start();
include '../Database/config.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// ดึง user_id จาก session
$user_id = $_SESSION['user_id'];

// ดึงการแจ้งเตือนทั้งหมด
$query = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

$stmt->close();
$conn->close();

// ตรวจสอบสถานะจากการดำเนินการ
$status_message = '';
$status_type = '';
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'cleared':
            $status_message = "เคลียร์การแจ้งเตือนทั้งหมดสำเร็จ";
            $status_type = "success";
            break;
        case 'no_notifications':
            $status_message = "ไม่มีแจ้งเตือนให้เคลียร์";
            $status_type = "info";
            break;
        case 'error':
            $status_message = "เกิดข้อผิดพลาด กรุณาลองใหม่";
            $status_type = "danger";
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การแจ้งเตือนทั้งหมด - Marine Seafood Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Prompt', sans-serif;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="fw-bold mb-4">การแจ้งเตือนทั้งหมด</h2>

        <!-- แสดงข้อความแจ้งเตือน -->
        <?php if (!empty($status_message)): ?>
            <div class="alert alert-<?php echo $status_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $status_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- ปุ่มสำหรับจัดการการแจ้งเตือน -->
        <div class="action-buttons">
            <a href="mark_as_read.php?action=clear_all" class="btn btn-outline-danger" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการเคลียร์การแจ้งเตือนทั้งหมด? การดำเนินการนี้ไม่สามารถย้อนกลับได้')">
                <i class="bi bi-trash me-1"></i> เคลียร์การแจ้งเตือนทั้งหมด
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if (!empty($notifications)): ?>
                    <ul class="list-group list-group-flush" id="notification_list">
                        <?php foreach ($notifications as $notification): ?>
                            <li class="list-group-item d-flex align-items-center <?php echo $notification['is_read'] ? 'text-muted' : ''; ?>">
                                <i class="bi <?php
                                    if ($notification['type'] == 'order') echo 'bi-box-seam text-primary';
                                    elseif ($notification['type'] == 'promotion') echo 'bi-percent text-success';
                                    elseif ($notification['type'] == 'product') echo 'bi-tag-fill text-warning';
                                    else echo 'bi-info-circle text-info';
                                ?> fs-4 me-3"></i>
                                <div>
                                    <h5 class="mb-1"><?php echo htmlspecialchars($notification['title']); ?></h5>
                                    <p class="mb-0"><?php echo htmlspecialchars($notification['message']); ?></p>
                                    <small class="text-muted"><?php echo date('d ม.ค. Y H:i น.', strtotime($notification['created_at'])); ?></small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-center text-muted">ไม่มีแจ้งเตือน</p>
                <?php endif; ?>
            </div>
        </div>
        <a href="../Frontend/dashboard.php" class="btn btn-outline-primary mt-3">
            <i class="bi bi-arrow-left me-1"></i> กลับไปหน้า Dashboard
        </a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>