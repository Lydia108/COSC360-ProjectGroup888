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
include 'connection.php'; // Make sure this path is correct

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

<body>
    <div class="sidebar">
        <a href="main.php"><i class="fa-solid fa-house-chimney">&nbsp;Home</i></a>
        <div class="actions">
            <a href="post.php">Make Post</a>
            <div class="info">
                <a href="#">My Profile</a>
                <img src="<?php echo $iconData ? 'data:image/jpeg;base64,' . $iconData : '../Images/profile.jpg'; ?>"
                    id="sidebarAvatar" title="click for more features"/>
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
                <button class="like">Liked</button>———————————————————————————————<button
                    class="comment">Commented</button>
            </legend>
            <div class="content">
                <p class="title">Still developing here!</p>

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