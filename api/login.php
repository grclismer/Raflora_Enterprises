<?php
// Turn off error display but log them
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => "Database connection failed"]);
    exit();
}

// Log in process
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get user input from the form
    $loginUsername = $_POST['username'] ?? '';
    $loginPassword = $_POST['password'] ?? '';

    // Validate input
    if (empty($loginUsername) || empty($loginPassword)) {
        echo json_encode(['status' => 'error', 'message' => 'Username and password are required']);
        exit();
    }

    // Prepare a SQL statement to prevent SQL injection and fetch user type
    $stmt = $conn->prepare("SELECT user_id, user_name, password, role FROM accounts_tbl WHERE user_name = ?");
    
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => "Database error"]);
        exit();
    }
    
    $stmt->bind_param("s", $loginUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        // Bind the result to variables
        $stmt->bind_result($user_id, $db_user_name, $hashed_password, $db_role);
        $stmt->fetch();

        // Verify the password against the stored hash
        if (password_verify($loginPassword, $hashed_password)) {
            
            // Check if the hash needs to be rehashed
            if (password_needs_rehash($hashed_password, PASSWORD_DEFAULT)) {
                $newHash = password_hash($loginPassword, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE accounts_tbl SET password = ? WHERE user_id = ?");
                $update_stmt->bind_param("si", $newHash, $user_id);
                $update_stmt->execute();
                $update_stmt->close();
            }

            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $db_user_name;
            $_SESSION['is_logged_in'] = true;
            $_SESSION['role'] = $db_role;

            // Set cookie for "remember me" functionality
            if (isset($_POST['remember_me'])) {
                setcookie('user_id', $user_id, time() + (86400 * 30), "/"); 
                setcookie('username', $db_user_name, time() + (86400 * 30), "/");
            }
            
            // Return a JSON response with the redirection URL
            $redirect_url = '';
            if ($db_role === 'admin_type') {
                $redirect_url = '/raflora_enterprises/admin_dashboard/inventory.php';
            } elseif ($db_role === 'client_type') {
                $redirect_url = '/raflora_enterprises/user/landing.php';
            } else {
                $redirect_url = '/raflora_enterprises/user/landing.php';
            }
            
            echo json_encode(['status' => 'success', 'redirect_url' => $redirect_url]);
        } else {
            // Invalid password
            echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
        }
    } else {
        // User not found
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

// Close the database connection
$conn->close();
?>