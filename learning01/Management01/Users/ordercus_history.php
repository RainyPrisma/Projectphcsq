<?php
session_start();
require_once "../Backend/productreq.php";
include '../Backend/ordercustomer_req.php'
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History | Custom Seafoods</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Assets/CSS/account.css">
    <link rel="stylesheet" href="../Assets/CSS/cushistory.css">
    <link rel="icon" href="https://customseafoods.com/cdn/shop/files/CS_Logo_2_1000.webp?v=1683664967" type="image/png">
</head>
<body>
    <header class="page-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Order History</h1>
                    <p class="mb-0 text-light">View and manage your previous orders</p>
                </div>
                <div>
                    <a href="account.php" class="btn btn-light me-2">
                        <i class="fas fa-user me-1"></i> Account
                    </a>
                    <a href="../Frontend/logout.php" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card sort-box">
                    <div class="card-body d-md-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center mb-3 mb-md-0">
                            <i class="fas fa-history me-2 text-primary"></i>
                            <h5 class="mb-0">You have <?php echo $order_count; ?> order<?php echo $order_count != 1 ? 's' : ''; ?></h5>
                        </div>
                        <form method="get" class="d-flex align-items-center">
                            <label for="sort" class="me-2 text-nowrap">
                                <i class="fas fa-sort me-1 text-primary"></i> Sort by:
                            </label>
                            <select name="sort" id="sort" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                                <option value="date_desc" <?php echo $sort_by == 'date_desc' ? 'selected' : ''; ?>>Newest Date</option>
                                <option value="date_asc" <?php echo $sort_by == 'date_asc' ? 'selected' : ''; ?>>Oldest Date</option>
                                <option value="price_desc" <?php echo $sort_by == 'price_desc' ? 'selected' : ''; ?>>Highest Price</option>
                                <option value="price_asc" <?php echo $sort_by == 'price_asc' ? 'selected' : ''; ?>>Lowest Price</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Your Orders</h5>
                        <span class="order-badge">
                            <i class="fas fa-shopping-bag me-1"></i> <?php echo $order_count; ?> Order<?php echo $order_count != 1 ? 's' : ''; ?>
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($order_count > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-hashtag me-1"></i> Order ID</th>
                                        <th><i class="far fa-calendar-alt me-1"></i> Order Date</th>
                                        <th><i class="fas fa-money-bill-wave me-1"></i> Total</th>
                                        <th><i class="fas fa-fish me-1"></i> Products</th>
                                        <th><i class="fas fa-file-invoice me-1"></i> Receipt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <strong>#<?php echo htmlspecialchars($row['order_reference']); ?></strong>
                                        </td>
                                        <td class="order-date">
                                            <?php 
                                                $date = new DateTime(htmlspecialchars($row['created_at']));
                                                echo $date->format('d M Y, h:i A'); 
                                            ?>
                                        </td>
                                        <td class="price-column">
                                            <?php echo number_format(htmlspecialchars($row['total_price']), 2); ?>฿
                                        </td>
                                        <td>
                                            <?php 
                                                $items = htmlspecialchars($row['item']);
                                                // Limit to 40 characters with ellipsis
                                                echo strlen($items) > 40 ? substr($items, 0, 40) . '...' : $items;
                                            ?>
                                        </td>
                                        <td>
                                            <a href="generate_receipt.php?id=<?php echo $row['order_reference']; ?>&mode=download" class="btn btn-sm btn-ocean">
                                                <i class="fas fa-download me-1"></i> Download
                                            </a>
                                            <a href="generate_receipt.php?id=<?php echo $row['order_reference']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-shopping-cart"></i>
                            <h4>No orders yet</h4>
                            <p class="text-muted">You haven't placed any orders with us yet.</p>
                            <a href="../Frontend/products.php" class="btn btn-ocean mt-2">
                                <i class="fas fa-fish me-1"></i> Browse Products
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wave Decoration -->
    <div class="wave-decoration"></div>
    <script src="../Assets/JS/paginationControl.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>