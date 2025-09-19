<?php
require_once 'security.php';
secure_session_start();

header('Content-Type: application/json');

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => "Connection failed: " . $conn->connect_error]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF check specific to the 'login' form
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token('login', $token)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
        exit();
    }

    // Get form data
    $loginUsername = trim($_POST['username'] ?? '');
    $loginPassword = trim($_POST['password'] ?? '');

    // Prepare query
    $stmt = $conn->prepare("SELECT user_id, user_name, password, role FROM accounts_tbl WHERE user_name = ?");
    $stmt->bind_param("s", $loginUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $db_user_name, $hashed_password, $db_role);
        $stmt->fetch();

        // Verify password
        if (password_verify($loginPassword, $hashed_password)) {

            // Rehash password if needed
            if (password_needs_rehash($hashed_password, PASSWORD_DEFAULT)) {
                $newHash = password_hash($loginPassword, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE accounts_tbl SET password = ? WHERE user_id = ?");
                $update_stmt->bind_param("si", $newHash, $user_id);
                $update_stmt->execute();
                $update_stmt->close();
            }

            // Set session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $db_user_name;
            $_SESSION['is_logged_in'] = true;
            $_SESSION['role'] = $db_role;

            // Remember me
            if (isset($_POST['remember_me'])) {
                setcookie('user_id', $user_id, time() + (86400 * 30), "/");
            }

            // Redirect based on role
            $redirect_url = ($db_role === 'admin_type') 
                ? '../admin_dashboard/inventory.php'
                : '../api/landing.php';

            echo json_encode(['status' => 'success', 'redirect_url' => $redirect_url]);

        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
    }

    $stmt->close();
}
$conn->close();
?>