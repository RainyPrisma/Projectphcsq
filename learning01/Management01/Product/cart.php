<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../Backend/vendor/autoload.php';
include '../Frontend/modal.php';

if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_id'])) {
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
            // เพิ่มการแจ้งเตือนในตาราง notifications
            $user_id = $_SESSION['user_id'];
            $title = "สั่งซื้อสำเร็จ";
            $message = "คำสั่งซื้อ #$order_reference สำเร็จแล้ว (ยอดรวม: $" . number_format($total_price, 2) . ")";
            $type = "order";

            $query_notify = "INSERT INTO notifications (user_id, title, message, type, created_at) 
                             VALUES (?, ?, ?, ?, NOW())";
            $stmt_notify = $conn->prepare($query_notify);
            $stmt_notify->bind_param("isss", $user_id, $title, $message, $type);
            $stmt_notify->execute();
            $stmt_notify->close();

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../Assets/CSS/cart.css">
    <!-- เพิ่ม Bootstrap CSS เพื่อให้ alert และปุ่มทำงานได้ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--tempocss-->
    <style>
                body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .cart-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        .cart-header h1 {
            margin: 0;
            color: #333;
        }

        .cart-items {
            margin-bottom: 20px;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            margin-bottom: 10px;
        }

        .cart-item h3 {
            margin: 0;
            color: #333;
        }

        .cart-item p {
            margin: 5px 0;
            color: #666;
        }

        .cart-total {
            text-align: right;
            font-size: 1.2em;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .cart-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        /* ส่วนใหม่สำหรับคูปอง */
        .coupon-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #eee;
        }

        .coupon-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-grow: 1;
        }

        .form-group label {
            margin: 0;
            font-weight: bold;
            color: #333;
        }

        .form-group input {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 200px;
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .form-group input:focus {
            border-color: #2196F3;
            outline: none;
            box-shadow: 0 0 5px rgba(33, 150, 243, 0.3);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }

        .btn-continue {
            background-color: #4CAF50;
            color: white;
        }

        .btn-remove {
            background-color: #ff4444;
            color: white;
        }

        .btn-clear {
            background-color: #666;
            color: white;
        }

        .btn-checkout {
            background-color: #2196F3;
            color: white;
            font-weight: bold;
        }

        .btn-apply-coupon {
            background-color: #28a745;
            color: white;
        }

        .btn-remove-coupon {
            background-color: #dc3545;
            color: white;
        }

        .empty-cart {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .empty-cart h2 {
            color: #333;
            margin-bottom: 10px;
        }

        /* Hover effects */
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .cart-item:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s;
        }

        .btn-apply-coupon:hover {
            background-color: #218838;
        }

        .btn-remove-coupon:hover {
            background-color: #c82333;
        }

        /* Alert styles */
        .alert {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 1.2em;
            cursor: pointer;
            color: inherit;
            padding: 0 5px;
        }

        .btn-close:hover {
            color: #000;
            opacity: 0.7;
        }
    </style>
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
                            <h3><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $quantity; ?>)</h3>
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
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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

    <!-- เพิ่ม Bootstrap JavaScript เพื่อให้ alert และปุ่มปิดทำงาน -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>