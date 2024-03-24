<?php
session_start();
include 'connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'] ?? null;
    $postTitle = $_POST['postTitle'] ?? '';
    $postContent = $_POST['postContent'] ?? '';
    if (empty($postTitle) || empty($postContent)) {
        echo 'Post title or content cannot be empty.';
        exit;  // 
    }

    $postTag = '';
    if (preg_match('/#(\w+)/', $postContent, $matches)) {
        $postTag = $matches[0];
    }
    // post table
    $stmt = $conn->prepare("INSERT INTO post (postTitle, postContent, postUserId, postDate, postTag) VALUES (?, ?, ?, NOW(), ?)");
    $stmt->bind_param('ssis', $postTitle, $postContent, $userId, $postTag);

    if ($stmt->execute()) {
        $last_id = $stmt->insert_id; // last inserted ID for the post

        //file upload
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $key => $image) {
                $imageTmp = file_get_contents($image);
                $stmt = $conn->prepare("INSERT INTO picture (postId, postPicture) VALUES (?, ?)");
                $null = NULL; 
                $stmt->bind_param('ib', $last_id, $null);
                $stmt->send_long_data(1, $imageTmp);
                if (!$stmt->execute()) {
                    echo "Error: " . $stmt->error;
                }
            }
        }
        echo "success";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}

?>