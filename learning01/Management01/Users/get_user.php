<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
}

$mysqli = new mysqli('localhost', 'root', '1234', 'management01');

if ($mysqli->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Connection failed: ' . $mysqli->connect_error
    ]));
}

try {
    $email = $_SESSION['user_email'];  // เปลี่ยนจาก 'email' เป็น 'user_email'
    
    $stmt = $mysqli->prepare('SELECT username, full_name, address, city, state, zip_code, country, gender, phone_number, email FROM user_details WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        echo json_encode([
            'success' => true,
            'data' => $user
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$mysqli->close();