<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raflora Enterprises</title>
    <link rel="stylesheet" href="../assets/css/user/landing.css">
    <link rel="stylesheet" href="../assets/css/user/footer.css">
    <link rel="stylesheet" href="../assets/css/user/navbar.css">
    <link rel="stylesheet" href="../assets/css/user/dark_mode.css">
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <script src="../assets/js/user/dark_mode.js"></script> 
    <link rel="stylesheet" href="../assets/css/user/dark_mode.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="../assets/js/user/navbar.js" defer></script>
    
</head>
<body>
    <div class="landing-container">
        <nav class="navbar">
            <img src="../assets/images/logo/raflora-logo.jpg" alt="logo" class="logo" />
            <div class="hamburger-menu">
                <i class="fas fa-bars"></i>
            </div>
            <ul class="nav-links">
                <li class="active"><a href="../guest/g-home.php" class="nav-link">Home</a></li>
                <li><a href="../guest/g-gallery.php" class="nav-link">Gallery</a></li>
                <li><a href="../guest/g-about.php" class="nav-link">About</a></li>
                <li><a href="../user/user_login.php" class="nav-link">Log-in</a></li>

            </ul>
        </nav>
        <!-- <button id="dark-mode-icon-toggle" 
            class="fixed top-4 left-4 p-3 rounded-full text-2xl transition-colors duration-300 hover:bg-gray-200 dark:hover:bg-gray-800 z-[9999] focus:outline-none">
        <i id="dark-mode-icon" class="fa-solid fa-moon text-blue-600 dark:text-yellow-400"></i>
        </button> -->
        <div class="Landing-page">
            <div class="Home-bg">
                <h1>Raflora Enterprises</h1>
                <p>Raflora Enterprises Flower Arrangement and<br>
                    Event Stylist Scheduling Management System</p>
                <img src="../assets/images/Home/box-img1.jpg" alt="Home">
            </div>
            <div class="Set-img" id="images">
                <h1 class="Section-Heading" id="Header"> </h1>
                <img src="../assets/images/portrait/section1.jpg" alt="image1">
                <img src="../assets/images/portrait/section2.jpg" alt="image2">
                <p class="description">Raflora Enterprises Management Team</p>
            </div>
            <div class="Feedback-content" id="Feeds">
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
    <script src="./js/script.js"></script>
</body>
</html>
