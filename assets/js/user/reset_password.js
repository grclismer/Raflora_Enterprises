function togglePasswordVisibility(input, icon) {
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}

function validatePasswords() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    if (!password || !confirmPassword) return true;
    
    let isValid = true;
    
    // Clear previous validation messages
    clearValidationMessages();
    
    // Check password length
    if (password.value.length > 0 && password.value.length < 6) {
        showValidationMessage(password, 'Password must be at least 6 characters', 'error');
        isValid = false;
    }
    
    // Check if passwords match
    if (password.value && confirmPassword.value && password.value !== confirmPassword.value) {
        showValidationMessage(confirmPassword, 'Passwords do not match', 'error');
        isValid = false;
    }
    
    // Show success if passwords match and meet requirements
    if (password.value && confirmPassword.value && password.value === confirmPassword.value && password.value.length >= 6) {
        showValidationMessage(confirmPassword, 'Passwords match!', 'success');
    }
    
    return isValid;
}

function showValidationMessage(input, message, type) {
    // Remove existing validation message
    const existingMessage = input.parentNode.querySelector('.validation-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Create new validation message
    const validationMessage = document.createElement('div');
    validationMessage.className = `validation-message ${type}`;
    validationMessage.textContent = message;
    input.parentNode.appendChild(validationMessage);
}

function clearValidationMessages() {
    const messages = document.querySelectorAll('.validation-message');
    messages.forEach(message => message.remove());
}

function showMessage(message, type) {
    // Create or update message display
    let messageDiv = document.querySelector('.welcome-message');
    if (!messageDiv) {
        messageDiv = document.createElement('div');
        messageDiv.className = 'welcome-message';
        const form = document.querySelector('form');
        form.insertBefore(messageDiv, form.firstChild);
    }
    
    messageDiv.textContent = message;
    messageDiv.className = `welcome-message ${type}`;
}


// Password visibility toggle
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const showPasswordCheckbox = document.getElementById('showPasswordCheckbox');
    
    // Toggle password visibility with eye icons (if they exist)
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            togglePasswordVisibility(passwordInput, togglePassword);
        });
    }
    
    if (toggleConfirmPassword && confirmPasswordInput) {
        toggleConfirmPassword.addEventListener('click', function() {
            togglePasswordVisibility(confirmPasswordInput, toggleConfirmPassword);
        });
    }
    
    // Toggle password visibility with checkbox
    if (showPasswordCheckbox) {
        showPasswordCheckbox.addEventListener('change', function() {
            const showPassword = this.checked;
            
            // Toggle both password fields
            if (passwordInput) {
                passwordInput.type = showPassword ? 'text' : 'password';
            }
            if (confirmPasswordInput) {
                confirmPasswordInput.type = showPassword ? 'text' : 'password';
            }
            
            // Also update eye icons if they exist
            if (togglePassword) {
                togglePassword.classList.toggle('fa-eye-slash', !showPassword);
                togglePassword.classList.toggle('fa-eye', showPassword);
            }
            if (toggleConfirmPassword) {
                toggleConfirmPassword.classList.toggle('fa-eye-slash', !showPassword);
                toggleConfirmPassword.classList.toggle('fa-eye', showPassword);
            }
        });
    }
    
    // Password strength check AND validation
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
            validatePasswords(); // Also validate when password changes
        });
    }
    
    // Real-time password validation
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', validatePasswords);
    }
    
    // Form submission enhancement
    const form = document.getElementById('resetPasswordForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validatePasswords()) {
                e.preventDefault();
                showMessage('Please fix the password errors before submitting.', 'error');
            }
        });
    }
});

function togglePasswordVisibility(input, icon) {
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
    
    // Also update the checkbox to stay in sync
    const showPasswordCheckbox = document.getElementById('showPasswordCheckbox');
    if (showPasswordCheckbox) {
        showPasswordCheckbox.checked = (input.type === 'text');
    }
}

// Calculate password strength (returns 0-4)
function calculatePasswordStrength(password) {
    let strength = 0;
    
    // Length check
    if (password.length >= 6) strength += 1;
    
    // Contains numbers
    if (/\d/.test(password)) strength += 1;
    
    // Contains lowercase and uppercase
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 1;
    
    // Contains special characters
    if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 1;
    
    return strength;
}

