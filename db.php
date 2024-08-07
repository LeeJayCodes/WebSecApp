<?php
function getDbConnection() {
    $host = 'localhost';
    $dbname = 'WebSecApp'; // Change this to your database name if you have one, Create one if you don't have database
    $user = 'root';
    $pass = 'seyed313'; // Change this to your database password if you have one

    $dsn = "mysql:host=$host;dbname=$dbname";
    try {
        $conn = new PDO($dsn, $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
        exit;
    }
}
