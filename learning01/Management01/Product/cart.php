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

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "1234", "management01");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

date_default_timezone_set('Asia/Bangkok');

// ตัวแปรเก็บสถานะคูปอง
$coupon_applied = false;
$discount_amount = 0;
$coupon_code = '';
$coupon_message = '';
$coupon_message_type = '';

// ตรวจสอบการใช้คูปอง
if (isset($_POST['apply_coupon'])) {
    $coupon_code = trim($_POST['coupon_code']);
    if (empty($coupon_code)) {
        $coupon_message = "กรุณากรอกโค้ดคูปอง";
        $coupon_message_type = "danger";
    } else {
        $sql = "SELECT * FROM coupons WHERE coupon_code = ? AND is_active = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $coupon_code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $coupon = $result->fetch_assoc();
            $current_date = date('Y-m-d H:i:s');
            $total_price = 0;

            // คำนวณยอดรวมก่อนส่วนลด
            foreach ($_SESSION['cart'] as $item) {
                $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
                $total_price += $item['price'] * $quantity;
            }

            // ตรวจสอบเงื่อนไขคูปอง
            if ($current_date < $coupon['valid_from'] || $current_date > $coupon['valid_until']) {
                $coupon_message = "คูปองนี้หมดอายุแล้ว";
                $coupon_message_type = "danger";
            } elseif ($total_price < $coupon['min_purchase']) {
                $coupon_message = "ยอดสั่งซื้อขั้นต่ำต้องไม่ต่ำกว่า " . number_format($coupon['min_purchase'], 2) . " บาท";
                $coupon_message_type = "danger";
            } elseif ($coupon['usage_limit'] > 0 && $coupon['current_usage'] >= $coupon['usage_limit']) {
                $coupon_message = "คูปองนี้ถูกใช้ครบจำนวนแล้ว";
                $coupon_message_type = "danger";
            } else {
                // คำนวณส่วนลด
                if ($coupon['discount_type'] == 'percentage') {
                    $discount_amount = ($coupon['discount_amount'] / 100) * $total_price;
                } else {
                    $discount_amount = $coupon['discount_amount'];
                }

                // เก็บข้อมูลคูปองใน session เพื่อใช้ตอนชำระเงิน
                $_SESSION['applied_coupon'] = [
                    'coupon_id' => $coupon['coupon_id'],
                    'coupon_code' => $coupon['coupon_code'],
                    'discount_amount' => $discount_amount,
                    'discount_type' => $coupon['discount_type']
                ];

                $coupon_applied = true;
                $coupon_message = "ใช้คูปองสำเร็จ! ลดราคา " . number_format($discount_amount, 2) . " บาท";
                $coupon_message_type = "success";
            }
        } else {
            $coupon_message = "ไม่พบคูปองนี้ หรือคูปองถูกปิดใช้งาน";
            $coupon_message_type = "danger";
        }
        $stmt->close();
    }
}

// ลบคูปองที่ใช้
if (isset($_POST['remove_coupon'])) {
    unset($_SESSION['applied_coupon']);
    $coupon_message = "ลบคูปองเรียบร้อยแล้ว";
    $coupon_message_type = "info";
}

