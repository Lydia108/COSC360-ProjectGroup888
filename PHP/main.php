<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="utf-8">
    <title>Bloggie</title>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/main.css">
    <script src='https://www.w3schools.cn/fonts/kit/a076d05399.js'></script>
    <script src="https://kit.fontawesome.com/d1344ce34d.js" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/0485a9f289.js" crossorigin="anonymous"></script>
    <script>
        function toggleLike() {
            var like = document.getElementById("thumbsup");
            var like1 = document.getElementById("thumbsup1");

            // Toggle visibility
            if (like.style.display === "none") {
                like.style.display = "inline-block";
                like1.style.display = "none";
            } else {
                like.style.display = "none";
                like1.style.display = "inline-block";
            }
        }
    </script>
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
        <a href="#"><i class="fa-solid fa-house-chimney">&nbsp;Home</i></a>
        <div class="search-bar">
            <input type="text" placeholder="Search...">
            <i class="fa fa-search"></i>
        </div>
        <div class="actions">
            <a href="post.php">Make Post</a>
            <div class="info">
                <a href="profile.php">My Profile</a>
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
    <div class="post">
        <p class="title">blablabla</p>
        <img src="../Images/th.jpg" />
        <div class="context">
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
        <!-- <div class="comment">
            <input type="comment" placeholder="Leave a comment...">
            <a href="#"><i class="fa-solid fa-envelope"></i></a>
        </div> -->
        <div class="user">
            <img src="../Images/test.jpg" id="test" />
            <p class="username" id="username">smith</p>
            <a href="#"><i class='far fa-thumbs-up' id="thumbsup" onclick="toggleLike()"></i></a>
            <a href="#"><i class='fas fa-thumbs-up' id="thumbsup1" style="display:none;" onclick="toggleLike()"></i></a>
            <!-- <p class="count" id="count">123</p> -->
        </div>
    </div>

    <div class="navBar">
        <p>Tags/Categories</p>
        <button>Lifestyle</button>
        <button>Technology</button>
        <button>Education</button>
        <button>Travel</button>
        <button>Health&Fitness</button>
        <button>Gastronomy</button>
        <button>Personal</button>
        <button>Career</button>
        <button>Arts&Culture</button>

    </div>

    <div class="site-footer">
        <footer class="app">Bloggie</footer>
        <footer class="intro">The simplest way to connect with others through questions and answers.</footer>
        <footer class="contact">Stay contact with us:</footer>
        <footer class="icon">
            <img src="../Images/linkedin.png" />
            <img src="../Images/x.webp" />
            <img src="../Images/ins.webp" />
        </footer>
        <footer class="copyright">&copy; 2024 Bloggie. All rights reserved.</footer>
    </div>



</body>




</html>