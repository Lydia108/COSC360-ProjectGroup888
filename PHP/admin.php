<?php
session_start();
include 'connection.php';

// Security check: ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['userType'] != '1') {
    header("Location: login.php");
    exit;
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
        $adminFullName = htmlspecialchars($user['firstName']) . " " . htmlspecialchars($user['lastName']);
    }
    $stmt->close();
}
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM user WHERE userId = ?");
    $stmt->bind_param("i", $deleteId);
    if ($stmt->execute()) {
        echo '<script>alert("User deleted successfully.");</script>';
    } else {
        echo '<script>alert("Error deleting user.");</script>';
    }
    $stmt->close();
}
// Fetch users
$query = "SELECT * FROM user";
$result = $conn->query($query);
$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Fetch posts and their pictures
$posts = [];
$postQuery = "SELECT post.postId, post.postTitle, post.postContent, post.postDate
              FROM post 
              ORDER BY post.postDate ASC";
$postResult = $conn->query($postQuery);

if ($postResult->num_rows > 0) {
    while ($row = $postResult->fetch_assoc()) {
        $postId = $row['postId'];

        // Fetch associated pictures
        $stmt = $conn->prepare("SELECT postPicture FROM picture WHERE postId = ?");
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        $pictures = [];
        while ($pictureRow = $result->fetch_assoc()) {
            $pictures[] = 'data:image/jpeg;base64,' . base64_encode($pictureRow['postPicture']);
        }
        $stmt->close();

        // Append pictures to the post
        $row['pictures'] = $pictures;
        $posts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Administrator</title>
    <link rel="stylesheet" href="../CSS/admin.css">

    <script>
    function deleteUserVisibility() {
        var table = document.getElementById('userTable');
        var deleteButton = document.getElementById('deleteButton');
        var searchUser = document.getElementById('searchUser');

        if (table.style.display === 'none') {
            table.style.display = 'table';
            deleteButton.style.display = 'block';
            searchUser.style.display = 'block';
        } else {
            table.style.display = 'none';
            deleteButton.style.display = 'none';
            searchUser.style.display = 'none';
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
    let usageDataVisible = false;

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
            usageDataVisible = true;
        } else {
            toggleUsageDataVisibility();
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

    function searchUsersByFullName() {
        var input = document.getElementById("searchUser");
        var filter = input.value.toUpperCase();
        var table = document.getElementById("userTable");
        var tr = table.getElementsByTagName("tr");

        for (var i = 0; i < tr.length; i++) {
            var td = tr[i].getElementsByTagName("td")[2];
            if (td) {
                var txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
    </script>

</head>

<body>
    <h2>Welcome Admin <?php echo $adminFullName; ?>!</h2>

    <button onclick="deleteUserVisibility()" id="deleteUser">Delete User</button>
    <button onclick="deletePostVisibility()" id="deletePost">Delete Post</button>
    <button onclick="checkWebUsage()" id="checkUsage">Usage</button>
    <input class='searchUser' id='searchUser' placeholder='search users by name' style="display:none;"
        onkeyup="searchUsersByFullName()" />
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

    <form method="POST" action="deletePost.php">
        <div id="postTable" style="display:none;">
            <?php foreach ($posts as $post) : ?>
            <div class="postContainer">
                <!-- Checkbox for selecting the post -->
                <input type="checkbox" id="postCheckbox_<?php echo $post['postId']; ?>" name="delete_post_checkbox[]"
                    value="<?php echo htmlspecialchars($post['postId']); ?>">
                <label for="postCheckbox_<?php echo $post['postId']; ?>">
                    <h3><?php echo htmlspecialchars($post['postTitle']); ?></h3>
                    <div class="postPictures">
                        <?php foreach ($post['pictures'] as $picture) : ?>
                        <img src="<?php echo $picture; ?>" alt="Post Picture" width="100">
                        <?php endforeach; ?>
                    </div>
                    <p><?php echo htmlspecialchars($post['postContent']); ?></p>
                </label>
            </div>
            <?php endforeach; ?>
            <button type="submit" id="deletePosts" style="margin-top: 20px;">Delete Selected Posts</button>
        </div>
    </form>
    <script>
    <?php if (isset($_SESSION['message'])) : ?>
    alert("<?php echo $_SESSION['message']; ?>");
    <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    </script>
    <div id="usageStats"></div>


    <p>
        <a href="main.php">Go back to main page</a>
    </p>

</body>

</html>