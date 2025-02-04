<?php
session_start();
session_destroy(); // ทำลาย session
session_write_close(); // ปิดการเขียน session
header('Location: login.php');
exit();
?>