<?php
session_start();
require 'db.php'; // Ensure this path is correct

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

$conn = getDbConnection(); // Get database connection

// Fetch restaurants from the database
$stmt = $conn->prepare("SELECT id, name FROM restaurants");
$stmt->execute();
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant List</title>
</head>
<body>
    <h1>Welcome to the Restaurant Review Site!</h1>
    <p>Choose a restaurant to see reviews and add your own:</p>
    <ul>
        <?php foreach ($restaurants as $restaurant): ?>
            <li><a href="restaurant_detail.php?id=<?php echo htmlspecialchars($restaurant['id']); ?>">
                <?php echo htmlspecialchars($restaurant['name']); ?>
            </a></li>
        <?php endforeach; ?>
    </ul>
    <form action="sign_out.php" method="post">
        <button type="submit">Sign Out</button>
    </form>
</body>
</html>