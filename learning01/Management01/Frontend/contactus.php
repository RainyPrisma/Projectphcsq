<?php
require_once '../Backend/submit_form.php';
// กำหนดจำนวนการส่งสูงสุดต่อวัน
$max_submissions_per_day = 3;

// ตรวจสอบว่าวันนี้มีการบันทึกการส่งแล้วหรือไม่
$today = date('Y-m-d');
if (!isset($_SESSION['contact_submissions']) || $_SESSION['contact_submissions_date'] != $today) {
    // ถ้ายังไม่มีหรือเป็นวันใหม่ ให้เริ่มนับใหม่
    $_SESSION['contact_submissions'] = 0;
    $_SESSION['contact_submissions_date'] = $today;
}

// ตรวจสอบว่าเกินจำนวนที่กำหนดหรือไม่
$submission_count = $_SESSION['contact_submissions'];
$remaining_submissions = $max_submissions_per_day - $submission_count;
$limit_reached = ($submission_count >= $max_submissions_per_day);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us</title>
  <link rel="stylesheet" href="../Assets/CSS/contactus.css">
  <link rel="icon" href="https://customseafoods.com/cdn/shop/files/CS_Logo_2_1000.webp?v=1683664967" type="image/png">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
  <div class="ocean-background">
    <div class="waves"></div>
  </div>
  
  <a href="../Frontend/<?php echo (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ? 'index.php' : 'dashboard.php'; ?>" class="btn btn-custom">
    <i class="fas fa-arrow-left"></i> Back to Home
  </a>
  
  <div class="contact-container">
    <header class="header">
      <h1><i class="fas fa-water"></i> Contact Us</h1>
      <p>Dive into conversation - we're here to help!</p>
    </header>

    <section class="contact-info">
      <div class="info-item">
        <i class="fas fa-map-marker-alt"></i>
        <h3>Address</h3>
        <p>Software Park Building 99/31 Chaeng Watthana Rd, Pak Kret District, 11120</p>
      </div>
      <div class="info-item">
        <i class="fas fa-phone"></i>
        <h3>Phone</h3>
        <p><a href="tel:+66983083185">+66 98 308 3185</a></p>
      </div>
      <div class="info-item">
        <i class="fas fa-envelope"></i>
        <h3>Email</h3>
        <p><a href="mailto:petcharat.nsm@gmail.com">petcharat.nsm@gmail.com</a></p>
      </div>
    </section>

    <section class="contact-form">
      <h2><i class="fas fa-paper-plane"></i> Send Us a Message</h2>
      
      <?php if ($limit_reached): ?>
      <div class="rate-limit-info danger">
        <i class="fas fa-exclamation-circle"></i> คุณได้ส่งข้อความครบตามจำนวนที่กำหนดแล้ว (<?php echo $max_submissions_per_day; ?> ข้อความต่อวัน) กรุณาลองใหม่ในวันพรุ่งนี้
      </div>
      <?php else: ?>
      <div class="rate-limit-info <?php echo ($remaining_submissions <= 1) ? 'warning' : ''; ?>">
        <i class="fas fa-info-circle"></i> คุณสามารถส่งข้อความได้อีก <?php echo $remaining_submissions; ?> ครั้งในวันนี้
      </div>
      <?php endif; ?>
      
      <!-- แสดงข้อความแจ้งเตือนถ้ามี -->
      <?php if (isset($_SESSION['contact_success'])): ?>
        <div class="rate-limit-info" style="border-left-color: #28a745;">
          <i class="fas fa-check-circle"></i> <?php echo $_SESSION['contact_success']; ?>
          <?php unset($_SESSION['contact_success']); ?>
        </div>
      <?php endif; ?>
      
      <?php if (isset($_SESSION['contact_error'])): ?>
        <div class="rate-limit-info danger">
          <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['contact_error']; ?>
          <?php unset($_SESSION['contact_error']); ?>
        </div>
      <?php endif; ?>
      
      <form action="../Backend/submit_form.php" method="post" <?php if ($limit_reached) echo 'onsubmit="return false;"'; ?>>
        <div class="form-group">
          <label for="name">Name</label>
          <input type="text" id="name" name="name" required <?php if ($limit_reached) echo 'disabled'; ?>>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>" readonly>
        </div>

        <div class="form-group">
          <label for="message">Message</label>
          <textarea id="message" name="message" rows="5" required <?php if ($limit_reached) echo 'disabled'; ?>></textarea>
        </div>

        <button type="submit" <?php if ($limit_reached) echo 'disabled style="opacity: 0.6; cursor: not-allowed;"'; ?>>
          Send Message <i class="fas fa-paper-plane"></i>
        </button>
      </form>
    </section>

    <section class="map">
      <h2><i class="fas fa-map-marked-alt"></i> Our Location</h2>
      <div class="map-container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3872.9120286620355!2d100.529708!3d13.9042062!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30e283629d3d57bd%3A0x2a72be2241b7c621!2sSoftware%20Park%20Thailand!5e0!3m2!1sen!2sth!4v1738052659024!5m2!1sen!2sth" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>
    </section>

    <section class="social-icons">
      <a href="#" class="social-icon"><i class="fab fa-facebook"></i></a>
      <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
      <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
      <a href="#" class="social-icon"><i class="fab fa-linkedin"></i></a>
    </section>
  </div>
</body>
</html>