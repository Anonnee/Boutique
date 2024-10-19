<?php
include 'config.php'; // Include the database connection

session_start(); // Start a new session or resume an existing one

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the input data from the form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the SQL query to fetch the user's data
    $sql = "SELECT id, password FROM Users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username); // Bind the username to the query
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $hashed_password); // Fetch user ID and hashed password

    if ($stmt->num_rows == 1) {
        // If the user is found, verify the password
        $stmt->fetch(); // Fetch the result
        if (password_verify($password, $hashed_password)) {
            // Password is correct, start a session for the user
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;

            // Redirect to the profile page
            header("Location: profile.php");
            exit(); // Stop further execution after redirect
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that username.";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!-- HTML Form for Login -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>