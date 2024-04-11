<?php
session_start();
require 'connection.php'; // It's better to use require when the script must have this file to run.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $firstName = $conn->real_escape_string(trim($_POST['firstName']));
    $lastName = $conn->real_escape_string(trim($_POST['lastName']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; 

    // Input validation
    $errors = []; // Initialize an array to store error messages

    if (empty($firstName) || strlen($firstName) > 30) {
        $errors[] = "First name is required and must not exceed 30 characters.";
    }

    if (empty($lastName) || strlen($lastName) > 30) {
        $errors[] = "Last name is required and must not exceed 30 characters.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (strlen($password) < 6 || strlen($password) > 30 || 
        !preg_match('/[A-Z]/', $password) || 
        !preg_match('/[a-z]/', $password) || 
        !preg_match('/\d/', $password) ||    
        !preg_match('/[!@#$%^&*()-=_+{}[\]|:\'",.<>?\/]/', $password)) { 
        $errors[] = "Password must be 6-30 characters long and include uppercase and lowercase letters, a number, and a symbol.";
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT emailAddress FROM user WHERE emailAddress = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result(); // Needed to check row count
    if ($stmt->num_rows > 0) {
        $errors[] = "Email already in use. Please use a different email.";
    }
    $stmt->close();

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
        exit; // Stop script execution if there are errors
    }

    // No errors; proceed with user registration
    $avatarContent = null; // Initialize variable to hold binary data of the avatar

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        // Validate file size and type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['avatar']['type'];
        if (in_array($fileType, $allowedTypes) && $_FILES['avatar']['size'] <= 2 * 1024 * 1024) {
            $avatarContent = file_get_contents($_FILES['avatar']['tmp_name']);
        } else {
            echo "<script>alert('Invalid file type or size. Max 2MB allowed.');</script>";
            exit;
        }
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO user (firstName, lastName, emailAddress, password, icon, userType) VALUES (?, ?, ?, ?, ?, 0)");
    $null = null; // Placeholder for the blob parameter
    $stmt->bind_param('ssssb', $firstName, $lastName, $email, $hashedPassword, $null);

    if ($avatarContent !== null) {
        $stmt->send_long_data(4, $avatarContent);
    }

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $conn->insert_id;
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
ob_end_flush();
?>


<!DOCTYPE html>

<html>

<head lang="en">
    <meta charset="utf-8">
    <title>Bloggie</title>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/signup.css">
    <script defer>
        window.addEventListener('DOMContentLoaded', (event) => {
            const form = document.querySelector("form[name='handleSignup']");
            const fnameInput = document.querySelector("#firstName");
            const lnameInput = document.querySelector("#lastName");
            const emailInput = document.querySelector("#email");
            const passwordInput = document.querySelector("#password");

            form.addEventListener("submit", function(e) {
                let invalidForm = false;

                // Validate name fields
                resetInputStyleAndErrorMessage(fnameInput);
                if (fnameInput.value.trim() === "" || fnameInput.value.length > 30) {
                    showError(fnameInput, "First name cannot be empty and must not exceed 30 characters.");
                    invalidForm = true;
                }

                resetInputStyleAndErrorMessage(lnameInput);
                if (lnameInput.value.trim() === "" || lnameInput.value.length > 30) {
                    showError(lnameInput, "Last name cannot be empty and must not exceed 30 characters.");
                    invalidForm = true;
                }


                // Validate email
                resetInputStyleAndErrorMessage(emailInput);
                if (!isValidEmail(emailInput.value)) {
                    showError(emailInput, "Invalid email format.");
                    invalidForm = true;
                }

                // Validate password
                resetInputStyleAndErrorMessage(passwordInput);
                if (!isValidPassword(passwordInput.value)) {
                    showError(passwordInput,
                        "Password must be between 6 and 18 characters and include uppercase, lowercase letters, numbers, and symbols."
                    );
                    invalidForm = true;
                }

                if (invalidForm) {
                    e.preventDefault();
                }
            });

            function isValidEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }

            function isValidPassword(password) {
                return password.length >= 6 && password.length <= 18 &&
                    /[a-z]/.test(password) &&
                    /[A-Z]/.test(password) &&
                    /\d/.test(password) &&
                    /[!@#$%^&*(),.?":{}|<>]/.test(password);
            }

            function resetInputStyleAndErrorMessage(inputElement) {
                inputElement.style.borderColor = '';
                const existingErrorMessage = inputElement.nextElementSibling;
                if (existingErrorMessage && existingErrorMessage.classList.contains('error-message')) {
                    existingErrorMessage.remove();
                }
            }

            function showError(inputElement, message) {
                resetInputStyleAndErrorMessage(inputElement);
                const errorDisplay = document.createElement('p');
                errorDisplay.className = 'error-message';
                errorDisplay.textContent = message;
                errorDisplay.style.color = 'red';
                errorDisplay.style.display = 'block';
                inputElement.style.borderColor = 'red';
                inputElement.classList.add('error');
                inputElement.closest('.form-group').appendChild(errorDisplay);
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

        document.addEventListener('DOMContentLoaded', function() {
            var avatarImage = document.getElementById('avatarImage');
            var avatarInput = document.getElementById('avatarInput');

            avatarImage.addEventListener('click', function() {
                avatarInput.click(); // Trigger the click event on the hidden file input
            });

            avatarInput.addEventListener('change', function(event) {
                if (event.target.files.length > 0) {
                    // Update the image preview if a file is selected
                    var src = URL.createObjectURL(event.target.files[0]);
                    avatarImage.src = src;
                }
            });
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

<body>

    <div class="name">
        <h1>Bloggie</h1>
    </div>
    <div class="container">
        <form name="handleSignup" action="signup.php" method="post" enctype="multipart/form-data">

            <h2>Register now!</h2>

            <div class="form-group">
                <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;">
                <img src="../Images/profile.jpg" id="avatarImage" style="cursor: pointer;" />
            </div>

            <div class="form-group">
                <label for="firstName">First Name: </label>
                <input type="text" placeholder="Please type your first name" id="firstName" name="firstName" required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" placeholder="Please type your last name" id="lastName" name="lastName" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" placeholder="user@example.com" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <div class="passwContainer">
                    <input type="password" placeholder="Length: 6 - 30 characters" id="password" name="password" required>
                    <span id="toggleText" onclick="togglePasswordVisibility()">Show</span>
                </div>
            </div>


            <div class="goToLogin">
                Already have an account? <a href="login.php">Click here</a> to login.
            </div>
            <button type="submit">Sign up</button>
        </form>
        <p id="error-message" style="color: red;"></p>
    </div>

</body>




</html>