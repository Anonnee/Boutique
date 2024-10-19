<?php
session_start(); // Start the session

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty.</p>";
    exit();
}

include 'config.php'; // Include the database connection

$product_ids = implode(',', array_keys($_SESSION['cart']));
$sql = "SELECT id, name, price FROM Products WHERE id IN ($product_ids)";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<ul class='list-group'>";
    while ($product = $result->fetch_assoc()) {
        $quantity = $_SESSION['cart'][$product['id']];
        $total_price = $product['price'] * $quantity;

        echo "<li class='list-group-item'>";
        echo htmlspecialchars($product['name']) . " - Quantity: $quantity - Total: $" . number_format($total_price, 2);
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Your cart is empty.</p>";
}