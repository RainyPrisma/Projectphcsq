<?php
// Database connection
$servername = "localhost";
$username = "root"; // your database username
$password = "1234"; // your database password
$dbname = "management01"; // your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update user data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = 1; // This should be dynamically set based on the logged-in user
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $updateSql = "UPDATE users SET username='$username', email='$email', phone_number='$phone_number', password='$hashed_password' WHERE id=$userId";
    if ($conn->query($updateSql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();

// Redirect back to the account page
header("Location: ../Frontend/index.php");
exit();
?>