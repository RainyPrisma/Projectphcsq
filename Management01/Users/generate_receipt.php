<?php
session_start();

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_email'])) {
    header('Location: ../Frontend/login.php');
    exit();
}

require_once "../Backend/productreq.php";
require '../../vendor/autoload.php';

use Mpdf\Mpdf;
$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'default_font' => 'sarabun',
    'default_font_size' => 14,
    'fontDir' => [__DIR__ . '/../../vendor/mpdf/mpdf/ttfonts'],
    'fontdata' => [
        'sarabun' => [
            'R' => 'THSarabunNew.ttf'
        ]
    ]
]);

// Register font
$mpdf->AddFont('sarabun');

if (!isset($_GET['id'])) {
    die('Order ID not provided');
}

$order_reference = $_GET['id'];
$user_email = $_SESSION['user_email'];

// ตรวจสอบว่า order นี้เป็นของ user คนนี้จริงๆ
$sql = "SELECT * FROM orderhistory WHERE order_reference = ? AND email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $order_reference, $user_email);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    die('Order not found or unauthorized access');
}

// โหลด CSS file
$stylesheet = file_get_contents('../Assets/CSS/receipt_style.css');
$mpdf->WriteHTML($stylesheet, 1);

// แยกรายการสินค้า (ถ้ามีหลายรายการ)
$items = explode(",", $order['item']);

// สร้าง HTML สำหรับรายการสินค้า
$items_html = '';
foreach ($items as $item) {
    $items_html .= '<tr>
        <td>' . trim($item) . '</td>
        <td class="text-right">' . number_format($order['total_price']) . ' ฿</td>
    </tr>';
}

// สร้าง HTML สำหรับใบเสร็จ
$html = '
<div class="company-info">
    <h2>Custom Seafoods</h2>
    <p>123 ถนนตัวอย่าง, เขตตัวอย่าง, กรุงเทพฯ 10xxx</p>
    <p>โทร: 02-xxx-xxxx | อีเมล: info@customseafoods.com</p>
    <p>เลขประจำตัวผู้เสียภาษี: xxxxxxxxxxxxx</p>
</div>

<div class="header">
    <h1>ใบเสร็จรับเงิน / RECEIPT</h1>
    <p class="receipt-number">เลขที่: ' . $order['order_reference'] . '</p>
</div>

<div class="order-details">
    <p>วันที่: ' . $order['created_at'] . '</p>
    <p>ชื่อลูกค้า: ' . $order['email'] . '</p>
</div>

<table class="items">
    <tr>
        <th>รายการสินค้า</th>
        <th style="text-align: right;">ราคา</th>
    </tr>
    ' . $items_html . '
</table>

<div class="total">
    <h3>ยอดรวมทั้งสิ้น: ' . number_format($order['total_price'], 2) . ' บาท</h3>
</div>

<div class="signature">
    <div class="signature-line"></div>
    <p class="signature-text">ผู้รับเงิน / Cashier</p>
</div>
';

$mpdf->WriteHTML($html);

// สร้างชื่อไฟล์
$filename = 'receipt_' . $order['order_reference'] . '.pdf';

// ส่ง PDF ให้ดาวน์โหลด
$mpdf->Output($filename, 'D');

$stmt->close();
$conn->close();
?>