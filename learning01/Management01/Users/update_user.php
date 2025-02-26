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
   $mysqli->begin_transaction();
   
   $email = $_SESSION['user_email'];

   // อัพเดต users table
   $sql1 = "UPDATE users SET 
           username = ?,
           phone_number = ?
           WHERE email = ?";

   $stmt1 = $mysqli->prepare($sql1);
   $stmt1->bind_param('sss',
       $_POST['username'],
       $_POST['phone_number'],
       $email
   );
   $stmt1->execute();

   // อัพเดต user_details table 
   $sql2 = "UPDATE user_details SET 
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

   $stmt2 = $mysqli->prepare($sql2);
   $stmt2->bind_param('ssssssssss',
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
   $stmt2->execute();

   // ถ้าทั้งสอง queries สำเร็จ
   if ($stmt1->affected_rows >= 0 && $stmt2->affected_rows >= 0) {
       $mysqli->commit();
       echo json_encode([
           'success' => true,
           'message' => 'Profile updated successfully'
       ]);
   } else {
       $mysqli->rollback();
       echo json_encode([
           'success' => false,
           'message' => 'Update failed'
       ]);
   }

   $stmt1->close();
   $stmt2->close();

} catch (Exception $e) {
   $mysqli->rollback();
   echo json_encode([
       'success' => false,
       'message' => $e->getMessage()
   ]);
}

$mysqli->close();