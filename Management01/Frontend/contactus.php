<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - Ocean Theme</title>
  <link rel="stylesheet" href="../Assets/CSS/contactus.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
  <div class="ocean-background">
    <div class="waves"></div>
  </div>
  
  <a href="index.php" class="btn btn-custom">
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
      <form action="submit_form.php" method="post">
        <div class="form-group">
          <label for="name">Name</label>
          <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
          <label for="message">Message</label>
          <textarea id="message" name="message" rows="5" required></textarea>
        </div>

        <button type="submit">Send Message <i class="fas fa-paper-plane"></i></button>
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