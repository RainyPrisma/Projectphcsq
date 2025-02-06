<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "1234", "management01");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check session
if (!isset($_SESSION['user_email'])) {
    header('Location: ../login.php');
    exit();
}

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $product_name = $_POST['product_name'];
    $detail = $_POST['detail'];
    $quantity = $_POST['quantity'];
    $price = $_POST['product_price'];  // แก้จาก price เป็น product_price ให้ตรงกับ form

    // Add to cart session with new fields
    $_SESSION['cart'][] = [
        'name' => $product_name,
        'detail' => $detail,
        'quantity' => $quantity,
        'price' => $price
    ];
}

// Fetch products with new fields
$sql = "SELECT * FROM productdetails where product_id = '4'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://i.pinimg.com/736x/0e/20/49/0e204916ebb9f86ee7f5cfc7433b91c0.jpg" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Gallery - Buying</title>
    <link rel="stylesheet" href="../View/Gallery.css">
</head>
<body>
<header class="gallery-header">
    <!-- Header content remains the same -->
    <div class="dropdown">
        <button class="dropdown-btn">
            <h1>Product Gallery <span class="dropdown-arrow">▾</span></h1>
        </button>
        <div class="dropdown-content">
            <div class="dropdown-category">
                <h3>Categories</h3>
                <a href="Fish.php" class="dropdown-item"><span class="icon">🐠</span>Any of Fish</a>
                <a href="Occt.php" class="dropdown-item"><span class="icon">🐙</span>Any of Squid</a>
                <a href="Shrimp.php" class="dropdown-item"><span class="icon">🐚</span>Any of Shrimp</a>
                <a href="Shell.php" class="dropdown-item"><span class="icon">🐚</span>Any of Shell</a>
            </div>
            <div class="dropdown-category">
                <h3>Special</h3>
                <a href="#" class="dropdown-item"><span class="icon">🔥</span>Hot Deals</a>
                <a href="#" class="dropdown-item"><span class="icon">⭐</span>New Arrivals</a>
            </div>
        </div>
    </div>
    <div class="header-buttons">
        <a href="../index.php" class="btn">Home</a>
        <a href="../Cart/cart.php" class="btn">Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
    </div>
</header>

<main class="gallery-container">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="gallery-item">';
            // ส่วนแสดงข้อมูล
            echo '<img src="' . $row["image_url"] . '" alt="Product Image">';
            echo '<h2>' . $row["product_name"] . '</h2>';
            echo '<p class="detail">' . $row["detail"] . '</p>';
            echo '<p>Quantity: ' . $row["quantity"] . '</p>';
            echo '<p class="price">$' . $row["price"] . '</p>';
            
            // ส่วน form สำหรับเพิ่มลงตะกร้า
            echo '<form method="POST" action="">';
            echo '<input type="hidden" name="product_name" value="' . $row["product_name"] . '">';
            echo '<input type="hidden" name="detail" value="' . $row["detail"] . '">';
            echo '<input type="hidden" name="quantity" value="' . $row["quantity"] . '">';
            echo '<input type="hidden" name="product_price" value="' . $row["price"] . '">';
            echo '<button type="submit" name="add_to_cart">Add to Cart</button>';
            echo '</form>';
            echo '</div>';
        }
    } else {
        echo '<p>No products found.</p>';
    }
    ?>
</main>

<script src="../Controller/script.js"></script>
</body>
</html>