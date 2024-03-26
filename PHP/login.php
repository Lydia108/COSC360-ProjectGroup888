<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="utf-8">
    <title>Bloggie</title>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/signup.css">

    <script defer>
    window.addEventListener('DOMContentLoaded', (event) => {
        document.querySelector("form[name='handleSignup']").addEventListener("submit", function(e) {
            const pass = document.querySelector("input[name='password']").value;
            const email = document.querySelector("input[name='email']").value;
            const errorMessage = document.getElementById("error-message");

            errorMessage.textContent = "";
            if (!isValidEmail(email)) {
                e.preventDefault();
                errorMessage.textContent += "Invalid email format, e.g., user@example.com\n";
            }
        });

        function isValidEmail(email) {
            const atPosition = email.indexOf("@");
            const dotPosition = email.lastIndexOf(".");
            return !(atPosition < 1 || dotPosition < atPosition + 2 || dotPosition + 2 >= email.length);
        }

        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("password");
            var toggleText = document.getElementById("toggleText");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleText.textContent = "Hide";
            } else {
                passwordInput.type = "password";
                toggleText.textContent = "Show";
            }
        }

        document.getElementById("toggleText").addEventListener("click", togglePasswordVisibility);
    });
    </script>
    <canvas id="canvas"></canvas>
    <script>
    var canvas = document.getElementById('canvas');
    var ctx = canvas.getContext('2d');

    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    var particles = [];
    for (var i = 0; i < 100; i++) {
        particles.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            radius: Math.random() * 5 + 1,
            color: 'white',
            speedX: (Math.random() - 0.5) * 2,
            speedY: (Math.random() - 0.5) * 2
        });
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        particles.forEach(function(p) {
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2, false);
            ctx.fillStyle = p.color;
            ctx.fill();

            p.x += p.speedX;
            p.y += p.speedY;

            if (p.x < 0 || p.x > canvas.width) p.speedX *= -1;
            if (p.y < 0 || p.y > canvas.height) p.speedY *= -1;
        });

        requestAnimationFrame(draw);
    }

    draw();
    </script>
</head>


<?php
session_start();

include "connection.php";

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    header("Location: main.php");
    exit();
}

if (isset($_GET['guest']) && $_GET['guest'] == true) {
    echo "<script>console.log('hello');</script>";
    $_SESSION['is_guest'] = true;
    header('Location: main.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $hashedPassword = md5($password); 
    $sql = "SELECT * FROM user WHERE emailAddress = '$email' AND password = '$hashedPassword'";
    $results = mysqli_query($conn, $sql);

    if (mysqli_num_rows($results) > 0) {
        $row = mysqli_fetch_assoc($results);
        $_SESSION['user_id'] = $row['userId'];
        $_SESSION['logged_in'] = true; // 
        $_SESSION['is_guest'] = false; // 
        $_SESSION['userType'] = $row['userType']; // 

        header("Location: main.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Invalid email or password.";
        header("Location: login.php");
        exit();
    }
}


if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    echo "<script type='text/javascript'>window.onload = function() { alert('$error_message'); }</script>";
    unset($_SESSION['error_message']);
}
?>

<body>
    <div class="name">
        <h1>Bloggie</h1>
    </div>
    <div class="container">
        <form name="handleSignup" action="login.php"  method="post">
            <h2>Sign in!</h2>
            <!-- <div class="row"> -->
            <img src="../Images/profile.jpg" />

            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" placeholder="user@example.com" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <div class="passwContainer">
                    <input type="password" placeholder="Length: 6 - 30 characters" id="password" name="password"
                        required>
                    <span id="toggleText" onclick="togglePasswordVisibility()">Show</span>
                </div>
            </div>
            <div class="goToLogin">
                Don't have an account? <a href="signup.php">Click here</a> to register.
                <br>
                <button id="loginAsGuest">Login as a guest</button>
                <script>
                document.getElementById('loginAsGuest').addEventListener('click', function() {
                    window.location.href = 'login.php?guest=true'; // Directly redirect to main.php
                });
                </script>

            </div>
            <button type="submit" id="name">Sign in</button>
        </form>
        <p id="error-message" style="color: red; text-align:center;"></p>
    </div>

</body>




</html>