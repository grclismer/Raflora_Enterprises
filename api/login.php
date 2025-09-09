<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "0bdb-login_form";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $loginUsername = $_POST['username'];
    $loginPassword = $_POST['password'];

    // CORRECTION: Use the 'username' column, not 'first_name'
    $stmt = $conn->prepare("SELECT id, username, password FROM login_tbl WHERE username = ?");
    $stmt->bind_param("s", $loginUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        // CORRECTION: Use 'db_username' to bind to the 'username' column
        $stmt->bind_result($id, $db_username, $hashed_password);
        $stmt->fetch();

        if (password_verify($loginPassword, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $db_username;

            if (isset($_POST['remember_me'])) {
                setcookie('user_id', $id, time() + (86400 * 30), "/"); 
            }

            echo "Login successful!";
        } else {
            echo "Error: Invalid credentials.";
        }
    } else {
        echo "Error: Invalid credentials.";
    }

    $stmt->close();
}

$conn->close();
?>