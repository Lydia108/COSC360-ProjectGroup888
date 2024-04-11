<?php
session_start();
include 'connection.php';
// Check logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check admin
if ($_SESSION['userType'] != '1') {
    header("Location: main.php");
    exit();
}
$adminFullName = "";
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT firstName, lastName FROM user WHERE userId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $adminFullName = $user['firstName'] . " " . $user['lastName'];
    }
    $stmt->close();
}
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM user WHERE userId = ?");
    $stmt->bind_param("i", $deleteId);
    if ($stmt->execute()) {
        // $deleteMessage = "User deleted successfully.";
        echo '<script>alert("User deleted successfully.");</script>';
    } else {
        echo '<script>alert("Error deleting user.");</script>';
    }
    $stmt->close();
}
$query = "SELECT * FROM user";
$result = $conn->query($query);

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Fetch posts
$postQuery = "SELECT * FROM post ORDER BY postDate ASC";
$postResult = $conn->query($postQuery);

$posts = [];
if ($postResult->num_rows > 0) {
    while ($row = $postResult->fetch_assoc()) {
        $posts[] = $row;
    }
}

// Fetch posts and their pictures
$posts = [];
$postQuery = "SELECT post.*, GROUP_CONCAT(picture.postPicture SEPARATOR '|') AS pictures 
              FROM post 
              LEFT JOIN picture ON post.postId = picture.postId 
              GROUP BY post.postId 
              ORDER BY post.postDate ASC";
$postResult = $conn->query($postQuery);

if ($postResult->num_rows > 0) {
    while ($row = $postResult->fetch_assoc()) {
        $row['pictures'] = explode('|', $row['pictures']); 
        $posts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Administrator</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        td,
        th {
            margin: 20px;
        }

        #deleteUser {
            border-radius: 15px;
            padding: 10px 15px;
            width: 200px;
            background-color: #f3cd4f;
            font-size: 22px;
            cursor: pointer;
            font-weight: bold;
        }

        #deleteUser:hover {
            filter: brightness(0.9);
            color: white;
            cursor: pointer;

        }

        #deletePost {
            border-radius: 15px;
            padding: 10px 15px;
            width: 200px;
            background-color: #f3cd4f;
            font-size: 22px;
            cursor: pointer;
            font-weight: bold;
        }

        #deletePost:hover {
            filter: brightness(0.9);
            color: white;
            cursor: pointer;

        }

        #checkUsage {
            border-radius: 15px;
            padding: 10px 15px;
            width: 200px;
            background-color: #f3cd4f;
            font-size: 22px;
            cursor: pointer;
            font-weight: bold;
        }

        #checkUsage:hover {
            filter: brightness(0.9);
            color: white;
            cursor: pointer;
        }

        .postContainer {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }

        .postContainer h3 {
            margin-top: 0;
        }

        .postPictures {
            display: flex;
            flex-wrap: wrap;
            margin: 10px 0;
        }

        .postPictures img {
            margin-right: 10px;
            margin-bottom: 10px;
            max-width: 100px;
            height: auto;
        }
    </style>

    <script>
        function deleteUserVisibility() {
            var table = document.getElementById('userTable');
            var deleteButton = document.getElementById('deleteButton');

            if (table.style.display === 'none') {
                table.style.display = 'table';
                deleteButton.style.display = 'block';
            } else {
                table.style.display = 'none';
                deleteButton.style.display = 'none';
            }
        }


        function deleteSelected() {
            var checkboxes = document.getElementsByName('delete_checkbox');
            var selectedIds = [];

            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                    selectedIds.push(checkboxes[i].value);
                }
            }

            if (selectedIds.length > 0 && confirm('Are you sure you want to delete the selected users?')) {
                window.location.href = 'admin.php?delete=' + selectedIds.join(',');
            }
        }


        function selectAllCheckboxes() {
            var checkboxes = document.getElementsByName('delete_checkbox');
            var selectAllCheckbox = document.getElementById('selectAll');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = selectAllCheckbox.checked;
            }
        }
        let usageDataVisible = false; // Track whether the usage data is currently shown

        function checkWebUsage() {
            if (!usageDataVisible) {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'usageData.php', true);
                xhr.onload = function() {
                    if (this.status == 200) {
                        var data = JSON.parse(this.responseText);
                        displayUsageData(data);
                    }
                };
                xhr.send();
                usageDataVisible = true; // Set to true after loading data
            } else {
                toggleUsageDataVisibility(); // Call toggle function if data is already visible
            }
        }

        function displayUsageData(data) {
            var statsHtml = `
        <h3>Website Usage Statistics</h3>
        <p>Total Blogs: ${data.totalBlogs}</p>
        <p>Total Users: ${data.totalUsers}</p>
        <p>Total Comments: ${data.totalComments}</p>
    `;

            document.getElementById('usageStats').innerHTML = statsHtml;
        }

        function toggleUsageDataVisibility() {
            var usageStats = document.getElementById('usageStats');
            if (usageStats.style.display === 'none' || !usageStats.style.display) {
                usageStats.style.display = 'block';
                usageDataVisible = true;
            } else {
                usageStats.style.display = 'none';
                usageDataVisible = false;
            }
        }

        function deletePostVisibility() {
            var table = document.getElementById('postTable');
            var deleteButton = document.getElementById('deletePosts');

            if (table.style.display === 'none') {
                table.style.display = 'table';
                deleteButton.style.display = 'block';
            } else {
                table.style.display = 'none';
                deleteButton.style.display = 'none';
            }
        }
    </script>

