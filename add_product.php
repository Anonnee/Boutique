<?php
session_start();
include 'config.php';

// Check if user is admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image_url = $_POST['image_url'];
    $category = $_POST['category'];

    $sql = "INSERT INTO Products (name, description, price, image_url, category) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdss", $name, $description, $price, $image_url, $category);

    if ($stmt->execute()) {
        // Redirect back to the profile page with success message
        header('Location: profile.php?message=Product added successfully');
    } else {
        header('Location: profile.php?error=Failed to add product');
    }
    
    $stmt->close();
    $conn->close();
}
?>