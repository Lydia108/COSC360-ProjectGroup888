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
            errorDisplay.style.display = 'block'; //
            inputElement.style.borderColor = 'red'
            inputElement.classList.add(
                'error');
            inputElement.after(errorDisplay);
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
</head>
<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'connection.php';
    $firstName = isset($_POST['firstName']) ? $conn->real_escape_string($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? $conn->real_escape_string($_POST['lastName']) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $password = isset($_POST['password']) ? $conn->real_escape_string($_POST['password']) : ''; 

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

    // Insert new record
    $sql = "INSERT INTO user (firstName, lastName, emailAddress, password) 
            VALUES ('$firstName', '$lastName', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>



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