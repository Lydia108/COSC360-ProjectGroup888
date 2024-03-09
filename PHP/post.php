<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="utf-8">
    <title>Bloggie</title>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/post.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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


<body>
    <div class="sidebar">
        <a href="#"><i class="fa-solid fa-house-chimney">&nbsp;Home</i></a>
        <div class="search-bar">
            <input type="text" placeholder="Search...">
            <i class="fa fa-search"></i>
        </div>
        <div class="actions">
            <a href="#">Make Post</a>
            <div class="info">
                <a href="#">My Profile</a>
                <img src="../Images/test.jpg" alt="Avatar">
            </div>
        </div>
    </div>
    <div class="post">
        <input class="title" placeholder="Edit title..." />
        <br>
        <!-- <img src="../Images/th.jpg" /> -->

        <input class="context" placeholder="Enter text..." />
        <!-- <img src="../Images/test.jpg" id="default" /> -->
        <button class="photo"><i class="fa-regular fa-image"></i>
        </button>
        <button class="submit">Post now</button>
        <!-- <div class="comment">
            <input type="comment" placeholder="Leave a comment...">
            <a href="#"><i class="fa-solid fa-envelope"></i></a>
        </div> -->

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