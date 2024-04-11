<?php
include 'connection.php';

$postId = isset($_GET['postId']) ? intval($_GET['postId']) : 0;

$stmt = $conn->prepare("SELECT postPicture FROM picture WHERE postId = ?");
$stmt->bind_param("i", $postId);
$stmt->execute();
$result = $stmt->get_result();

if ($picture = $result->fetch_assoc()) {
    header('Content-Type: ' . $picture['mimeType']);

    echo $picture['postPicture'];
} else {

    header("HTTP/1.0 404 Not Found");
}

$stmt->close();
?>
