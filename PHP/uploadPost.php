<?php
session_start();
include 'connection.php'; // 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'] ?? null;
    $postTitle = $_POST['postTitle'] ?? '';
    $postContent = $_POST['postContent'] ?? '';
    $postTag = '';

    if (preg_match('/#(\w+)/', $postContent, $matches)) {
        $postTag = $matches[0]; 
    }
    $stmt = $conn->prepare("INSERT INTO post (postTitle, postContent, postUserId, postDate, postTag) VALUES (?, ?, ?, NOW(), ?)");
    $stmt->bind_param('ssis', $postTitle, $postContent, $userId, $postTag);

    if ($stmt->execute()) {
        echo "<script>alert('Post saved successfully!'); </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
