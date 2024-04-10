<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="utf-8">
    <title>Bloggie</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/main.css">
    <script src='https://www.w3schools.cn/fonts/kit/a076d05399.js'></script>
    <script src="https://kit.fontawesome.com/d1344ce34d.js" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/0485a9f289.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.5.1/dist/gsap.min.js"></script>
    <script>
    gsap.to("#backToTopButton", {
        duration: 1,
        opacity: 0.5,
        y: -20,
        yoyo: true,
        repeat: -1,
        ease: "power1.inOut"
    });
    </script>
    <canvas id="canvas"></canvas>
    <script>
    var canvas = document.getElementById('canvas');
    var ctx = canvas.getContext('2d');

    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    var particles = [];
    for (var i = 0; i < 100; i++) {
        particles.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            radius: Math.random() * 5 + 1,
            color: 'white',
            speedX: (Math.random() - 0.5) * 2,
            speedY: (Math.random() - 0.5) * 2
        });
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        particles.forEach(function(p) {
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2, false);
            ctx.fillStyle = p.color;
            ctx.fill();

            p.x += p.speedX;
            p.y += p.speedY;

            if (p.x < 0 || p.x > canvas.width) p.speedX *= -1;
            if (p.y < 0 || p.y > canvas.height) p.speedY *= -1;
        });

        requestAnimationFrame(draw);
    }

    draw();
    </script>


</head>
<!-- profile -->
<?php
session_start();
include 'connection.php';
// check guest
if($_SESSION['is_guest'] == 'true') {
    $welcomeMessage = "Welcome to Bloggie";
     $iconData = ''; 
}else if (isset($_SESSION['user_id'])) {
    $_SESSION['is_guest'] = false; 
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT firstName, lastName, emailAddress, icon, userType FROM user WHERE userId = ?");
    $stmt->bind_param("i", $userId); 
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $firstName = htmlspecialchars($user['firstName']);
        $lastName = htmlspecialchars($user['lastName']);
        $welcomeMessage = "Welcome, " . $firstName . " " . $lastName; 
        if ($user['icon']) {
            $iconData = base64_encode($user['icon']);
        } else {
            $iconData = ''; 
        }
        if ( $user['userType'] == 1) {
            echo "1";
        }        
    } else {
        echo "No user found.";
    }
    $stmt->close();
} else {
    header("Location: login.php");
    exit();
}
?>
<!-- post and picture -->
<!-- order by #comment to make sure hot topic always comes first -->
<?php
include 'connection.php';
$query = "SELECT p.postId, p.postTitle, p.postContent, p.postTag, p.postDate, u.firstName, u.lastName, u.icon, COUNT(c.postId) as commentCount
FROM post p 
INNER JOIN user u ON p.postUserId = u.userId 
LEFT JOIN comment c ON p.postId = c.postId
GROUP BY p.postId, p.postTitle, p.postContent, p.postTag, p.postDate, u.firstName, u.lastName, u.icon
ORDER BY commentCount DESC, p.postDate DESC";
$postsResult = $conn->query($query);
$posts = [];

if ($postsResult->num_rows > 0) {
    while($row = $postsResult->fetch_assoc()) {
        // firstPicture
        $pictureQuery = "SELECT postPicture FROM picture WHERE postId = ?";
        $pictureStmt = $conn->prepare($pictureQuery);
        $pictureStmt->bind_param("i", $row['postId']);
        $pictureStmt->execute();
        $pictureResult = $pictureStmt->get_result();
        if ($pictureRow = $pictureResult->fetch_assoc()) {
            $row['firstPicture'] = 'data:image/jpeg;base64,' . base64_encode($pictureRow['postPicture']);
        } else {
            $row['firstPicture'] = null; 
        }
        $posts[] = $row;
    }
}
?>

