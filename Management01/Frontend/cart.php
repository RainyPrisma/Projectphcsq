<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../Backend/vendor/autoload.php'; // โหลด PHPMailer

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
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'bignagniza13@gmail.com'; // เปลี่ยนเป็นอีเมลของคุณ
            $mail->Password = 'dugxaxfziwqizhpk';   // ใส่รหัสผ่านแอป (App Password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('bignagniza13@gmail.com', 'AdminSeafoodMagica'); // เปลี่ยนชื่อร้าน
            $mail->addAddress($_SESSION['user_email']);
            $mail->isHTML(true);
            $mail->Subject = 'Your Order Confirmation';

            $emailBody = "<h2>Thank you for your order!</h2><p>Here are the details:</p><ul>";
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $emailBody .= "<li>{$item['name']} - $" . number_format($item['price'], 2) . "</li>";
                $total += $item['price'];
            }
            $emailBody .= "</ul><p><strong>Total: $" . number_format($total, 2) . "</strong></p>";

            $mail->Body = $emailBody;
            $mail->send();

            echo "<script>alert('Order placed! A confirmation email has been sent.');</script>";
            unset($_SESSION['cart']);
        } catch (Exception $e) {
            echo "<script>alert('Order placed, but email could not be sent.');</script>";
        }
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
