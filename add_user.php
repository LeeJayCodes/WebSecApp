<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];

    // The rest of your code
    $conn = getDbConnection();
    try {
        // Vulnerable to SQL Injection
        $sql = "INSERT INTO user (username) VALUES ('$username')";
        $result = $conn->exec($sql);
        
        if ($result !== false) {
            echo "New user added successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->errorInfo()[2];
        }
    } catch (PDOException $e) {
        echo "Error: " . $sql . "<br>" . $e->getMessage();
    }
}
?>