function checkPasswordStrength(password) {
    const passwordStrength = document.querySelector('.password-strength');
    const strength = calculatePasswordStrength(password);
    
    // Show/hide strength indicator
    if (passwordStrength) {
        if (password.length > 0) {
            passwordStrength.classList.add('show');
        } else {
            passwordStrength.classList.remove('show');
        }
    }
    
    // Update strength indicator
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');
    
    if (strengthFill && strengthText) {
        switch(strength) {
            case 0:
                strengthFill.style.width = '5%';
                strengthFill.style.background = '#dc3545';
                strengthText.textContent = 'Very Weak';
                strengthText.style.color = '#dc3545';
                break;
            case 1:
                strengthFill.style.width = '25%';
                strengthFill.style.background = '#dc3545';
                strengthText.textContent = 'Weak';
                strengthText.style.color = '#dc3545';
                break;
            case 2:
                strengthFill.style.width = '50%';
                strengthFill.style.background = '#ffc107';
                strengthText.textContent = 'Fair';
                strengthText.style.color = '#ffc107';
                break;
            case 3:
                strengthFill.style.width = '75%';
                strengthFill.style.background = '#28a745';
                strengthText.textContent = 'Good';
                strengthText.style.color = '#28a745';
                break;
            case 4:
                strengthFill.style.width = '100%';
                strengthFill.style.background = '#28a745';
                strengthText.textContent = 'Strong';
                strengthText.style.color = '#28a745';
                break;
        }
    }
    
    return strength;
}

function validatePasswords() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    if (!password || !confirmPassword) return true;
    
    let isValid = true;
    
    // Clear previous validation messages
    clearValidationMessages();
    
    // Check password length
    if (password.value.length > 0 && password.value.length < 6) {
        showValidationMessage(password, 'Password must be at least 6 characters', 'error');
        isValid = false;
    }
    
    // NEW: Check password strength - BLOCK WEAK PASSWORDS
    if (password.value.length >= 6) {
        const strength = calculatePasswordStrength(password.value);
        
        // Block passwords that are "Very Weak" or "Weak"
        if (strength <= 1) {
            showValidationMessage(password, 'Password is too weak. Include numbers and mixed case letters.', 'error');
            isValid = false;
        }
        // Warn for "Fair" passwords but still allow
        else if (strength === 2) {
            showValidationMessage(password, 'Password is fair. Consider adding special characters for better security.', 'warning');
        }
    }
    
    // Check if passwords match
    if (password.value && confirmPassword.value && password.value !== confirmPassword.value) {
        showValidationMessage(confirmPassword, 'Passwords do not match', 'error');
        isValid = false;
    }
    
    // Show success if passwords match and meet requirements
    if (password.value && confirmPassword.value && password.value === confirmPassword.value && password.value.length >= 6) {
        const strength = calculatePasswordStrength(password.value);
        if (strength >= 3) {
            showValidationMessage(confirmPassword, 'Passwords match and are secure!', 'success');
        } else {
            showValidationMessage(confirmPassword, 'Passwords match', 'success');
        }
    }
    
    return isValid;
}

function showValidationMessage(input, message, type) {
    // Remove existing validation message
    const existingMessage = input.parentNode.querySelector('.validation-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Create new validation message
    const validationMessage = document.createElement('div');
    validationMessage.className = `validation-message ${type}`;
    validationMessage.textContent = message;
    input.parentNode.appendChild(validationMessage);
}

function clearValidationMessages() {
    const messages = document.querySelectorAll('.validation-message');
    messages.forEach(message => message.remove());
}

function showMessage(message, type) {
    // Create or update message display
    let messageDiv = document.querySelector('.welcome-message');
    if (!messageDiv) {
        messageDiv = document.createElement('div');
        messageDiv.className = 'welcome-message';
        const form = document.querySelector('form');
        form.insertBefore(messageDiv, form.firstChild);
    }
    
    messageDiv.textContent = message;
    messageDiv.className = `welcome-message ${type}`;
}