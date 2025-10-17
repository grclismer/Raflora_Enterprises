<?php
session_start();

// Debug session
error_log("Account Settings - Session: " . print_r($_SESSION, true));

// If user is not logged in, redirect to login
if (!isset($_SESSION['user_id'])) {
    // Try to get user_id from cookie as fallback
    if (isset($_COOKIE['user_id'])) {
        $_SESSION['user_id'] = $_COOKIE['user_id'];
        $_SESSION['username'] = $_COOKIE['username'] ?? '';
        $_SESSION['is_logged_in'] = true;
    } else {
        header("Location: ../guest/login.php");
        exit();
    }
}

// Now get user data directly from database for the page
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);
$user_data = [];

if (!$conn->connect_error) {
    // UPDATED QUERY TO INCLUDE PROFILE PICTURE
    $stmt = $conn->prepare("SELECT user_id, first_name, last_name, user_name, email, mobile_number, address, profile_picture FROM accounts_tbl WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user_data = $result->fetch_assoc();
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <link rel="stylesheet" href="../assets/css/user/account_settings.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<!-- Add this in the body section, before the script tag -->
<input type="hidden" id="currentUserId" value="<?php echo $user_data['user_id']; ?>">
<body>
    <!-- Account Settings Container -->
    <div id="settings-card" class="settings-card">
        <button class="home-btn">
            <a href="../user/landing.php"><i class="fas fa-times"></i></a>
        </button>
        <h2 class="settings-title">Account Settings</h2>

        <!-- Profile Picture Upload -->
        <div class="profile-picture-section">
            <div class="profile-container">
                <div class="profile-image-container" onclick="viewProfileImage()">
                    <i id="avatarIcon" class="profile-placeholder-icon <?php echo !empty($user_data['profile_picture']) ? 'hidden' : ''; ?>"></i>
                    <img id="profileImage" src="<?php echo !empty($user_data['profile_picture']) ? '/raflora_enterprises/' . $user_data['profile_picture'] : ''; ?>" alt="Profile" class="profile-image <?php echo empty($user_data['profile_picture']) ? 'hidden' : ''; ?>">
                    <div id="profileMenu" class="profile-menu <?php echo empty($user_data['profile_picture']) ? 'hidden' : ''; ?>">
                        <button type="button" onclick="event.stopPropagation(); toggleProfileMenu()" class="profile-menu-button">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                    </div>
                </div>
                
                <div id="profileDropdown" class="profile-dropdown hidden">
                    <button type="button" onclick="document.getElementById('profile-upload').click()" class="profile-dropdown-item">
                        <i class="fas fa-camera profile-dropdown-icon profile-dropdown-icon-change"></i>
                        Change Photo
                    </button>
                    <button type="button" onclick="removeProfilePicture()" class="profile-dropdown-item">
                        <i class="fas fa-trash profile-dropdown-icon profile-dropdown-icon-remove"></i>
                        Remove Photo
                    </button>
                </div>
            </div>
            <input type="file" id="profile-upload" class="profile-upload-input" accept="image/*" onchange="previewImage(event)">
        </div>

        <!-- START FORM HERE -->
        <form id="settingsForm" onsubmit="handleSave(event)">
            
            <!-- User ID and Username Display -->
            <div class="form-grid">
                <div class="form-group form-group-icon">
                    <input type="text" value="User ID: <?php echo $user_data['user_id'] ?? 'Loading...'; ?>" readonly
                           class="form-input readonly-input">
                    <i class="fa fa-id-card-clip form-icon"></i>
                </div>
                <div class="form-group form-group-icon">
                    <input type="text" placeholder="Username" value="<?php echo $user_data['user_name'] ?? 'Loading...'; ?>" readonly
                           class="form-input readonly-input">
                    <i class="fa fa-user form-icon"></i>
                </div>
            </div>
            
            <!-- First Name and Last Name Group -->
            <div class="form-grid">
                <div class="form-group form-group-icon">
                    <input type="text" id="reg-firstname" name="firstname" placeholder="First Name" maxlength="50" required 
                           value="<?php echo $user_data['first_name'] ?? ''; ?>"
                           class="form-input">
                    <i class="fa-solid fa-signature form-icon"></i>
                </div>
                <div class="form-group form-group-icon">
                    <input type="text" id="reg-lastname" name="lastname" placeholder="Last Name" maxlength="50" required 
                           value="<?php echo $user_data['last_name'] ?? ''; ?>"
                           class="form-input">
                    <i class="fa-solid fa-signature form-icon"></i>
                </div>
            </div>

            <!-- Email and Contact Number Group -->
            <div class="form-grid">
                <div class="form-group form-group-icon">
                    <input type="email" id="reg-email" placeholder="Email" value="<?php echo $user_data['email'] ?? ''; ?>" required
                           class="form-input">
                    <i class="fa fa-envelope form-icon"></i>
                </div>
                <div class="form-group form-group-icon">
                    <input type="tel" id="reg-phone" name="phone" placeholder="Contact Number (e.g., 900-123-4567)" maxlength="15" 
                           value="<?php echo $user_data['mobile_number'] ?? ''; ?>"
                           class="form-input">
                    <i class="fa-solid fa-phone form-icon"></i>
                </div>
            </div>
            
            <div class="form-group form-group-icon address-group">
                <input type="text" id="reg-address" name="address" placeholder="Address (Optional)" maxlength="255"
                        value="<?php echo $user_data['address'] ?? ''; ?>"
                        class="form-input">
                <i class="fa-solid fa-map-location-dot form-icon"></i>
            </div>
            
            <!-- Security Section -->
            <div class="section-title security-title">Security</div>
            
            <!-- Change the password section to -->
            <div class="password-section">
                <h4 class="password-title">Change Password (Leave blank if not changing):</h4>
                <div class="password-group">
                    <div class="form-group form-group-icon">
                        <input type="password" id="current-password" placeholder="Current Password" class="form-input">
                        <i class="fa fa-lock form-icon"></i>
                    </div>
                    <div class="form-group form-group-icon">
                        <input type="password" id="new-password" placeholder="New Password" class="form-input">
                        <i class="fa fa-lock form-icon"></i>
                    </div>
                    <div class="form-group form-group-icon">
                        <input type="password" id="confirm-new-password" placeholder="Confirm New Password" class="form-input">
                        <i class="fa fa-lock form-icon"></i>
                    </div>
                </div>
            </div>
            <button type="submit" class="save-btn">
                <i class="fa-solid fa-floppy-disk"></i> Save Changes
            </button>
        </form>
    <!-- QR Code Login Section -->
<div class="section-title qr-title">QR Code Login</div>
<div class="qr-section">
    <div class="qr-content">
        <span class="qr-title">Your Login QR Code</span>
        <p class="qr-description">Use this QR code to log in quickly. Save it to your phone and scan it on the login page.</p>
        
        <!-- QR Code Display -->
        <div class="qr-code-container">
            <div class="qr-code-display">
                <img id="qrCodeImage" 
     src="/Raflora_Enterprises/api/get_user_qr.php?user_id=<?php echo $user_data['user_id']; ?>" 
     alt="Your Login QR Code" 
     class="qr-image">
            </div>
            
            <!-- Download Button -->
            <button onclick="downloadQRCode()" class="download-qr-btn">
                <i class="fas fa-download"></i> Save QR Code
            </button>
        </div>
    </div>
</div>
        <!-- Danger Zone -->
        <div class="section-title danger-title">Deactivate account</div>
        <!-- Delete Account -->
        <div class="delete-section">
            <div class="delete-content">
                <span class="delete-title">Delete Account</span>
                <p class="delete-description">Permanently remove your account.</p>
            </div>
            <button onclick="handleDelete()" class="delete-btn">
                Delete
            </button>
        </div>
    </div>
    <!-- Image View Modal -->
    <div id="imageViewModal" class="image-view-modal hidden">
        <div class="image-modal-content">
            <button onclick="closeImageView()" class="image-modal-close">
                <i class="fas fa-times"></i>
            </button>
            <img id="modalProfileImage" src="" alt="Profile Preview" class="image-modal-preview">
        </div>
    </div>
    <!-- Message Modal (for alerts) -->
    <div id="message-box" class="message-modal hidden">
        <div class="modal-content">
            <h3 id="modal-title" class="modal-title">Message</h3>
            <p id="modal-content" class="modal-text">Content here.</p>
            <div class="modal-actions">
                <button onclick="closeMessageModal()" class="modal-btn confirm-btn">OK</button>
                <button id="modal-cancel" onclick="closeMessageModal()" class="modal-btn cancel-btn hidden">Cancel</button>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/user/account_settings.js"></script>
</body>
</html>