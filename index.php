<?php
include 'config.php'; // Include the database connection

session_start(); // Start session

// Handle Login Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    // Get login form data
    $username = $_POST['login_username'];
    $password = $_POST['login_password'];

    // Prepare and execute the SQL query to fetch the user's data
    $sql = "SELECT id, password, role FROM Users WHERE username = ?"; // Fetch the role as well
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username); // Bind the username to the query
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $hashed_password, $role); // Fetch user ID, hashed password, and role

    if ($stmt->num_rows == 1) {
        // If the user is found, verify the password
        $stmt->fetch(); // Fetch the result
        if (password_verify($password, $hashed_password)) {
            // Password is correct, start a session for the user
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role; // Store the user's role in session

            // Redirect to the profile page
            header("Location: profile.php");
            exit(); // Stop further execution after redirect
        } else {
            $login_error_message = "Invalid password.";
        }
    } else {
        $login_error_message = "No user found with that username.";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}

// Handle Register Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Get the registration form data
    $username = $_POST['register_username'];
    $email = $_POST['register_email'];
    $password = password_hash($_POST['register_password'], PASSWORD_DEFAULT); // Hash the password for security
    $role = 'user'; // Assign a default role as 'user'. You can modify this if needed.

    // Prepare and execute the SQL query to insert the new user
    $sql = "INSERT INTO Users (username, email, password, role) VALUES (?, ?, ?, ?)"; // Insert the role
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $email, $password, $role); // Bind the parameters, including the role

    // Check if the query was successful
    if ($stmt->execute()) {
        // Automatically log in the user after registration
        $user_id = $stmt->insert_id; // Get the inserted user ID
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role; // Set the user's role in session

        // Show success message and redirect to profile
        $register_success_message = "Registration successful!";
        header("refresh:5;url=profile.php"); // Redirect to profile after 5 seconds
    } else {
        $register_error_message = "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login or Register</title>
    <!-- Bootstrap v5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS for styling -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar a {
            color: white !important;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h5 {
            color: #343a40;
        }
        footer {
            background-color: #343a40;
            color: white;
            padding: 10px 0;
            text-align: center;
        }
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
        }
        /* Error toast */
        .toast-error {
            background-color: #ff4c4c;
            color: white;
        }
        /* Success toast */
        .toast-success {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <!-- Login Card -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-center">Login</h5>
                    <form method="POST" action="">
                        <input type="hidden" name="login" value="1">
                        <div class="mb-3">
                            <label for="login_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="login_username" name="login_username" required>
                        </div>
                        <div class="mb-3">
                            <label for="login_password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="login_password" name="login_password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Register Card -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-center">Register</h5>
                    <form method="POST" action="">
                        <input type="hidden" name="register" value="1">
                        <div class="mb-3">
                            <label for="register_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="register_username" name="register_username" required>
                        </div>
                        <div class="mb-3">
                            <label for="register_email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="register_email" name="register_email" required>
                        </div>
                        <div class="mb-3">
                            <label for="register_password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="register_password" name="register_password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark">Register</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifications -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <?php if (isset($login_error_message)): ?>
        <div class="toast align-items-center toast-error show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <?php echo $login_error_message; ?>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($register_error_message)): ?>
        <div class="toast align-items-center toast-error show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <?php echo $register_error_message; ?>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($register_success_message)): ?>
        <div class="toast align-items-center toast-success show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <?php echo $register_success_message; ?>
                    Redirecting to profile in 5 seconds...
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

<!-- Bootstrap v5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>