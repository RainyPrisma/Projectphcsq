<?php
session_start();
require_once '../Backend/productreq.php';
// Fetch products with new fields
$sql = "SELECT * FROM productdetails where product_id = '2'";
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
    <link rel="stylesheet" href="/learning01/Management01/Assets/CSS/gallery.css">
    <link rel="icon" href="https://customseafoods.com/cdn/shop/files/CS_Logo_2_1000.webp?v=1683664967" type="image/png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../Assets/JS/search.js"></script>
    <script src="../Assets/JS/script.js"></script>
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
                <a href="gallery.php" class="dropdown-item"><span class="icon">🏠</span>Main Page</a>
                <a href="Fish.php" class="dropdown-item"><span class="icon">🐠</span>Any of Fish</a>
                <a href="Occt.php" class="dropdown-item"><span class="icon">🐙</span>Any of Squid</a>
                <a href="Shrimp.php" class="dropdown-item"><span class="icon">🦐</span>Any of Shrimp</a>
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
        <input type="text" id="searchInput" placeholder="Search products...">
        <a href="../Frontend/index.php" class="btn">Home</a>
        <a href="cart.php" class="btn">Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
    </div>
</header>

<main class="gallery-container">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            ?>
            <div class="gallery-item">
                <a href="product_details.php?id=<?php echo $row['name']; ?>" class="product-link">
                    <img src="<?php echo $row['image_url']; ?>" alt="<?php echo $row['name']; ?>">
                    <div class="product-info">
                        <h2><?php echo $row['name']; ?></h2>
                        <p class="detail"><?php echo $row['detail']; ?></p>
                        <p class="quantity">Stock: <?php echo $row['quantity']; ?></p>
                        <p class="price">฿<?php echo number_format($row['price'], 2); ?></p>
                    </div>
                </a>
                
                <form method="POST">
                    <input type="hidden" name="name" value="<?php echo $row['name']; ?>">
                    <input type="hidden" name="detail" value="<?php echo $row['detail']; ?>">
                    <input type="number" name="quantity" min="1" max="<?php echo $row['quantity']; ?>" value="1" class="quantity-input">
                    <input type="hidden" name="product_price" value="<?php echo $row['price']; ?>">
                    <button type="submit" name="add_to_cart" class="add-to-cart-btn">Add to Cart</button>
                </form>
            </div>
            <?php
        }
    } else {
        echo '<div class="no-products">No products found.</div>';
    }
    ?>
</main>

<footer class="gallery-footer">
    <p>&copy; <?php echo date('Y'); ?> Custom Seafoods. All rights reserved.</p>
    <p>ติดต่อ: info@customseafoods.com | โทร: 02-123-4567</p>
</footer>

</body>
</html>