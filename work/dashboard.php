<?php 

    session_start();
    include('config.php'); 

    if (!isset($_SESSION['user_login'])) {
        header("location: index.php");
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="index.css">
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="index.js"></script>
    <link rel="stylesheet" href="index.js">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>

  

<nav>
        <div class="navbar navbar-expand-sm navbar-white bg-white">
            <div class="container">
            <a href="#" class="navbar-brand"><img src="imgs/Designer-removebg-preview.png" alt="" width="90px" height="90px"></a>
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbar1">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div id="navbar1" class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="index.html" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="gallary.html" class="nav-link">Gallary</a>
                    </li>
                    <li class="nav-item">   
                        <a href="aboutus.html" class="nav-link">Aboutus</a>
                    </li>
                    <li class="nav-item">   
                        <a href="aboutus.html" class="nav-link">login</a>
                    </li>
                </ul>
                <div class="col-md-3 text-end">
                <?php if (isset($_SESSION['user_login'])) { ?>
                <a href="login.php" class="btn btn-danger">Logout</a>
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
        
    </nav>

    

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
   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>
