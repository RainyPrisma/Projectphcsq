<?php
session_start();
require 'config.php';

class AccountHandler {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getUserData($email) {
        $sql = "SELECT * FROM view_account WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return false;
    }
    
    public function updateUserData($data) {
        $sql = "UPDATE users SET 
                username = ?, 
                full_name = ?, 
                phone_number = ?, 
                address = ?, 
                city = ?, 
                state = ?, 
                zip_code = ?, 
                country = ?,
                gender = ?
                WHERE email = ?";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssssssss", 
            $data['username'], 
            $data['full_name'], 
            $data['phone_number'], 
            $data['address'], 
            $data['city'], 
            $data['state'], 
            $data['zip_code'], 
            $data['country'],
            $data['gender'],
            $data['email']
        );
        
        if ($stmt->execute()) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'];
        }
    }
}

// สำหรับการรับ AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $handler = new AccountHandler($conn);
    $result = $handler->updateUserData($_POST);
    echo json_encode($result);
    exit;
}
?>