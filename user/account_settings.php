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
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/user/account_settings.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class', // Enable class-based dark mode
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'primary-blue': '#000000ff',
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center font-sans p-4 bg-white">
    

    <!-- Account Settings Container -->
    <div id="settings-card" class="w-full max-w-xl bg-gray-800 p-6 sm:p-8 rounded-2xl shadow-2xl text-white card-bg">
        <button class="home-btn">
                <a href="../user/landing.php"><i class="fas fa-times"></i></a>
            </button>
        <h2 class="text-2xl font-bold text-center mb-2 border-b border-gray-700 pb-3">Account Settings</h2>
        

        <!-- Profile Picture Upload - MOVED OUTSIDE THE FORM -->
        <div class="profile-picture-section">
            <div class="profile-container">
                <div class="profile-image-container" onclick="viewProfileImage()">
                    <!-- Placeholder Avatar -->
                    <i id="avatarIcon" class="profile-placeholder-icon <?php echo !empty($user_data['profile_picture']) ? 'hidden' : ''; ?>"></i>
                    <img id="profileImage" src="<?php echo !empty($user_data['profile_picture']) ? '/raflora_enterprises/' . $user_data['profile_picture'] : ''; ?>" alt="Profile" class="profile-image <?php echo empty($user_data['profile_picture']) ? 'hidden' : ''; ?>">
                    
                    <!-- Three-dot menu (only show when image exists) -->
                    <div id="profileMenu" class="profile-menu <?php echo empty($user_data['profile_picture']) ? 'hidden' : ''; ?>">
                        <button type="button" onclick="event.stopPropagation(); toggleProfileMenu()" class="profile-menu-button">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        
                        <!-- Dropdown menu -->
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
                </div>
            </div>
            <label for="profile-upload" class="profile-upload-label">
                Change Profile Photo
            </label>
            <!-- FILE INPUT MOVED OUTSIDE FORM -->
            <input type="file" id="profile-upload" class="profile-upload-input" accept="image/*" onchange="previewImage(event)">
        </div>

        <!-- START FORM HERE -->
        <form id="settingsForm" onsubmit="handleSave(event)">
            
            <!-- User ID and Username Display (Read Only - As requested) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="form-group relative form-group-icon">
                    <input type="text" value="User ID: <?php echo $user_data['user_id'] ?? 'Loading...'; ?>" readonly
                           class="w-full p-3 pl-4 rounded-lg input-dark transition duration-200 cursor-not-allowed opacity-75 text-sm">
                    <i class="fa fa-id-card-clip absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="form-group relative form-group-icon">
                    <input type="text" placeholder="Username" value="<?php echo $user_data['user_name'] ?? 'Loading...'; ?>" readonly
                           class="w-full p-3 pl-4 rounded-lg input-dark transition duration-200 cursor-not-allowed opacity-75">
                    <i class="fa fa-user absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <!-- First Name and Last Name Group -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="form-group relative form-group-icon">
                    <input type="text" id="reg-firstname" name="firstname" placeholder="First Name" maxlength="50" required 
                           value="<?php echo $user_data['first_name'] ?? ''; ?>"
                           class="w-full p-3 pl-4 rounded-lg input-dark transition duration-200">
                    <i class="fa-solid fa-signature absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="form-group relative form-group-icon">
                    <input type="text" id="reg-lastname" name="lastname" placeholder="Last Name" maxlength="50" required 
                           value="<?php echo $user_data['last_name'] ?? ''; ?>"
                           class="w-full p-3 pl-4 rounded-lg input-dark transition duration-200">
                    <i class="fa-solid fa-signature absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Email and Contact Number Group -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="form-group relative form-group-icon">
                    <input type="email" id="reg-email" placeholder="Email" value="<?php echo $user_data['email'] ?? ''; ?>" required
                           class="w-full p-3 pl-4 rounded-lg input-dark transition duration-200">
                    <i class="fa fa-envelope absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="form-group relative form-group-icon">
                    <input type="tel" id="reg-phone" name="phone" placeholder="Contact Number (e.g., 900-123-4567)" maxlength="15" 
                           value="<?php echo $user_data['mobile_number'] ?? ''; ?>"
                           class="w-full p-3 pl-4 rounded-lg input-dark transition duration-200">
                    <i class="fa-solid fa-phone absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <div class="form-group relative form-group-icon mb-6">
                <input type="text" id="reg-address" name="address" placeholder="Address (Optional)" maxlength="255"
                        value="<?php echo $user_data['address'] ?? ''; ?>"
                        class="w-full p-3 pl-4 rounded-lg input-dark transition duration-200">
                <i class="fa-solid fa-map-location-dot absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            
            <!-- Security Section -->
            <div class="section-title border-b border-gray-700 mb-4 pb-2 text-xl font-semibold text-blue-400">Security</div>
            
            <!-- Change Password Group -->
            <div class="mb-6 space-y-4">
                <h4 class="text-lg font-medium text-gray-300">Change Password (Leave blank if not changing):</h4>
                <div class="form-group relative form-group-icon">
                    <input type="password" id="current-password" placeholder="Current Password"
                           class="w-full p-3 pl-4 rounded-lg input-dark transition duration-200">
                    <i class="fa fa-lock absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="form-group relative form-group-icon">
                    <input type="password" id="new-password" placeholder="New Password"
                           class="w-full p-3 pl-4 rounded-lg input-dark transition duration-200">
                    <i class="fa fa-lock absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="form-group relative form-group-icon">
                    <input type="password" id="confirm-new-password" placeholder="Confirm New Password"
                           class="w-full p-3 pl-4 rounded-lg input-dark transition duration-200">
                    <i class="fa fa-lock absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <button type="submit" class="btn w-full py-3 mt-4 rounded-xl font-bold bg-blue-600 text-white hover:bg-blue-700 transition duration-300 shadow-md shadow-blue-900/50">
                <i class="fa-solid fa-floppy-disk mr-2"></i> Save Changes
            </button>
        </form>

        <!-- Danger Zone -->
        <div class="section-title border-b border-gray-700 mt-8 mb-4 pb-2 text-xl font-semibold text-red-400">Deactivate account</div>
        
        <!-- Delete Account -->
        <div class="flex items-center justify-between p-4 bg-red-900/30 border border-red-800 rounded-xl">
            <div>
                <span class="text-lg font-medium text-red-300">Delete Account</span>
                <p class="text-sm text-gray-400">Permanently remove your account and all associated data.</p>
            </div>
            <button onclick="handleDelete()" class="btn py-2 px-4 rounded-lg font-bold bg-red-600 text-white hover:bg-red-700 transition duration-300 whitespace-nowrap">
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
    <div id="message-box" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center p-4 z-50">
        <div class="bg-gray-800 p-6 rounded-xl shadow-2xl max-w-sm w-full text-white">
            <h3 id="modal-title" class="text-xl font-semibold mb-3">Message</h3>
            <p id="modal-content" class="text-gray-300 mb-4">Content here.</p>
            <div class="flex justify-end space-x-2">
                <button onclick="closeMessageModal()" class="py-2 px-4 bg-blue-600 rounded-lg hover:bg-blue-700 transition">OK</button>
                <button id="modal-cancel" onclick="closeMessageModal()" class="py-2 px-4 bg-gray-600 rounded-lg hover:bg-gray-700 transition hidden">Cancel</button>
            </div>
        </div>
    </div>

    <script src="../assets/js/user/account_settings.js"></script>
</body>
</html>