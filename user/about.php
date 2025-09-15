<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../user/login.html");
    exit();
}

// Check if user is an admin or client
$is_admin = ($_SESSION['role'] === 'admin_type');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
    <link rel="stylesheet" href="../assets/css/user/about.css">
    <link rel="stylesheet" href="../assets/css/user/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
</head>
<body>
    <div class="landing-container">
        <nav class="navbar">
            <img src="../assets/images/logo/raflora-logo.jpg" alt="logo" class="logo" />
            <ul class="nav-links">
               <?php if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) : ?>
                    <!-- <li><a href="../api/landing.php" class="nav-link">Home</a></li>
                    <li><a href="/user/gallery.php" class="nav-link">Gallery</a></li>
                    <li><a href="/user/about.php" class="nav-link">About</a></li>
                    <li><a href="../user/booking.php" class="nav-link">Book</a></li>
                    <li><a href="../user/login.php" class="nav-link">Log-in</a></li> -->
                <?php else : ?>
                    <li><a href="../api/landing.php" class="nav-link">Home</a></li>
                    <li><a href="../user/gallery.php" class="nav-link">Gallery</a></li>
                    <li><a href="../user/about.php" class="nav-link">About</a></li>
                    <li><a href="../user/booking.php" class="nav-link">Book</a></li>
                    <li><a href="../api/logout.php" class="nav-link">Log-out</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="About-Info">
            <div class="profile-section">
                <div class="profile-content">
                    <h1>Antonio A. Adriatico Jr.</h1>
                    <h3>Subheading for description or instructions</h3>
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
                    <h3>Subheading for description or instructions</h3>
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
  <script src="../assets/js/user/main.js"></script>
</body>
</html>