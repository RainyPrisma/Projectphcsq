<?php
session_start();
include '../Database/config.php';
include '../Database/usersessioncheck.php';
// อัพเดท timestamp ของกิจกรรมล่าสุด
$_SESSION['last_activity'] = time();

// ดึงข้อมูลสินค้าจาก productlist
if (isset($_GET['id'])) {
    $product_name = $_GET['id'];
    $sql = "SELECT * FROM productlist WHERE name = ?";
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

// ดึงรูปภาพเพิ่มเติมจาก product_images
$sql_images = "SELECT imagelink FROM product_images WHERE product_id = ? ORDER BY image_order ASC LIMIT 5";
$stmt_images = $conn->prepare($sql_images);
$stmt_images->bind_param("i", $product['id']);
$stmt_images->execute();
$images_result = $stmt_images->get_result();
$additional_images = [];

while ($image = $images_result->fetch_assoc()) {
    $additional_images[] = $image['imagelink'];
}

// จัดการ Add to Cart
if (isset($_POST['add_to_cart'])) {
    $product_name = trim($_POST['name']);
    $detail = trim($_POST['detail']);
    $quantity_in_stock = (int)$_POST['quantity'];
    $custom_quantity = isset($_POST['custom_quantity']) ? (int)$_POST['custom_quantity'] : 1;
    $price = (float)$_POST['product_price'];

    // ตรวจสอบข้อมูลที่ส่งมา
    if ($custom_quantity <= 0 || $custom_quantity > $quantity_in_stock) {
        $_SESSION['add_to_cart_message'] = "จำนวนสินค้าที่ต้องการ ($custom_quantity) ไม่ถูกต้องหรือเกินสต็อกที่มี ($quantity_in_stock)";
    } else {
        // ตรวจสอบว่าตะกร้ามีอยู่แล้วหรือไม่
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $item_found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['name'] === $product_name) {
                // อัปเดตจำนวนถ้าพบสินค้าในตะกร้า
                $new_quantity = $item['quantity'] + $custom_quantity;
                if ($new_quantity > $quantity_in_stock) {
                    $_SESSION['add_to_cart_message'] = "จำนวนสินค้าที่ต้องการ ($new_quantity) เกินสต็อกที่มี ($quantity_in_stock)";
                } else {
                    $item['quantity'] = $new_quantity;
                    $_SESSION['add_to_cart_message'] = "เพิ่ม $product_name จำนวน $custom_quantity ชิ้น รวมเป็น $new_quantity ชิ้นในตะกร้าแล้ว";
                    $item_found = true;
                }
                break;
            }
        }

        // เพิ่มรายการใหม่ถ้าไม่พบในตะกร้า
        if (!$item_found) {
            $_SESSION['cart'][] = [
                'name' => $product_name,
                'detail' => $detail,
                'quantity' => $custom_quantity,
                'price' => $price
            ];
            $_SESSION['add_to_cart_message'] = "เพิ่ม $product_name จำนวน $custom_quantity ชิ้นลงในตะกร้าแล้ว";
        }
    }
}

// ดึงสินค้าที่เกี่ยวข้องจาก productlist
$category = explode(' ', $product['detail'])[0];
$sql_related = "SELECT * FROM productlist WHERE detail LIKE ? AND name != ? LIMIT 4";
$stmt_related = $conn->prepare($sql_related);
$category_param = "%$category%";
$stmt_related->bind_param("ss", $category_param, $product_name);
$stmt_related->execute();
$related_results = $stmt_related->get_result();

// Review Section
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$has_purchased = false;
if ($user_id) {
    $sql_purchase = "SELECT COUNT(*) FROM orderhistory WHERE email = ? AND item LIKE ?";
    $stmt_purchase = $conn->prepare($sql_purchase);
    $item_param = "%{$product['name']}%";
    $stmt_purchase->bind_param("ss", $_SESSION['user_email'], $item_param);
    $stmt_purchase->execute();
    $purchase_count = $stmt_purchase->get_result()->fetch_row()[0];
    $has_purchased = $purchase_count > 0;
    $stmt_purchase->close();
}

