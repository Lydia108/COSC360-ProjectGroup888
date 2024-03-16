
<?php

$servername = "localhost";
$database = "blog";
$user = "root";
$password = "root123";
 
$conn = new mysqli($servername, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>