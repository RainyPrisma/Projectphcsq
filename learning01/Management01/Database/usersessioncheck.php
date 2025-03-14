<?php
require_once dirname(__DIR__) . '../Assets/src/UserCookieManager.php';
// เรียกใช้คลาส UserCookieManager

use src\UserCookieManager;

$cookieManager = new UserCookieManager();

if (!isset($_SESSION['user_email'])) {
   header('Location: login.php');
   exit();
} else {
   $user_email = $_SESSION['user_email'];
   $sql = "SELECT * FROM users WHERE email = ?";
   $stmt = $conn->prepare($sql);
   $stmt->bind_param("s", $user_email);
   $stmt->execute();
   $result = $stmt->get_result();
   
   if ($result->num_rows > 0) {
       $user_data = $result->fetch_assoc();
       
       // บันทึกข้อมูลลง Cookie
       $cookieData = [
           'user_id' => $user_data['id'],
           'username' => $user_data['username'],
           'email' => $user_data['email'],
           'last_login' => date('Y-m-d H:i:s')
       ];
       $cookieManager->setUserCookie($cookieData);
       
       // บันทึกข้อมูลลง Session
       $_SESSION['user_email'] = $user_data['email'];
       $_SESSION['user_name'] = $user_data['username'];
   }
}

// ตรวจสอบ Session Timeout
$session_timeout = 1800; // 30 นาที
if (!isset($_SESSION['last_activity']) || (time() - $_SESSION['last_activity']) > $session_timeout) {
   session_unset();
   session_destroy();
   header("Location: ../Frontend/login.php");
   exit;
}
?>