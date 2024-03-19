<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="utf-8">
    <title>Bloggie</title>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/profile.css">
    <script src="https://kit.fontawesome.com/d1344ce34d.js" crossorigin="anonymous"></script>
</head>
<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    echo "<div>Welcome, user ID: " . $userId . "</div>";
} else {
    header("Location: login.php");
    exit();
}
?>

<body>
    <div class="sidebar">
        <a href="main.php"><i class="fa-solid fa-house-chimney">&nbsp;Home</i></a>
        <div class="actions">
            <a href="post.php">Make Post</a>
            <div class="info">
                <a href="#">My Profile</a>
                <img src="../Images/test.jpg" alt="Avatar">
                <div class="dropdown-content">
                    <a href="profile.php">Profile</a>
                    <a href="logout.php">Logout</a>
                </div>
                <?php

if (isset($_SESSION['user_id'])) {
$userId = $_SESSION['user_id'];

echo "<div class='ses'>Welcome, user ID: " . $userId . "</div>";
} else {
header("Location: login.php");
exit();
}
?> 
            </div>
        </div>
    </div>
    <div class="container1">
        <span>
            <img src="../Images/test.jpg">
        </span>
        <span class="profile">
            <p class="name">
                First name: 

                Last name:
            </p>

            <p class="address">Address: </p>

            <p class="number">Phone number: </p>
            <button>
                Edit
            </button>
            <button id="update">
                Update
            </button>
        </span>

    </div>
    <div class="container2">
        <fieldset>
            <legend>
                <button class="like">Liked</button>———————————————————————————————<button
                    class="comment">Commented</button>
            </legend>
            <div class="content">

                <img src="../Images/test.jpg">
                <p class="title">hello!</p>

                <img src="../Images/test.jpg">
                <p class="title">hello!</p>

                <img src="../Images/test.jpg">
                <p class="title">hello!</p>
                

            </div>
        </fieldset>
    </div>

</body>




</html>