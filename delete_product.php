<?php
include 'config.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['id'];

    // Prepare and execute delete query
    $sql = "DELETE FROM Products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete product!']);
    }

    $stmt->close();
    $conn->close();
}