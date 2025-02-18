<?php
// เพิ่ม header สำหรับ CORS และกำหนดชนิดข้อมูลเป็น JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // หรือระบุโดเมนที่อนุญาตให้เรียกใช้

// เชื่อมต่อฐานข้อมูล
require_once '../Database/config.php'; // ไฟล์ config สำหรับเชื่อมต่อฐานข้อมูล

// กำหนดค่าเริ่มต้นสำหรับการกรอง
$filter_is_active = isset($_GET['is_active']) ? $_GET['is_active'] : '';
$filter_email = isset($_GET['email']) ? $_GET['email'] : '';
$filter_username = isset($_GET['username']) ? $_GET['username'] : '';
$filter_date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$filter_date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// จำนวนรายการต่อหน้า
$records_per_page = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// สร้าง query พื้นฐาน
$sql_base = "FROM login_logs WHERE 1=1";
$where_conditions = [];
$params = [];
$types = "";

// เพิ่มเงื่อนไขการกรอง
if ($filter_is_active !== '') {
    $where_conditions[] = "is_active = ?";
    $params[] = $filter_is_active;
    $types .= "s";
}

if (!empty($filter_email)) {
    $where_conditions[] = "email LIKE ?";
    $params[] = "%$filter_email%";
    $types .= "s";
}

if (!empty($filter_username)) {
    $where_conditions[] = "username LIKE ?";
    $params[] = "%$filter_username%";
    $types .= "s";
}

if (!empty($filter_date_from)) {
    $where_conditions[] = "DATE(login_time) >= ?";
    $params[] = $filter_date_from;
    $types .= "s";
}

if (!empty($filter_date_to)) {
    $where_conditions[] = "DATE(login_time) <= ?";
    $params[] = $filter_date_to;
    $types .= "s";
}

// เพิ่มเงื่อนไขเข้าไปใน SQL
if (!empty($where_conditions)) {
    $sql_base .= " AND " . implode(" AND ", $where_conditions);
}

// สร้าง SQL สำหรับนับจำนวนทั้งหมด
$count_sql = "SELECT COUNT(*) as total " . $sql_base;

// สร้าง SQL สำหรับดึงข้อมูล
$sql = "SELECT * " . $sql_base . " ORDER BY login_time DESC LIMIT ?, ?";

// เพิ่มพารามิเตอร์สำหรับ LIMIT
$params_with_limit = $params;
$params_with_limit[] = $offset;
$params_with_limit[] = $records_per_page;
$types_with_limit = $types . "ii";

// ดึงจำนวนทั้งหมดสำหรับการแบ่งหน้า
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$row = $count_result->fetch_assoc();
$total_records = $row['total'];
$total_pages = ceil($total_records / $records_per_page);

// ดึงข้อมูลหลัก
$stmt = $conn->prepare($sql);
if (!empty($params_with_limit)) {
    $stmt->bind_param($types_with_limit, ...$params_with_limit);
}
$stmt->execute();
$result = $stmt->get_result();

// แปลงผลลัพธ์เป็น array
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// สร้าง response object
$response = [
    'data' => $data,
    'pagination' => [
        'total_records' => $total_records,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'records_per_page' => $records_per_page
    ],
    'filters' => [
        'username' => $filter_username,
        'email' => $filter_email,
        'is_active' => $filter_is_active,
        'date_from' => $filter_date_from,
        'date_to' => $filter_date_to
    ]
];

// ปิดการเชื่อมต่อ
$stmt->close();
$count_stmt->close();
$conn->close();

// ส่งคืนข้อมูลเป็น JSON
echo json_encode($response);
?>