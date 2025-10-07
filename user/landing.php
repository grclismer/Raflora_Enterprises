<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user data including profile picture
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT user_name, profile_picture FROM accounts_tbl WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raflora Enterprises</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/user/dark_mode.js"></script> 
    <link rel="stylesheet" href="../assets/css/user/landing.css">
    <link rel="stylesheet" href="../assets/css/user/footer.css">
    <link rel="stylesheet" href="../assets/css/user/navbar.css">
    <link rel="stylesheet" href="../assets/css/user/dark_mode.css">
    <script src="../assets/js/user/dark_mode.js" defer></script>
    <script src="../assets/js/user/navbar.js" ></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
</head>
<body>

    <div class="landing-container">
        
        <nav class="navbar">
            <img src="../assets/images/logo/raflora-logo.jpg" alt="logo" class="logo" />
            <div class="hamburger-menu">
                <i class="fas fa-bars"></i>
            </div>
            <ul class="nav-links">
                <li class="active"><a href="../user/landing.php" class="nav-link">Home</a></li>
                <li><a href="../user/gallery.php" class="nav-link">Gallery</a></li>
                <li><a href="../user/about.php" class="nav-link">About</a></li>
                <li><a href="../user/booking.php" class="nav-link">Book</a></li>
                <div class="user-dropdown-toggle">
                    <div class="flex items-center">
    <?php if (!empty($user_data['profile_picture'])): ?>
        <?php 
        $clean_path = ltrim($user_data['profile_picture'], '/');
        $image_path = '/Raflora_Enterprises/' . $clean_path;
        ?>
        <img src="<?php echo $image_path; ?>" 
             alt="Profile" 
             class="w-8 h-8 rounded-full object-cover border-2 border-white">
    <?php else: ?>
        <div class="w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center border-2 border-white">
            <i class="fa fa-user text-white text-sm"></i>
        </div>
    <?php endif; ?>
    <span class="ml-2 text-white font-medium"><?php echo $user_data['user_name'] ?? 'User'; ?></span>
</div>
                    <ul class="user-dropdown-menu">
                        <li><a href="../user/account_settings.php">Account settings</a></li>
                        <li><a href="../user/my_bookings.php">My Bookings</a></li>
                        <li><a href="../api/logout.php">Log Out</a></li>
                    </ul>
                </div>
            </ul>
        </nav>
        
        <!-- DARK MODE BUTTON: Gumamit ng z-[9999] para hindi matabunan ng ibang elements -->
        <button id="dark-mode-icon-toggle">
            <i id="dark-mode-icon" class="fa-solid fa-moon"></i>
        </button>
        
        <div class="Landing-page">
            <div class="Home-bg">
                <!-- Idinagdag ang dark:text-white at dark:text-gray-300 -->
                <h1>Raflora Enterprises</h1>
                <p>Raflora Enterprises Flower Arrangement and<br>
                    Event Stylist Scheduling Management System</p>
                <img src="../assets/images/Home/box-img1.jpg" alt="Home">
            </div>
            <div class="Set-img" id="images">
                 <!-- Idinagdag ang dark:text-white at dark:bg-gray-800 -->
                <h1> </h1>
                <img src="../assets/images/portrait/section1.jpg" alt="image1">
                <img src="../assets/images/portrait/section2.jpg" alt="image2">
                <p >Raflora Enterprises Management Team</p>
            </div>
            <div class="Feedback-content" id="Feeds">
                <!-- Kailangan mong i-style ang .Review at .Client sa iyong dark_mode.css o landing.css -->
                <div class="Review" id="Rev-1">
                    <div class="Client" id="client-1">
                        <img src="../assets/images/img/VVip.jpg" alt="Vvip-client">
                        <h2>John Doe</h2>
                        <h4>Vvip</h4>
                    </div>
                    <h3>Well organized, Good services</h3>
                </div>
                <div class="Review" id="Rev-2">
                    <div class="Client" id="client-2">
                        <img src="../assets/images/img/Sheraton.jpg" alt="Sheraton-client">
                        <h2>Johnson</h2>
                        <h4>Sheraton</h4>
                    </div>
                    <h3>Well organized, Good services</h3>
                </div>
                <div class="Review" id="Rev-3">
                    <div class="Client" id="client-3">
                        <img src="../assets/images/img/Okura.jpg" alt="Okura-client">
                        <h2>James Smith</h2>
                        <h4>Okura</h4>
                    </div>
                    <h3>Well organized, Good services</h3>
                </div>
            </div>
        </div>
        <footer class="footer" id="Page-footer">
            <h1 class="Contact">Contact us</h1>
                <div class="social-icons-container">
                    <div class="Social-Facebook">
                        <h3>Facebook</h3>
                        <p>Please visit us on</p>
                        <a href="https://www.facebook.com/RafloraEnterprises " target="_blank">
                            <img src="../assets/images/icon/facebook-icon.png" alt="facebook">
                        </a>
                        <a href="https://www.facebook.com/RafloraEnterprises" class="hyper-link-facebook">
                            www.facebook.com/RafloraEnterprises
                        </a>
                    </div>

                    <div class="Social-Email">
                        <h3>Email</h3>
                        <p>Please Reach us on</p>
                        <a href="https://mail.google.com/mail/u/0/#inbox?compose=new">
                            <img src="../assets/images/icon/gmail-icon.png" alt="gmail">
                        </a>
                        <a href="https://mail.google.com/mail/u/0/#inbox?compose=new" class="hyper-link-gmail">
                            @raflora18.gmail.com
                        </a>
                    </div>
                </div>
        </footer>
    </div> 
    <script src="./js/script.js"></script>
</body>
</html>
