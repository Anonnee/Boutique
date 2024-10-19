<?php
session_start(); // Start the session

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php'; // Include the database connection

$user_id = $_SESSION['user_id'];

// Fetch user-specific information to check their role
$sql = "SELECT role FROM Users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

$is_admin = ($role === 'admin');

// Handle Status Update by Admin
if ($is_admin && isset($_POST['update_order_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    $update_sql = "UPDATE Orders SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $new_status, $order_id);
    $update_stmt->execute();
    $update_stmt->close();
}

// Handle Order Cancellation by Admin
if ($is_admin && isset($_POST['cancel_order'])) {
    $order_id = $_POST['order_id'];

    $delete_sql = "DELETE FROM Orders WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $order_id);
    $delete_stmt->execute();
    $delete_stmt->close();
}

// Fetch orders based on the role
if ($is_admin) {
    // Admins can see all orders with user information
    $order_sql = "SELECT Orders.id AS order_id, Orders.total_price, Orders.status, Users.username FROM Orders JOIN Users ON Orders.user_id = Users.id";
} else {
    // Normal users can see only their own orders
    $order_sql = "SELECT id AS order_id, total_price, status FROM Orders WHERE user_id = ?";
}

$stmt = $conn->prepare($order_sql);
if (!$is_admin) {
    $stmt->bind_param("i", $user_id); // Bind user_id for normal users
}
$stmt->execute();
$order_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <!-- Bootstrap v5 CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .order-table th, .order-table td {
            text-align: center;
            vertical-align: middle;
        }
        .order-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<div class="container mt-5">
    <h2 class="mb-4">Order History</h2>

    <table class="table table-bordered table-hover order-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <?php if ($is_admin): ?>
                    <th>Customer</th>
                <?php endif; ?>
                <th>Total Price</th>
                <th>Status</th>
                <?php if ($is_admin): ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php while($order = $order_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                <?php if ($is_admin): ?>
                    <td><?php echo htmlspecialchars($order['username']); ?></td>
                <?php endif; ?>
                <td>$<?php echo htmlspecialchars(number_format($order['total_price'], 2)); ?></td>
                <td><?php echo htmlspecialchars($order['status']); ?></td>
                <?php if ($is_admin): ?>
                    <td class="order-actions">
                        <!-- Status Update Form -->
                        <form method="POST" action="">
                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                            <select name="status" class="form-select form-select-sm">
                                <option value="pending" <?php if ($order['status'] === 'pending') echo 'selected'; ?>>Pending</option>
                                <option value="shipped" <?php if ($order['status'] === 'shipped') echo 'selected'; ?>>Shipped</option>
                                <option value="delivered" <?php if ($order['status'] === 'delivered') echo 'selected'; ?>>Delivered</option>
                                <option value="canceled" <?php if ($order['status'] === 'canceled') echo 'selected'; ?>>Canceled</option>
                            </select>
                            <button type="submit" name="update_order_status" class="btn btn-sm btn-primary mt-1">Update Status</button>
                        </form>

                    </td>
                <?php endif; ?>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <a href="profile.php" class="btn btn-secondary mt-3">Back to Products</a>
</div>
<?php include 'footer.php'; ?>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
