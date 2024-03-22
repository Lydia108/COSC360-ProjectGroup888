<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="utf-8">
    <title>Bloggie</title>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/post.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src='https://www.w3schools.cn/fonts/kit/a076d05399.js'></script>
    <script src="https://kit.fontawesome.com/d1344ce34d.js" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/0485a9f289.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const navBarButtons = document.querySelectorAll('.navBar button');
        const textarea = document.querySelector('.context');
        navBarButtons.forEach(button => {
            button.addEventListener('click', function() {
                const currentText = textarea.value;
                if (currentText.includes('#')) {
                    alert('You can only add one tag.');
                } else {
                    const tagText = `#${this.textContent}# `;
                    textarea.value = `${currentText} ${tagText}`;
                }
            });
        });
    });
    </script>
    <div class="post">
        <input class="title" placeholder="Edit title..." />
        <br>
        <textarea class="context" placeholder="Enter text..."></textarea>

        <img id="imagePreview" alt="Image preview" />
        <div class="upload-row">
            <button class="photo" onclick="document.getElementById('imageUpload').click();">
                <i class="fa-regular fa-image" id="symbol"></i>
            </button>
            <div id="previewContainer"></div>
            <button class="submit">Post now</button>
        </div>
        <input type="file" id="imageUpload" accept="image/*" multiple style="display: none;" />

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
        $('.submit').click(function(event) {
            event.preventDefault();

            let postTitle = $('.title').val();
            let postContent = $('.context').val();
            let formData = new FormData();
            formData.append('postTitle', postTitle);
            formData.append('postContent', postContent);
            let files = $('#imageUpload')[0].files;
            for (let i = 0; i < files.length; i++) {
                formData.append('images[]', files[i]);
            }
            // AJAX 
            $.ajax({
                url: 'uploadPost.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    alert('Post submitted successfully.');
                    window.location.href = 'main.php';
                },
                error: function() {
                    alert('Error submitting the post.');
                }
            });
        });

    });
    </script>
    <script>
    document.getElementById('imageUpload').addEventListener('change', function(event) {
        const files = event.target.files;
        const previewContainer = document.getElementById('previewContainer');

        if (previewContainer.children.length + files.length > 5) {
            alert('You can select up to 5 images in total.');
            return;
        }

        Array.from(files).forEach(file => {
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = document.createElement('img');
                img.setAttribute('src', e.target.result);
                img.className = 'preview-img';
                img.onclick = function() {
                    previewContainer.removeChild(img);
                };
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });
    </script>
    <script>
    function toggleLike() {
        var like = document.getElementById("thumbsup");
        var like1 = document.getElementById("thumbsup1");

        if (like.style.display === "none") {
            like.style.display = "inline-block";
            like1.style.display = "none";
        } else {
            like.style.display = "none";
            like1.style.display = "inline-block";
        }
    }
    </script>

</body>




</html>