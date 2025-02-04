<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "1234", "management01");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_email'])) {
    header('Location: ../login.php');
    exit();
}

// ตรวจสอบว่ามีการกดปุ่ม Add to Cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];

    // เพิ่มสินค้าไปยัง session cart
    $_SESSION['cart'][] = [
        'id' => $product_id,
        'name' => $product_name,
        'price' => $product_price
    ];
}

// ดึงข้อมูลสินค้า
$sql = "SELECT * FROM fishproduct";
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
    <div class="dropdown">
        <button class="dropdown-btn">
            <h1>Any of Fish 🐠<span class="dropdown-arrow">▾</span></h1>
        </button>
        <div class="dropdown-content">
            <div class="dropdown-category">
                <h3>Categories</h3>
                <a href="Fish.php" class="dropdown-item"><span class="icon">🐠</span>Any of Fish</a>
                <a href="Occt.php" class="dropdown-item"><span class="icon">🐙</span>Any of Occtupuss</a>
                <a href="Shell.php" class="dropdown-item"><span class="icon">🐚</span>Any of Shell</a>
            </div>
            <div class="dropdown-category">
                <h3>Special</h3>
                <a href="gallery.php" class="dropdown-item"><span class="icon">🔥</span>Hot Deals</a>
                <a href="#" class="dropdown-item"><span class="icon">⭐</span>New Arrivals</a>
            </div>
        </div>
    </div>
    <div class="header-buttons">
        <a href="gallery.php" class="btn">Back to main</a>
        <a href="../index.php" class="btn">Home</a>
        <a href="../Cart/cart.php" class="btn">Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
    </div>
</header>

    <main class="gallery-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="gallery-item">';
                echo '<img src="' . $row["image_url"] . '" alt="' . $row["name"] . '">';
                echo '<h2>' . $row["name"] . '</h2>';
                echo '<p>$' . $row["price"] . '</p>';
                echo '<form method="POST" action="">';
                echo '<input type="hidden" name="product_id" value="' . $row["id"] . '">';
                echo '<input type="hidden" name="product_name" value="' . $row["name"] . '">';
                echo '<input type="hidden" name="product_price" value="' . $row["price"] . '">';
                echo '<button type="submit" name="add_to_cart">Add to Cart</button>';
                echo '</form>';
                echo '</div>';
            }
        } else {
            echo '<p>No products found.</p>';
        }
        $conn->close();
        ?>
    </main>
    <script src="../Controller/script.js"></script>
</body>
</html>
