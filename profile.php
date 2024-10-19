<?php
session_start(); // Start the session

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php'; // Include the database connection

// Fetch user-specific information
$user_id = $_SESSION['user_id'];
$sql = "SELECT username, email, role FROM Users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Bind the user ID to the query
$stmt->execute();
$stmt->bind_result($username, $email, $role); // Fetch username, email, and role from the database
$stmt->fetch();
$stmt->close();

$is_admin = ($role === 'admin'); // Check if the user is admin

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Handle Add to Cart via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }

    // Return success response
    echo json_encode(['success' => true, 'message' => 'Product added to cart!']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
    <!-- Bootstrap v5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Toast styling */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <p>Email: <?php echo htmlspecialchars($email); ?></p>

    <?php if ($is_admin): ?>
        <!-- Admin Section: View, Delete, and Add Products -->
        <h3>Admin Dashboard: Manage Products</h3>
        <table class="table table-hover" id="productsTable">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $product_sql = "SELECT id, name, description, price FROM Products";
                $product_result = $conn->query($product_sql);

                if ($product_result->num_rows > 0):
                    while($product = $product_result->fetch_assoc()): ?>
                        <tr id="product-<?php echo $product['id']; ?>">
                            <td><?php echo htmlspecialchars($product['id']); ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['description']); ?></td>
                            <td>$<?php echo htmlspecialchars($product['price']); ?></td>
                            <td>
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm me-2">View</a>
                                <button class="btn btn-danger btn-sm delete-product" data-id="<?php echo $product['id']; ?>">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr>
                        <td colspan="5">No products available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php else: ?>
        <!-- Normal User Section: View Products and Add to Cart -->
        <h3>Available Products</h3>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $product_sql = "SELECT id, name, description, price FROM Products";
                $product_result = $conn->query($product_sql);

                if ($product_result->num_rows > 0):
                    while($product = $product_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['id']); ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['description']); ?></td>
                            <td>$<?php echo htmlspecialchars($product['price']); ?></td>
                            <td>
                                <button class="btn btn-dark btn-sm add-to-cart" data-id="<?php echo $product['id']; ?>">Add to Cart</button>
                            </td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr>
                        <td colspan="5">No products available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Add Product Modal (Only for Admin) -->
<?php if ($is_admin): ?>

    <!-- Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm" method="POST" action="add_product.php">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name:</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description:</label>
                            <textarea id="description" name="description" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price:</label>
                            <input type="number" id="price" name="price" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="image_url" class="form-label">Image URL:</label>
                            <input type="text" id="image_url" name="image_url" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category:</label>
                            <input type="text" id="category" name="category" class="form-control" required>
                        </div>
                        <input type="submit" value="Add Product" class="btn btn-success">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="productToast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Notification</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage"></div>
    </div>

<?php endif; ?>

<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cartModalLabel">Your Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="cartItems"></div> <!-- Cart items will be populated here -->
                <div id="emptyCartMessage" class="alert alert-info mt-3" style="display:none;">
                    Your cart is empty!
                </div>
                <div class="text-center mt-4">
                    <button id="checkoutButton" class="btn btn-success" style="display:none;" onclick="window.location.href='checkout.php'">
                        Proceed to Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<!-- Bootstrap JS and jQuery (for AJAX request) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    // Handle product addition via AJAX
    $('.add-to-cart').click(function () {
        var productId = $(this).data('id');

        // Send AJAX request to add product to cart
        $.ajax({
            url: '', // Keep the request on the same page
            type: 'POST',
            data: { product_id: productId },
            dataType: 'json', // Expect JSON response
            success: function (response) {
                if (response.success) {
                    alert(response.message); // Show success message
                } else {
                    alert('Failed to add product to cart.');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });

    // Load cart items into the modal when it is opened
    $('#cartModal').on('show.bs.modal', function () {
        $.ajax({
            url: 'fetch_cart.php', // Assumed this is the file returning cart items
            type: 'GET',
            success: function (data) {
                $('#cartItems').html(data);

                // Check if cart is empty or has items
                if (data.trim().length === 0) {
                    // If the cart is empty, show message and hide the checkout button
                    $('#emptyCartMessage').show();
                    $('#checkoutButton').hide();
                } else {
                    // If the cart has items, show the checkout button and hide the empty cart message
                    $('#emptyCartMessage').hide();
                    $('#checkoutButton').show();
                }
            },
            error: function () {
                $('#cartItems').html('<div class="alert alert-danger">Failed to load cart items.</div>');
                $('#checkoutButton').hide(); // Hide the button if there is an error
            }
        });
    });


    // Handle product deletion via AJAX
    $(document).on('click', '.delete-product', function () {
        var productId = $(this).data('id');

        if (confirm('Are you sure you want to delete this product?')) {
            $.ajax({
                url: 'delete_product.php',
                type: 'POST',
                data: { id: productId },
                success: function (response) {
                    if (response.success) {
                        $('#product-' + productId).remove();
                    } else {
                        alert('Failed to delete product.');
                    }
                }
            });
        }
    });
});

</script>
</body>
</html>