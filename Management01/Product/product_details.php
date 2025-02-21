<?php
session_start();
require_once '../Backend/productreq.php';
// อัพเดท timestamp ของกิจกรรมล่าสุด
$_SESSION['last_activity'] = time();

// Fetch product details
if (isset($_GET['id'])) {
    $product_name = $_GET['id'];
    $sql = "SELECT * FROM productdetails WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $product_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if (!$product) {
        header('Location: gallery.php');
        exit();
    }
} else {
    header('Location: gallery.php');
    exit();
}

// Fetch product images from product_images table
$sql_images = "SELECT imagelink FROM product_images WHERE product_id = ? ORDER BY image_order ASC LIMIT 5";
$stmt_images = $conn->prepare($sql_images);
$stmt_images->bind_param("i", $product['id']); // สมมติว่า productdetails มี column id
$stmt_images->execute();
$images_result = $stmt_images->get_result();
$additional_images = [];

while ($image = $images_result->fetch_assoc()) {
    $additional_images[] = $image['imagelink'];
}

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $product_name = $_POST['name'];
    $detail = $_POST['detail'];
    $quantity = $_POST['quantity'];
    $price = $_POST['product_price'];
    $custom_quantity = $_POST['custom_quantity'];

    // Add to cart session with new fields
    $_SESSION['cart'][] = [
        'name' => $product_name,
        'detail' => $detail,
        'quantity' => $custom_quantity,
        'price' => $price
    ];
    
    // สร้าง session flash message สำหรับแสดงข้อความ
    $_SESSION['add_to_cart_message'] = "เพิ่ม $product_name จำนวน $custom_quantity ชิ้นลงในตะกร้าแล้ว";
}

// Fetch related products (same category or similar products)
$category = explode(' ', $product['detail'])[0]; // สมมติว่า detail มีหมวดหมู่เป็นคำแรก
$sql_related = "SELECT * FROM productdetails WHERE detail LIKE ? AND name != ? LIMIT 4";
$stmt_related = $conn->prepare($sql_related);
$category_param = "%$category%";
$stmt_related->bind_param("ss", $category_param, $product_name);
$stmt_related->execute();
$related_results = $stmt_related->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://customseafoods.com/cdn/shop/files/CS_Logo_2_1000.webp?v=1683664967" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title><?php echo $product['name']; ?> - Product Details</title>
    <link rel="stylesheet" href="/learning01/Management01/Assets/CSS/gallery.css">
    <link rel="stylesheet" href="/learning01/Management01/Assets/CSS/product_details.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../Assets/JS/script.js"></script>
    <script src="../Assets/JS/gallerydetailsproduct.js"></script>
</head>
<body>
<header class="gallery-header">
    <div class="dropdown">
        <button class="dropdown-btn">
            <h1>Product Details <span class="dropdown-arrow">▾</span></h1>
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
        <a href="../Frontend/index.php" class="btn">Home</a>
        <a href="gallery.php" class="btn">Back to Gallery</a>
        <a href="../Frontend/cart.php" class="btn">Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
    </div>
</header>

