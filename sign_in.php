<?php
session_start();
require 'db.php'; // Include your database connection file

    // Vulnerable SQL query
    // admin' OR '1'='1
// function authenticateUser($username, $password) {
//     $conn = getDbConnection();
//     // Vulnerable SQL query
//     $sql = "SELECT id, password FROM users WHERE username = '$username' AND password = '$password'";
//     $stmt = $conn->query($sql);

//     if ($stmt->rowCount() == 0) {
//         die("Debug: No user found with that username and password."); // Stop execution and show message
//     }

//     $user = $stmt->fetch(PDO::FETCH_ASSOC);
//     $_SESSION['loggedin'] = true;
//     $_SESSION['username'] = $username;
//     $_SESSION['user_id'] = $user['id']; // Storing user_id in the session
//     return true;
// }

    // secured SQL query
    function authenticateUser($username, $password) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
    
        if ($stmt->rowCount() == 0) {
            die("Debug: No user found with that username."); // Stop execution and show message
        }
    
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // for hashed password use password_verify($password, $user['password']) for condition
        
        if ($password === $user['password']) { // Use direct comparison for plain text passwords
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user['id']; // Storing user_id in the session
            return true;
        } else {
            die("Debug: Password does not match."); // Stop execution and show message
        }

        //Hashed

        // if (password_verify($password, $user['password'])) {
        //     $_SESSION['loggedin'] = true;
        //     $_SESSION['username'] = $username;
        //     $_SESSION['user_id'] = $user['id']; // Storing user_id in the session
        //     return true;
        // } else {
        //     die("Debug: Password does not match."); // Stop execution and show message
        // }
    }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Call the function to authenticate user
    if (authenticateUser($username, $password)) {
        // Redirect to a new page
        header("Location: restaurant_list.php");
        exit;
    } else {
        // Authentication failed
        $error = "Invalid username or password.";
        echo "<script>alert('$error'); window.location.href='index.php';</script>";
    }
} else {
    // Not a POST request
    header("Location: index.php");
    exit;
}
