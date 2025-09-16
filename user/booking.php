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
    <title>Reservation</title>
    <link rel="stylesheet" href="../assets/css/user/booking.css">
    <link rel="stylesheet" href="../assets/css/user/footer.css">
    <link rel="stylesheet" href="../assets/css/user/navbar.css">
    <script src="../assets/js/user/navbar.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
</head>
<body>
    <div class="booking-container">
        <nav class="navbar">
            <img src="../assets/images/logo/raflora-logo.jpg" alt="logo" class="logo" />
            <div class="hamburger-menu">
                <i class="fas fa-bars"></i>
            </div>
            <ul class="nav-links">
                <li><a href="../api/landing.php" class="nav-link">Home</a></li>
                <li><a href="../user/gallery.php" class="nav-link">Gallery</a></li>
                <li><a href="../user/about.php" class="nav-link">About</a></li>
                <li class="active"><a href="../user/booking.php" class="nav-link">Book</a></li>
                <li class="user-dropdown-toggle">
                    <i class="fas fa-user-circle user-icon"></i>
                    <ul class="user-dropdown-menu">
                        <li><a href="#">Edit Account</a></li>
                        <li><a href="#">My Bookings</a></li>
                        <li><a href="../api/logout.php">Log Out</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div class="main-container">
            <h1 class="page-title">Please fill-up the form</h1>
            <div class="form-container">
                <form action="#" method="post">
                    <div class="form-group-row">
                        <div class="form-field">
                            <label for="full-name">Full name</label>
                            <input type="text" id="full-name" placeholder="John Doe">
                        </div>
                        <div class="form-field">
                            <label for="contact-number">Contact number</label>
                            <input type="tel" id="contact-number" placeholder="+69 9123456789">
                        </div>
                        <div class="form-field">
                            <label for="email">Email</label>
                            <input type="email" id="email" placeholder="johndoe@example.com">
                        </div>
                    </div>

                    <div class="form-group-address">
                        <div class="form-field">
                            <label for="address">Address</label>
                            <input type="text" id="address" placeholder="1234 Makati st, sample, sample address B2 04145, Makati City">
                        </div>
                    </div>
                    
                    <div class="form-group-row">
                        <div class="form-field">
                            <label for="color-scheme-upload">Color scheme</label>
                            <div class="file-upload-box">
                                <label for="color-scheme-upload">
                                    <img src="../assets/images/icon/upload_ico.png" alt="Color scheme upload">
                                    <span class="upload-text"></span>
                                </label>
                                <input type="file" id="color-scheme-upload" accept="image/*" class="file-input">
                            </div>
                        </div>
                    
                        <div class="form-field">
                            <label for="event-schedule">Event Schedule</label>
                            <div class="date-time-group">
                                <div class="date-field">
                                    <img src="../assets/images//icon/calendar.png" alt="Calendar icon">
                                    <input type="date" id="event-date">
                                </div>
                                <div class="time-field">
                                    <img src="../assets/images//icon/clock.png" alt="Clock icon">
                                    <input type="time" id="event-time">
                                </div>
                            </div>
                        </div>
                        <div class="form-field form-field-regards">
                            <label for="form-field form-field-regards">Your Regards:</label>
                            <textarea id="regards" name="message" 
                                    placeholder="Type your message here..."></textarea>
                        </div>
                    </div>
                    <div class="form-group-row">
                        <div class="form-field">
                            <label for="theme">Theme</label>
                            <select id="theme">
                                <option value="">Select an option</option>
                                <optgroup label="Personal Events">
                                    <option value="wedding">Wedding</option>
                                    <option value="birthday">Birthday Party</option>
                                    <option value="reunion">Reunion</option>
                                    <option value="funeral">Funeral</option>
                                </optgroup>
                                <optgroup label="Corporate Events">
                                    <option value="meetings">Meetings</option>
                                    <option value="conferences">Conferences</option>
                                </optgroup>
                                <optgroup label="Venues">
                                    <option value="hotels">Hotels</option>
                                    <option value="churches">Churches</option>
                                    <option value="outdoor">Outdoor</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="form-field">
                            <label for="payment-method">Payment Method</label>
                            <select id="payment-method">
                                <option value="">Select an option</option>
                                <option value="">Online Bank</option>
                                <option value="ewallet">E-Wallet</option>
                            </select>
                        </div>
                        <div class="form-field">
                            <label for="payment-type">Payment</label>
                            <div class="payment-selection">
                                <button type="button" class="payment-button selected">Half Payment</button>
                                <button type="button" class="payment-button">Full Payment</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-preferred-design">
                        <h3>Preferred Design</h3>
                        <p>upload here:</p>
                        <div class="design-upload-group">
                            <div class="design-upload-box">
                                <label for="design-upload-1">
                                    <img src="../assets/images//icon/upload_image_placeholder.png" alt="Upload design image">
                                </label>
                                <input type="file" id="design-upload-1" class="file-input">
                            </div>
                            <div class="design-upload-box">
                                <label for="design-upload-2">
                                    <img src="../assets/images//icon/upload_image_placeholder.png" alt="Upload design image">
                                </label>
                                <input type="file" id="design-upload-2" class="file-input">
                            </div>
                            <div class="design-upload-box">
                                <label for="design-upload-3">
                                    <img src="../assets/images//icon/upload_image_placeholder.png" alt="Upload design image">
                                </label>
                                <input type="file" id="design-upload-3" class="file-input">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-action">
                        <button type="submit" class="submit-button"><a href="../user/billing.php">Place order</a></button>
                    </div>
                </form>
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
</body>
</html>