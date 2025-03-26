<?php
session_start();
require_once '../Backend/productreq.php';

$_SESSION['last_activity'] = time();

if (!isset($conn) || $conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//เก็บ session
$is_admin = false;
if (isset($_SESSION['user_id'])) {
    $sql = "SELECT role FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $is_admin = ($user['role'] === 'admin');
    $stmt->close();
}

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

$additional_images = [];
if (!empty($product['additional_images'])) {
    $additional_images = json_decode($product['additional_images'], true);
    if (!is_array($additional_images)) {
        $additional_images = [];
    }
}

// ฟังก์ชันเรียงลำดับรูปภาพ
if ($is_admin && isset($_POST['sort_images'])) {
    $new_order = json_decode($_POST['sort_images'], true);
    if (is_array($new_order) && !empty($new_order)) {
        $sorted_images = [];
        foreach ($new_order as $index) {
            if (isset($additional_images[$index])) {
                $sorted_images[] = $additional_images[$index];
            }
        }
        $additional_images = $sorted_images;
        $sorted_images_json = json_encode($additional_images);
        $sql_sort_images = "UPDATE productlist SET additional_images = ? WHERE id = ?";
        $stmt_sort_images = $conn->prepare($sql_sort_images);
        $stmt_sort_images->bind_param("si", $sorted_images_json, $product['id']);
        $stmt_sort_images->execute();
        $stmt_sort_images->close();
        $_SESSION['upload_message'] = "เรียงลำดับรูปภาพเรียบร้อยแล้ว";
    }
    exit("Sort completed");
}

// สำหรับลบรูปภาพ
if ($is_admin && isset($_POST['delete_image']) && isset($_POST['image_index'])) {
    $image_index = (int)$_POST['image_index'];
    if (isset($additional_images[$image_index])) {
        $image_to_delete = $additional_images[$image_index];
        if (file_exists($image_to_delete)) {
            unlink($image_to_delete);
        }
        unset($additional_images[$image_index]);
        $additional_images = array_values($additional_images);
        $updated_images_json = json_encode($additional_images);
        $sql_delete_image = "UPDATE productlist SET additional_images = ? WHERE id = ?";
        $stmt_delete_image = $conn->prepare($sql_delete_image);
        $stmt_delete_image->bind_param("si", $updated_images_json, $product['id']);
        $stmt_delete_image->execute();
        $stmt_delete_image->close();
        $_SESSION['upload_message'] = "ลบรูปภาพเรียบร้อยแล้ว";
    }
    header("Location: product_details.php?id=" . urlencode($product_name));
    exit();
}

if ($is_admin && isset($_POST['update_product'])) {
    $new_name = $_POST['name'];
    $new_detail = $_POST['detail'];
    $new_price = (float)$_POST['price'];
    $new_quantity = (int)$_POST['quantity'];

    $sql_update = "UPDATE productlist SET name = ?, detail = ?, price = ?, quantity = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssdii", $new_name, $new_detail, $new_price, $new_quantity, $product['id']);
    $stmt_update->execute();
    $stmt_update->close();

    if ($new_name !== $product_name) {
        header("Location: product_details.php?id=" . urlencode($new_name));
    } else {
        header("Location: product_details.php?id=" . urlencode($product_name));
    }
    exit();
}

