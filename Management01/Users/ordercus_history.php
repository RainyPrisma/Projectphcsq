<?php
session_start();
require_once "../Backend/productreq.php";
// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header('Location: ../Frontend/login.php');
    exit();
}

// Get user's email from session
$user_email = $_SESSION['user_email'];

// Query to get only the current user's orders
$sql = "SELECT * FROM orderhistory WHERE email = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/CSS/account.css">
    <link rel="stylesheet" href="../Assets/CSS/cushistory.css">
    <link rel="icon" href="https://customseafoods.com/cdn/shop/files/CS_Logo_2_1000.webp?v=1683664967" type="image/png">
</head>
<body>
    <header class="page-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Order History</h1>
                <div>
                    <a href="account.php" class="btn btn-ocean me-2">Back to Account</a>
                    <a href="../Frontend/logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container mt-4">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Total Amount</th>
                        <th>Product List</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['order_reference']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_price']); ?>฿</td>
                        <td><?php echo htmlspecialchars($row['item']); ?></td>
                        <td>
                            <!--ในอนาคต จะเปลี่ยนเป็นลิงค์ไปยังหน้าดาวน์โหลดใบเสร็จ-->
                            <a href="generate_receipt.php?id=<?php echo $row['order_reference']; ?>"  
                                class="btn btn-sm btn-ocean">Download</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Wave Decoration -->
    <div class="wave-decoration"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>