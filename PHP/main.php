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
<!-- post and picture-->
<?php
$query = "SELECT p.postId, p.postTitle, p.postContent, p.postTag, p.postDate, u.firstName, u.lastName, u.icon 
FROM post p 
INNER JOIN user u ON p.postUserId = u.userId 
ORDER BY p.postDate DESC";
$postsResult = $conn->query($query);
$posts = [];

if ($postsResult->num_rows > 0) {
    while($row = $postsResult->fetch_assoc()) {
        // firstPicture
        $pictureQuery = "SELECT postPicture FROM picture WHERE postId = ? ORDER BY uploadTime ASC";
        $pictureStmt = $conn->prepare($pictureQuery);
        $pictureStmt->bind_param("i", $row['postId']);
        $pictureStmt->execute();
        $pictureResult = $pictureStmt->get_result();
        if ($pictureRow = $pictureResult->fetch_assoc()) {
            $row['firstPicture'] = 'data:image/jpeg;base64,' . base64_encode($pictureRow['postPicture']);
        } else {
            $row['firstPicture'] = '../Images/noImage.jpg';
        }
        $posts[] = $row;
        
    }
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
    <?php foreach ($posts as $row): ?>
    <div class="post" title="Click for more details"
        onclick="window.location.href='content.php?postId=<?= $row['postId']; ?>';">
        <p class="title"><?= htmlspecialchars($row['postTitle']); ?></p>
        <img src="<?= $row['firstPicture'] ?>" alt="Post image"
            style="max-width: 600px; height: 70%; object-fit:cover;">
        <div class="context">
            <?= strlen($row['postContent']) > 500 ? substr(htmlspecialchars($row['postContent']), 0, 500) . "..." : htmlspecialchars($row['postContent']); ?>
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