<main class="product-details-container">
    <!-- แสดงข้อความเมื่อเพิ่มสินค้าลงตะกร้า -->
    <?php if(isset($_SESSION['add_to_cart_message'])): ?>
    <div class="alert alert-success text-center mt-2" id="cart-message">
        <?php 
            echo $_SESSION['add_to_cart_message']; 
            unset($_SESSION['add_to_cart_message']);
        ?>
    </div>
    <script>
        setTimeout(function() {
            document.getElementById('cart-message').style.display = 'none';
        }, 3000);
    </script>
    <?php endif; ?>

    <div class="product-details">
        <div class="product-image">
            <div class="main-image">
                <img id="main-product-image" src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>">
            </div>
            <div class="gallery-thumbnails">
                <!-- Main image as first thumbnail -->
                <div class="thumbnail active" data-image="<?php echo $product['image_url']; ?>">
                    <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?> - Main View">
                </div>
                
                <?php
                // Display additional images if available
                foreach ($additional_images as $image_url) {
                    echo '<div class="thumbnail" data-image="' . $image_url . '">';
                    echo '<img src="' . $image_url . '" alt="' . $product['name'] . ' - Additional View">';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        <div class="product-info">
            <h1><?php echo $product['name']; ?></h1>
            <h2 class="price">฿<?php echo number_format($product['price'], 2); ?></h2>
            
            <div class="product-description">
                <h3>รายละเอียดสินค้า</h3>
                <p><?php echo $product['detail']; ?></p>
                
                <!-- คุณสมบัติพิเศษหรือข้อมูลโภชนาการ (สมมติว่ามีฟิลด์นี้ในฐานข้อมูล) -->
                <?php if(isset($product['nutrition'])): ?>
                <div class="nutrition-facts">
                    <h4>คุณค่าทางโภชนาการ (ต่อ 100 กรัม)</h4>
                    <p><?php echo $product['nutrition']; ?></p>
                </div>
                <?php endif; ?>
                
                <div class="product-stock">
                    <?php if($product['quantity'] > 0): ?>
                        <p class="in-stock">มีสินค้าในสต็อก: <?php echo $product['quantity']; ?> ชิ้น</p>
                    <?php else: ?>
                        <p class="out-of-stock">สินค้าหมด</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <form method="POST" class="add-to-cart-form">
                <input type="hidden" name="name" value="<?php echo $product['name']; ?>">
                <input type="hidden" name="detail" value="<?php echo $product['detail']; ?>">
                <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                <input type="hidden" name="quantity" value="<?php echo $product['quantity']; ?>">
                
                <div class="quantity-selector">
                    <label for="custom_quantity">จำนวน:</label>
                    <input type="number" id="custom_quantity" name="custom_quantity" value="1" min="1" max="<?php echo $product['quantity']; ?>" <?php echo $product['quantity'] <= 0 ? 'disabled' : ''; ?>>
                </div>
                
                <button type="submit" name="add_to_cart" class="add-to-cart-btn" <?php echo $product['quantity'] <= 0 ? 'disabled' : ''; ?>>
                    <?php echo $product['quantity'] > 0 ? 'เพิ่มลงตะกร้า' : 'สินค้าหมด'; ?>
                </button>
            </form>
            
            <!-- การจัดส่งและการคืนสินค้า -->
            <div class="shipping-info">
                <h3>การจัดส่งและการคืนสินค้า</h3>
                <p><strong>การจัดส่ง:</strong> จัดส่งฟรีเมื่อซื้อครบ 1,000 บาท</p>
                <p><strong>รับประกันความสด:</strong> หากได้รับสินค้าไม่สด สามารถขอเงินคืนได้ภายใน 24 ชั่วโมง</p>
            </div>
        </div>
    </div>
    
    <!-- สินค้าที่เกี่ยวข้อง -->
    <div class="related-products">
        <h2>สินค้าที่เกี่ยวข้อง</h2>
        <div class="related-products-grid">
            <?php
            if ($related_results->num_rows > 0) {
                while ($related = $related_results->fetch_assoc()) {
                    ?>
                    <div class="related-product-item">
                        <a href="product_details.php?id=<?php echo $related['name']; ?>">
                            <img src="<?php echo $related['image_url']; ?>" alt="<?php echo $related['name']; ?>">
                            <h3><?php echo $related['name']; ?></h3>
                            <p class="price">฿<?php echo number_format($related['price'], 2); ?></p>
                        </a>
                    </div>
                    <?php
                }
            } else {
                echo '<p>ไม่พบสินค้าที่เกี่ยวข้อง</p>';
            }
            ?>
        </div>
    </div>
</main>

<footer class="gallery-footer">
    <p>&copy; <?php echo date('Y'); ?> Custom Seafoods. All rights reserved.</p>
    <p>ติดต่อ: info@customseafoods.com | โทร: 02-123-4567</p>
</footer>

</body>
</html>