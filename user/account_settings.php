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
                        'primary-blue': '#3b82f6',
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom styles for dark inputs and smooth transitions */
        .input-dark {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
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
            background-color: #1f2937; 
        }
        html:not(.dark) {
             /* Default background (yung dark blue na ginamit mo sa original) */
             background-color: #111827; 
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
<body class="min-h-screen flex items-center justify-center font-sans p-4 bg-gray-900">

    <!-- Dark Mode Toggle Button (TOP LEFT - Global Control) -->
    <button id="top-dark-mode-icon-toggle" class="fixed top-4 left-4 p-3 rounded-full text-2xl transition duration-300 hover:bg-gray-700 z-50 focus:outline-none">
        <!-- Initial icon state will be set by JS on load -->
        <i id="top-dark-mode-icon" class="fa-solid fa-moon text-blue-400"></i>
    </button>


    <!-- Account Settings Container -->
    <div id="settings-card" class="w-full max-w-xl bg-gray-800 p-6 sm:p-8 rounded-2xl shadow-2xl text-white card-bg">
        
        <h2 class="text-3xl font-bold text-center mb-6 border-b border-gray-700 pb-3">Account Settings</h2>

        <form id="settingsForm" onsubmit="handleSave(event)">
            
            <!-- User ID and Username Display (Read Only - As requested) -->
            <div class="section-title border-b border-gray-700 mb-4 pb-2 text-xl font-semibold text-blue-400">User Information (Read Only)</div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="form-group relative form-group-icon">
                    <!-- Sample Data: Papalitan ito ng actual user ID value mo -->
                    <input type="text" value="User ID: C0012345" readonly
                           class="w-full p-3 pl-4 rounded-lg input-dark transition duration-200 cursor-not-allowed opacity-75 text-sm">
                    <i class="fa fa-id-card-clip absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="form-group relative form-group-icon">
                    <!-- Sample Data: Papalitan ito ng actual username value mo -->
                    <input type="text" placeholder="Username" value="client_jane_doe" readonly
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
                    <i id="avatarIcon" class="fa fa-user text-6xl text-gray-500"></i>
                    <img id="profileImage" src="" alt="Profile" class="absolute w-full h-full object-cover hidden">
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
                           value="Jane"
                           class="w-full p-3 pl-4 rounded-lg input-dark transition duration-200">
                    <i class="fa-solid fa-signature absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="form-group relative form-group-icon">
                    <input type="text" id="reg-lastname" name="lastname" placeholder="Last Name" maxlength="50" required 
                           value="Doe"
                           class="w-full p-3 pl-4 rounded-lg input-dark transition duration-200">
                    <i class="fa-solid fa-signature absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Email and Contact Number Group -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="form-group relative form-group-icon">
                    <input type="email" id="reg-email" placeholder="Email" value="jane.doe@example.com" required
                           class="w-full p-3 pl-4 rounded-lg input-dark transition duration-200">
                    <i class="fa fa-envelope absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="form-group relative form-group-icon">
                    <input type="tel" id="reg-phone" name="phone" placeholder="Contact Number (e.g., 900-123-4567)" maxlength="15" 
                           value="917-555-1234"
                           class="w-full p-3 pl-4 rounded-lg input-dark transition duration-200">
                    <i class="fa-solid fa-phone absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <div class="form-group relative form-group-icon mb-6">
                <input type="text" id="reg-address" name="address" placeholder="Address (Optional)" maxlength="255"
                        value="123 Main St, Manila"
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
        <div class="section-title border-b border-gray-700 mt-8 mb-4 pb-2 text-xl font-semibold text-red-400">Danger Zone</div>
        
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
        // --- Global Dark Mode Functions (Gamit ang localStorage) ---
        const DARK_MODE_KEY = 'global_dark_mode';

        /**
         * Ina-apply ang Dark Mode state sa buong page.
         * @param {boolean} isDark - True para sa Dark Mode, False para sa Light Mode.
         */
        function applyDarkMode(isDark) {
            const html = document.documentElement;
            const card = document.getElementById('settings-card');
            
            if (isDark) {
                html.classList.add('dark');
                html.style.backgroundColor = '#1f2937'; // Para sa smooth transition
                document.body.classList.remove('bg-gray-900');
                document.body.classList.add('bg-gray-800');
                card.classList.remove('bg-gray-800', 'text-white');
                card.classList.add('bg-gray-700', 'text-gray-100');
            } else {
                html.classList.remove('dark');
                html.style.backgroundColor = '#111827';
                document.body.classList.remove('bg-gray-800');
                document.body.classList.add('bg-gray-900');
                card.classList.remove('bg-gray-700', 'text-gray-100');
                card.classList.add('bg-gray-800', 'text-white');
            }
            
            // Update icons based on the new state
            updateIcons(isDark);
            
            // Save preference to local storage for global access
            localStorage.setItem(DARK_MODE_KEY, isDark ? 'enabled' : 'disabled');
        }

        /**
         * Ina-update ang icon para sa moon/sun.
         * @param {boolean} isDark - True kung Dark Mode.
         */
        function updateIcons(isDark) {
            // Updated to only target the two external icons
            const icons = [document.getElementById('top-dark-mode-icon'), document.getElementById('bottom-dark-mode-icon')];
            
            icons.forEach(icon => {
                if (!icon) return; // check kung existing
                
                // Clear previous classes
                icon.classList.remove('fa-sun', 'text-yellow-400', 'fa-moon', 'text-blue-400');
                
                if (isDark) {
                    // Dark Mode is ON: Show SUN icon
                    icon.classList.add('fa-sun', 'text-yellow-400');
                } else {
                    // Dark Mode is OFF: Show MOON icon
                    icon.classList.add('fa-moon', 'text-blue-400');
                }
            });
        }
        
        /**
         * Toggle event handler.
         */
        function toggleDarkMode() {
            const currentMode = localStorage.getItem(DARK_MODE_KEY) === 'enabled';
            applyDarkMode(!currentMode);
        }

        // --- Initialization ---
        document.addEventListener('DOMContentLoaded', () => {
            // Check saved preference on load
            const savedMode = localStorage.getItem(DARK_MODE_KEY);
            const isDark = savedMode === 'enabled';
            
            // Apply initial state
            applyDarkMode(isDark);
            
            // Attach listeners to all external toggle buttons
            document.getElementById('top-dark-mode-icon-toggle').addEventListener('click', toggleDarkMode);
            document.getElementById('bottom-dark-mode-icon-toggle').addEventListener('click', toggleDarkMode);
        });


        // --- Modal/Message Box Functions (Replaces alert/confirm) ---
        function showMessage(title, content, isConfirm = false, onConfirm = null) {
            const box = document.getElementById('message-box');
            document.getElementById('modal-title').textContent = title;
            document.getElementById('modal-content').textContent = content;
            const confirmBtn = document.getElementById('modal-cancel');
            
            if (isConfirm) {
                confirmBtn.classList.remove('hidden');
                confirmBtn.onclick = closeModal; // Default cancel behavior
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


        // --- Profile Image Preview ---
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('profileImage');
                const icon = document.getElementById('avatarIcon');
                output.src = reader.result;
                output.classList.remove('hidden');
                icon.classList.add('hidden');
            }
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        // --- Form Handlers ---
        function handleSave(event) {
            event.preventDefault(); // Prevents actual form submission
            
            // Basic validation check for new password consistency
            const currentPass = document.getElementById('current-password').value;
            const newPass = document.getElementById('new-password').value;
            const confirmPass = document.getElementById('confirm-new-password').value;
            
            if (newPass || confirmPass) {
                if (!currentPass) {
                    showMessage("Error", "Kailangan ang Current Password kapag nagpapalit ng New Password.");
                    return;
                }
                if (newPass !== confirmPass) {
                    showMessage("Error", "Ang Bagong Password at Kumpirmasyon ay hindi tugma. Pakisubukang muli.");
                    return;
                }
            }

            // DITO MO GAGAWIN ANG ACTUAL API/PHP POST REQUEST
            
            // Simulate successful save
            showMessage("Success!", `Ang iyong settings ay matagumpay na na-save!`, false);
        }

        function handleDelete() {
            // Confirmation before deletion
            showMessage(
                "Confirm Deletion", 
                "Sigurado ka bang gusto mong tanggalin nang permanente ang iyong account? Hindi na ito mababawi.",
                true,
                () => {
                    // Dito mo ilalagay ang DELETE API call.
                    showMessage("Account Deleted", "Ang iyong account ay na-delete na. Salamat!", false);
                    // Redirect to homepage/logout page
                    // window.location.href = '/logout';
                }
            );
        }
    </script>
</body>
</html>
