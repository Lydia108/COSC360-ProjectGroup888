<?php
session_start();
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $postId = $_POST['postId'] ?? null;
    $comment = $_POST['comment'] ?? '';

    if ($postId && $comment) {
        $stmt = $conn->prepare("INSERT INTO comment (postId, postComment, postUserId, postDate) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("isi", $postId, $comment, $userId);
        if ($stmt->execute()) {
            echo "Loading...Please hold...";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: Missing post ID or comment.";
    }
    $conn->close();
} else {
    echo "Invalid request or not logged in.";
}
?>