<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="utf-8">
    <title>Bloggie</title>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/signup.css">
    <script defer>
        window.addEventListener('DOMContentLoaded', (event) => {
            document.querySelector("form[name='handleSignup']").addEventListener("submit", function (e) {
                const pass = document.querySelector("input[name='password']").value;
                const email = document.querySelector("input[name='email']").value;
                const errorMessage = document.getElementById("error-message");

                errorMessage.textContent = "";
                if (!isValidEmail(email)) {
                    e.preventDefault();
                    errorMessage.textContent += "Invalid email format, e.g., user@example.com\n";
                }
                if (pass.length < 6 || pass.length > 30 || !isValidPassword(pass)) {
                    e.preventDefault();
                    pass.value = "";
                    errorMessage.textContent += "Password must be between 6 and 30 characters and include uppercase, lowercase letters, numbers, and symbols.\n";
                }
            });

            function isValidEmail(email) {
                const atPosition = email.indexOf("@");
                const dotPosition = email.lastIndexOf(".");
                return !(atPosition < 1 || dotPosition < atPosition + 2 || dotPosition + 2 >= email.length);
            }

            function isValidPassword(password) {
                const hasLowerCase = /[a-z]/.test(password);
                const hasUpperCase = /[A-Z]/.test(password);
                const hasNumber = /\d/.test(password);
                const hasSymbol = /[!@#$%^&*()-=_+{}[\]|;:'",.<>?/]/.test(password);
                return hasLowerCase && hasUpperCase && hasNumber && hasSymbol;
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


<body>
    <div class="name">
        <h1>Bloggie</h1>
    </div>
    <div class="container">
        <form name="handleSignup" action="#" onsubmit="return validateForm();" method="post">
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
                <button type="button" onclick="location.href='main.php'">Login as a guest</button>
            </div>
            <button type="submit" id="guest">Sign in</button>
        </form>
        <p id="error-message" style="color: red;"></p>
    </div>

</body>




</html>