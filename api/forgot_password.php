<?php
// api/forgot_password.php
date_default_timezone_set('Asia/Manila');
session_start();
header('Content-Type: application/json');

// Use Composer's autoloader
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => "Database connection failed"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Email is required']);
        exit();
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id, first_name, user_name FROM accounts_tbl WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email address not found in our system.']);
        $stmt->close();
        $conn->close();
        exit();
    }

    $stmt->bind_result($user_id, $first_name, $user_name);
    $stmt->fetch();
    $stmt->close();

    // Generate token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+2 minutes'));

    // Delete old tokens
    $delete_stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
    $delete_stmt->bind_param("s", $email);
    $delete_stmt->execute();
    $delete_stmt->close();

    // Store new token
    $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $token, $expires);
    
    if ($stmt->execute()) {
    $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/raflora_enterprises/reset_password.php?token=" . $token;
    
    if (sendResetEmail($email, $first_name, $resetLink)) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Success! We\'ve sent a password reset link to your email. <br><small>💡 <em>Don\'t forget to check your spam folder!</em></small>'
        ]);
    } else {
        echo json_encode([
            'status' => 'error', 
            'message' => 'We couldn\'t send the email. <br><small>Please check if the email address is correct and try again.</small>'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Something went wrong on our end. <br><small>Please wait a moment and try again.</small>'
    ]);
}
    
    $stmt->close();
}

$conn->close();

function sendResetEmail($email, $name, $resetLink) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings for Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'enterprisesraflora@gmail.com';
        $mail->Password   = 'kmklwrltbmthicfh';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        $mail->Timeout = 30;
        
        // Recipients
        $mail->setFrom('enterprisesraflora@gmail.com', 'Raflora Enterprises');
        $mail->addAddress($email, $name);
        $mail->addReplyTo('enterprisesraflora@gmail.com', 'Raflora Enterprises');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request - Raflora Enterprises';
        $mail->Body = "
<!DOCTYPE html>
<html>
<head>
    <style>
        /* ... your CSS styles ... */
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🌺 Raflora Enterprises</h1>
            <h2>Password Reset Request</h2>
        </div>
        <div class='content'>
            <p>Hello <strong>" . htmlspecialchars($name) . "</strong>,</p>
            <p>You requested to reset your password for your Raflora Enterprises account.</p>
            <p>Click the button below to reset your password:</p>
            <p style='text-align: center; margin: 30px 0;'>
                <a href='" . $resetLink . "' class='button'>🔐 Reset My Password</a>
            </p>
            <p>Or copy and paste this link in your browser:</p>
            <div class='code'>" . $resetLink . "</div>
            <p style='color: #d9534f;'><strong>⚠️ This link will expire in 2 minutes.</strong></p>
            <!-- ↑↑↑ THIS IS THE LINE ↑↑↑ -->
            <p>If you didn't request this password reset, please ignore this email. Your account remains secure.</p>
        </div>
        <div class='footer'>
            <p>Need help? Contact us at enterprisesraflora@gmail.com</p>
            <p>&copy; " . date('Y') . " Raflora Enterprises. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
";
        $mail->AltBody = "Hello $name, Click here to reset your password: $resetLink (expires in 2 minutes)";
        
        return $mail->send();
        
    } catch (Exception $e) {
        error_log("PHPMailer Exception: " . $e->getMessage());
        return false;
    }
}
?>