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

    $sql = "UPDATE user_details SET 
            username = ?,
            full_name = ?,
            address = ?,
            city = ?,
            state = ?,
            zip_code = ?,
            country = ?,
            gender = ?,
            phone_number = ?
            WHERE email = ?";

    $stmt = $mysqli->prepare($sql);
    
    $stmt->bind_param('ssssssssss', 
        $_POST['username'],
        $_POST['full_name'],
        $_POST['address'],
        $_POST['city'],
        $_POST['state'],
        $_POST['zip_code'],
        $_POST['country'],
        $_POST['gender'],
        $_POST['phone_number'],
        $email
    );

    $stmt->execute();

    if ($stmt->affected_rows >= 0) {
        echo json_encode([
            'success' => true,
            'message' => 'User updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Update failed'
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