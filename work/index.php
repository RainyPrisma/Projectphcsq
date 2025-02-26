<?php 

    session_start();
    include('config.php'); 
    echo "<body style='background-color:white'>";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dbbookart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="index1.css">
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="index.js"></script>
    <link rel="stylesheet" href="index.js">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
</head>
<body>
    <nav>
        <div class="navbar navbar-expand-sm bg-dark navbar-dark">
            <div class="container">
            <a href="index.php" class="navbar-brand"><img src="imgs/Designer-removebg-preview.png" alt="" width="90px" height="90px"></a>
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbar1">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div id="navbar1" class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="gallary.html" class="nav-link">Gallary</a>
                    </li>
                    <li class="nav-item">   
                        <a href="aboutus.html" class="nav-link">Aboutus</a>
                    </li>
                    <li class="nav-item">   
                    
                    </li>
                </ul>
                
            </p>
                <div class="col-md-3">
                            <?php if (isset($_SESSION['user_login'])) { ?>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                        <?php } else { ?>
                    <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
                    <a href="register.php" class="btn btn-primary">Sign-up</a>
                <?php } ?>
                </div>
                
                
                <div class="nav-container">
                    <div class="nav-profile">
                        <p class="nav-profile-name">Cart</p>
                        <div onclick="openCart()" style="cursor: pointer;" class="nav-profile-cart">
                            <i class="fas fa-cart-shopping"></i>
                            <div id="cartcount" class="cartcount" style="display: none;">
                                0
                            </div>
                        </div>
                    </div>
                </div>

                 </div>
            </div>
        </div>  
         <div style="color:white;"> 
            <?php 

                    if (isset($_SESSION['user_login'])) {
                        $userId = $_SESSION['user_login'];

                        // Prepare and execute the SQL query
                        try {
                            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                            $stmt->execute([$userId]);

                            // Fetch and display the data
                            while ($row = $stmt->fetch()) {
                                echo "User ID: " . $row['id'] . "<br>";
                                echo "Email: " . $row['email'] . "<br>";
                            
                            // Display other desired data from the database
                            }
                        } catch (PDOException $e) {
                            // Handle any errors
                            echo "Error: " . $e->getMessage();
                        }
                    }
                    ?>       
        </div>
    </nav>
   
    <br>
    <div class="container">
        <div class="sidebar">
            <input onkeyup="searchsomething(this)" id="txt_search" type="text" class="sidebar-search" placeholder="Search something...">
            
            <a onclick="searchproduct('all')" class="sidebar-items">
                All product
            </a>
            <a onclick="searchproduct('Adventures')" class="sidebar-items">
                Adventures
            </a>
            <a onclick="searchproduct('Story')" class="sidebar-items">
                Story
            </a>
            <a onclick="searchproduct('Anime')" class="sidebar-items">
                Anime
            </a>
            
        </div>
        <div id="productlist" class="product">          
        </div>
    </div>
    <div id="modalDesc" class="modal" style="display: none;">
        <div onclick="closeModal()" class="modal-bg"></div>
        <div class="modal-page">
            <h2>Detail</h2>
            <br>
            <div class="modaldesc-content">
                <img id="mdd-img" class="modaldesc-img" src="imgs/1653718795081.jpg" alt="">
                <div class="modaldesc-detail">
                    <p id="mdd-name" style="font-size: 1.5vw;">Product name</p>
                    <p id="mdd-price" style="font-size: 1.2vw;">THB</p>
                    <br>
                    <p id="mdd-desc" style="color: #adadad;">Lorem iaudantium harum doloremque alias. Quae, vel ipsum quasi, voluptas, sit optio nemo magni numquam non dicta voluptates porro! Vero eveniet numquam sit aut vel eligendi officiis iste tenetur expedita. Delectus vero quibusdam adipisci in. Esse.</p>
                    <br>
                    <div class="btn-control">
                        <button onclick="closeModal()" class="btn">
                            Close
                        </button>
                        <button onclick="addtocart()" class="btn btn-buy">
                            Add to cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<br>
    <div id="modalCart" class="modal" style="display: none;">
        <div onclick="closeModal()" class="modal-bg"></div>
        <div class="modal-page">
            <h2>My Cart</h2>
            <br>
            <div id="mycart" class="cartlist">              
            </div>
            <div class="btn-control">
                <button onclick="closeModal()" class="btn">
                    Cancel
                </button>
                <button onclick="buynow()" class="btn btn-buy">
                    Buy
                </button>
            </div>
        </div>
    </div>
    


    <!-- Site footer -->
    <footer class="site-footer">
      <div class="container">
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <h6>About</h6>
            <p class="text-justify">dbbook.com <i>The Book </i> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quam laudantium odio quasi odit. Animi dolore eum totam. Dicta atque molestias iure quibusdam reiciendis a magnam cum facilis dolorem, quas qui?</p>
          </div>

          <div class="col-xs-6 col-md-3">
            <h6>Creator </h6>
            <ul class="footer-links">
              <li><a href="#">Tanapon</a></li>
              <li><a href="#">Petcharat</a></li>
            </ul>
          </div>

          <div class="col-xs-6 col-md-3">
            <h6>Quick Links</h6>
            <ul class="footer-links">
              <li><a href="aboutus.html">About Us</a></li>
              <li><a href="https://www.facebook.com/inrefresh.Ptcr">Contact Us</a></li>
            </ul>
          </div>
        </div>
        <hr>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-md-8 col-sm-6 col-xs-12">
            <p class="copyright-text">Copyright &copy; 2024 Design By 
         <a href="#">TheBook</a>.
            </p>
          </div>

          <!-- <div class="col-md-4 col-sm-6 col-xs-12">
            <ul class="social-icons">
              <li><a class="facebook" href="#"><i class="fa fa-facebook"></i></a></li>
              <li><a class="twitter" href="#"><i class="fa fa-twitter"></i></a></li>
              <li><a class="dribbble" href="#"><i class="fa fa-dribbble"></i></a></li>
              <li><a class="linkedin" href="#"><i class="fa fa-linkedin"></i></a></li>   
            </ul>
          </div> -->
        </div>
      </div>
</footer>
			
   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>
