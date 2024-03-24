<?php
session_start();
include 'connection.php'; // Ensure this file has the database connection details
if (!isset($_SESSION['user_id']) || $_SESSION['userType'] != '1') {
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
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Administrator</title>
    <link rel="stylesheet" href="admin.css">
    <style>
    #deleteUser {
        border-radius: 15px;
        padding: 10px 15px;
        width: 200px;
        background-color: #f3cd4f;
        font-size: 22px;
        cursor:pointer;
        font-weight:bold;
    }

    #deleteUser:hover {
        filter: brightness(0.9);
        color: white;
        cursor:pointer;

    }
    #deletePost {
        border-radius: 15px;
        padding: 10px 15px;
        width: 200px;
        background-color: #f3cd4f;
        font-size: 22px;
        cursor:pointer;
        font-weight:bold;
    }

    #deletePost:hover {
        filter: brightness(0.9);
        color: white;
        cursor:pointer;

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
    function deletePostVisibility(){
        alert("Still developing...");
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
    </script>
</head>

<body>
    <h2>Welcome Admin <?php echo $adminFullName; ?>!</h2>

    <button onclick="deleteUserVisibility()" id="deleteUser">Delete User</button>
    <button onclick="deletePostVisibility()" id="deletePost">Delete Post</button>
    <table id="userTable" border="1" style="display:none;">
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAll" title="select all" onchange="selectAllCheckboxes()"></th>
                <th>User ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email Address</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) : ?>
            <tr>
                <td><input type="checkbox" name="delete_checkbox" value="<?php echo $user['userId']; ?>"></td>
                <td><?php echo $user['userId']; ?></td>
                <td><?php echo $user['firstName']; ?></td>
                <td><?php echo $user['lastName']; ?></td>
                <td><?php echo $user['emailAddress']; ?></td>
                <td><a href="admin.php?delete=<?php echo $user['userId']; ?>"
                        onclick="return confirm('Are you sure you want to delete this user?');">Delete</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button id="deleteButton" style="display:none;" onclick="deleteSelected()">Delete Selected</button>

    <p>
        <a href="main.php">Go back to main page</a>
    </p>

</body>

</html>