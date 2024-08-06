<?php
session_start();
require 'db.php'; // Include your database connection file

function addUser($username, $password) {
    $conn = getDbConnection(); // Get the PDO connection

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    
    // Vulnerable Implementation - Saving the password as plain text
    // Bind parameters and execute
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password); 
    $stmt->execute();

    // //Secured Implementation - Hasing the password
    // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    // // Bind parameters and execute
    // $stmt->bindParam(':username', $username);
    // $stmt->bindParam(':password', $hashed_password); 
    // $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Call the function to add user
    addUser($username, $password);

    // Redirect to login page after registration
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
</head>
<body>
    <h1>Create User</h1>
    <form action="create_user.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Create Account</button>
    </form>
</body>
</html>