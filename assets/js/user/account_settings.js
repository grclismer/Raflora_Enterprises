// Profile Picture Functions
function toggleProfileMenu() {
    const dropdown = document.getElementById('profileDropdown');
    dropdown.classList.toggle('hidden');
}

function previewImage(event) {
    const input = event.target;
    const profileImage = document.getElementById('profileImage');
    const avatarIcon = document.getElementById('avatarIcon');
    const profileMenu = document.getElementById('profileMenu');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            profileImage.src = e.target.result;
            profileImage.classList.remove('hidden');
            avatarIcon.classList.add('hidden');
            profileMenu.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function removeProfilePicture() {
    const profileImage = document.getElementById('profileImage');
    const avatarIcon = document.getElementById('avatarIcon');
    const profileMenu = document.getElementById('profileMenu');
    const profileUpload = document.getElementById('profile-upload');
    
    profileImage.src = '';
    profileImage.classList.add('hidden');
    avatarIcon.classList.remove('hidden');
    profileMenu.classList.add('hidden');
    profileUpload.value = '';
    
    toggleProfileMenu();
}

function viewProfileImage() {
    const profileImage = document.getElementById('profileImage');
    const modal = document.getElementById('imageViewModal');
    const modalImage = document.getElementById('modalProfileImage');
    
    if (profileImage.src && !profileImage.classList.contains('hidden')) {
        modalImage.src = profileImage.src;
        modal.classList.remove('hidden');
    }
}

function closeImageView() {
    const modal = document.getElementById('imageViewModal');
    modal.classList.add('hidden');
}

// Message Modal Functions
function showMessage(title, content, showCancel = false) {
    const modal = document.getElementById('message-box');
    const modalTitle = document.getElementById('modal-title');
    const modalContent = document.getElementById('modal-content');
    const cancelBtn = document.getElementById('modal-cancel');
    
    modalTitle.textContent = title;
    modalContent.textContent = content;
    
    if (showCancel) {
        cancelBtn.classList.remove('hidden');
    } else {
        cancelBtn.classList.add('hidden');
    }
    
    modal.classList.remove('hidden');
}

function closeMessageModal() {
    const modal = document.getElementById('message-box');
    modal.classList.add('hidden');
}

// QR Code Functions
function downloadQRCode() {
    const qrImage = document.getElementById('qrCodeImage');
    const link = document.createElement('a');
    link.href = qrImage.src;
    link.download = 'my-login-qr-code.png';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Delete Account Function
function handleDelete() {
    showMessage(
        'Delete Account', 
        'Are you sure you want to permanently delete your account? This action cannot be undone.',
        true
    );
    
    document.getElementById('modal-cancel').onclick = closeMessageModal;
    
    const confirmBtn = document.querySelector('.confirm-btn');
    confirmBtn.onclick = function() {
        showMessage('Account Deleted', 'Your account has been permanently deleted.');
    };
}

// Form validation (just for user feedback, doesn't prevent submission)
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('settingsForm');
    
    // Client-side validation for better UX
    form.addEventListener('submit', function(event) {
        const currentPassword = document.getElementById('current-password').value;
        const newPassword = document.getElementById('new-password').value;
        const confirmPassword = document.getElementById('confirm-new-password').value;
        
        // Check if any password field is filled
        if (currentPassword || newPassword || confirmPassword) {
            // If any password field is filled, all must be filled
            if (!currentPassword || !newPassword || !confirmPassword) {
                event.preventDefault();
                showMessage('Error', 'Please fill all password fields if you want to change your password.');
                return;
            }
            
            // Check if new passwords match
            if (newPassword !== confirmPassword) {
                event.preventDefault();
                showMessage('Error', 'New passwords do not match.');
                return;
            }
            
            // Check password strength
            if (newPassword.length < 6) {
                event.preventDefault();
                showMessage('Error', 'New password must be at least 6 characters long.');
                return;
            }
        }
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('profileDropdown');
        const profileContainer = document.querySelector('.profile-container');
        
        if (!profileContainer.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.display = 'none';
        }, 5000);
    });
});

// Phone number formatting
document.getElementById('reg-phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.length > 0) {
        if (value.length <= 3) {
            value = value;
        } else if (value.length <= 6) {
            value = value.slice(0, 3) + '-' + value.slice(3);
        } else {
            value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 10);
        }
    }
    
    e.target.value = value;
});