// Handle review submission
if (isset($_POST['submit_review']) && $has_purchased) {
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);
    
    if ($rating < 1 || $rating > 5 || empty($comment)) {
        $_SESSION['review_message'] = "กรุณาให้คะแนนและความคิดเห็นที่ถูกต้อง";
    } else {
        $sql_review = "INSERT INTO product_reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)";
        $stmt_review = $conn->prepare($sql_review);
        $stmt_review->bind_param("iiis", $product['id'], $user_id, $rating, $comment);
        
        if ($stmt_review->execute()) {
            $_SESSION['review_message'] = "รีวิวของคุณถูกบันทึกเรียบร้อยแล้ว";
        } else {
            $_SESSION['review_message'] = "เกิดข้อผิดพลาดในการบันทึกรีวิว";
        }
        $stmt_review->close();
    }
}

// ดึงรีวิวที่มีอยู่
$sql_reviews = "SELECT r.*, u.username 
                FROM product_reviews r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.product_id = ? 
                ORDER BY r.created_at DESC";
$stmt_reviews = $conn->prepare($sql_reviews);
$stmt_reviews->bind_param("i", $product['id']);
$stmt_reviews->execute();
$reviews = $stmt_reviews->get_result();
$stmt_reviews->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://customseafoods.com/cdn/shop/files/CS_Logo_2_1000.webp?v=1683664967" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title><?php echo htmlspecialchars($product['name']); ?> - Product Details</title>
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
        <a href="cart.php" class="btn">Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
    </div>
</header>

