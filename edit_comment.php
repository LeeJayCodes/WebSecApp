<?php
session_start();
require 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

$conn = getDbConnection();
$commentId = $_GET['id'] ?? 0;

// Fetch the comment to edit
$stmt = $conn->prepare("SELECT comment, user_id FROM comments WHERE id = :id");
$stmt->bindParam(':id', $commentId);
$stmt->execute();
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SESSION['user_id'] != $comment['user_id']) {
    echo "You do not have permission to edit this comment.";
    exit;
}

// Handle the comment update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $updatedComment = $_POST['comment'];
    $stmt = $conn->prepare("UPDATE comments SET comment = :comment WHERE id = :id AND user_id = :userId");
    $stmt->bindParam(':comment', $updatedComment);
    $stmt->bindParam(':id', $commentId);
    $stmt->bindParam(':userId', $_SESSION['user_id']);
    $stmt->execute();

    header("Location: restaurant_detail.php?id=" . $_SESSION['restaurant_id']); // Redirect back to the restaurant detail page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Comment</title>
</head>
<body>
    <h1>Edit Comment</h1>
    <form action="edit_comment.php?id=<?php echo $commentId; ?>" method="post">
        <textarea name="comment" required><?php echo htmlspecialchars($comment['comment']); ?></textarea>
        <button type="submit">Update Comment</button>
    </form>
    <form action="sign_out.php" method="post">
        <button type="submit">Sign Out</button>
    </form>
</body>
</html>