</head>

<body>
    <h2>Welcome Admin <?php echo $adminFullName; ?>!</h2>

    <button onclick="deleteUserVisibility()" id="deleteUser">Delete User</button>
    <button onclick="deletePostVisibility()" id="deletePost">Delete Post</button>
    <button onclick="checkWebUsage()" id="checkUsage">Usage</button>

    <table id="userTable" border="1" style="display:none;">
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAll" title="select all" onchange="selectAllCheckboxes()"></th>
                <th>User ID</th>
                <th>Full Name</th>
                <th>Email Address</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) : ?>
                <?php if ($user['userId'] != $_SESSION['user_id']) : ?>
                    <tr>
                        <td><input type="checkbox" name="delete_checkbox" value="<?php echo $user['userId']; ?>"></td>
                        <td><?php echo $user['userId']; ?></td>
                        <td><?php echo $user['firstName'] . " " . $user['lastName']; ?></td>
                        <td><?php echo $user['emailAddress']; ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>


    <button id="deleteButton" style="display:none;" onclick="deleteSelected()">Delete Selected User(s)</button>

    <!-- <form method="POST" action="deletePost.php">
        <table id="postTable" style="display:none;">
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Post ID</th>
                    <th>Title</th>
                    <th>Content</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post) : ?>
                    <tr>
                        <td><input type="checkbox" name="delete_post_checkbox[]" value="<?php echo htmlspecialchars($post['postId']); ?>"></td>
                        <td><?php echo htmlspecialchars($post['postId']); ?></td>
                        <td><?php echo htmlspecialchars($post['postTitle']); ?></td>
                        <td><?php echo htmlspecialchars($post['postContent']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" id="deletePosts">Delete Selected Post(s)</button>
    </form> -->

    <form method="POST" action="deletePost.php">
        <div id="postTable" style="display:none;">
            <?php foreach ($posts as $post) : ?>
                <div class="postContainer">
                    <input type="checkbox" name="delete_post_checkbox[]" value="<?php echo htmlspecialchars($post['postId']); ?>">
                    <h3><?php echo htmlspecialchars($post['postTitle']); ?></h3>
                    <div class="postPictures">

                        <?php if (!empty($post['postId'])) : ?>
                            <img src="getImage.php?postId=<?php echo htmlspecialchars($post['postId']); ?>" alt="Post Picture" width="100">
                        <?php endif; ?>


                    </div>
                    <p><?php echo htmlspecialchars($post['postContent']); ?></p>
                </div>
            <?php endforeach; ?>
            <button type="submit" id="deletePosts">Delete Selected Post(s)</button>
        </div>
    </form>



    <div id="usageStats"></div>


    <p>
        <a href="main.php">Go back to main page</a>
    </p>

</body>

</html>