<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../Backend/vendor/autoload.php';
include '../Frontend/modal.php';

if (!isset($_SESSION['user_email'])) {
    header('Location: ../login.php');
    exit();
}

$user_email = $_SESSION['user_email'];

if (isset($_POST['remove_item'])) {
    $index = $_POST['item_index'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

if (isset($_POST['clear_cart'])) {
    unset($_SESSION['cart']);
}

if (isset($_POST['checkout'])) {
    if (!isset($_SESSION['user_email'])) {
        echo "<script>alert('Error: No email found! Please login.');</script>";
    } else {
        // สร้างการเชื่อมต่อกับฐานข้อมูล
        $conn = new mysqli("localhost", "root", "1234", "management01");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // คำนวณยอดรวมและรวบรวมรายการสินค้า
        $total_price = 0;
        $items_list = array();
        
        foreach ($_SESSION['cart'] as $item) {
            $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
            $total_price += $item['price'] * $quantity;
            $items_list[] = $item['name'] . " (x" . $quantity . ")";
            
            // อัพเดตจำนวนสินค้าในฐานข้อมูล
            $update_sql = "UPDATE productlist SET quantity = quantity - ? 
                          WHERE name = ? AND quantity >= ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("isi", $quantity, $item['name'], $quantity);
            $stmt->execute();
            $stmt->close();
        }
        
        // แก้ไขส่วนสร้าง order reference แบบมีตัวอักษรนำหน้า (แทนที่ intval ซึ่งเป็นสาเหตุของ out of range error)
        $order_reference = 'ORD-' . date('ymd') . '-' . rand(1000, 9999);
        
        // บันทึกข้อมูลการสั่งซื้อลงในฐานข้อมูลโดยใช้ order_reference แทน order_id
        $username = $_SESSION['username'] ?? 'Guest'; // ถ้าไม่มี username ให้ใช้ 'Guest'
        $items_string = implode(", ", $items_list);
  
        $insert_sql = "INSERT INTO orderhistory (order_id, order_reference, username, email, item, total_price) 
              VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("issssd", $order_id, $order_reference, $username, $user_email, $items_string, $total_price);
        
        if ($stmt->execute()) {
            // ส่งอีเมลยืนยันการสั่งซื้อ
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'bignagniza13@gmail.com';
                $mail->Password = 'dugxaxfziwqizhpk';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('bignagniza13@gmail.com', 'AdminSeafoodMagica');
                $mail->addAddress($user_email);
                $mail->isHTML(true);
                $mail->Subject = 'Your Order Confirmation';

                $emailBody = "<h2>Thank you for your order!</h2>";
                $emailBody .= "<p>Order Reference: " . $order_reference . "</p>";
                $emailBody .= "<p>Here are the details:</p><ul>";
                
                foreach ($items_list as $item) {
                    $emailBody .= "<li>" . $item . "</li>";
                }
                
                $emailBody .= "</ul><p><strong>Total: $" . number_format($total_price, 2) . "</strong></p>";

                $mail->Body = $emailBody;
                $mail->send();

                echo "<script>showModal('Order placed successfully! Order Reference: " . $order_reference . "\\nA confirmation email has been sent.');</script>";
                unset($_SESSION['cart']);
            } catch (Exception $e) {
                echo "<script>showModal('Order placed successfully! Order Reference: " . $order_reference . "\\nBut email could not be sent.');</script>";
            }
        } else {
            echo "<script>showModal('Error placing order. Please try again.');</script>";
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../Assets/CSS/cart.css">
</head>
<body>
    <div class="cart-container">
        <div class="cart-header">
            <h1>Shopping Cart</h1>
            <a href="gallery.php" class="btn btn-continue">Continue Shopping</a>
        </div>

        <?php if (!empty($_SESSION['cart'])): ?>
            <div class="cart-items">
                <?php
                $total = 0;
                foreach ($_SESSION['cart'] as $index => $item):
                    $total += $item['price'];
                ?>
                    <div class="cart-item">
                        <div>
                            <h3><?php echo $item['name']; ?></h3>
                            <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
                        </div>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="item_index" value="<?php echo $index; ?>">
                            <button type="submit" name="remove_item" class="btn btn-remove">Remove</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-total">
                <p style="margin: 0;"><strong>Total: $<?php echo number_format($total, 2); ?></strong></p>
            </div>

            <div class="cart-actions">
                <form method="POST" style="display: inline-block;">
                    <button type="submit" name="clear_cart" class="btn btn-clear">Clear Cart</button>
                </form>
                <form method="POST" style="display: inline-block;">
                    <button type="submit" name="checkout" class="btn btn-checkout">Proceed to Checkout</button>
                </form>
            </div>

        <?php else: ?>
            <div class="empty-cart">
                <h2>Your cart is empty</h2>
                <p>Add some products to your cart and they will appear here</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
