<?php
session_start();
include 'connection.php'; // Ensure this file has the database connection details

// Check if user is not an admin, redirect if not
if (!isset($_SESSION['user_id']) || $_SESSION['userType'] != '1') {
    header("Location: main.php");
    exit();
}

// Handle user deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM user WHERE userId = ?");
    $stmt->bind_param("i", $deleteId);
    if ($stmt->execute()) {
        $deleteMessage = "User deleted successfully.";
    } else {
        $deleteMessage = "Error deleting user.";
    }
    $stmt->close();
}

// Fetch all users
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
    <title>Admin Panel</title>
    <!-- Add your CSS and JS links here -->
</head>
<body>

<h2>Welcome Admin!</h2>

<?php if (isset($deleteMessage)) : ?>
    <p><?php echo $deleteMessage; ?></p>
<?php endif; ?>

<table border="1">
    <thead>
        <tr>
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
                <td><?php echo $user['userId']; ?></td>
                <td><?php echo $user['firstName']; ?></td>
                <td><?php echo $user['lastName']; ?></td>
                <td><?php echo $user['emailAddress']; ?></td>
                <td><a href="admin.php?delete=<?php echo $user['userId']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="main.php">Go back to main page</a>

</body>
</html>
