<?php
$token = $_GET['token'] ?? '';
$message = '';
$success = false;
$valid_token = false;
$email = '';
$token_expired = false;

// DEBUG: Check what's in the URL
echo "<!-- DEBUG: Raw GET token: " . ($_GET['token'] ?? 'EMPTY') . " -->";
echo "<!-- DEBUG: Cleaned token: $token -->";
echo "<!-- DEBUG: Full URL: " . $_SERVER['REQUEST_URI'] . " -->";
// reset_password.php
date_default_timezone_set('Asia/Manila');
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed");
}

$token = $_GET['token'] ?? '';
$message = '';
$success = false;
$valid_token = false;
$email = '';
$token_expired = false; // Initialize the variable

// Validate token - IMPROVED VERSION
if ($token) {
    $stmt = $conn->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email, $expires_at);
        $stmt->fetch();
        
        // BETTER TIME COMPARISON
        $current_time = time();
        $expire_time = strtotime($expires_at);
        
        if ($expire_time > $current_time) {
            $valid_token = true;
            $token_expired = false;
        } else {
            $valid_token = false;
            $token_expired = true;
            // Delete expired token
            $delete_stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $delete_stmt->bind_param("s", $token);
            $delete_stmt->execute();
            $delete_stmt->close();
        }
    } else {
        // Token not found in database
        $valid_token = false;
        $token_expired = false;
    }
    $stmt->close();
} else {
    // No token provided
    $valid_token = false;
    $token_expired = false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $valid_token) {
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($new_password) || empty($confirm_password)) {
        $message = "Please fill in all fields";
    } elseif (strlen($new_password) < 6) {
        $message = "Password must be at least 6 characters long";
    } elseif ($new_password !== $confirm_password) {
        $message = "Passwords do not match";
    } else {
        // Update password in accounts_tbl
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_stmt = $conn->prepare("UPDATE accounts_tbl SET password = ? WHERE email = ?");
        $update_stmt->bind_param("ss", $hashed_password, $email);
        
        if ($update_stmt->execute()) {
            // Delete used token
            $delete_stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $delete_stmt->bind_param("s", $token);
            $delete_stmt->execute();
            $delete_stmt->close();
            
            $success = true;
            $message = "Password reset successfully! You can now login with your new password.";
        } else {
            $message = "Failed to reset password. Please try again.";
        }
        $update_stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Raflora Enterprises</title>
    <link rel="stylesheet" href="assets/css/user/login.css">
    <link rel="stylesheet" href="assets/css/user/reset_password.css">
    <script src="assets/js/user/reset_password.js" defer></script>
    
</head>
<body>
    <div class="Login-form">
        <div class="wrapper">
            <form method="POST" id="resetPasswordForm">
    <span class="return">
        <a href="guest/g-home.php" class="close-btn">
            <i class="fas fa-times">X</i>
        </a>
    </span>
    
    <div class="form-header">
        <div class="logo-container">
            <!-- <div class="logo-icon">🌺</div> -->
            <h1>Reset Password</h1>
        </div>
        <p class="form-subtitle">Secure your account with a new password</p>
    </div>
    
    <!-- Messages -->
    <?php if ($message): ?>
        <div class="message-container <?php echo $success ? 'success' : 'error'; ?>">
            <div class="message-icon">
                <?php if ($success): ?>
                    <i class="fas fa-check-circle"></i>
                <?php else: ?>
                    <i class="fas fa-exclamation-circle"></i>
                <?php endif; ?>
            </div>
            <div class="message-content">
                <?php echo htmlspecialchars($message); ?>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Reset Password Form -->
    <?php if (!$success && $valid_token): ?>
        <div class="form-body">
            <div class="input-group">
                <div class="input-box">
                    <input type="password" id="password" name="password" placeholder=" " required minlength="6">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        New Password
                    </label>
                    
                </div>
               
                <div class="input-box">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder=" " required minlength="6">
                    <label for="confirm_password">
                        <i class="fas fa-lock"></i>
                        Confirm New Password
                    </label>
                </div>
            </div>
            <!-- Password Strength Indicator -->
            <div class="password-strength">
                <div class="strength-bar">
                    <div class="strength-fill" id="strengthFill"></div>
                </div>
                <div class="strength-text" id="strengthText">Password strength</div>
            </div>
            <!-- Show Password Checkbox -->
            <div class="show-password-container">
                <label class="checkbox-label">
                    <input type="checkbox" id="showPasswordCheckbox">
                    <span class="checkmark">
                        <i class="fas fa-check"></i>
                    </span>
                    <span class="checkbox-text">Show Password</span>
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-full">
                <i class="fas fa-key"></i>
                Reset Password
            </button>
        </div>
        
    <?php elseif ($token && !$valid_token): ?>
        <!-- Token exists but is invalid/expired -->
        <div class="error-state">
            <div class="error-icon">
                <?php if ($token_expired): ?>
                    <i class="fas fa-clock"></i>
                <?php else: ?>
                    <i class="fas fa-link-slash"></i>
                <?php endif; ?>
            </div>
            <div class="error-content">
                <h3>
                    <?php if ($token_expired): ?>
                        Link Expired
                    <?php else: ?>
                        Invalid Link
                    <?php endif; ?>
                </h3>
                <p>
                    <?php if ($token_expired): ?>
                        This password reset link has expired. For security reasons, reset links are only valid for a limited time.
                    <?php else: ?>
                        This password reset link is invalid or has already been used.
                    <?php endif; ?>
                </p>
            </div>
            <div class="error-actions">
               
                <a href="#forgot-password" class="btn btn-primary" onclick="window.location.href='user/user_login.php#forgot-form'">
                    <i class="fas fa-envelope"></i>
                    Request New Link
                </a>
            </div>
        </div>
        
    <?php elseif (!$token): ?>
        <!-- No token provided -->
        <div class="error-state">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="error-content">
                <h3>Missing Reset Link</h3>
                <p>No password reset token was provided. Please check your email and click the reset link, or request a new one.</p>
            </div>
            <div class="error-actions">
                <a href="user/user_login.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    Return to Login
                </a>
                <a href="#forgot-password" class="btn btn-primary" onclick="window.location.href='guest/g-home.php#forgot-form'">
                    <i class="fas fa-envelope"></i>
                    Request Reset Link
                </a>
            </div>
        </div>
        
    <?php endif; ?>

    <!-- Success Message -->
    <?php if ($success): ?>
        <div class="success-state">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="success-content">
                <h3>Password Updated!</h3>
                <p>Your password has been reset successfully. You can now log in with your new password.</p>
            </div>
            <div class="success-actions">
                <a href="guest/g-home.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    Continue to Login
                </a>
            </div>
        </div>
    <?php endif; ?>
</form>
        </div>
    </div>
                    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js"></script>
</body>
</html>