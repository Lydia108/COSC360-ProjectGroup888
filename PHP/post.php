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
        <a href="main.php"><i class="fa-solid fa-house-chimney">&nbsp;Home</i></a>
        <div class="search-bar">
            <input type="text" placeholder="Search...">
            <i class="fa fa-search"></i>
        </div>
        <div class="actions">
            <a href="post.php">Make Post</a>
            <div class="info">
                <a href="profile.php">My Profile</a>
                <img src="../Images/test.jpg" alt="Avatar">
            </div>
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




        <!-- <input class="newTag" placeholder="Add new tag"></input> -->

        <!-- <input class="newTag" id="newTag" placeholder="Add new tag" style="width: auto; min-width: 120px;" />
        <span id="textWidthCalculator" style="visibility: hidden; position: absolute;"></span> -->

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newTagInput = document.getElementById('newTag');
            const calculatorSpan = document.getElementById('textWidthCalculator');
            const navBar = document.querySelector('.navBar');

            // Adjust newTag input width based on its content
            function adjustInputWidth() {
                calculatorSpan.textContent = newTagInput.value || newTagInput.placeholder;
                newTagInput.style.width = `${calculatorSpan.offsetWidth + 10}px`; // +10 for padding
            }

            newTagInput.addEventListener('input', adjustInputWidth);

            // Initially adjust input width
            adjustInputWidth();

            // Function to add a new tag button
            function addNewTag(tagText) {
                const newButton = document.createElement('button');
                newButton.textContent = tagText;
                newButton.className = 'dynamic-button'; // Use this class to style your buttons
                // Insert the new button before the newTag input box
                navBar.insertBefore(newButton, newTagInput);
            }

            // Listen for Enter key in newTag input
            newTagInput.addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); // Prevent form submission if any
                    if (newTagInput.value.trim() !== '') {
                        addNewTag(newTagInput.value.trim());
                        newTagInput.value = ''; // Clear input
                        adjustInputWidth(); // Reset input width
                    }
                }
            });
        });
    </script>

    <div class="post">
        <input class="title" placeholder="Edit title..." />
        <br>
        <!-- <img src="../Images/th.jpg" /> -->

        <textarea class="context" placeholder="Enter text..." ></textarea>
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