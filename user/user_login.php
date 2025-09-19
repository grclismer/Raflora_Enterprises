<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Register</title>
    <link rel="stylesheet" href="../assets/css/user/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <script src="../assets/js/user/login.js" defer></script>
</head>
<body>
    <?php
    session_start();
    require_once "../api/security.php"; // <-- your file with generate_csrf_token()

    $login_csrf = generate_csrf_token('login');
    $register_csrf = generate_csrf_token('register');
    $forgot_csrf = generate_csrf_token('forgot');
    ?>
    <!-- LOGIN FORM -->
    <div class="Login-form" id="loginFormContainer">
        <div class="wrapper">
            <form id="loginForm" action="../api/login.php" method="post">
                <span class="return">
                    <a href="../guest/g-home.html"><h2>X</h2></a>
                </span>
                <h1>Login</h1>
                <div id="welcomeMessageLogin" class="welcome-message"></div>
                <div class="input-box">
                    <input type="text" id="username" name="username" placeholder=" " maxlength="30" required>
                    <label for="username">Username</label>
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="input-box">
                    <input type="password" id="password" name="password" placeholder=" " maxlength="50" required>
                    <label for="password">Password</label>
                    <i class="fa-solid fa-eye-slash" id="togglePassword"></i>
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div class="remember-forgot">
                    <label><input type="checkbox" name="remember_me"> Remember me</label>
                    <a href="#frogot-form" id="showForgotPassword">Forgot password?</a>
                </div>

                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($login_csrf); ?>">

                <button type="submit" class="btn">Login</button>
                <div class="register-link">
                    <p>Don't have an account? <a href="#register-form" id="showRegister">Register</a></p>
                </div>
            </form>
        </div>
    </div>

    <!-- REGISTER FORM -->
    <div class="Login-form hidden" id="registerFormContainer">
        <div class="regwrapper">
            <form id="registrationForm" action="../api/register.php" method="post">
                <span class="return">
                    <a href="../guest/g-home.html"><h2>X</h2></a>
                </span>
                <h1>Register</h1>
                <div class="regrow-1" id="set1">
                    <div id="welcomeMessageRegister" class="welcome-message"></div>
                    <div class="input-box">
                        <input type="text" id="reg-firstname" name="firstname" placeholder=" " maxlength="50" required>
                        <label for="reg-firstname">First Name</label>
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="input-box">
                        <input type="text" id="reg-lastname" name="lastname" placeholder=" " maxlength="50" required>
                        <label for="reg-lastname">Last Name</label>
                        <i class="fa-solid fa-user"></i>
                    </div>
                </div>
                <div class="regrow-2" id="set2">
                    <div class="input-box">
                        <input type="text" id="reg-username" name="username" placeholder=" " maxlength="30" required>
                        <label for="reg-username">Username</label>
                        <i class="fa-solid fa-user"></i>
                    </div>
                </div>
                <div class="regrow-3" id="set3">
                    <div class="input-box">
                        <input type="password" id="reg-password" name="password" placeholder=" " maxlength="50" required>
                        <label for="reg-password">Password</label>
                        <i class="fa-solid fa-eye-slash" id="reg-togglePassword"></i>
                        <i class="fa-solid fa-lock"></i>
                    </div>
                </div>
                <div class="regrow-4" id="set4">
                    <div class="input-box">
                        <input type="password" id="reg-confirm-password" name="confirm_password" placeholder=" " maxlength="50" required>
                        <label for="reg-confirm-password">Confirm Password</label>
                        <i class="fa-solid fa-eye-slash" id="reg-toggleConfirmPassword"></i>
                        <i class="fa-solid fa-lock"></i>
                    </div>
                </div>
                <div class="regrow-5" id="set5">
                    <div class="input-box">
                        <input type="text" id="reg-address" name="address" placeholder=" " maxlength="255" required>
                        <label for="reg-address">Address</label>
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                </div>
                <div class="regrow-6" id="set6">
                    <div class="input-box">
                        <input type="email" id="reg-email" name="email" placeholder=" " required>
                        <label for="reg-email">Email</label>
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div class="input-box">
                        <input type="text" id="reg-mobilenumber" name="mobilenumber" placeholder=" " maxlength="11" required>
                        <label for="reg-mobilenumber">Mobile Number</label>
                        <i class="fa-solid fa-mobile-screen-button"></i>
                    </div>
                </div>

                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($register_csrf); ?>">

                <button type="submit" class="btn">Register</button>
                <div class="register-link">
                    <p>Already have an account? <a href="#login-form" id="showLoginFromRegister">Login</a></p>
                </div>
            </form>
        </div>
    </div>

    <!-- FORGOT PASSWORD FORM -->
    <div class="Login-form hidden" id="forgotPasswordContainer">
        <div class="wrapper">
            <form id="forgotPasswordForm" action="../api/forgot_password.php" method="post">
                <span class="return">
                    <a href="../guest/g-home.html"><h2>X</h2></a>
                </span>
                <h1>Forgot Password</h1>
                <div id="forgotPasswordMessage" class="welcome-message"></div>
                <p class="forgot-instructions">Enter your email and we'll send you a link to reset your password.</p>
                <div class="input-box">
                    <input type="email" id="forgot-email" name="email" placeholder=" " required>
                    <label for="forgot-email">Email</label>
                    <i class="fa-solid fa-envelope"></i>
                </div>

                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($forgot_csrf); ?>">
                <button type="submit" class="btn">Reset Password</button>
                <div class="register-link">
                    <p>Remember your password? <a href="#" id="showLoginFromForgot">Login</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
