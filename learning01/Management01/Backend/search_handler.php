<?php
session_start();

// Set headers first
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Check authentication
if (!isset($_SESSION['user_email']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0); // Prevent PHP errors from breaking JSON output

try {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "1234";
    $dbname = "management01";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Get search term
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    // Prepare SQL with multiple field search
    $sql = "SELECT pl.*, p.id as product_id 
            FROM productlist pl 
            JOIN product p ON pl.product_id = p.id 
            WHERE pl.name LIKE ? 
            OR pl.detail LIKE ? 
            OR pl.product_id LIKE ? 
            OR CAST(pl.price AS CHAR) LIKE ? 
            OR CAST(pl.quantity AS CHAR) LIKE ?";

    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);

    // Execute query
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    // Get results
    $result = $stmt->get_result();
    
    // Fetch all results
    $products = array();
    while ($row = $result->fetch_assoc()) {
        $products[] = array(
            'id' => $row['id'],
            'product_id' => $row['product_id'],
            'name' => htmlspecialchars($row['name']),
            'detail' => htmlspecialchars($row['detail']),
            'price' => number_format($row['price'], 2),
            'quantity' => $row['quantity'],
            'orderdate' => $row['orderdate'],
            'image_url' => htmlspecialchars($row['image_url'])
        );
    }

    // Return JSON response
    echo json_encode([
        'status' => 'success',
        'data' => $products
    ]);

} catch (Exception $e) {
    // Return error in JSON format
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

// Clean up
if (isset($stmt)) {
    $stmt->close();
}
if (isset($conn)) {
    $conn->close();
}
?>