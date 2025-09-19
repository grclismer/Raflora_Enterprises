<?php
// Function to start a secure session
function secure_session_start() {
    $secure = false; // Set to true if using HTTPS
    $httponly = true; // Prevents JavaScript access
    $samesite = 'Lax'; // Mitigates CSRF attacks

    if (session_status() == PHP_SESSION_NONE) {
        // Set session cookie parameters
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => $_SERVER['HTTP_HOST'],
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite
        ]);
        session_start();
    }
}

// Function to generate a form-specific CSRF token
function generate_csrf_token($form_name) {
    if (empty($_SESSION['csrf_tokens'][$form_name])) {
        $_SESSION['csrf_tokens'][$form_name] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_tokens'][$form_name];
}

// Function to verify a form-specific CSRF token
function verify_csrf_token($form_name, $token) {
    if (empty($_SESSION['csrf_tokens'][$form_name])) {
        return false;
    }
    // Use hash_equals() for a secure string comparison
    if (hash_equals($_SESSION['csrf_tokens'][$form_name], $token)) {
        // The token is valid, so remove it to prevent replay attacks
        unset($_SESSION['csrf_tokens'][$form_name]);
        return true;
    }
    return false;
}
?>