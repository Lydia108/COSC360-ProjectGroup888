<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="utf-8">
    <title>Bloggie</title>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/content.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://kit.fontawesome.com/d1344ce34d.js" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/0485a9f289.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
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
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
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
if ($_SESSION['is_guest']== 'true') {
    $welcomeMessage = "";
    $iconData = ''; 
} else if (isset($_SESSION['user_id'])) {
    $_SESSION['is_guest'] = false; 
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT firstName, lastName, emailAddress, icon FROM user WHERE userId = ?");
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
    } else {
        echo "No user found.";
    }
    $stmt->close();
} else {
    header("Location: login.php");
    exit();
} 


?>
<!-- content -->
<?php
include 'connection.php';
$isGuest = isset($_SESSION['is_guest']) && $_SESSION['is_guest'] == true;
$firstName = "Guest";
$lastName = "";
$iconData2 = ''; // 

if (!$isGuest) {
    if (isset($_SESSION['user_id'])) {
        $_SESSION['is_guest'] = false; 
        $userId = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT firstName, lastName, emailAddress, icon FROM user WHERE userId = ?");
        $stmt->bind_param("i", $userId); 
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $firstName = htmlspecialchars($user['firstName']);
            $lastName = htmlspecialchars($user['lastName']);
            $iconData2 = $user['icon'] ? 'data:image/jpeg;base64,' . base64_encode($user['icon']) : '';
        } else {
            echo "No user found.";
        }
        $stmt->close();
    } 
}

$postId = isset($_GET['postId']) ? intval($_GET['postId']) : 0;
$pictures = [];
if ($postId > 0) {
    $stmt = $conn->prepare("SELECT p.*, u.firstName, u.lastName, u.icon FROM post p INNER JOIN user u ON p.postUserId = u.userId WHERE p.postId = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
        // Retrieve all pictures
        $pictureStmt = $conn->prepare("SELECT postPicture FROM picture WHERE postId = ?");
        $pictureStmt->bind_param("i", $postId);
        $pictureStmt->execute();
        $pictureResult = $pictureStmt->get_result();
        
        while ($pictureRow = $pictureResult->fetch_assoc()) {
            $pictures[] = 'data:image/jpeg;base64,' . base64_encode($pictureRow['postPicture']);
        }
        $pictureStmt->close();
    } else {
        echo "Post not found.";
        exit();
    }
    $stmt->close();
}
?>
<!-- comment -->
<?php
include 'connection.php'; 
$postId = isset($_GET['postId']) ? intval($_GET['postId']) : 0;
if ($postId > 0) {
    $commentsQuery = "SELECT c.*, u.firstName, u.lastName, u.icon 
    FROM comment c 
    INNER JOIN user u ON c.postUserId = u.userId 
    WHERE c.postId = ? 
    ORDER BY c.postDate DESC";
    $stmt = $conn->prepare($commentsQuery);
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $commentsResult = $stmt->get_result();
    $comments = [];
    while($comment = $commentsResult->fetch_assoc()) {
        $comments[] = $comment;
    }
    $stmt->close();
    $conn->close();
}
?>

