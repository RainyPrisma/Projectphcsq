<?php 
session_start();
require '../Database/config.php';

// ถ้ามีการล็อกอินแล้ว ให้ redirect ไปหน้า index.php
if(isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_or_phone = $_POST['email_or_phone'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ? OR phone_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email_or_phone, $email_or_phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id']; // Make sure to store user_id in session
            $_SESSION['phone_number'] = $user['phone_number'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();
            
            // Get client IP address
            function getClientIP() {
               // ถ้ามี HTTP_X_FORWARDED_FOR ให้ใช้ IP แรกที่พบ
               if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                  $ipAddresses = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                  return trim($ipAddresses[0]);
               }
               // ถ้าไม่มีให้ใช้ REMOTE_ADDR
               else if (isset($_SERVER['REMOTE_ADDR'])) {
                  return $_SERVER['REMOTE_ADDR'];
               }
               
               return 'Unknown';
            }

            $ip_address = getClientIP();

            // Validate IP address
            if (filter_var($ip_address, FILTER_VALIDATE_IP)) {
               // Insert into database
               $login_sql = "INSERT INTO login_logs (user_id, username, email, login_time, ip_address) VALUES (?, ?, ?, NOW(), ?)";
               $login_stmt = $conn->prepare($login_sql);
               $login_stmt->bind_param("isss", $user['id'], $user['username'], $user['email'], $ip_address);
               $login_stmt->execute();
            } else {
               // ถ้า IP ไม่ถูกต้อง ให้บันทึกเป็น Unknown
               $ip_address = 'Unknown';
               $login_sql = "INSERT INTO login_logs (user_id, username, email, login_time, ip_address) VALUES (?, ?, ?, NOW(), ?)";
               $login_stmt = $conn->prepare($login_sql);
               $login_stmt->bind_param("isss", $user['id'], $user['username'], $user['email'], $ip_address);
               $login_stmt->execute();
            }
                                    
            // ทุก role ไปที่หน้า index.php
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('รหัสผ่านไม่ถูกต้อง');</script>";
        }
    } else {
        echo "<script>alert('ไม่พบผู้ใช้งาน');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management website</title>
    <link rel="stylesheet" href="../Assets/CSS/login.css">
    <script src="../Assets/JS/disable-autocomplete.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <div class="bg-img">
        <div class="content">
           <header>Login</header>
           <form action="" method="POST">
              <div class="field">
                 <span class="fa fa-user"></span>
                 <input type="text" name="email_or_phone" onkeydown="return noSpecialChars(event)" required placeholder="Email Account">
              </div>
              <div class="field space">
                 <span class="fa fa-lock"></span>
                 <input type="password" name="password" class="pass-key" required placeholder="Password">
                 <span class="show">SHOW</span>
              </div>
              <div class="pass">
                 <a href="../Backend/forgot_password.php">Forgot Password?</a>
              </div>
              <div class="field">
                 <input type="submit" value="LOGIN">
              </div>
           </form>
           <!--<div class="login">
              Or login with
           </div>
           <div class="links">
              <div class="facebook">
                 <i class="fab fa-facebook-f"><span>Facebook</span></i>
              </div>
              <div class="instagram">
                 <i class="fab fa-instagram"><span>Instagram</span></i>
              </div>
           </div>-->
           <p></p>
           <div class="signup">
              Don't have account?
              <a href="register.html">Signup Now</a>
           </div>
        </div>
     </div>
     <script>
        const pass_field = document.querySelector('.pass-key');
        const showBtn = document.querySelector('.show');
        showBtn.addEventListener('click', function(){
         if(pass_field.type === "password"){
           pass_field.type = "text";
           showBtn.textContent = "HIDE";
           showBtn.style.color = "#3498db";
         }else{
           pass_field.type = "password";
           showBtn.textContent = "SHOW";
           showBtn.style.color = "#222";
         }
        });
     </script>
      <script>
         function noSpecialChars(event) {
         const regex = /[!#$%^&*(),?":{}|<>]/; // อักษรพิเศษที่ต้องการบล็อค
         if (regex.test(event.key)) {
            return false; // บล็อคการพิมพ์
         }
         }
      </script>
</body>
</html>