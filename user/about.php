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

if (!$conn->connect_error && isset($_SESSION['user_id'])) {
    // IMPORTANT: Include profile_picture in the query
    $stmt = $conn->prepare("SELECT user_name, profile_picture FROM accounts_tbl WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc() ?? [];
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
    <link rel="stylesheet" href="../assets/css/user/about.css">
    <link rel="stylesheet" href="../assets/css/user/footer.css">
    <link rel="stylesheet" href="../assets/css/user/navbar.css">
    <script src="../assets/js/user/navbar.js" defer></script>
        <!-- 1. TAILWIND CSS CDN (CRITICAL for dark mode classes) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- 2. DARK MODE CONFIGURATION + LOGIC (Nasa iisang JS file na dapat ang config at functions) -->
    <!-- I-check kung tama ang path na ito: ../assets/js/user/dark_mode.js -->
    <!-- Inalis ang 'defer' para iwas conflict -->
    <script src="../assets/js/user/dark_mode.js"></script> 
<!-- Font Awesome link na stable (6.4.0) -->
    <link rel="stylesheet" href="../assets/css/user/dark_mode.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="about-container">
        <nav class="navbar">
            <img src="../assets/images/logo/raflora-logo.jpg" alt="logo" class="logo" />
            <div class="hamburger-menu">
                <i class="fas fa-bars"></i>
            </div>
            <ul class="nav-links">
                <li><a href="../user/landing.php" class="nav-link">Home</a></li>
                <li><a href="../user/gallery.php" class="nav-link">Gallery</a></li>
                <li class="active"><a href="../user/about.php" class="nav-link">About</a></li>
                <li><a href="../user/booking.php" class="nav-link">Book</a></li>
                <li class="user-dropdown-toggle">
                    <div class="navbar-profile">
                        <?php if (!empty($user_data['profile_picture'])): ?>
                            <!-- Profile picture with CSS class -->
                            <img src="/raflora_enterprises/<?php echo ltrim($user_data['profile_picture'], '/'); ?>" 
                                alt="Profile" 
                                class="profile-picture profile-picture-small">
                        <?php else: ?>
                            <!-- Default icon with CSS class -->
                            <div class="profile-default-icon">
                                <i class="fa fa-user"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Username (optional) -->
                        <span class="navbar-username"><?php echo $user_data['user_name'] ?? 'User'; ?></span>
                    </div>
                    <ul class="user-dropdown-menu">
                        <li><a href="../user/account_settings.php">Account settings</a></li>
                        <li><a href="../user/my_bookings.php">My Bookings</a></li>
                        <li><a href="../api/logout.php">Log Out</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <button id="dark-mode-icon-toggle" 
            class="fixed top-4 left-4 p-3 rounded-full text-2xl transition-colors duration-300 hover:bg-gray-200 dark:hover:bg-gray-800 z-[9999] focus:outline-none">
        <!-- CRITICAL: Ang JS ang magse-set ng Moon o Sun icon base sa current state -->
        <i id="dark-mode-icon" class="fa-solid fa-moon text-blue-600 dark:text-yellow-400"></i>
        </button>
        <div class="About-Info">
            <div class="profile-section">
                <div class="profile-content">
                    <h1>Antonio A. Adriatico Jr.</h1>
                    <h3>CREATIVE DIRECTOR</h3>
                    <p>Body text for your whole article or post. We'll put in some lorem ipsum to show how a filled-out page might look:</p>
                    <br>    
                    <p>Excepteur efficient emerging, minim veniam anim aute carefully curated Ginza conversation exquisite perfect nostrud nisi intricate Content. Qui international first-class nulla ut. adipisicing, essential lovely queen tempor eiusmod irure. Exclusive izakaya charming Scandinavian impeccable aute quality of life soft power pariatur Melbourne occaecat discerning. Qui wardrobe aliquip, et Porter destination Toto remarkable officia Helsinki excepteur Basset hound. Zürich sleepy perfect consectetur.</p>
                </div>
                    <div class="profile-image-container">
                        <img src="../assets/images/portrait/antionio.jpg" alt="Antonio A. Adriatico Jr">
                    </div>
            </div>
            <div class="profile-section reverse">
                <div class="profile-image-container">
                    <img src="../assets/images/portrait/Rafael.jpg" alt="Raffy Chrisrtian Zamora">
                </div>
                <div class="profile-content">
                    <h1>Raffy Chrisrtian Zamora</h1>
                    <h3>PROPRIETOR </h3>
                    <p>Body text for your whole article or post. We'll put in some lorem ipsum to show how a filled-out page might look:</p>
                    <br>
                    <p>Excepteur efficient emerging, minim veniam anim aute carefully curated Ginza conversation exquisite perfect nostrud nisi intricate Content. Qui international first-class nulla ut. adipisicing, essential lovely queen tempor eiusmod irure. Exclusive izakaya charming Scandinavian impeccable aute quality of life soft power pariatur Melbourne occaecat discerning. Qui wardrobe aliquip, et Porter destination Toto remarkable officia Helsinki excepteur Basset hound. Zürich sleepy perfect consectetur.</p>
                </div>
            </div>
        </div>
        
      <!-- Footer -->
        <footer class="footer" id="Page-footer">
                <h1 class="Contact">Contact us</h1>
                    <div class="social-icons-container">
                        <div class="Social-Facebook">
                            <h3>Facebook</h3>
                            <p>Please visit us on</p>
                            <a href="https://www.facebook.com/RafloraEnterprises">
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
  <!-- <script src="../assets/js/user/main.js"></script> -->
</body>
</html>