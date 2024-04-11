<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="utf-8">
    <title>Bloggie</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/search.css">
    <script src='https://www.w3schools.cn/fonts/kit/a076d05399.js'></script>
    <script src="https://kit.fontawesome.com/d1344ce34d.js" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/0485a9f289.js" crossorigin="anonymous"></script>

</head>
<?php
session_start();
include 'connection.php';

$posts = [];
$iconData = null; 
if(isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmtIcon = $conn->prepare("SELECT icon FROM user WHERE userId = ?");
    $stmtIcon->bind_param("i", $userId);
    $stmtIcon->execute();
    $resultIcon = $stmtIcon->get_result();

    if ($resultIcon->num_rows > 0) {
        $userData = $resultIcon->fetch_assoc();
        $iconData = $userData['icon'] ? 'data:image/jpeg;base64,' . base64_encode($userData['icon']) : '../Images/profile.jpg';
    }

    $stmtIcon->close();
}
if(isset($_GET['title'])) {
    $searchTerm = '%' . $_GET['title'] . '%';
    $stmt = $conn->prepare("SELECT p.postId, p.postTitle, p.postContent, p.postTag, p.postDate, u.userId, u.firstName, u.lastName, u.icon, COUNT(c.postId) as commentCount
    FROM post p 
    INNER JOIN user u ON p.postUserId = u.userId 
    LEFT JOIN comment c ON p.postId = c.postId
    WHERE p.postTitle LIKE ?
    GROUP BY p.postId, p.postTitle, p.postContent, p.postTag, p.postDate, u.userId, u.firstName, u.lastName, u.icon
    ORDER BY commentCount DESC, p.postDate DESC");
    
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $postsResult = $stmt->get_result();

    if ($postsResult->num_rows > 0) {
        while($row = $postsResult->fetch_assoc()) {
            $postId = $row['postId'];
            $postTitle = htmlspecialchars($row['postTitle']);
            $postContent = htmlspecialchars($row['postContent']);
            $postTag = htmlspecialchars($row['postTag']);
            $postDate = htmlspecialchars($row['postDate']);
            $userId = $row['userId'];
            $firstName = htmlspecialchars($row['firstName']);
            $lastName = htmlspecialchars($row['lastName']);
            $iconData = $row['icon'] ? 'data:image/jpeg;base64,' . base64_encode($row['icon']) : '../Images/profile.jpg';
            $commentCount = $row['commentCount'];

            $pictureQuery = "SELECT postPicture FROM picture WHERE postId = ?";
            $pictureStmt = $conn->prepare($pictureQuery);
            $pictureStmt->bind_param("i", $postId);
            $pictureStmt->execute();
            $pictureResult = $pictureStmt->get_result();

            $pictures = [];
            while ($pictureRow = $pictureResult->fetch_assoc()) {
                $pictures[] = 'data:image/jpeg;base64,' . base64_encode($pictureRow['postPicture']);
            }

            $posts[] = [
                'postId' => $postId,
                'postTitle' => $postTitle,
                'postContent' => $postContent,
                'postTag' => $postTag,
                'postDate' => $postDate,
                'userId' => $userId,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'iconData' => $iconData,
                'commentCount' => $commentCount,
                'pictures' => $pictures
            ];
        }
    }
    $stmt->close();
}

?>

<body>
    <div class="sidebar">
        <a href="main.php"><i class="fa-solid fa-house-chimney">&nbsp;Home</i></a>

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
                <img src="<?php echo $iconData ? $iconData : '../Images/profile.jpg'; ?>" id="avatarImage"
                    title="click for more features" />
                <!-- if guest then hide div -->
                <?php if (!isset($_SESSION['is_guest']) || $_SESSION['is_guest'] !== true): ?>
                <div class="dropdown-content" id="dropdownContent">
                    <a href="profile.php">Profile</a>
                    <a href="logout.php">Logout</a>
                    <?php 
                    if ($_SESSION['userType'] == 1): ?>
                    <a href="admin.php">Admin</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
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
                <?php
                if ($_SESSION['is_guest'] == 'true') {
                     echo "<div class='ses'>Welcome to Bloggie</div>"; 
                } elseif (isset($_SESSION['user_id'])) { 
                    $userId = $_SESSION['user_id'];
                    echo "<div class='ses'>Welcome! ";
                if (!empty($posts)) {
                    echo $firstName . " " . $lastName;
                }
                    echo "</div>";
                } else {
                    header("Location: login.php"); 
                exit();
                }
                ?>
            </div>
        </div>
    </div>
    <div class="search-results">
        <?php foreach ($posts as $post): ?>
        <div class='post' onclick="window.location.href='content.php?postId=<?= $post['postId']; ?>';">
            <h2>Title: <?= $post['postTitle']; ?></h2>
            <?php if (!empty($post['pictures'])): ?>
            <?php foreach ($post['pictures'] as $picture): ?>
            <img src="<?= $picture; ?>" alt="Post image">
            <?php endforeach; ?>
            <?php endif; ?>
            <p>Content: <?= substr($post['postContent'], 0, 300); ?>...</p>
            <?php if (!empty($post['postTag'])): ?>
            <p>Tag: <?= $post['postTag']; ?></p>
            <?php endif; ?>
            <p>Posted by: <?= $post['firstName'] . ' ' . $post['lastName']; ?></p>
            <p>Comments: <?= $post['commentCount']; ?></p>
        </div>
        <?php endforeach; ?>

        <?php if (empty($posts)): ?>
        <p class="no-posts-found">No posts found. Please click top-left symbol to go back.<br>
            You may wanna <a href="post.php">create one</a>!
        </p>
        <?php endif; ?>
    </div>




</body>


</html>