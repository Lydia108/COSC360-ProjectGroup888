<?php
// Include database connection settings
require_once 'connection.php'; // Make sure this path is correct

// Initialize default values or retrieve from session/inputs
$postLike = 0; // Default initial likes
$postUserId = $_SESSION['user_id'] ?? null; // Example: Retrieved from session
$postDate = date('Y-m-d H:i:s'); // Current date and time
$postTag = ''; // You'll need to handle how you set this, possibly from another form input

// Check if the request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["title"]) && !empty($_POST["content"])) {
    $title = $conn->real_escape_string($_POST["title"]);
    $content = $conn->real_escape_string($_POST["content"]);

    // SQL query to insert post data into the database
    $stmt = $conn->prepare("INSERT INTO post (postTitle, postContent, postLike, postUserId, postDate, postTag) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiis", $title, $content, $postLike, $postUserId, $postDate, $postTag);

    if ($stmt->execute()) {
        echo "New post created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request";
}
?>
