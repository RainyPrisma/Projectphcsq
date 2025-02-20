<!--Login page-->
<?php
require_once('../Backend/functionlogin.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management website</title>
    <link rel="stylesheet" href="../Assets/CSS/login.css">
    <script src="../Assets/JS/disable-autocomplete.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

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

      <!-- Bootstrap Bundle with Popper -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>