<body>
    <a href="#top" id="backToTopButton" title="Go to top"> TOP </a>
    <div class="sidebar">
        <a href="main.php"><i class="fa-solid fa-house-chimney">&nbsp;Home</i></a>
        <div class="search-bar">
            <input type="text" placeholder="Search...">
            <i class="fa fa-search"></i>
        </div>
        <div class="actions">
            <?php if (isset($_SESSION['is_guest']) && $_SESSION['is_guest'] == true): ?>
            <button id="guestLogin" onclick="window.location.href='login.php'">Log in</button>
            <?php else: ?>
            <a href="post.php">Make Post</a>
            <?php endif; ?>
            <div class="info">
                <?php if (isset($_SESSION['is_guest']) && $_SESSION['is_guest'] == true): ?>
                <button id="guestLogin" onclick="window.location.href='signup.php'">Sign up</button>
                <?php else: ?>
                <a href="profile.php">My profile</a>
                <?php endif; ?>
                <img src="<?= $iconData ? 'data:image/jpeg;base64,' . $iconData : '../Images/profile.jpg'; ?>"
                    id="avatarImage" />
                <!-- if guest then hide div -->
                <?php if (!isset($_SESSION['is_guest']) || $_SESSION['is_guest'] !== true): ?>
                <div class="dropdown-content">
                    <a href="profile.php">Profile</a>
                    <a href="logout.php">Logout</a>
                </div>
                <?php endif; ?>
                <?php
                if (isset($_SESSION['is_guest']) && $_SESSION['is_guest'] == true) {
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

    <div class="navBar">
        <p>Tags/Categories</p>
        <button>Lifestyle</button>
        <button>Technology</button>
        <button>Education</button>
        <button>Travel</button>
        <button>Health&Fitness</button>
        <button>Gastronomy</button>
        <button>Career</button>
        <button>Arts&Culture</button>
    </div>

    <div class="content">
        <p class="userName">
            <img src="<?= $post['icon'] ? 'data:image/jpeg;base64,'.base64_encode($post['icon']) : '../Images/profile.jpg'; ?>"
                alt="Author's avatar" style="width: 50px; height: 50px; border-radius: 50%;">
            <span><?= htmlspecialchars($post['firstName']) . " " . htmlspecialchars($post['lastName']); ?></span>
        </p>
        <p class="datetime"><?= htmlspecialchars($post['postDate']); ?></p>
        <h1><?= htmlspecialchars($post['postTitle']); ?></h1>
        <p class="context"><?= nl2br(htmlspecialchars($post['postContent'])); ?></p>
        <!-- Display all images -->
        <?php foreach ($pictures as $picture): ?>
        <img src="<?= $picture ?>" alt="Post image" style="cursor: pointer;"
            onclick="showImageFullScreen('<?= $picture ?>')">
        <?php endforeach; ?>

        <?php if(!$isGuest): ?>
        <button id="comment"><i class='far fa-comment-alt'></i></button>
        <?php endif; ?>
        <!-- <a href="#"><i class='far fa-thumbs-up' id="thumbsup" onclick="toggleLike()"></i></a>
        <a href="#"><i class='fas fa-thumbs-up' id="thumbsup1" style="display:none;" onclick="toggleLike()"></i></a> -->

    </div>
    <div id="fullscreen-overlay" style="display: none;">
        <img id="fullscreen-image" src="" alt="Full Screen Image">
    </div>

    <script>
    function showImageFullScreen(imageSrc) {
        var overlay = document.getElementById('fullscreen-overlay');
        var image = document.getElementById('fullscreen-image');

        image.src = imageSrc;
        overlay.style.display = 'flex';

        overlay.onclick = function() {
            overlay.style.display = 'none';
        };
    }
    </script>
    <?php if(!$isGuest): ?>
    <div class="separate">
        <hr>
    </div>
    <?php endif; ?>
    <!-- guest hide -->
    <?php if(!$isGuest): ?>
    <div class="addComment">
        <textarea placeholder="Comment something..." id="autoresizing"></textarea>
        <br>
        <button id="submitComment">Comment</button>
    </div>
    <?php endif; ?>
    <script>
    $(document).ready(function() {
        $('.addComment').hide();
        $('#comment').click(function() {
            $('.addComment').toggle();
        });
    });
    </script>

    <div class="separate">
        <hr>
    </div>
    <div class="commentsList">
        <ul>
            <?php foreach ($comments as $index => $comment): ?>
            <li>
                <img src="<?= $comment['icon'] ? 'data:image/jpeg;base64,' . base64_encode($comment['icon']) : '../Images/profile.jpg'; ?>"
                    alt="User Avatar" class="avatar">
                <div class="contents">
                    <span
                        class="user"><?= htmlspecialchars($comment['firstName']) . " " . htmlspecialchars($comment['lastName']); ?>:</span>
                    <span class="dateTime"><i><?= htmlspecialchars($comment['postDate']); ?></i></span>
                    <br>
                    <span class="detail"><?= htmlspecialchars($comment['postComment']); ?></span>
                </div>
            </li>
            <?php if ($index < count($comments) - 1): ?>
            <hr class="comment-divider">
            <?php endif; ?>
            <?php endforeach; ?>
        </ul>
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

    <script>
    $(document).ready(function() {
        $('#submitComment').click(function(event) {
            event.preventDefault(); // 
            var comment = $('#autoresizing').val(); // 
            var postId = <?= $postId; ?>; // 
            // 确保评论不为空
            if (comment.trim() === '') {
                alert('Please enter a comment.');
                return;
            }

            $.ajax({
                url: 'uploadComment.php',
                type: 'POST',
                data: {
                    comment: comment,
                    postId: postId
                },
                success: function(data) {
                    alert('Comment posted successfully!');
                    $('#autoresizing').val('');
                    $('.commentsList ul').append(data);
                    window.location.reload(true);

                },

                error: function() {
                    alert('Error posting the comment.');
                }
            });
        });
    });
    </script>

</body>




</html>