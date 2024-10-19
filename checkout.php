<?php
session_start(); // Start the session

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit(); // Exit the script to prevent further execution
}

include 'config.php'; // Include the database connection

// Check if there are items in the cart
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<div class='container mt-5 text-center'>";
    echo "<h3>Your cart is empty!</h3>";
    echo '<br><a href="profile.php" class="btn btn-primary">Back to Products</a>';
    echo "</div>";
    exit();
}

// Initialize variables
$user_id = $_SESSION['user_id'];
$order_items = $_SESSION['cart'];
$total_price = 0;
$success = true;

// Start the HTML output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container mt-5">
    <h2 class="mb-4 text-center">Checkout Summary</h2>

    <?php
    // Begin order placement
    $conn->begin_transaction();

    try {
        // Insert each product into the Orders table
        $order_sql = "INSERT INTO Orders (user_id, total_price, product_id, quantity, status) VALUES (?, ?, ?, ?, 'pending')";
        $order_stmt = $conn->prepare($order_sql);

        foreach ($order_items as $product_id => $quantity) {
            // Get the price of the product
            $product_sql = "SELECT price FROM Products WHERE id = ?";
            $product_stmt = $conn->prepare($product_sql);
            $product_stmt->bind_param("i", $product_id);
            $product_stmt->execute();
            $product_stmt->bind_result($price);
            $product_stmt->fetch();
            $product_stmt->close();

            if (!$price) {
                $success = false;
                throw new Exception("Failed to fetch product details.");
            }

            // Calculate the total price
            $total_price += $price * $quantity;

            // Insert into Orders
            $order_stmt->bind_param("iidi", $user_id, $total_price, $product_id, $quantity);
            $order_stmt->execute();
        }

        // Commit the transaction if everything is successful
        $conn->commit();
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();
        $success = false;
    }

    // Close the prepared statement
    $order_stmt->close();

    // If order was successful, clear the cart
    if ($success) {
        unset($_SESSION['cart']);
        echo "<div class='alert alert-success text-center'>";
        echo "<h4>Order placed successfully!</h4>";
        echo "<p>Total Price: $" . number_format($total_price, 2) . "</p>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-danger text-center'>";
        echo "<h4>Failed to place the order. Please try again later.</h4>";
        echo "</div>";
    }

    $conn->close();
    ?>

    <div class="text-center mt-4">
        <a href="profile.php" class="btn btn-primary">Back to Products</a>
    </div>
</div>
<?php include 'footer.php'; ?>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
