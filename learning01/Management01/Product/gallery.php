<?php
session_start();
require_once '../Backend/productreq.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Gallery - Custom Seafoods</title>
    <!-- Favicon and CSS -->
    <link rel="icon" href="https://customseafoods.com/cdn/shop/files/CS_Logo_2_1000.webp?v=1683664967" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/learning01/Management01/Assets/CSS/gallery.css">
    <link rel="stylesheet" href="/learning01/Management01/Assets/CSS/botton.css">
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../Assets/JS/search.js"></script>
    <script src="../Assets/JS/script.js"></script>
</head>
<body>
    <header class="gallery-header">
        <div class="dropdown">
            <button class="dropdown-btn">
                <h1><i class="fas fa-fish"></i> Custom Seafoods <span class="dropdown-arrow">▾</span></h1>
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
            <div class="search-container">
                <input type="text" 
                    id="searchInput" 
                    placeholder="Search products..." 
                    value="<?php echo htmlspecialchars($search_term ?? ''); ?>"
                    data-category="<?php echo htmlspecialchars($category_filter ?? ''); ?>"
                    data-price-min="<?php echo isset($price_min) ? $price_min : 0; ?>"
                    data-price-max="<?php echo isset($price_max) ? $price_max : 1000000; ?>">
                <button id="searchButton" class="search-btn"><i class="fas fa-search"></i></button>
            </div>
            <a href="../Frontend/<?php echo (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ? 'index.php' : 'dashboard.php'; ?>" class="btn"><i class="fas fa-home"></i> Home</a>
            <a href="cart.php" class="btn cart-btn"><i class="fas fa-shopping-cart"></i> Cart <span class="cart-count"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span></a>
        </div>
    </header>

    <!-- Filter Section -->
    <div class="container filter-container mt-3">
        <div class="filter-section shadow-sm rounded">
            <h3><i class="fas fa-filter"></i> Filter Products</h3>
            <form class="filter-form" method="GET" action="gallery.php">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="category"><i class="fas fa-tag"></i> Category:</label>
                        <select name="category" id="category" class="form-select">
                            <option value="">All Categories</option>
                            <option value="Fish" <?php echo isset($category_filter) && $category_filter == 'Fish' ? 'selected' : ''; ?>>Fish</option>
                            <option value="Squid" <?php echo isset($category_filter) && $category_filter == 'Squid' ? 'selected' : ''; ?>>Squid</option>
                            <option value="Shrimp" <?php echo isset($category_filter) && $category_filter == 'Shrimp' ? 'selected' : ''; ?>>Shrimp</option>
                            <option value="Shell" <?php echo isset($category_filter) && $category_filter == 'Shell' ? 'selected' : ''; ?>>Shell</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label><i class="fas fa-money-bill-wave"></i> Price Range (฿):</label>
                        <div class="price-inputs">
                            <input type="number" name="price_min" min="0" value="<?php echo isset($price_min) ? $price_min : ''; ?>" class="form-control" placeholder="Min">
                            <span>to</span>
                            <input type="number" name="price_max" min="0" value="<?php echo isset($price_max) && $price_max != 1000000 ? $price_max : ''; ?>" class="form-control" placeholder="Max">
                        </div>
                    </div>
                </div>
                
                <!-- Preserve search term when filtering -->
                <?php if (!empty($search_term ?? '')): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_term); ?>">
                <?php endif; ?>
                
                <div class="filter-buttons">
                    <button type="submit" class="filter-btn"><i class="fas fa-filter"></i> Apply Filters</button>
                    <a href="gallery.php" class="filter-btn reset-btn"><i class="fas fa-sync-alt"></i> Reset</a>
                </div>
            </form>
        </div>

        <?php if (isset($category_filter) && !empty($category_filter) || (isset($price_min) && $price_min > 0) || (isset($price_max) && $price_max < 1000000) || !empty($search_term ?? '')): ?>
        <div class="active-filters">
            <strong><i class="fas fa-tags"></i> Active Filters:</strong>
            <?php if (isset($category_filter) && !empty($category_filter)): ?>
                <span class="badge bg-primary">Category: <?php echo $category_filter; ?> <a href="?<?php echo http_build_query(array_merge($_GET, array('category' => ''))); ?>" title="Remove filter">✕</a></span>
            <?php endif; ?>
            
            <?php if (isset($price_min) && $price_min > 0 || isset($price_max) && $price_max < 1000000): ?>
                <span class="badge bg-primary">Price: ฿<?php echo number_format(isset($price_min) ? $price_min : 0); ?> - ฿<?php echo number_format(isset($price_max) ? $price_max : 1000000); ?> <a href="?<?php echo http_build_query(array_merge($_GET, array('price_min' => 0, 'price_max' => 1000000))); ?>" title="Remove filter">✕</a></span>
            <?php endif; ?>
            
            <?php if (!empty($search_term ?? '')): ?>
                <span class="badge bg-primary">Search: "<?php echo htmlspecialchars($search_term); ?>" <a href="?<?php echo http_build_query(array_merge($_GET, array('search' => ''))); ?>" title="Remove search">✕</a></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Product Count and Sort -->
    <div class="container mt-3">
        <div class="products-header">
            <div class="product-count">
                <?php if (isset($result)): ?>
                <p><i class="fas fa-box"></i> Showing <?php echo $result->num_rows; ?> products</p>
                <?php endif; ?>
            </div>
            <div class="sort-options">
                <label for="sort"><i class="fas fa-sort"></i> Sort By:</label>
                <select id="sort" class="form-select form-select-sm">
                    <option value="default">Default</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                    <option value="name">Name</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Product Gallery -->
    <main class="gallery-container">
        <?php
        if (isset($result) && $result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $stock_status = $row['quantity'] > 10 ? 'in-stock' : ($row['quantity'] > 0 ? 'low-stock' : 'out-of-stock');
                $stock_text = $row['quantity'] > 10 ? 'In Stock' : ($row['quantity'] > 0 ? 'Low Stock' : 'Out of Stock');
                ?>
                <div class="gallery-item">
                    <div class="product-badge <?php echo $row['quantity'] <= 5 ? 'badge-danger' : ''; ?>">
                        <?php if($row['quantity'] <= 5 && $row['quantity'] > 0): ?>
                            <span>Only <?php echo $row['quantity']; ?> left!</span>
                        <?php elseif($row['quantity'] <= 0): ?>
                            <span>Out of Stock</span>
                        <?php endif; ?>
                    </div>
                    
                    <a href="product_details.php?id=<?php echo $row['name']; ?>" class="product-link">
                        <div class="img-container">
                            <img src="<?php echo $row['image_url']; ?>" alt="<?php echo $row['name']; ?>">
                        </div>
                        <div class="product-info">
                            <h2><?php echo $row['name']; ?></h2>
                            <p class="detail"><?php echo substr($row['detail'], 0, 80); ?><?php echo strlen($row['detail']) > 80 ? '...' : ''; ?></p>
                            <div class="stock-container">
                                <span class="stock-indicator <?php echo $stock_status; ?>"></span>
                                <span class="stock-text"><?php echo $stock_text; ?> (<?php echo $row['quantity']; ?>)</span>
                            </div>
                            <p class="price">฿<?php echo number_format($row['price'], 2); ?></p>
                        </div>
                    </a>
                    
                    <form method="POST" class="cart-form">
                        <div class="cart-controls">
                            <input type="hidden" name="name" value="<?php echo $row['name']; ?>">
                            <input type="hidden" name="detail" value="<?php echo $row['detail']; ?>">
                            <input type="hidden" name="product_price" value="<?php echo $row['price']; ?>">
                            
                            <div class="quantity-control">
                                <button type="button" class="quantity-btn minus"><i class="fas fa-minus"></i></button>
                                <input type="number" name="quantity" min="1" max="<?php echo $row['quantity']; ?>" value="1" class="quantity-input">
                                <button type="button" class="quantity-btn plus"><i class="fas fa-plus"></i></button>
                            </div>
                            
                            <button type="submit" name="add_to_cart" class="add-to-cart-btn" <?php echo $row['quantity'] <= 0 ? 'disabled' : ''; ?>>
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </form>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="no-products">
                <i class="fas fa-search fa-3x"></i>
                <h3>No products found</h3>
                <p>Try adjusting your search or filter criteria</p>
                <a href="gallery.php" class="reset-search">Reset All Filters</a>
            </div>
            <?php
        }
        ?>
    </main>

    <footer class="gallery-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3><i class="fas fa-fish"></i> Custom Seafoods</h3>
                <p>Your trusted source for premium quality seafood products.</p>
            </div>
            <div class="footer-section">
                <h3><i class="fas fa-phone"></i> Contact Us</h3>
                <p><i class="fas fa-envelope"></i> Email: info@customseafoods.com</p>
                <p><i class="fas fa-phone"></i> Phone: 098-308-3185</p>
            </div>
            <div class="footer-section">
                <h3><i class="fas fa-link"></i> Quick Links</h3>
                <ul>
                    <li><a href="../Frontend/index.php">Home</a></li>
                    <li><a href="../Product/gallery.php">Products</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="../Frontend/contactus.php">Contact</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Custom Seafoods. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>