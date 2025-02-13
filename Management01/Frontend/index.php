<?php
session_start();
require_once dirname(__DIR__) . '../Assets/src/UserCookieManager.php';
 // เรียกใช้คลาส UserCookieManager

use src\UserCookieManager;

$cookieManager = new UserCookieManager();
// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "1234", "management01");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
} else {
    $user_email = $_SESSION['user_email'];
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        
        // บันทึกข้อมูลลง Cookie
        $cookieData = [
            'user_id' => $user_data['id'],
            'username' => $user_data['username'],
            'email' => $user_data['email'],
            'last_login' => date('Y-m-d H:i:s')
        ];
        $cookieManager->setUserCookie($cookieData);
        
        // บันทึกข้อมูลลง Session
        $_SESSION['user_email'] = $user_data['email'];
        $_SESSION['user_name'] = $user_data['username'];
    }
}

// ตรวจสอบ Session Timeout
$session_timeout = 1800; // 30 นาที
if (!isset($_SESSION['last_activity']) || (time() - $_SESSION['last_activity']) > $session_timeout) {
    session_unset();
    session_destroy();
    header("Location: ../Frontend/login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarineHomepage</title>
    <link rel="icon" href="https://customseafoods.com/cdn/shop/files/CS_Logo_2_1000.webp?v=1683664967" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/CSS/index.css">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-ocean sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="https://customseafoods.com/cdn/shop/files/CS_Logo_2_1000.webp?v=1683664967" alt="Logo" class="me-2">
                <span>Marine Seafood Hub</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-house-fill"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gallery.php">
                            <i class="bi bi-collection-fill"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../Frontend/contactus.php">
                            <i class="bi bi-telephone-fill"></i> Contact Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../Backend/management.php">
                            <i class="bi bi-gear-fill"></i> Management
                        </a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="account.php" class="btn btn-outline-light me-2">
                        <i class="bi bi-person-circle"></i> Account
                    </a>
                    <a href="logout.php" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>
        <!-- Hero Carousel -->
    <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="../Assets/images/fish.jpg" class="d-block w-100" alt="Welcome slide">
                <div class="carousel-caption">
                    <h1>Welcome to Marine Seafood Hub</h1>
                    <p class="fs-4">Your Premier Destination for Marine Industry Excellence</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="../Assets/images/banner3.jpg" class="d-block w-100" alt="Sustainable slide">
                <div class="carousel-caption">
                    <h1>Sustainable Seafood Solutions</h1>
                    <p class="fs-4">Leading the Way in Responsible Marine Resource Management</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="../Assets/images/banner2.jpg" class="d-block w-100" alt="Global slide">
                <div class="carousel-caption">
                    <h1>Global Marine Network</h1>
                    <p class="fs-4">Connecting Businesses Across the Seven Seas</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
    <!-- Main Content -->
    <div class="container py-4">
        <!-- Stats Section -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card h-100 stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-graph-up fs-1 text-primary me-3"></i>
                            <h3>Market Growth</h3>
                        </div>
                        <p class="card-text fs-5">Global marine market value: $450B</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-box-seam fs-1 text-primary me-3"></i>
                            <h3>Shipping Volume</h3>
                        </div>
                        <p class="card-text fs-5">90% of global trade by sea</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-people-fill fs-1 text-primary me-3"></i>
                            <h3>Employment</h3>
                        </div>
                        <p class="card-text fs-5">Over 1.5M marine industry jobs</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="wave-divider"></div>

        <!-- Market Insights Section -->
        <div class="row mb-4">
            <div class="col">
                <div class="card bg-light">
                    <div class="card-body">
                        <h2 class="card-title mb-3">
                            <i class="bi bi-lightning-fill text-warning"></i> Market Insights
                        </h2>
                        <p class="card-text fs-5">
                            The marine industry is experiencing rapid digital transformation, with significant investments in sustainable technologies and smart shipping solutions. Key growth areas include renewable marine energy, aquaculture, and eco-friendly shipping technologies.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Sectors Section -->
        <h2 class="mb-4 text-center">Key Business Sectors</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 ecosystem-card">
                    <div class="card-body text-center">
                        <i class="bi bi-lightbulb fs-1 mb-3 text-warning"></i>
                        <h3>Maritime Logistics</h3>
                        <p class="card-text">Global shipping and port operations</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 ecosystem-card">
                    <div class="card-body text-center">
                        <i class="bi bi-water fs-1 mb-3 text-success"></i>
                        <h3>Aquaculture</h3>
                        <p class="card-text">Sustainable seafood production</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 ecosystem-card">
                    <div class="card-body text-center">
                        <i class="bi bi-gear-wide-connected fs-1 mb-3 text-info"></i>
                        <h3>Marine Technology</h3>
                        <p class="card-text">Innovation in marine equipment</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>