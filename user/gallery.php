<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../user/user_login.html");
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
    <title>Gallery</title>
    <link rel="stylesheet" href="../assets/css/user/gallery.css">
    <link rel="stylesheet" href="../assets/css/user/footer.css">
    <link rel="stylesheet" href="../assets/css/user/navbar.css">
    <script src="../assets/js/user/navbar.js" defer></script>
    <script src="../assets/js/user/gallery.js" defer></script> 
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
    <div class="landing-container">
        <nav class="navbar">
            <img src="../assets/images/logo/raflora-logo.jpg" alt="logo" class="logo" />
            <div class="hamburger-menu">
                <i class="fas fa-bars"></i>
            </div>
            <ul class="nav-links">
                <li><a href="../user/landing.php" class="nav-link">Home</a></li>
                <li class="active"><a href="../user/gallery.php" class="nav-link">Gallery</a></li>
                <li><a href="../user/about.php" class="nav-link">About</a></li>
                <li><a href="../user/booking.php" class="nav-link">Book</a></li>
                <li class="user-dropdown-toggle">
                    <i class="fas fa-user-circle user-icon"></i>
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
        <div class="Gallery-Page">
            <div class="Header-Bg">
                <h1>Raflora <br>Enterprises</h1>
                <h3 >Event's Gallery</h3>
                <img src="../assets/images/Gallery/event/entrance1.jpg" alt="Raflora Gallery" />
            </div>
            
            <div class="Gallery-Collage">
                
                <div class="Image-Set1">
                    <h1>Event's Back Drop</h1>
                    <img class="main-gallery-img" src="../assets/images/Gallery/backdrop/backdrop1.jpg" alt="Beautiful Event Backdrop Display 1" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/backdrop/backdrop2.jpg" alt="Beautiful Event Backdrop Display 2" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/backdrop/backdrop3.jpg" alt="Beautiful Event Backdrop Display 3" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/backdrop/backdrop4.jpg" alt="Beautiful Event Backdrop Display 4" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/backdrop/backdrop5.jpg" alt="Beautiful Event Backdrop Display 5" />
                </div>
                
                <div class="Image-Set2">
                    <h1>Wedding</h1>
                    <img class="main-gallery-img" src="../assets/images/Gallery/wedding/wed6.jpg" alt="Elegant Wedding Floral Arrangement 1" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/wedding/wed1.jpg" alt="Elegant Wedding Floral Arrangement 2" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/wedding/wed2.jpg" alt="Elegant Wedding Floral Arrangement 3" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/wedding/wed3.jpg" alt="Elegant Wedding Floral Arrangement 4" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/wedding/wed4.jpg" alt="Elegant Wedding Floral Arrangement 5" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/wedding/wed5.jpg" alt="Elegant Wedding Floral Arrangement 6" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/wedding/wed7.jpg" alt="Elegant Wedding Floral Arrangement 7" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/wedding/wed8.jpg" alt="Elegant Wedding Floral Arrangement 8" />
                </div>
                
                <div class="Image-Set3">
                    <h1>Flowers</h1>
                    <img class="main-gallery-img" src="../assets/images/Gallery/flowers/flowerset3.jpg" alt="Vibrant Flower Bouquet 1" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset1.jpg" alt="Vibrant Flower Bouquet 2" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset2.jpg" alt="Vibrant Flower Bouquet 3" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset4.jpg" alt="Vibrant Flower Bouquet 4" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset5.jpg" alt="Vibrant Flower Bouquet 5" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset6.jpg" alt="Vibrant Flower Bouquet 6" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset8.jpg" alt="Vibrant Flower Bouquet 7" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset9.jpg" alt="Vibrant Flower Bouquet 8" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset10.jpg" alt="Vibrant Flower Bouquet 9" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset11.jpg" alt="Vibrant Flower Bouquet 10" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset12.jpg" alt="Vibrant Flower Bouquet 11" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset13.jpg" alt="Vibrant Flower Bouquet 12" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset14.jpg" alt="Vibrant Flower Bouquet 13" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset15.jpg" alt="Vibrant Flower Bouquet 14" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset16.jpg" alt="Vibrant Flower Bouquet 15" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset17.jpg" alt="Vibrant Flower Bouquet 16" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset18.jpg" alt="Vibrant Flower Bouquet 17" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset19.jpg" alt="Vibrant Flower Bouquet 18" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset20.jpg" alt="Vibrant Flower Bouquet 19" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset21.jpg" alt="Vibrant Flower Bouquet 20" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset22.jpg" alt="Vibrant Flower Bouquet 21" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset23.jpg" alt="Vibrant Flower Bouquet 22" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset24.jpg" alt="Vibrant Flower Bouquet 23" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset25.jpg" alt="Vibrant Flower Bouquet 24" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset26.jpg" alt="Vibrant Flower Bouquet 25" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset27.jpg" alt="Vibrant Flower Bouquet 26" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset28.jpg" alt="Vibrant Flower Bouquet 27" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset29.jpg" alt="Vibrant Flower Bouquet 28" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset30.jpg" alt="Vibrant Flower Bouquet 29" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset31.jpg" alt="Vibrant Flower Bouquet 30" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset32.jpg" alt="Vibrant Flower Bouquet 31" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset33.jpg" alt="Vibrant Flower Bouquet 32" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flower1.jpg" alt="Vibrant Flower Bouquet 33" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flower2.jpg" alt="Vibrant Flower Bouquet 34" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flower3.jpg" alt="Vibrant Flower Bouquet 35" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flower4.jpg" alt="Vibrant Flower Bouquet 36" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flower5.jpg" alt="Vibrant Flower Bouquet 37" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flower6.jpg" alt="Vibrant Flower Bouquet 38" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/flowers/flowerset7.jpg" alt="Vibrant Flower Bouquet 39" />
                </div>
                
                <div class="Image-Set4">
                    <h1>Christmas</h1>
                    <img class="main-gallery-img" src="../assets/images/Gallery/xmass/xmass4.jpg" alt="Festive Christmas Decor and Flowers 1" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/xmass/xmass1.jpg" alt="Festive Christmas Decor and Flowers 2" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/xmass/xmass2.jpg" alt="Festive Christmas Decor and Flowers 3" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/xmass/xmass3.jpg" alt="Festive Christmas Decor and Flowers 4" />
                </div>
                
                <div class="Image-Set5">
                    <h1>Birthday</h1>
                    <img class="main-gallery-img" src="../assets/images/Gallery/Birthday/kik1.jpg" alt="Colorful Birthday Party Setup 1" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/Birthday/kik2.jpg" alt="Colorful Birthday Party Setup 2" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/Birthday/gallery1.jpg" alt="Colorful Birthday Party Setup 3" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/Birthday/gallery2.jpg" alt="Colorful Birthday Party Setup 4" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/Birthday/gallery3.jpg" alt="Colorful Birthday Party Setup 5" />
                </div>
                
                <div class="Image-Set6">
                    <h1>Funeral</h1>
                    <img class="main-gallery-img" src="../assets/images/Gallery/Funeral/funeral1.jpg" alt="Sober Funeral Floral Wreath 1" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/Funeral/funeral2.jpg" alt="Sober Funeral Floral Wreath 2" />
                    <img class="hidden-gallery-img" src="../assets/images/Gallery/Funeral/funeral3.jpg" alt="Sober Funeral Floral Wreath 3" />
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
    
    <div id="galleryModal" class="modal">
        <span class="close-btn">&times;</span>
        <div class="modal-content">
            <a class="prev">&#10094;</a>
            <a class="next">&#10095;</a>
            <img class="modal-image" id="modalImage" alt="Gallery Image">
            <div id="caption"></div>
        </div>
    </div>
</body>
</html>