<main class="product-details-container">
    <!-- Cart Message -->
    <?php if (isset($_SESSION['add_to_cart_message'])): ?>
    <div class="alert alert-success text-center mt-2" id="cart-message">
        <?php 
            echo htmlspecialchars($_SESSION['add_to_cart_message']); 
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
                <img id="main-product-image" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="gallery-thumbnails">
                <div class="thumbnail active" data-image="<?php echo htmlspecialchars($product['image_url']); ?>">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?> - Main View">
                </div>
                <?php foreach ($additional_images as $image_url): ?>
                    <div class="thumbnail" data-image="<?php echo htmlspecialchars($image_url); ?>">
                        <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($product['name']); ?> - Additional View">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="product-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <h2 class="price">฿<?php echo number_format($product['price'], 2); ?></h2>
            
            <div class="product-description">
                <h3>รายละเอียดสินค้า</h3>
                <p><?php echo htmlspecialchars($product['detail']); ?></p>
                
                <?php if (isset($product['nutrition'])): ?>
                <div class="nutrition-facts">
                    <h4>คุณค่าทางโภชนาการ (ต่อ 100 กรัม)</h4>
                    <p><?php echo htmlspecialchars($product['nutrition']); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="product-stock">
                    <?php if ($product['quantity'] > 0): ?>
                        <p class="in-stock">มีสินค้าในสต็อก: <?php echo $product['quantity']; ?> ชิ้น</p>
                    <?php else: ?>
                        <p class="out-of-stock">สินค้าหมด</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <form method="POST" class="add-to-cart-form">
                <input type="hidden" name="name" value="<?php echo htmlspecialchars($product['name']); ?>">
                <input type="hidden" name="detail" value="<?php echo htmlspecialchars($product['detail']); ?>">
                <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($product['price']); ?>">
                <input type="hidden" name="quantity" value="<?php echo htmlspecialchars($product['quantity']); ?>">
                
                <div class="quantity-selector">
                    <label for="custom_quantity">จำนวน:</label>
                    <input type="number" id="custom_quantity" name="custom_quantity" value="1" min="1" max="<?php echo htmlspecialchars($product['quantity']); ?>" <?php echo $product['quantity'] <= 0 ? 'disabled' : ''; ?>>
                </div>
                
                <button type="submit" name="add_to_cart" class="add-to-cart-btn" <?php echo $product['quantity'] <= 0 ? 'disabled' : ''; ?>>
                    <?php echo $product['quantity'] > 0 ? 'เพิ่มลงตะกร้า' : 'สินค้าหมด'; ?>
                </button>
            </form>
            
            <div class="shipping-info">
                <h3>การจัดส่งและการคืนสินค้า</h3>
                <p><strong>การจัดส่ง:</strong> จัดส่งฟรีเมื่อซื้อครบ 1,000 บาท</p>
                <p><strong>รับประกันความสด:</strong> หากได้รับสินค้าไม่สด สามารถขอเงินคืนได้ภายใน 24 ชั่วโมง</p>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <div class="related-products">
        <h2>สินค้าที่เกี่ยวข้อง</h2>
        <div class="related-products-grid">
            <?php if ($related_results->num_rows > 0): ?>
                <?php while ($related = $related_results->fetch_assoc()): ?>
                    <div class="related-product-item">
                        <a href="product_details.php?id=<?php echo htmlspecialchars($related['name']); ?>">
                            <img src="<?php echo htmlspecialchars($related['image_url']); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>">
                            <h3><?php echo htmlspecialchars($related['name']); ?></h3>
                            <p class="price">฿<?php echo number_format($related['price'], 2); ?></p>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>ไม่พบสินค้าที่เกี่ยวข้อง</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Review Section -->
    <div class="product-reviews">
        <h2>รีวิวสินค้า</h2>
        
        <?php if (isset($_SESSION['review_message'])): ?>
        <div class="alert alert-success text-center" id="review-message">
            <?php 
                echo htmlspecialchars($_SESSION['review_message']); 
                unset($_SESSION['review_message']);
            ?>
        </div>
        <script>
            setTimeout(function() {
                document.getElementById('review-message').style.display = 'none';
            }, 3000);
        </script>
        <?php endif; ?>

        <?php if ($user_id): ?>
            <?php if ($has_purchased): ?>
                <div class="review-form">
                    <h3>เขียนรีวิวของคุณ</h3>
                    <form method="POST">
                        <div class="rating-selector">
                            <label>ให้คะแนน:</label>
                            <select name="rating" required>
                                <option value="5">5 ดาว</option>
                                <option value="4">4 ดาว</option>
                                <option value="3">3 ดาว</option>
                                <option value="2">2 ดาว</option>
                                <option value="1">1 ดาว</option>
                            </select>
                        </div>
                        <div class="comment-box">
                            <label>ความคิดเห็น:</label>
                            <textarea name="comment" rows="4" placeholder="เขียนความคิดเห็นของคุณที่นี่..." required></textarea>
                        </div>
                        <button type="submit" name="submit_review" class="submit-review-btn">ส่งรีวิว</button>
                    </form>
                </div>
            <?php else: ?>
                <p class="review-notice">คุณต้องซื้อสินค้านี้ก่อนจึงจะสามารถเขียนรีวิวได้</p>
            <?php endif; ?>
        <?php else: ?>
            <p class="review-notice">กรุณาเข้าสู่ระบบเพื่อเขียนรีวิว</p>
        <?php endif; ?>

        <div class="reviews-list">
            <?php if ($reviews->num_rows > 0): ?>
                <?php while ($review = $reviews->fetch_assoc()): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <span class="review-username"><?php echo htmlspecialchars($review['username']); ?></span>
                            <span class="review-rating">
                                <?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?>
                            </span>
                        </div>
                        <p class="review-comment"><?php echo htmlspecialchars($review['comment']); ?></p>
                        <span class="review-date"><?php echo date('d/m/Y H:i', strtotime($review['created_at'])); ?></span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>ยังไม่มีรีวิวสำหรับสินค้านี้</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<footer class="gallery-footer">
    <p>© <?php echo date('Y'); ?> Custom Seafoods. All rights reserved.</p>
    <p>ติดต่อ: info@customseafoods.com | โทร: 02-123-4567</p>
</footer>

</body>
</html>
<?php
// ปิดการเชื่อมต่อฐานข้อมูล
$stmt->close();
$stmt_images->close();
$stmt_related->close();
$conn->close();
?>