<body>
    <a href="#top" id="backToTopButton" title="Go to top"> TOP </a>
    <div class="sidebar">
        <a href="#"><i class="fa-solid fa-house-chimney">&nbsp;Home</i></a>
        <div class="search-bar">
            <input type="text" id="searchTerm" placeholder="Search post title...">
            <i class="fa fa-search" id="searchIcon"></i>
        </div>
        <!-- <script>
        function searchPost() {
            let searchTerm = document.getElementById('searchTerm').value;

            if (searchTerm.trim() !== '') {
                let xhr = new XMLHttpRequest();

                xhr.open('GET', 'search.php?term=' + encodeURIComponent(searchTerm), true);

                xhr.onload = function() {
                    if (this.status === 200) {
                        document.getElementById('searchTerm').innerHTML = this.responseText;
                    }
                };

                xhr.send();
            }
        }
        document.getElementById('searchIcon').addEventListener('click', function() {
            let searchTerm = document.getElementById('searchTerm').value;

            if (searchTerm.trim() !== '') {
                document.getElementById('searchTerm').value = searchTerm;
                searchPost();
            }
        });
        </script> -->

        <div class="actions">
            <!-- if guest then switch makePost with login -->
            <?php if($_SESSION['is_guest'] == 'true') : ?>
            <button id="guestLogin" onclick="window.location.href='login.php'">Log in</button>
            <?php elseif(isset($_SESSION['user_id'])): ?>
            <a href="post.php">Make Post</a>
            <?php endif; ?>
            <div class="info">
                <?php if($_SESSION['is_guest'] == 'true') : ?>
                <button id="guestLogin" onclick="window.location.href='signup.php'">Sign up</button>
                <?php else: ?>
                <a href="profile.php">My Profile</a>
                <?php endif; ?>
                <img src="<?php echo $iconData ? 'data:image/jpeg;base64,' . $iconData : '../Images/profile.jpg'; ?>"
                    id="avatarImage" title="click for more features" />
                <!-- if guest then hide div -->
                <?php if (!($_SESSION['is_guest'] == 'true')): ?>
                <div class="dropdown-content" id="dropdownContent">
                    <a href="profile.php">Profile</a>
                    <a href="logout.php">Logout</a>
                    <?php 
                    if ($_SESSION['userType'] == 1): ?>
                    <a href="admin.php">Admin</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php
                if ($_SESSION['is_guest'] == 'true') {
                    echo "<div class='ses'>Welcome to Bloggie</div>"; 
                } elseif (isset($_SESSION['user_id'])) { 

                    $userId = $_SESSION['user_id'];
                    echo "<div class='ses'>Welcome, " . $firstName . " " . $lastName . "</div>"; // 
                } else {
                    header("Location: login.php"); 
                    exit();
                }
                ?>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var avatarImage = document.getElementById('avatarImage');
        var dropdownContent = document.getElementById('dropdownContent');
        var isDropdownVisible = false;

        avatarImage.addEventListener('click', function(event) {
            event.stopPropagation();
            isDropdownVisible = !isDropdownVisible;
            if (isDropdownVisible) {
                dropdownContent.classList.add('show');
            } else {
                dropdownContent.classList.remove('show');
            }
        });

        document.addEventListener('click', function(event) {
            if (event.target !== avatarImage && event.target !== dropdownContent) {
                isDropdownVisible = false;
                dropdownContent.classList.remove('show');
            }
        });
    });
    </script>
    <?php foreach ($posts as $row): ?>
    <div class="post" title="Click for more details"
        onclick="window.location.href='content.php?postId=<?= $row['postId']; ?>';">
        <p class="title"><?= htmlspecialchars($row['postTitle']); ?></p>
        <p class="postTag"><?= htmlspecialchars($row['postTag']); ?></p>
        <?php if ($row['firstPicture'] !== null): ?>
        <img src="<?= $row['firstPicture'] ?>" alt="Post image"
            style="max-width: 600px; height: 70%; object-fit:cover;">
        <?php endif; ?>
        <div class="context" <?php if ($row['firstPicture'] === null) echo 'style="width: 92%;"'; ?>>
            <?= strlen($row['postContent']) > 300 ? substr(htmlspecialchars($row['postContent']), 0, 297) . "..." : htmlspecialchars($row['postContent']); ?>
        </div>
        <div class="user">
            <img src="<?= $row['icon'] ? 'data:image/jpeg;base64,' . base64_encode($row['icon']) : '../Images/profile.jpg'; ?>"
                alt="User avatar" id="test" />
            <p class="username" id="username">
                <?= htmlspecialchars($row['firstName']) . " " . htmlspecialchars($row['lastName']); ?></p>
            <a href="#" title="Like"><i class='far fa-thumbs-up' id="thumbsup"
                    onclick="toggleLike(event, this)"></i></a>
            <a href="#" title="Unlike"><i class='fas fa-thumbs-up' id="thumbsup1" style="display:none;"
                    onclick="toggleLike(event, this)"></i></a>
        </div>
    </div>

    <?php endforeach; ?>
    <script>
    function toggleLike(event, element) {
        event.stopPropagation();
        var like = element.id === "thumbsup" ? "thumbsup" : "thumbsup1";
        var oppositeLike = element.id === "thumbsup" ? "thumbsup1" : "thumbsup";
        element.style.display = "none";
        document.getElementById(oppositeLike).style.display = "inline-block";
    }
    </script>
    <script>
    window.onscroll = function() {
        scrollFunction()
    };

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("backToTopButton").style.display = "block";
        } else {
            document.getElementById("backToTopButton").style.display = "none";
        }
    }

    document.getElementById("backToTopButton").onclick = function() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }
    </script>
    <div class="navBar">
        <p>Tags/Categories</p>
        <button onclick="toggleButton(this)">Lifestyle</button>
        <button onclick="toggleButton(this)">Technology</button>
        <button onclick="toggleButton(this)">Education</button>
        <button onclick="toggleButton(this)">Travel</button>
        <button onclick="toggleButton(this)">Health&Fitness</button>
        <button onclick="toggleButton(this)">Gastronomy</button>
        <button onclick="toggleButton(this)">Career</button>
        <button onclick="toggleButton(this)">Arts&Culture</button>
    </div>
    <script>
    function toggleButton(button) {
        var isActive = button.classList.contains('active');
        if (isActive) {
            button.classList.remove('active');
        } else {
            button.classList.add('active');
        }

        var posts = document.querySelectorAll('.post');
        posts.forEach(function(post) {
            post.classList.remove('hidden');
        });

        if (!isActive) {
            filterPostsByTag(button.textContent);
        }
    }

    function filterPostsByTag(tag) {
        console.log("Filtering posts by tag:", tag);

        var selectedTags = document.querySelectorAll('.navBar button.active');
        var selectedTagNames = Array.from(selectedTags).map(btn => btn.textContent.trim());

        var posts = document.querySelectorAll('.post');
        var hasVisiblePost = false;

        posts.forEach(function(post) {
            var postTags = post.querySelector('.postTag').textContent;
            console.log("Post tags:", postTags);

            var allTagsPresent = selectedTagNames.every(tagName => {
                return postTags.toLowerCase().indexOf('#' + tagName.toLowerCase() + '#') !== -1;
            });

            if (allTagsPresent) {
                if (post.classList.contains('hidden')) {
                    post.classList.remove('hidden');
                }
                hasVisiblePost = true;
            } else {
                post.classList.add('hidden');
            }
        });

        var nopostElement = document.querySelector('.nopost');
        var footerElement = document.querySelector('.site-footer');

        if (nopostElement) {
            if (!hasVisiblePost) {
                console.log("No posts found for selected tags:", selectedTagNames.join(', '));
                nopostElement.style.display = 'block';
            } else {
                nopostElement.style.display = 'none';
            }
        }

        if (footerElement) {
            if (!hasVisiblePost) {
                footerElement.style.display = 'none';
            } else {
                footerElement.style.display = 'block';
            }
        }
    }

    // mouseon
    document.addEventListener('DOMContentLoaded', function() {
        var buttons = document.querySelectorAll('.navBar button');
        buttons.forEach(function(btn) {
            btn.addEventListener('mouseover', function() {
                if (!btn.classList.contains('active')) {
                    btn.classList.add('hover');
                }
            });

            btn.addEventListener('mouseout', function() {
                btn.classList.remove('hover');
            });

            btn.addEventListener('click', function() {
                btn.classList.remove('hover');
            });
        });
    });
    </script>
    <!-- Check posts exist -->
    <?php if (!empty($posts)): ?>
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
    <?php endif; ?>
    <?php if (empty($posts)): ?>
    <div class="nopost">
        There's nothing posted here yet...you may <a href="post.php">create one</a>.
    </div>

    <?php endif; ?>

</body>

</html>