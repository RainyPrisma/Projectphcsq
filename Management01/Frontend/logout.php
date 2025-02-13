<?php
session_start();
require_once dirname(__DIR__) . '../Assets/src/UserCookieManager.php';

use src\UserCookieManager;

// ล้าง cookie
$cookieManager = new UserCookieManager();
$cookieManager->clearUserCookie();

// ล้าง session
session_unset(); // ลบตัวแปรเซสชันทั้งหมด
session_destroy();
session_write_close();


header('Location: login.php');
exit();
?>