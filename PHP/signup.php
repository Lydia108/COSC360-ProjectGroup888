<?php
ob_start(); 

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'connection.php'; 
    $defaultAvatarPath = '../Images/profile.jpg'; 
    $avatarContent = file_get_contents($defaultAvatarPath);
    // Sanitize input and assign variables
    $firstName = isset($_POST['firstName']) ? $conn->real_escape_string($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? $conn->real_escape_string($_POST['lastName']) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $password = isset($_POST['password']) ? $conn->real_escape_string($_POST['password']) : ''; 
    $hashedPassword = md5($password); // Hash the password

    $userType = 0; // Default user type

    // Validate input lengths and formats
    if (strlen($firstName) > 30 || strlen($lastName) > 30) {
        echo "Name too long.";
        $conn->close();
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        $conn->close();
        exit();
    }
    if (strlen($password) < 6 || strlen($password) > 30 || 
        !preg_match('/[A-Z]/', $password) || // Check for uppercase
        !preg_match('/[a-z]/', $password) || // Check for lowercase
        !preg_match('/\d/', $password) ||    // Check for digit
        !preg_match('/[!@#$%^&*()-=_+{}[\]|:\'",.<>?\/]/', $password)) { 
        echo "Invalid password format.";
        $conn->close();
        exit();
    }

    // Check if email already exists
    $checkEmailQuery = "SELECT * FROM user WHERE emailAddress = '$email'";
    $result = $conn->query($checkEmailQuery);
    if ($result->num_rows > 0) {
        echo "<script>
                alert('Email already in use. Please use a different email.');
                window.location.href='signup.php';
              </script>";
        $conn->close();
        exit();
    }

    $sql = "INSERT INTO user (firstName, lastName, emailAddress, password, icon, userType) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssbi', $firstName, $lastName, $email, $hashedPassword, $null, $userType);
    $null = null; 
    $stmt->send_long_data(4, $avatarContent); 
    if ($stmt->execute()) {
        $newUserId = $conn->insert_id;
        $_SESSION['user_id'] = $newUserId; 
        unset($_SESSION['is_guest']); 
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    
    
    
    $stmt->close();

    $conn->close(); // 
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
        <form name="handleSignup" action="signup.php" onsubmit="return validateForm();" method="post">
            <h2>Register now!</h2>
            <!-- <div class="row"> -->
            <div class="avatar">
                <a href="#">
                    <img src="../Images/profile.jpg" />
                </a>
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
                    <input type="password" placeholder="Length: 6 - 30 characters" id="password" name="password"
                        required>
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