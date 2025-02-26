<div class="container">
    <header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
      <a href="index.php" class="d-flex align-items-center col-md-3 mb-2 mb-md-0 text-dark text-decoration-none">
            Website
        <div class="col-md-3 text-end">
          <?php if (isset($_SESSION['user_login'])) { ?>
            <a href="logout.php" class="btn btn-danger">Logout</a>
          <?php } else { ?>
            <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
            <a href="register.php" class="btn btn-primary">Sign-up</a>
          <?php } ?>
        </div>
        
    </header>

    

    
</div>