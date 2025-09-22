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
    <script src="../assets/js/user/booking.js" defer></script>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
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
                <form action="../config/client_booking.php" method="post">
                    <div class="form-group-row">
                        <div class="form-field">
                            <label for="full-name">Full name</label>
                            <input type="text" id="full_name" name="full_name" placeholder="full name">
                        </div>
                        <div class="form-field">
                            <label for="contact-number">Contact number</label>
                            <input type="tel" id="mobile_number" name="mobile_number" placeholder="+69 " maxlength="11">
                        </div>
                        <div class="form-field">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="email address">
                        </div>
                    </div>

                    <div class="form-group-address">
                        <div class="form-field">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" placeholder="address">
                        </div>
                    </div>
                    
                    <div class="form-group-row">
                        <div class="form-field">
                            <label for="color-scheme-upload">Color scheme</label>
                            <div class="file-upload-box">
                                <label for="color-scheme-upload">
                                    <i class="fi fi-br-cloud-upload"></i>
                                    <!-- <img src="../assets/images/icon/upload_ico.png" alt="Color scheme upload"> -->
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
                                    <input type="date" name="event_date" id="event-date">
                                </div>
                                <div class="time-field">
                                    <img src="../assets/images//icon/clock.png" alt="Clock icon">
                                    <input type="time" name="event_time" id="event-time">
                                </div>
                            </div>
                        </div>
                        <div class="form-field form-field-regards">
                            <label for="form-field form-field-regards">Your Recommendations :</label>
                            <textarea id="regards" name="recommendations" 
                                    placeholder="Type your message here..."></textarea>
                        </div>
                    </div>
                    <div class="form-group-row">
                        <div class="form-field">
                            <label for="theme">Event</label>
                            <select id="theme" name="event_theme">
                                <option value="">Select an event</option>
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
                        <div class="form-field hidden" id="packages-field">
                            <label for="packages">Packages</label>
                            <select id="packages"name="packages">
                                <option value="">Select Packages</option>
                                <optgroup label="Wedding Package" data-event="wedding">
                                <option class="package-option" value="Package 1">Wedding Package 1</option>
                                <option class="package-option" value="Package 2">Wedding Package 2</option>
                                <option class="package-option" value="Package 3">Wedding Package 3</option>
                                </optgroup>
                                <optgroup label="Birthday Party Package" data-event="birthday">
                                    <option class="package-option" value="Package 1">Birthday Package 1</option>
                                    <option class="package-option" value="Package 2">Birthday Package 2</option>
                                    <option class="package-option" value="Package 3">Birthday Package 3</option>
                                </optgroup>
                                <optgroup label="Reunion Package" data-event="reunion">
                                    <option class="package-option" value="Package 1">Reunion Package 1</option>
                                    <option class="package-option" value="Package 2">Reunion Package 2</option>
                                    <option class="package-option" value="Package 3">Reunion Package 3</option>
                                </optgroup>
                                <optgroup label="Funeral Package" data-event="funeral">
                                    <option class="package-option" value="Package 1">Funeral Package 1</option>
                                    <option class="package-option" value="Package 2">Funeral Package 2</option>
                                    <option class="package-option" value="Package 3">Funeral Package 3</option>
                                </optgroup>
                                <optgroup label="Meetings Package" data-event="meetings">
                                    <option class="package-option" value="Package 1">Meetings Package 1</option>
                                    <option class="package-option" value="Package 2">Meetings Package 2</option>
                                    <option class="package-option" value="Package 3">Meetings Package 3</option>
                                </optgroup>
                                <optgroup label="Conferences Package" data-event="conferences">
                                    <option class="package-option" value="Package 1">Conferences Package 1</option>
                                    <option class="package-option" value="Package 2">Conferences Package 2</option>
                                    <option class="package-option" value="Package 3">Conferences Package 3</option>
                                </optgroup>
                                <optgroup label="Hotels Package" data-event="hotels">
                                    <option class="package-option" value="Package 1">Hotel Package 1</option>
                                    <option class="package-option" value="Package 2">Hotel Package 2</option>
                                    <option class="package-option" value="Package 3">Hotel Package 3</option>
                                </optgroup>
                                <optgroup label="Churches Package" data-event="churches">
                                    <option class="package-option" value="Package 1">Church Package 1</option>
                                    <option class="package-option" value="Package 2">Church Package 2</option>
                                    <option class="package-option" value="Package 3">Church Package 3</option>
                                </optgroup>
                                <optgroup label="Outdoor Package" data-event="outdoor">
                                    <option class="package-option" value="Package 1">Outdoor Package 1</option>
                                    <option class="package-option" value="Package 2">Outdoor Package 2</option>
                                    <option class="package-option" value="Package 3">Outdoor Package 3</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="form-field">
                            <label for="payment-method">Payment Method</label>
                            <select id="payment-method"name="payment_method">
                                <option value="">Select payment method</option>
                                <option value="">Online Bank</option>
                                <option value="ewallet">E-Wallet</option>
                            </select>
                        </div>
                        <div class="form-field">
                            <label for="payment-type">Payment</label>
                             <div class="payment-selection">
                                <label><input type="radio" name="payment_type" value="Down Payment" checked> Down Payment</label>
                                <label><input type="radio" name="payment_type" value="Full Payment"> Full Payment</label>
                            </div>
                            <!-- <div class="payment-selection" name="payment" >
                                <button type="button" class="payment-button selected" >Down Payment</button>
                                <button type="button" class="payment-button">Full Payment</button>
                            </div> -->
                        </div>
                    </div>
                    <div class="form-group-preferred-design">
                        <h3>Preferred Design</h3>
                        <p>upload here:</p>
                        <div class="design-upload-group">
                            <div class="design-upload-box">
                                <label for="design-upload-1">
                                    <!-- <i class="fi fi-br-home my-custom-icon"></i> -->
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
                            <button type="submit" class="submit-button">Place order</button>
                        </div>
                    </form>
                </form>
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
    
</body>
</html>