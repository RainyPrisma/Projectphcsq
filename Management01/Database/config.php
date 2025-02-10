<?php
$host = 'localhost';
$db   = 'management01';
$user = 'root';
$pass = '1234';  // ใช้ '' (สตริงว่าง) ถ้าไม่มี password

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}
?>