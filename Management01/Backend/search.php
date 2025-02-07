<?php
include '../Database/config.php';

if(isset($_POST['search'])) {
    $search = $_POST['search'];
    
    $sql = "SELECT * FROM productdetails WHERE 
            product_name LIKE '%$search%' OR 
            detail LIKE '%$search%'";
            
    $result = $conn->query($sql);
    
    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<div class="gallery-item">';
            echo '<img src="' . $row["image_url"] . '" alt="Product Image">';
            echo '<h2>' . $row["product_name"] . '</h2>';
            echo '<p class="detail">' . $row["detail"] . '</p>';
            echo '<p>Quantity: ' . $row["quantity"] . '</p>';
            echo '<p class="price">$' . $row["price"] . '</p>';
            
            echo '<form method="POST" action="">';
            echo '<input type="hidden" name="product_name" value="' . $row["product_name"] . '">';
            echo '<input type="hidden" name="detail" value="' . $row["detail"] . '">';
            echo '<input type="hidden" name="quantity" value="' . $row["quantity"] . '">';
            echo '<input type="hidden" name="product_price" value="' . $row["price"] . '">';
            echo '<button type="submit" name="add_to_cart">Add to Cart</button>';
            echo '</form>';
            echo '</div>';
        }
    } else {
        echo '<p class="no-results">No products found.</p>';
    }
}
?>