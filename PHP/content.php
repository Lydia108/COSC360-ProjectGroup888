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

    <style>
    .dynamic-button {
        margin-right: 10px;
        padding: 5px 10px;
    }
    </style>


</head>
<!-- profile -->
<?php
session_start();
include 'connection.php'; 
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT firstName, lastName, emailAddress, icon FROM user WHERE userId = ?");
    $stmt->bind_param("i", $userId); // 'i' indicates the type is integer
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $firstName = htmlspecialchars($user['firstName']);
        $lastName = htmlspecialchars($user['lastName']);
        if ($user['icon']) {
            $iconData = base64_encode($user['icon']);
        } else {
            $iconData = ''; 
        }   // Convert the icon data to a data URI
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

$postId = isset($_GET['postId']) ? intval($_GET['postId']) : 0;

if ($postId > 0) {
    $stmt = $conn->prepare("SELECT p.*, u.firstName, u.lastName, u.icon FROM post p INNER JOIN user u ON p.postUserId = u.userId WHERE p.postId = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($post = $result->fetch_assoc()) {
        // Retrieve all pictures
        $pictureStmt = $conn->prepare("SELECT postPicture FROM picture WHERE postId = ?");
        $pictureStmt->bind_param("i", $postId);
        $pictureStmt->execute();
        $pictureResult = $pictureStmt->get_result();
        $pictures = [];
        while ($pictureRow = $pictureResult->fetch_assoc()) {
            $pictures[] = 'data:image/jpeg;base64,' . base64_encode($pictureRow['postPicture']);
        }
        $pictureStmt->close();
    } else {
        echo "Post not found.";
        exit();
    }
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid post ID.";
    exit();
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
            <a href="post.php">Make Post</a>
            <div class="info">
                <a href="profile.php">My Profile</a>
                <img src="<?php echo $iconData ? 'data:image/jpeg;base64,' . $iconData : '../Images/profile.jpg'; ?>"
                    id="avatarImage" />
                <div class="dropdown-content">
                    <a href="profile.php">Profile</a>
                    <a href="logout.php">Logout</a>
                </div>
                <?php
if (isset($_SESSION['user_id'])) {
$userId = $_SESSION['user_id'];

echo "<div class='ses'>Welcome, " . $firstName . " " . $lastName . "</div>";
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
        <button>Personal</button>
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
        <img src="<?= $picture ?>" alt="Post image" style="max-width: 100%; margin-top: 10px;">
        <?php endforeach; ?>
        <button id="comment"><i class='far fa-comment-alt'></i></button>
        <!-- <a href="#"><i class='far fa-thumbs-up' id="thumbsup" onclick="toggleLike()"></i></a>
        <a href="#"><i class='fas fa-thumbs-up' id="thumbsup1" style="display:none;" onclick="toggleLike()"></i></a> -->

    </div>
    <div class="separate">
        <hr>
    </div>
    <div class="addComment">
        <textarea placeholder="Comment something..." id="autoresizing"></textarea>
        <br>
        <button id="submitComment">Comment</button>
    </div>
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
                    <span class="user"><?= htmlspecialchars($comment['firstName']) . " " . htmlspecialchars($comment['lastName']); ?>:</span>
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