<?php
if (!isset($_SESSION['user_email'])) {
    header('Location: ../Frontend/login.php');
    exit();
}

// Get user's email from session
$user_email = $_SESSION['user_email'];

// At the top of your PHP script
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'date_desc';

// Define the SQL query based on the sort option
switch($sort_by) {
    case 'date_asc':
        $sql = "SELECT * FROM orderhistory WHERE email = ? ORDER BY created_at ASC";
        break;
    case 'price_desc':
        $sql = "SELECT * FROM orderhistory WHERE email = ? ORDER BY total_price DESC";
        break;
    case 'price_asc':
        $sql = "SELECT * FROM orderhistory WHERE email = ? ORDER BY total_price ASC";
        break;
    default:
        $sql = "SELECT * FROM orderhistory WHERE email = ? ORDER BY created_at DESC";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

// Count total orders
$order_count = $result->num_rows;
?>