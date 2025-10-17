// View profile image in modal
function viewProfileImage() {
    console.log('viewProfileImage called - opening modal only');
    const profileImage = document.getElementById('profileImage');
    if (profileImage.src && !profileImage.classList.contains('hidden')) {
        const modal = document.getElementById('imageViewModal');
        const modalImage = document.getElementById('modalProfileImage');
        
        modalImage.src = profileImage.src;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    } else {
        console.log('No image found, opening file selector');
        document.getElementById('profile-upload').click();
    }
}

// Close image view modal (JUST CLOSES, NO SAVING)
function closeImageView() {
    console.log('closeImageView called - closing modal only, NO SAVING');
    const modal = document.getElementById('imageViewModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Close message modal
function closeMessageModal() {
    console.log('closeMessageModal called - closing message modal only');
    const box = document.getElementById('message-box');
    box.classList.add('hidden');
    box.classList.remove('flex');
}

// Close modal when clicking outside the image
document.addEventListener('click', function(event) {
    const modal = document.getElementById('imageViewModal');
    if (event.target === modal) {
        console.log('Clicked outside modal - closing only, NO SAVING');
        closeImageView();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        console.log('Escape key pressed - closing modal only, NO SAVING');
        closeImageView();
    }
});

// Toggle profile menu
function toggleProfileMenu() {
    const dropdown = document.getElementById('profileDropdown');
    dropdown.classList.toggle('hidden');
}

// Close profile menu when clicking elsewhere
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('profileDropdown');
    if (!event.target.closest('#profileMenu')) {
        dropdown.classList.add('hidden');
    }
});

// Preview image without uploading (ONLY PREVIEW, NO SAVING)
function previewImage(event) {
    console.log('previewImage called - preview only, NO SAVING');
    event.preventDefault();
    event.stopPropagation();
    
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
        const profileMenu = document.getElementById('profileMenu');
        
        // ONLY UPDATE PREVIEW, DON'T UPLOAD
        output.src = reader.result;
        output.classList.remove('hidden');
        icon.classList.add('hidden');
        profileMenu.classList.remove('hidden');
        console.log('Image preview updated - NOT SAVED TO SERVER');
    };
    reader.readAsDataURL(file);
    
    return false;
}

// Remove profile picture
function removeProfilePicture() {
    showMessage(
        "Remove Profile Photo", 
        "Are you sure you want to remove your profile photo?",
        true,
        async () => {
            try {
                const response = await fetch('/raflora_enterprises/api/remove_profile_picture.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    // Update UI
                    const output = document.getElementById('profileImage');
                    const icon = document.getElementById('avatarIcon');
                    const profileMenu = document.getElementById('profileMenu');
                    
                    output.classList.add('hidden');
                    icon.classList.remove('hidden');
                    profileMenu.classList.add('hidden');
                    
                    showMessage("Success", "Profile photo removed successfully");
                } else {
                    showMessage("Error", "Failed to remove profile photo: " + data.message);
                }
            } catch (error) {
                showMessage("Error", "Failed to remove profile photo: " + error.message);
            }
        }
    );
}

// Modal/Message Box Functions
function showMessage(title, content, isConfirm = false, onConfirm = null) {
    const box = document.getElementById('message-box');
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-content').textContent = content;
    const confirmBtn = document.getElementById('modal-cancel');
    
    if (isConfirm) {
        confirmBtn.classList.remove('hidden');
        confirmBtn.onclick = closeMessageModal;
        document.querySelector('#message-box button:not(#modal-cancel)').onclick = () => {
            if (onConfirm) onConfirm();
            closeMessageModal();
        };
    } else {
        confirmBtn.classList.add('hidden');
        document.querySelector('#message-box button').onclick = closeMessageModal;
    }

    box.classList.remove('hidden');
    box.classList.add('flex');
}

// Updated save function to handle image upload (ONLY SAVES WHEN CLICKED)
async function handleSave(event) {
    event.preventDefault();
    console.log('=== SAVE CHANGES CLICKED - THIS IS THE ONLY PLACE SAVING SHOULD HAPPEN ===');
    
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
    
    // Add profile picture if a new one was selected
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
                
                // Clear the file input after successful save
                profileUpload.value = '';
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

// Initialization
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


// QR Code functionality
async function generateQRCode() {
    try {
        const generateBtn = document.getElementById('generateQRBtn');
        const downloadBtn = document.getElementById('downloadQRBtn');
        const qrImage = document.getElementById('qrCodeImage');
        const qrPlaceholder = document.getElementById('qrCodePlaceholder');
        
        generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
        generateBtn.disabled = true;

        const response = await fetch('../api/generate_qr_login.php');
        const result = await response.json();

        if (result.status === 'success') {
            // Generate QR code image
            const qrCodeUrl = `../api/get_qr_code.php?t=${Date.now()}`;
            qrImage.src = qrCodeUrl;
            qrImage.classList.remove('hidden');
            qrPlaceholder.classList.add('hidden');
            downloadBtn.classList.remove('hidden');
            
            // Store session ID for download
            qrImage.dataset.sessionId = result.session_id;
            
            showMessage('QR code generated successfully!', 'success');
        } else {
            showMessage('Failed to generate QR code: ' + result.message, 'error');
        }
    } catch (error) {
        console.error('Error generating QR code:', error);
        showMessage('Error generating QR code', 'error');
    } finally {
        const generateBtn = document.getElementById('generateQRBtn');
        generateBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Generate New QR Code';
        generateBtn.disabled = false;
    }
}
// QR Code Download Function
function downloadQRCode() {
    const userId = document.getElementById('currentUserId').value;
    const qrImageUrl = `../api/get_user_qr.php?user_id=${userId}&download=1`;
    
    // Create temporary link for download
    const link = document.createElement('a');
    link.href = qrImageUrl;
    link.download = `raflora-login-qr-${userId}.png`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Show success message
    showMessage('QR code downloaded successfully!', 'success');
}