// ลบสินค้าออกจากตะกร้า
if (isset($_POST['remove_item'])) {
    $index = $_POST['item_index'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

// ล้างตะกร้า
if (isset($_POST['clear_cart'])) {
    unset($_SESSION['cart']);
    unset($_SESSION['applied_coupon']);
}

// ชำระเงิน
if (isset($_POST['checkout'])) {
    if (!isset($_SESSION['user_email'])) {
        echo "<script>showModal('Error: No email found! Please login.');</script>";
    } else {
        // คำนวณยอดรวมและรวบรวมรายการสินค้า
        $total_price = 0;
        $items_list = array();
        
        foreach ($_SESSION['cart'] as $item) {
            $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
            $total_price += $item['price'] * $quantity;
            $items_list[] = $item['name'] . " (x" . $quantity . ")";
            
            // อัพเดตจำนวนสินค้าในฐานข้อมูล
            $update_sql = "UPDATE productlist SET quantity = quantity - ? WHERE name = ? AND quantity >= ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("isi", $quantity, $item['name'], $quantity);
            $stmt->execute();
            $stmt->close();
        }

        // หักส่วนลดถ้ามี
        $discount = 0;
        if (isset($_SESSION['applied_coupon'])) {
            $discount = $_SESSION['applied_coupon']['discount_amount'];
            $total_price -= $discount;

            // อัปเดต current_usage ของคูปอง
            $coupon_id = $_SESSION['applied_coupon']['coupon_id'];
            $update_coupon_sql = "UPDATE coupons SET current_usage = current_usage + 1 WHERE coupon_id = ?";
            $stmt = $conn->prepare($update_coupon_sql);
            $stmt->bind_param("i", $coupon_id);
            $stmt->execute();
            $stmt->close();
        }

        // สร้าง order reference
        $order_reference = 'ORD-' . date('ymd') . '-' . rand(1000, 9999);
        
        // บันทึกข้อมูลการสั่งซื้อ
        $username = $_SESSION['username'] ?? 'Guest';
        $items_string = implode(", ", $items_list);
  
        $insert_sql = "INSERT INTO orderhistory (order_reference, username, email, item, total_price, discount) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ssssdd", $order_reference, $username, $user_email, $items_string, $total_price, $discount);
        
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
                if ($discount > 0) {
                    $emailBody .= "<p>Discount Applied: $" . number_format($discount, 2) . "</p>";
                }
                $emailBody .= "<p>Here are the details:</p><ul>";
                
                foreach ($items_list as $item) {
                    $emailBody .= "<li>" . $item . "</li>";
                }
                
                $emailBody .= "</ul><p><strong>Total: $" . number_format($total_price, 2) . "</strong></p>";

                $mail->Body = $emailBody;
                $mail->send();

                echo "<script>showModal('Order placed successfully! Order Reference: " . $order_reference . "\\nA confirmation email has been sent.');</script>";
                unset($_SESSION['cart']);
                unset($_SESSION['applied_coupon']);
            } catch (Exception $e) {
                echo "<script>showModal('Order placed successfully! Order Reference: " . $order_reference . "\\nBut email could not be sent.');</script>";
                unset($_SESSION['cart']);
                unset($_SESSION['applied_coupon']);
            }
        } else {
            echo "<script>showModal('Error placing order. Please try again.');</script>";
        }
        
        $stmt->close();
    }
}

$conn->close();
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
                    $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
                    $item_total = $item['price'] * $quantity;
                    $total += $item_total;
                ?>
                    <div class="cart-item">
                        <div>
                            <h3><?php echo $item['name']; ?> (x<?php echo $quantity; ?>)</h3>
                            <p>Price: $<?php echo number_format($item_total, 2); ?></p>
                        </div>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="item_index" value="<?php echo $index; ?>">
                            <button type="submit" name="remove_item" class="btn btn-remove">Remove</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- ช่องกรอกคูปอง -->
            <div class="coupon-section">
                <form method="POST" class="coupon-form">
                    <div class="form-group">
                        <label for="coupon_code">Have a coupon code?</label>
                        <input type="text" name="coupon_code" id="coupon_code" placeholder="Enter coupon code" value="<?php echo isset($_SESSION['applied_coupon']) ? $_SESSION['applied_coupon']['coupon_code'] : ''; ?>">
                        <?php if (isset($_SESSION['applied_coupon'])): ?>
                            <button type="submit" name="remove_coupon" class="btn btn-remove-coupon">Remove Coupon</button>
                        <?php else: ?>
                            <button type="submit" name="apply_coupon" class="btn btn-apply-coupon">Apply Coupon</button>
                        <?php endif; ?>
                    </div>
                </form>
                <?php if (!empty($coupon_message)): ?>
                    <div class="alert alert-<?php echo $coupon_message_type; ?> alert-dismissible fade show">
                        <?php echo $coupon_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- แสดงยอดรวม -->
            <div class="cart-total">
                <p style="margin: 0;"><strong>Subtotal: $<?php echo number_format($total, 2); ?></strong></p>
                <?php if (isset($_SESSION['applied_coupon'])): ?>
                    <p style="margin: 0;">Discount: -$<?php echo number_format($_SESSION['applied_coupon']['discount_amount'], 2); ?></p>
                    <p style="margin: 0;"><strong>Total: $<?php echo number_format($total - $_SESSION['applied_coupon']['discount_amount'], 2); ?></strong></p>
                <?php else: ?>
                    <p style="margin: 0;"><strong>Total: $<?php echo number_format($total, 2); ?></strong></p>
                <?php endif; ?>
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