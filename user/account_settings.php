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
    <style>
        /* Custom styles for dark inputs and smooth transitions */
        .input-dark {
            background-color: rgba(248, 248, 248, 0.1);
            color: #f9f9f9ff;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .input-dark:focus {
            border-color: #3b82f6; /* Blue for focus */
            outline: none;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
        }
        .form-group-icon i {
            pointer-events: none; /* Allows clicks to pass through to the input */
        }
        
        /* Dark Mode Specific Styles */
        html.dark {
            /* Full dark background */
            background-color: #eceef1ff; 
        }
        html:not(.dark) {
             /* Default background (yung dark blue na ginamit mo sa original) */
             background-color: #ebebebff; 
        }
        body {
            /* Ensure body inherits size but not the color, container handles card color */
            transition: background-color 0.5s;
        }
        .card-bg {
             transition: background-color 0.5s;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center font-sans p-4 bg-white">

    <!-- Account Settings Container -->
    <div id="settings-card" class="w-full max-w-xl bg-gray-800 p-6 sm:p-8 rounded-2xl shadow-2xl text-white card-bg">
        
        <h2 class="text-2xl font-bold text-center mb-2 border-b border-gray-700 pb-3">Account Settings</h2>
        <form id="settingsForm" onsubmit="handleSave(event)">
            
            <!-- User ID and Username Display (Read Only - As requested) -->
            <!-- <div class="section-title border-b border-gray-700 mb-4 pb-2 text-xl font-semibold text-blue-400">User Information (Read Only)</div> -->

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

            
            <!-- Profile Info Section -->
            <div class="section-title border-b border-gray-700 mb-4 pb-2 text-xl font-semibold text-blue-400 mt-6">Profile & Contact Info</div>

            <!-- Profile Picture Upload -->
            <div class="flex flex-col items-center mb-6">
                <div class="relative w-28 h-28 rounded-full bg-gray-700 flex items-center justify-center overflow-hidden border-4 border-gray-600 mb-2">
                    <!-- Placeholder Avatar -->
                    <i id="avatarIcon" class="fa fa-user text-6xl text-gray-500 <?php echo !empty($user_data['profile_picture']) ? 'hidden' : ''; ?>"></i>
                    <img id="profileImage" src="<?php echo !empty($user_data['profile_picture']) ? '/raflora_enterprises/' . ltrim($user_data['profile_picture'], '/') : ''; ?>" alt="Profile" class="absolute w-full h-full object-cover <?php echo empty($user_data['profile_picture']) ? 'hidden' : ''; ?>">
                </div>
                <label for="profile-upload" class="cursor-pointer text-sm font-medium text-blue-400 hover:text-blue-300 transition duration-150">
                    Change Profile Photo
                </label>
                <input type="file" id="profile-upload" class="hidden" accept="image/*" onchange="previewImage(event)">
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

    <!-- Custom Modal/Message Box (for alerts) -->
    <div id="message-box" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center p-4 z-50">
        <div class="bg-gray-800 p-6 rounded-xl shadow-2xl max-w-sm w-full text-white">
            <h3 id="modal-title" class="text-xl font-semibold mb-3">Message</h3>
            <p id="modal-content" class="text-gray-300 mb-4">Content here.</p>
            <div class="flex justify-end space-x-2">
                <button onclick="closeModal()" class="py-2 px-4 bg-blue-600 rounded-lg hover:bg-blue-700 transition">OK</button>
                <button id="modal-cancel" onclick="closeModal()" class="py-2 px-4 bg-gray-600 rounded-lg hover:bg-gray-700 transition hidden">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        // Update profile picture preview function
        function previewImage(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                showMessage("Error", "Please select a valid image file (JPG, PNG, GIF, or WebP)");
                return;
            }

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                showMessage("Error", "File size must be less than 5MB");
                return;
            }

            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('profileImage');
                const icon = document.getElementById('avatarIcon');
                output.src = reader.result;
                output.classList.remove('hidden');
                icon.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }

        // --- Modal/Message Box Functions ---
        function showMessage(title, content, isConfirm = false, onConfirm = null) {
            const box = document.getElementById('message-box');
            document.getElementById('modal-title').textContent = title;
            document.getElementById('modal-content').textContent = content;
            const confirmBtn = document.getElementById('modal-cancel');
            
            if (isConfirm) {
                confirmBtn.classList.remove('hidden');
                confirmBtn.onclick = closeModal;
                document.querySelector('#message-box button:not(#modal-cancel)').onclick = () => {
                    if (onConfirm) onConfirm();
                    closeModal();
                };
            } else {
                confirmBtn.classList.add('hidden');
                document.querySelector('#message-box button').onclick = closeModal;
            }

            box.classList.remove('hidden');
            box.classList.add('flex');
        }

        function closeModal() {
            const box = document.getElementById('message-box');
            box.classList.add('hidden');
            box.classList.remove('flex');
        }

        async function handleSave(event) {
            event.preventDefault();
            
            // Basic validation
            const currentPass = document.getElementById('current-password').value;
            const newPass = document.getElementById('new-password').value;
            const confirmPass = document.getElementById('confirm-new-password').value;
            
            if (newPass || confirmPass) {
                if (!currentPass) {
                    showMessage("Error", "Current password is required when changing password.");
                    return;
                }
                if (newPass !== confirmPass) {
                    showMessage("Error", "New password and confirmation do not match.");
                    return;
                }
            }

            // Prepare form data
            const formData = new FormData();
            formData.append('firstname', document.getElementById('reg-firstname').value);
            formData.append('lastname', document.getElementById('reg-lastname').value);
            formData.append('email', document.getElementById('reg-email').value);
            formData.append('phone', document.getElementById('reg-phone').value);
            formData.append('address', document.getElementById('reg-address').value);
            
            // Add profile picture if selected
            const profileUpload = document.getElementById('profile-upload');
            if (profileUpload.files[0]) {
                formData.append('profile_picture', profileUpload.files[0]);
            }

            try {
                // Use upload_profile.php which handles both profile data and picture
                const response = await fetch('/raflora_enterprises/api/upload_profile.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    // Update profile image on page if new picture was uploaded
                    if (data.profile_picture) {
                        const profileImg = document.getElementById('profileImage');
                        const icon = document.getElementById('avatarIcon');
                        profileImg.src = '/raflora_enterprises/' + data.profile_picture.replace(/^\/+/, '');
                        profileImg.classList.remove('hidden');
                        icon.classList.add('hidden');
                    }
                    
                    showMessage("Success", data.message);
                    
                    // Clear password fields
                    document.getElementById('current-password').value = '';
                    document.getElementById('new-password').value = '';
                    document.getElementById('confirm-new-password').value = '';
                    
                } else {
                    showMessage("Error", "Failed to update profile: " + data.message);
                }
            } catch (error) {
                console.error('Save error:', error);
                showMessage("Error", "Failed to save changes: " + error.message);
            }
        }

        async function handleDelete() {
            showMessage(
                "Confirm Deletion", 
                "Are you sure you want to delete your account permanently? This action cannot be undone.",
                true,
                async () => {
                    try {
                        const response = await fetch('/raflora_enterprises/api/delete_account.php', {
                            method: 'POST'
                        });
                        
                        const data = await response.json();
                        
                        if (data.status === 'success') {
                            showMessage("Account Deleted", "Your account has been deleted successfully. Redirecting to homepage...");
                            setTimeout(() => {
                                window.location.href = '/raflora_enterprises/index.html';
                            }, 2000);
                        } else {
                            showMessage("Error", "Failed to delete account: " + data.message);
                        }
                    } catch (error) {
                        showMessage("Error", "Failed to delete account: " + error.message);
                    }
                }
            );
        }

        // --- Initialization ---
        document.addEventListener('DOMContentLoaded', () => {
            // Load profile picture on page load
            const profileImage = document.getElementById('profileImage');
            if (profileImage.src && !profileImage.src.includes('account_settings.php')) {
                const output = document.getElementById('profileImage');
                const icon = document.getElementById('avatarIcon');
                output.classList.remove('hidden');
                icon.classList.add('hidden');
            }
        });
    </script>
</body>
</html>