<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="utf-8">
    <title>Bloggie</title>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/content.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src='https://www.w3schools.cn/fonts/kit/a076d05399.js'></script>
    <script src="https://kit.fontawesome.com/d1344ce34d.js" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/0485a9f289.js" crossorigin="anonymous"></script>
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>

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
        const textarea = document.getElementById('autoresizing');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto'; // Reset the height
            this.style.height = this.scrollHeight + 'px'; // Adjust height based on content
        });
    </script>

    <style>
        .dynamic-button {
            margin-right: 10px;
            /* Adjust as needed */
            padding: 5px 10px;
            /* Adjust as needed */
        }
    </style>

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

    <div class="navBar">
        <p>Tags/Categories</p>
        <button>Sport</button>
        <button>Makeup</button>
        <button>Music</button>
        <button>Food</button>
        <button>Movie</button>
        <!-- <input class="newTag" placeholder="Add new tag"></input> -->

        

    </div>

    <div class="content">
        <p class="userName">
            <img src="../Images/profile.jpg">
            &nbsp;&nbsp;smith
        </p>
        <p class="datetime">2024-1-1 18:00:00</p>
        <p class="context">sadasdsasdadaddsdandadaadakdakjacacascncsacnanasnccnascsanasnsansasjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjadadsadcascascasssssssssssssssssssadsadsacjsamcasncsanfkafnaskfnsanksakxnaknaknsxknassssssssssssssssssssssssssssssssssssssssssssssss</p>
        <button id="comment"><i class='far fa-comment-alt'></i></button>

    </div>
    <div class="separate">
        <hr>
    </div>
    <div class="addComment">
        <textarea placeholder="Comment something..." id="autoresizing"></textarea>
        <br>
        <button id="submitComment">Comment</button>
    </div>
    <!-- <div class="separate">
        <hr>
    </div>
    <div class="showComment">
        <p class="userName">
            <img src="../Images/profile.jpg">
            &nbsp;&nbsp;smith
        </p>
    </div> -->



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