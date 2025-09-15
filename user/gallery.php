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
    <title>Gallery</title>
    <link rel="stylesheet" href="../assets/css/user/gallery.css">
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
        <!-- Gallery Header Section -->
        <div class="Gallery-Page">
          <div class="Header-Bg">
            <h1>Raflora <br>Enterprises</h1>
            <h3 >Event's Gallery</h3>
            <img src="../assets/images/Gallery/event/entrance1.jpg" alt="Raflora Gallery" />
          </div>
              <!-- Gallery Collage -->
          <div class="Gallery-Collage">
              <div class="Image-Set1">
                <h1>Event's Back Drop</h1>
                <img src="../assets/images/Gallery/backdrop/backdrop1.jpg" alt="Back Drop" />
              </div>
              <div class="Image-Set2">
                <h1>Wedding</h1>
                <img src="../assets/images/Gallery/wedding/wed6.jpg" alt="Wedding" />
              </div>
              <div class="Image-Set3">
                <h1>Flowers</h1>
                <img src="../assets/images/Gallery/flowers/flowerset3.jpg" alt="Flowers" />
              </div>
              <div class="Image-Set4">
                <h1>Christmas</h1>
                <img src="../assets/images/Gallery/xmass/xmass4.jpg" alt="Christmas" />
              </div>
              <div class="Image-Set5">
                <h1>Birthday</h1>
                <img src="../assets/images/Gallery/Birthday/kik1.jpg" alt="Birthday" />
              </div>
              <div class="Image-Set6">
                <h1>Funeral</h1>
                <img src="../assets/images/Gallery/Funeral/funeral1.jpg" alt="Funeral" />
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
  <script src="js/script.js"></script>
</body>
</html>