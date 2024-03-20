<?php
session_start();
include 'connection.php'; 

$response = [];

if (isset($_POST['userId'])) {
    $userId = $_POST['userId'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE user SET firstName=?, lastName=?, emailAddress=? WHERE userId=?");
    $stmt->bind_param("sssi", $firstName, $lastName, $email, $userId);

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
    }
    $stmt
    ->close();
} else {
$response['success'] = false;
$response['message'] = 'User ID not provided.';
}

echo json_encode($response);
?>