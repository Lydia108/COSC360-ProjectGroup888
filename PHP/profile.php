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
include 'connection.php'; // Ensure the path is correct

// Fetch user details
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $userStmt = $conn->prepare("SELECT firstName, lastName, emailAddress, icon FROM user WHERE userId = ?");
    $userStmt->bind_param("i", $userId);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    
    if ($userResult->num_rows > 0) {
        $user = $userResult->fetch_assoc();
        $firstName = htmlspecialchars($user['firstName']);
        $lastName = htmlspecialchars($user['lastName']);
        $iconData = $user['icon'] ? base64_encode($user['icon']) : '';
    } else {
        echo "No user found.";
    }
    $userStmt->close();
} else {
    header("Location: login.php");
    exit();
}

// Fetch posts
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$posts = [];  // Initialize an empty array for post information

$postStmt = $conn->prepare("
    SELECT 
        post.postId,
        post.postTitle,
        post.postContent,
        post.postLike,
        post.postUserId,
        post.postDate,
        post.postTag,
        MAX(picture.postPicture) AS postPicture,
        MAX(picture.uploadTime) AS uploadTime
    FROM 
        post
    LEFT JOIN 
        (
            SELECT postId, MAX(postPicture) AS postPicture, MAX(uploadTime) AS uploadTime
            FROM picture 
            GROUP BY postId 
        ) AS picture ON post.postId = picture.postId
    WHERE 
        post.postUserId = ?
    GROUP BY 
        post.postId, 
        post.postTitle, 
        post.postContent, 
        post.postLike, 
        post.postUserId, 
        post.postDate, 
        post.postTag
");
if ($postStmt) {
    $userId = $_SESSION['user_id'];  
    $postStmt->bind_param("i", $userId); 
    $postStmt->execute();
    $postResult = $postStmt->get_result();

    while ($row = $postResult->fetch_assoc()) {
        $posts[] = $row;
    }

    $postStmt->close();
} else {
    echo "Error in preparing SQL statement.";
}
?>



<body>
    <div class="sidebar">
        <a href="main.php"><i class="fa-solid fa-house-chimney">&nbsp;Home</i></a>
        <div class="actions">
            <a href="post.php">Make Post</a>
            <div class="info">
                <a href="#">My Profile</a>
                <img src="<?php echo $iconData ? 'data:image/jpeg;base64,' . $iconData : '../Images/profile.jpg'; ?>"
                    id="sidebarAvatar" title="click for more features" />
                <div class="dropdown-content" id="dropdownContent">
                    <a href="profile.php">Profile</a>
                    <a href="logout.php">Logout</a>
                    <?php 
                    if ($_SESSION['userType'] == 1): ?>
                    <a href="admin.php">Admin</a>
                    <?php endif; ?>
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var avatarImage = document.getElementById('sidebarAvatar');
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
    <div class="container1">
        <span>
            <a href="#"><img
                    src="<?php echo $iconData ? 'data:image/jpeg;base64,' . $iconData : '../Images/profile.jpg'; ?>"
                    title="Change Avatar" id="avatarImage" />
            </a>
            <input type="file" id="fileInput" name="avatar" style="display: none;" />
            <p class="hint">(You may change your avatar by clicking it.)</p>
        </span>
        <script>
        document.getElementById('avatarImage').addEventListener('click', function() {
            document.getElementById('fileInput').click();
        });

        document.getElementById('fileInput').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarImage').src = e.target.result;
                    document.getElementById('sidebarAvatar').src = e.target.result;
                };
                reader.readAsDataURL(file);

                let formData = new FormData();
                formData.append('avatar', file);

                fetch('uploadAvatar.php', {
                        method: 'POST',
                        body: formData,
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Picture uploaded and saved in the database succesfully!');
                        } else {
                            console.error('Failed to upload picture!');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        });
        </script>
        <span class="profile">
            <div id="userInfo">
                <p class="name">First name: <span
                        id="displayFirstName"><?php echo htmlspecialchars($user['firstName']); ?></span>
                    <input type="text" id="editFirstName" value="<?php echo htmlspecialchars($user['firstName']); ?>"
                        required maxlength="30" style="display:none;">
                </p>
                <p class="number">Last name: <span
                        id="displayLastName"><?php echo htmlspecialchars($user['lastName']); ?></span>
                    <input type="text" id="editLastName" value="<?php echo htmlspecialchars($user['lastName']); ?>"
                        required maxlength="30" style="display:none;">
                </p>
                <p class="email">&nbsp;&nbsp;Email: <span
                        id="displayEmail"><?php echo htmlspecialchars($user['emailAddress']); ?></span>
                    <input type="email" id="editEmail" value="<?php echo htmlspecialchars($user['emailAddress']); ?>"
                        required pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" style="display:none;">
                </p>
            </div>
            <div class="button-container">
                <button id="editButton">Edit</button>
                <button id="updateButton" style="display: none;">Save</button>
            </div>
        </span>
    </div>
    <div class="container2">
        <fieldset>
            <legend>
                <button class="like">My posts</button>
            </legend>
            <div class="content">
                <div
                    style="overflow-y: auto; overflow-x: hidden; max-height: 300px; scrollbar-width: thin; scrollbar-color: #555 transparent;">

                    <?php if (empty($posts)): ?>
                        <p class="no-posts-message">You don't have any posts. You can <a href="post.php">create one</a>.</p>
                    <?php else: ?>

                    <?php foreach ($posts as $post): ?>
                    <!-- Link to content.php with postId as parameter -->
                    <a href="content.php?postId=<?php echo $post['postId']; ?>"
                        style="text-decoration: none; color: inherit;" title='Click for more details'>
                        <div class="postItem" style="display: flex; align-items: flex-start; margin-bottom: 10px;">

                            <?php if ($post['postPicture']): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($post['postPicture']); ?>"
                                title='Click for more details' class="postImage"
                                style="margin-right: 10px; margin-top:20px;" />
                            <?php endif; ?>

                            <div>
                                <p class='postTitle'
                                    style="margin-bottom: 5px; text-decoration: none; border-bottom: none;">
                                    <?php echo htmlspecialchars($post['postTitle']); ?>
                                </p>

                                <p class="title" style="text-decoration: none; border-bottom: none;">
                                    <?php 
                                    $content = htmlspecialchars($post['postContent']);
                                    echo strlen($content) > 100 ? substr($content, 0, 100) . '...' : $content; 
                                ?>
                                </p>
                            </div>
                        </div>
                    </a>

                    <div style="border-bottom: 2px solid #555; width:100%;"></div>

                    <?php endforeach; ?>

                    <?php endif; ?>

                </div>
            </div>

        </fieldset>
    </div>
    <script>
    let isEditing = false;
    document.getElementById('editButton').addEventListener('click', function() {
        isEditing = !isEditing;
        if (isEditing) {
            document.getElementById('editFirstName').style.display = 'inline';
            document.getElementById('editLastName').style.display = 'inline';
            document.getElementById('editEmail').style.display = 'inline';
            document.getElementById('displayFirstName').style.display = 'none';
            document.getElementById('displayLastName').style.display = 'none';
            document.getElementById('displayEmail').style.display = 'none';
            document.getElementById('updateButton').style.display = 'inline-block';
            this.textContent = 'Cancel';
        } else {
            document.getElementById('editFirstName').style.display = 'none';
            document.getElementById('editLastName').style.display = 'none';
            document.getElementById('editEmail').style.display = 'none';
            document.getElementById('displayFirstName').style.display = 'inline';
            document.getElementById('displayLastName').style.display = 'inline';
            document.getElementById('displayEmail').style.display = 'inline';
            document.getElementById('updateButton').style.display = 'none';
            this.textContent = 'Edit';
        }
    });

    document.getElementById('updateButton').addEventListener('click', function(event) {
        const firstName = document.getElementById('editFirstName').value;
        const lastName = document.getElementById('editLastName').value;
        const email = document.getElementById('editEmail').value;
        const emailPattern = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        if (firstName.trim().length === 0 || firstName.length > 30 || lastName.trim().length === 0 || lastName
            .length > 30) {
            alert("First name and last name cannot be empty and must not exceed 30 characters.");
            event.preventDefault();
            return false;
        }

        if (!emailPattern.test(email)) {
            alert("Please enter a valid email address.");
            event.preventDefault();
            return false;
        }

        const formData = new FormData();
        formData.append('firstName', firstName);
        formData.append('lastName', lastName);
        formData.append('email', email);
        formData.append('userId', <?php echo $userId; ?>);

        fetch('updateProfile.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Profile updated successfully!');
                    document.getElementById('editButton').textContent = 'Edit';
                    isEditing = false;
                    alert("Profile updated successfully!");
                    location.reload();
                } else {
                    console.error('Failed to update profile.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
    </script>
</body>

</body>

</html>