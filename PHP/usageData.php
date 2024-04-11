<?php
include 'connection.php'; 

// Prepare your SQL queries
$queryBlogs = "SELECT COUNT(*) as totalBlogs FROM post";
$queryUsers = "SELECT COUNT(*) as totalUsers FROM user";
$queryComments = "SELECT COUNT(*) as totalComments FROM comment";



// Execute queries and fetch results
$resultBlogs = $conn->query($queryBlogs)->fetch_assoc();
$resultUsers = $conn->query($queryUsers)->fetch_assoc();
$resultComments = $conn->query($queryComments)->fetch_assoc();

// Package data into an associative array to be encoded as JSON
$usageData = [
    'totalBlogs' => $resultBlogs['totalBlogs'],
    'totalUsers' => $resultUsers['totalUsers'],
    'totalComments' => $resultComments['totalComments']
];

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($usageData);
?>