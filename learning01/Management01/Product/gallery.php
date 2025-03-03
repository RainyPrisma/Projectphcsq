<?php
session_start();
require_once '../Backend/productreq.php';
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
                    <a href="gallery.php?category=Fish" class="dropdown-item"><span class="icon">🐠</span>Any of Fish</a>
                    <a href="gallery.php?category=Squid" class="dropdown-item"><span class="icon">🐙</span>Any of Squid</a>
                    <a href="gallery.php?category=Shrimp" class="dropdown-item"><span class="icon">🦐</span>Any of Shrimp</a>
                    <a href="gallery.php?category=Shell" class="dropdown-item"><span class="icon">🐚</span>Any of Shell</a>
                </div>
                <div class="dropdown-category">
                    <h3>Special</h3>
                    <a href="#" class="dropdown-item"><span class="icon">🔥</span>Hot Deals</a>
                    <a href="#" class="dropdown-item"><span class="icon">⭐</span>New Arrivals</a>
                </div>
            </div>
        </div>
        <div class="header-buttons">
            <input type="text" id="searchInput" placeholder="Search products..." value="<?php echo htmlspecialchars($search_term); ?>">
            <a href="../Frontend/<?php echo (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ? 'index.php' : 'dashboard.php'; ?>" class="btn">Home</a>
            <a href="cart.php" class="btn">Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
        </div>
    </header>

    <!-- ส่วนฟิลเตอร์ -->
    <div class="container mt-3">
        <div class="filter-section">
            <h3>กรองสินค้า</h3>
            <form class="filter-form" method="GET" action="gallery.php">
                <div class="filter-group">
                    <label for="category">ประเภทสินค้า:</label>
                    <select name="category" id="category" class="form-select">
                        <option value="">ทั้งหมด</option>
                        <option value="Fish" <?php echo $category_filter == 'Fish' ? 'selected' : ''; ?>>ปลา</option>
                        <option value="Squid" <?php echo $category_filter == 'Squid' ? 'selected' : ''; ?>>หมึก</option>
                        <option value="Shrimp" <?php echo $category_filter == 'Shrimp' ? 'selected' : ''; ?>>กุ้ง</option>
                        <option value="Shell" <?php echo $category_filter == 'Shell' ? 'selected' : ''; ?>>หอย</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>ช่วงราคา (฿):</label>
                    <div class="price-inputs">
                        <input type="number" name="price_min" min="0" value="<?php echo $price_min; ?>" class="form-control" placeholder="ต่ำสุด">
                        <span>ถึง</span>
                        <input type="number" name="price_max" min="0" value="<?php echo $price_max == 1000000 ? '' : $price_max; ?>" class="form-control" placeholder="สูงสุด">
                    </div>
                </div>
                <!-- Preserve search term when filtering -->
                <?php if (!empty($search_term)): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_term); ?>">
                <?php endif; ?>
                <div class="filter-buttons">
                    <button type="submit" class="filter-btn">กรองสินค้า</button>
                    <a href="gallery.php" class="filter-btn reset-btn">รีเซ็ต</a>
                </div>
            </form>
        </div>

        <?php if (!empty($category_filter) || $price_min > 0 || $price_max < 1000000 || !empty($search_term)): ?>
        <div class="active-filters">
            <strong>ตัวกรองที่ใช้:</strong>
            <?php if (!empty($category_filter)): ?>
                <span>ประเภท: <?php echo $category_filter; ?> <a href="?<?php echo http_build_query(array_merge($_GET, array('category' => ''))); ?>" title="ลบตัวกรอง">✕</a></span>
            <?php endif; ?>
            
            <?php if ($price_min > 0 || $price_max < 1000000): ?>
                <span>ราคา: ฿<?php echo number_format($price_min); ?> - ฿<?php echo number_format($price_max); ?> <a href="?<?php echo http_build_query(array_merge($_GET, array('price_min' => 0, 'price_max' => 1000000))); ?>" title="ลบตัวกรอง">✕</a></span>
            <?php endif; ?>
            
            <?php if (!empty($search_term)): ?>
                <span>ค้นหา: "<?php echo htmlspecialchars($search_term); ?>" <a href="?<?php echo http_build_query(array_merge($_GET, array('search' => ''))); ?>" title="ลบการค้นหา">✕</a></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <main class="gallery-container">
        <?php
        if ($result && $result->num_rows > 0) {
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // รักษาค่าฟิลเตอร์ใน URL เมื่อมีการค้นหา
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const searchTerm = searchInput.value.trim();
                
                // สร้าง URL พร้อมพารามิเตอร์การค้นหาและฟิลเตอร์
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('search', searchTerm);
                
                // เก็บฟิลเตอร์ที่มีอยู่
                if ('<?php echo $category_filter; ?>') {
                    urlParams.set('category', '<?php echo $category_filter; ?>');
                }
                
                if (<?php echo $price_min; ?> > 0) {
                    urlParams.set('price_min', <?php echo $price_min; ?>);
                }
                
                if (<?php echo $price_max; ?> < 1000000) {
                    urlParams.set('price_max', <?php echo $price_max; ?>);
                }
                
                window.location.href = 'gallery.php?' + urlParams.toString();
            }
        });
    });
    </script>
</body>
</html>