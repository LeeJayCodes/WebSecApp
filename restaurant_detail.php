<?php
session_start();
require 'db.php'; // Ensure this path is correct

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}


$_SESSION['restaurant_id'] = $_GET['id'];

$conn = getDbConnection(); // Get database connection
$restaurantId = $_GET['id'];


// Fetch restaurant details
$stmt = $conn->prepare("SELECT name, description FROM restaurants WHERE id = :id");
$stmt->bindParam(':id', $restaurantId);
$stmt->execute();
$restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$restaurant) {
    die("Restaurant not found.");
}

// Handle comment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment'])) {
    if (isset($_SESSION['user_id'])) {
        $comment = $_POST['comment'];
        $userId = $_SESSION['user_id'];

        $stmt = $conn->prepare("INSERT INTO comments (restaurant_id, user_id, comment) VALUES (:restaurantId, :userId, :comment)");
        $stmt->bindParam(':restaurantId', $restaurantId);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':comment', $comment);
        $stmt->execute();

        // Redirect to the same page to display the new comment and avoid form resubmission
        header("Location: restaurant_detail.php?id=" . $restaurantId);
        exit;
    } else {
        echo "<script>alert('You must be logged in to post comments.'); window.location.href='index.php';</script>";
    }
}

// Fetch comments for this restaurant
$stmt = $conn->prepare("SELECT comments.id, comments.comment, comments.user_id, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE restaurant_id = :restaurantId ORDER BY comments.created_at DESC");
$stmt->bindParam(':restaurantId', $restaurantId);
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($restaurant['name']); ?></title>
</head>
<body>
    <h1><?php echo htmlspecialchars($restaurant['name']); ?></h1>
    <p><?php echo htmlspecialchars($restaurant['description']); ?></p>

    <h2>Comments:</h2>
<?php if (!empty($comments)): ?>
    <?php foreach ($comments as $comment): ?>
        <p>
            <strong><?php echo htmlspecialchars($comment['username']); ?>:</strong>
            <?php echo htmlspecialchars($comment['comment']); ?>
            <?php if (isset($comment['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                <a href="edit_comment.php?id=<?php echo $comment['id']; ?>">Edit</a>
            <?php endif; ?>
        </p>
    <?php endforeach; ?>
<?php else: ?>
    <p>No comments yet.</p>
<?php endif; ?>

    <h2>Add a Comment:</h2>
    <form action="restaurant_detail.php?id=<?php echo htmlspecialchars($restaurantId); ?>" method="post">
        <textarea name="comment" required></textarea>
        <button type="submit">Submit Comment</button>
    </form>
    <a href="restaurant_list.php" class="btn">Back to List</a>
    <form action="sign_out.php" method="post">
        <button type="submit">Sign Out</button>
    </form>
</body>
</html>