if ($is_admin && isset($_POST['update_images'])) {
    $new_images = $_FILES['new_images'];
    $upload_dir = "../uploads/Additional_IMAGE/"; // เปลี่ยนโฟลเดอร์
    $old_upload_dir = "../uploads/"; // โฟลเดอร์เก่า
    $current_images = $additional_images;
    $upload_message = [];

    // ตรวจสอบและย้ายไฟล์เก่าไปยังโฟลเดอร์ใหม่
    foreach ($current_images as $index => $image_path) {
        $old_path = $image_path;
        $file_name = basename($image_path);
        $new_path = $upload_dir . $file_name;

        if (strpos($old_path, "../uploads/Additional_IMAGE/") === false && file_exists($old_path)) {
            if (rename($old_path, $new_path)) {
                $current_images[$index] = $new_path;
            }
        }
    }

    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            $upload_message[] = "ไม่สามารถสร้างโฟลเดอร์ $upload_dir ได้";
        }
    }

    if (!is_writable($upload_dir)) {
        $upload_message[] = "โฟลเดอร์ $upload_dir ไม่มีสิทธิ์ในการเขียน กรุณาตั้งค่า permission เป็น 0755 หรือ 0777";
    } else {
        foreach ($new_images['tmp_name'] as $key => $tmp_name) {
            if ($new_images['error'][$key] === UPLOAD_ERR_OK && count($current_images) < 5) {
                $file_name = "product_" . $product['id'] . "_" . uniqid() . "_" . basename($new_images['name'][$key]);
                $file_path = $upload_dir . $file_name;

                $file_already_exists = false;
                foreach ($current_images as $existing_image) {
                    if (basename($existing_image) === $file_name) {
                        $file_already_exists = true;
                        break;
                    }
                }

                if (!$file_already_exists) {
                    $image_type = exif_imagetype($tmp_name);
                    $max_width = 800;
                    $max_height = 800;

                    list($width, $height) = getimagesize($tmp_name);
                    $new_width = $width;
                    $new_height = $height;

                    if ($width > $max_width || $height > $max_height) {
                        $ratio = min($max_width / $width, $max_height / $height);
                        $new_width = $width * $ratio;
                        $new_height = $height * $ratio;
                    }

                    if ($image_type == IMAGETYPE_JPEG) {
                        $image = imagecreatefromjpeg($tmp_name);
                    } elseif ($image_type == IMAGETYPE_PNG) {
                        $image = imagecreatefrompng($tmp_name);
                    } else {
                        move_uploaded_file($tmp_name, $file_path);
                        $current_images[] = $file_path;
                        $upload_message[] = "อัปโหลดรูปภาพ $file_name สำเร็จ (ไม่รองรับการปรับขนาด)";
                        continue;
                    }

                    $resized_image = imagecreatetruecolor($new_width, $new_height);
                    if ($image_type == IMAGETYPE_PNG) {
                        imagealphablending($resized_image, false);
                        imagesavealpha($resized_image, true);
                    }
                    imagecopyresampled($resized_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

                    if ($image_type == IMAGETYPE_JPEG) {
                        imagejpeg($resized_image, $file_path, 75);
                    } elseif ($image_type == IMAGETYPE_PNG) {
                        imagepng($resized_image, $file_path, 6);
                    }

                    imagedestroy($image);
                    imagedestroy($resized_image);

                    if (file_exists($file_path)) {
                        $current_images[] = $file_path;
                        $upload_message[] = "อัปโหลดและปรับขนาดรูปภาพ $file_name สำเร็จ";
                    } else {
                        $upload_message[] = "เกิดข้อผิดพลาดในการบันทึก $file_name";
                    }
                } else {
                    $upload_message[] = "รูปภาพ $file_name ซ้ำกับที่มีอยู่แล้ว";
                }
            } elseif ($new_images['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                $upload_message[] = "เกิดข้อผิดพลาดในการอัปโหลดไฟล์: " . $new_images['name'][$key] . " (Error Code: " . $new_images['error'][$key] . ")";
            }
        }

        if (count($current_images) > 5) {
            $current_images = array_slice($current_images, 0, 5);
            $upload_message[] = "สามารถอัปโหลดได้สูงสุด 5 รูปเท่านั้น";
        }

        $new_images_json = json_encode($current_images);
        $sql_update_images = "UPDATE productlist SET additional_images = ? WHERE id = ?";
        $stmt_update_images = $conn->prepare($sql_update_images);
        if ($stmt_update_images) {
            $stmt_update_images->bind_param("si", $new_images_json, $product['id']);
            if ($stmt_update_images->execute()) {
                $upload_message[] = "บันทึกข้อมูลรูปภาพในฐานข้อมูลสำเร็จ";
            } else {
                $upload_message[] = "เกิดข้อผิดพลาดในการบันทึกข้อมูลในฐานข้อมูล: " . $stmt_update_images->error;
            }
            $stmt_update_images->close();
        } else {
            $upload_message[] = "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error;
        }
    }

    if (!empty($upload_message)) {
        $_SESSION['upload_message'] = implode("<br>", $upload_message);
    }

    header("Location: product_details.php?id=" . urlencode($product_name));
    exit();
}

if (isset($_POST['add_to_cart'])) {
    $product_name = trim($_POST['name']);
    $detail = trim($_POST['detail']);
    $quantity_in_stock = (int)$_POST['quantity'];
    $custom_quantity = isset($_POST['custom_quantity']) ? (int)$_POST['custom_quantity'] : 1;
    $price = (float)$_POST['product_price'];

    if ($custom_quantity <= 0 || $custom_quantity > $quantity_in_stock) {
        $_SESSION['add_to_cart_message'] = "จำนวนสินค้าที่ต้องการ ($custom_quantity) ไม่ถูกต้องหรือเกินสต็อกที่มี ($quantity_in_stock)";
    } else {
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $item_found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['name'] === $product_name) {
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

$category = explode(' ', $product['detail'])[0];
$sql_related = "SELECT * FROM productlist WHERE detail LIKE ? AND name != ? LIMIT 4";
$stmt_related = $conn->prepare($sql_related);
$category_param = "%$category%";
$stmt_related->bind_param("ss", $category_param, $product_name);
$stmt_related->execute();
$related_results = $stmt_related->get_result();

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
    <script src="../Assets/JS/product_navigation.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <!-- รวม SweetAlert2 และ Helper -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../Assets/JS/sweet_alert_helper.js"></script>
    <style>
        .thumbnail {
            position: relative;
            display: inline-block;
            margin: 5px;
            cursor: move;
        }
        .delete-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: red;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        .admin-edit {
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
        }
        .admin-edit form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .admin-edit label {
            font-weight: bold;
        }
        .admin-edit input[type="text"],
        .admin-edit textarea,
        .admin-edit input[type="number"] {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }
        .admin-edit button {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .admin-edit button:hover {
            background-color: #45a049;
        }
        .sortable-ghost {
            opacity: 0.5;
        }
    </style>
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
    <?php if (isset($_SESSION['upload_message'])): ?>
    <div class="alert alert-info text-center mt-2" id="upload-message">
        <?php 
            echo $_SESSION['upload_message']; 
            unset($_SESSION['upload_message']);
        ?>
    </div>
    <script>
        setTimeout(function() {
            document.getElementById('upload-message').style.display = 'none';
        }, 5000);
    </script>
    <?php endif; ?>

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
                <button class="image-nav-button prev-button" id="prev-image"><</button>
                <img id="main-product-image" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <button class="image-nav-button next-button" id="next-image">></button>
            </div>
            <div class="gallery-thumbnails" id="sortable-thumbnails">
                <div class="thumbnail active" data-image="<?php echo htmlspecialchars($product['image_url']); ?>">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?> - Main View">
                </div>
                <?php foreach ($additional_images as $index => $image_url): ?>
                    <div class="thumbnail" data-image="<?php echo htmlspecialchars($image_url); ?>" data-index="<?php echo $index; ?>">
                        <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($product['name']); ?> - Additional View">
                        <?php if ($is_admin): ?>
                            <button class="delete-btn" data-index="<?php echo $index; ?>">X</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="product-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <h2 class="price">฿<?php echo number_format($product['price'], 2); ?></h2>
            
            <div class="product-description">
                <h3>รายละเอียดสินค้า</h3>
                <p class="description-text"><?php echo htmlspecialchars($product['detail']); ?></p>
                
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
                <p class="shipping-text"><strong>การจัดส่ง:</strong> จัดส่งฟรีเมื่อซื้อครบ 1,000 บาท</p>
                <p class="shipping-text"><strong>รับประกันความสด:</strong> หากได้รับสินค้าไม่สด สามารถขอเงินคืนได้ภายใน 24 ชั่วโมง</p>
            </div>
        </div>
    </div>
    
    <?php if ($is_admin): ?>
        <div class="admin-edit">
            <h3>แก้ไขข้อมูลสินค้า</h3>
            <form method="POST" action="">
                <label>ชื่อสินค้า:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                
                <label>รายละเอียด:</label>
                <textarea name="detail" required><?php echo htmlspecialchars($product['detail']); ?></textarea>
                
                <label>ราคา:</label>
                <input type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                
                <label>จำนวน:</label>
                <input type="number" name="quantity" value="<?php echo htmlspecialchars($product['quantity']); ?>" required>
                
                <button type="submit" name="update_product">บันทึกการเปลี่ยนแปลง</button>
            </form>

            <h3>จัดการรูปภาพเพิ่มเติม (เพิ่มได้อีก <?php echo 5 - count($additional_images); ?> รูป)</h3>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <label>อัปโหลดรูปภาพใหม่ (สูงสุด 5 รูป):</label>
                <input type="file" name="new_images[]" multiple accept="image/*" onchange="limitFiles(this, <?php echo 5 - count($additional_images); ?>)">
                <button type="submit" name="update_images">อัปเดตรูปภาพ</button>
            </form>
        </div>
    <?php endif; ?>

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

<script>
    const isAdmin = <?php echo json_encode($is_admin); ?>;
</script>
<script src="../Assets/JS/product_details.js"></script>
</body>
</html>
<?php
$stmt->close();
$stmt_related->close();
$conn->close();
?>