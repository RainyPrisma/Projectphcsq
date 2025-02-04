<?php
session_start();

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
        $_SESSION['user_email'] = $user_data['email'];
        $_SESSION['user_name'] = $user_data['username'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./View/index.css?v=1.1">
    <link rel="icon" href="https://i.pinimg.com/736x/0e/20/49/0e204916ebb9f86ee7f5cfc7433b91c0.jpg" type="image/png">
    <title>Dashboard</title>
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <div class="logo">
                <img src="https://i.pinimg.com/474x/18/d2/9b/18d29b964ba470502134e2d1cc0dcc35.jpg" alt="Logo">
            </div>
            <h2>Dashboard</h2>
            <ul>
                <li><a href="index.php">Home 🏠</a></li>
                <li><a href="Model/gallery.php">Products 📂</a></li>
                <li><a href="Model/contactus.php">Contact Us ☎️</a></li>
                <li><a href="Model/management.php">Management 👨‍🔧</a></li>
            </ul>
            <div class="account-section">
                <a href="Account.php" class="account-button">Account</a>
                <a href="logout.php" class="logout-button">Logout</a>
            </div>
        </nav>
        <div class="content">
            <h1>Welcome to Our Website</h1>
            <!--
            <div class="large-gallery-item">
                <a href="Gallery/gallery.php"></a>
                <img src="https://steamuserimages-a.akamaihd.net/ugc/718666459874519529/E1D82AC76FD978EEBB612975A8E2A8F9E7FFE77F/?imw=5000&imh=5000&ima=fit&impolicy=Letterbox&imcolor=%23000000&letterbox=false" alt="Large Image 1">
                        <div class="gallery-caption">
                            <h3>Anime Scene</h3>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Distinctio ducimus dignissimos voluptatem esse vel nulla impedit cumque mollitia tempora, perspiciatis qui! Fuga quam labore omnis voluptate ad culpa maxime dignissimos?</p>
                        </div>
            </div>
            <div class="large-gallery-item">
                <a href="Categories/Fish.php"></a>
                <img src="https://steamuserimages-a.akamaihd.net/ugc/718666459874519529/E1D82AC76FD978EEBB612975A8E2A8F9E7FFE77F/?imw=5000&imh=5000&ima=fit&impolicy=Letterbox&imcolor=%23000000&letterbox=false" alt="Large Image 1">
                        <div class="gallery-caption">
                            <h3>Anime Scene</h3>
                            <p>Beautiful anime landscape with cherry blossoms depicting a peaceful Japanese countryside.</p>
                        </div>
            </div>
            <div class="large-gallery-item">
                <a href="Categories/Occt.php"></a>
                <img src="https://steamuserimages-a.akamaihd.net/ugc/718666459874519529/E1D82AC76FD978EEBB612975A8E2A8F9E7FFE77F/?imw=5000&imh=5000&ima=fit&impolicy=Letterbox&imcolor=%23000000&letterbox=false" alt="Large Image 1">
                        <div class="gallery-caption">
                            <h3>Anime Scene</h3>
                            <p>Beautiful anime landscape with cherry blossoms depicting a peaceful Japanese countryside.</p>
                        </div>
            </div>
            <div class="large-gallery-item">
                <div>
                <a href="Categories/Shell.php"></a>
                <img src="https://steamuserimages-a.akamaihd.net/ugc/718666459874519529/E1D82AC76FD978EEBB612975A8E2A8F9E7FFE77F/?imw=5000&imh=5000&ima=fit&impolicy=Letterbox&imcolor=%23000000&letterbox=false" alt="Large Image 1">
                        <div class="gallery-caption">
                            <h3>Anime Scene</h3>
                            <p>Beautiful anime landscape with cherry blossoms depicting a peaceful Japanese countryside.</p>
                        </div>
            </div>
            -->
        </div>
    </div>
</body>
</html>