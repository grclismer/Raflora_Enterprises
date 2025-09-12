<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $loginUsername = $_POST['username'];
    $loginPassword = $_POST['password'];

    // CORRECTION: The column name for the username is 'user_name' in your database.
    // We also need to select the 'password' column to compare it.
    $stmt = $conn->prepare("SELECT user_id, user_name, password FROM accounts_tbl WHERE user_name = ?");
    $stmt->bind_param("s", $loginUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        // Correct the variable names to match the columns selected.
        $stmt->bind_result($user_id, $db_user_name, $hashed_password);
        $stmt->fetch();

        if (password_verify($loginPassword, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $db_user_name; // Use the correct column name

            if (isset($_POST['remember_me'])) {
                setcookie('user_id', $user_id, time() + (86400 * 30), "/"); 
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