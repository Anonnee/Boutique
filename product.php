<?php
session_start(); // Start the session

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit(); // Exit the script to prevent further execution
}

include 'config.php'; // Include the database connection

// Handle Delete Action
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM Products WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $delete_id);
    $delete_stmt->execute();
    $delete_stmt->close();
    header("Location: profile.php"); // Redirect to admin dashboard after deletion
    exit();
}

// Fetch product-specific information
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Handle Update Action
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $image_url = $_POST['image_url'];
        $category = $_POST['category'];

        $update_sql = "UPDATE Products SET name = ?, description = ?, price = ?, image_url = ?, category = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssdssi", $name, $description, $price, $image_url, $category, $product_id);
        $update_stmt->execute();
        $update_stmt->close();
        header("Location: product.php?id=$product_id"); // Refresh the product page after update
        exit();
    }

    // Fetch product details
    $sql = "SELECT name, description, price, image_url, category FROM Products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($name, $description, $price, $image_url, $category);
    $stmt->fetch();
    $stmt->close();
} else {
    echo "Product not found!";
    exit();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
    <!-- Bootstrap v5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Update Product: <?php echo htmlspecialchars($name); ?></h2>

    <!-- Update Product Form -->
    <form method="POST" action="">
        <div class="mb-3">
            <label for="name" class="form-label">Product Name:</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description:</label>
            <textarea id="description" name="description" class="form-control" required><?php echo htmlspecialchars($description); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price:</label>
            <input type="number" id="price" name="price" class="form-control" value="<?php echo htmlspecialchars($price); ?>" step="0.01" required>
        </div>
        <div class="mb-3">
            <label for="image_url" class="form-label">Image URL:</label>
            <input type="text" id="image_url" name="image_url" class="form-control" value="<?php echo htmlspecialchars($image_url); ?>" required>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Category:</label>
            <input type="text" id="category" name="category" class="form-control" value="<?php echo htmlspecialchars($category); ?>" required>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success">Update Product</button>
        </div>
    </form>

    <!-- Actions Links -->
    <div class="mt-3">
        <a href="profile.php" class="btn btn-primary">Back to Admin Dashboard</a>
        <a href="product.php?delete_id=<?php echo $product_id; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete Product</a>
    </div>
</div>

<!-- Bootstrap v5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'; ?>

</body>
</html>
