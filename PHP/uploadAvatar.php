<?php
session_start();
include 'connection.php'; 
$response = []; 

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        // 
        $fileName = $_FILES['avatar']['name'];
        $fileTmpName = $_FILES['avatar']['tmp_name'];
        $fileSize = $_FILES['avatar']['size'];
        $fileType = $_FILES['avatar']['type'];

        $fileContent = file_get_contents($fileTmpName);

        $stmt = $conn->prepare("UPDATE user SET icon=? WHERE userId=?");
        $null = NULL; // for bind_param
        $stmt->bind_param("bi", $null, $userId);
        $stmt->send_long_data(0, $fileContent); 

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Avatar updated successfully.";
        } else {
            $response['success'] = false;
            $response['message'] = "Failed to update avatar.";
        }
        $stmt->close();
    } else {
        $response['success'] = false;
        $response['message'] = "No file uploaded or there was an upload error.";
    }
} else {
    $response['success'] = false;
    $response['message'] = "User not authenticated.";
}

header('Content-Type: application/json');
echo json_encode($response);
?>
