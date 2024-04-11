<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['userType'] != '1') {
    header("Location: login.php");
    exit;
}

if (isset($_POST['delete_post_checkbox'])) {
    $selectedPosts = $_POST['delete_post_checkbox'];

    $conn->begin_transaction();

    try {
        foreach ($selectedPosts as $postId) {
            $stmt = $conn->prepare("DELETE FROM picture WHERE postId = ?");
            $stmt->bind_param("i", $postId);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("DELETE FROM post WHERE postId = ?");
            $stmt->bind_param("i", $postId);
            $stmt->execute();
            $stmt->close();
        }

        $conn->commit();
        $_SESSION['message'] = 'Selected posts and their pictures deleted successfully.';
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['message'] = 'Error occurred during deletion: ' . $e->getMessage();
    }
} else {
    $_SESSION['message'] = 'No posts selected for deletion.';
}

header("Location: admin.php");
exit;
?>
