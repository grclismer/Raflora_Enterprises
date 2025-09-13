document.addEventListener('DOMContentLoaded', function() {
    const loginFormContainer = document.getElementById('loginFormContainer');
    const registerFormContainer = document.getElementById('registerFormContainer');
    const forgotPasswordContainer = document.getElementById('forgotPasswordContainer');

    const showRegisterLink = document.getElementById('showRegister');
    const showLoginFromRegisterLink = document.getElementById('showLoginFromRegister');
    const showForgotPasswordLink = document.getElementById('showForgotPassword');
    const showLoginFromForgotLink = document.getElementById('showLoginFromForgot');

    function showForm(formToShow) {
        loginFormContainer.classList.add('hidden');
        registerFormContainer.classList.add('hidden');
        forgotPasswordContainer.classList.add('hidden');
        formToShow.classList.remove('hidden');
    }

    if (showRegisterLink) {
        showRegisterLink.addEventListener('click', function(event) {
            event.preventDefault();
            showForm(registerFormContainer);
        });
    }

    if (showLoginFromRegisterLink) {
        showLoginFromRegisterLink.addEventListener('click', function(event) {
            event.preventDefault();
            showForm(loginFormContainer);
        });
    }

    if (showForgotPasswordLink) {
        showForgotPasswordLink.addEventListener('click', function(event) {
            event.preventDefault();
            showForm(forgotPasswordContainer);
        });
    }

    if (showLoginFromForgotLink) {
        showLoginFromForgotLink.addEventListener('click', function(event) {
            event.preventDefault();
            showForm(loginFormContainer);
        });
    }

    // --- Login Form Functionality ---
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const loginForm = document.getElementById('loginForm');
    const welcomeMessageLoginDiv = document.getElementById('welcomeMessageLogin');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function(e) {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();
            welcomeMessageLoginDiv.textContent = ''; // Clear previous message
            
            fetch('../api/login.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => {
                // Check if the response is a redirect or a JSON object
                if (response.redirected) {
                    window.location.href = response.url;
                }
                return response.json(); // Attempt to parse as JSON
            })
            .then(data => {
                if (data.status === 'success') {
                    // Redirect based on the URL provided by the server
                    welcomeMessageLoginDiv.textContent = 'Login successful!';
                    welcomeMessageLoginDiv.style.color = 'green';
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1000);
                } else {
                    // Display the error message from the server
                    welcomeMessageLoginDiv.textContent = data.message;
                    welcomeMessageLoginDiv.style.color = 'red';
                    welcomeMessageLoginDiv.classList.add('show');
                    document.getElementById('password').value = '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                welcomeMessageLoginDiv.textContent = 'An error occurred. Please try again.';
                welcomeMessageLoginDiv.style.color = 'red';
                welcomeMessageLoginDiv.classList.add('show');
            });
        });
    }

    // --- Registration Form Functionality ---
    const regPasswordInput = document.getElementById('reg-password');
    const regConfirmPasswordInput = document.getElementById('reg-confirm-password');
    const regTogglePassword = document.getElementById('reg-togglePassword');
    const regToggleConfirmPassword = document.getElementById('reg-toggleConfirmPassword');
    const registrationForm = document.getElementById('registrationForm');
    const welcomeMessageRegDiv = document.getElementById('welcomeMessageRegister');

    // This is the password strength check logic for the REGISTRATION form only
    if (regPasswordInput) {
        regPasswordInput.addEventListener('keyup', function() {
            const password = this.value;
            let strength = 0;
            let feedback = '';
            let className = '';
            if (password.length > 0) {
                if (password.length >= 8) { strength += 1; }
                if (/[A-Z]/.test(password)) { strength += 1; }
                if (/[a-z]/.test(password)) { strength += 1; }
                if (/[0-9]/.test(password)) { strength += 1; }
                if (/[^A-Za-z0-9]/.test(password)) { strength += 1; }
                if (strength <= 2) { feedback = 'Weak'; className = 'weak'; }
                else if (strength <= 4) { feedback = 'Medium'; className = 'medium'; }
                else { feedback = 'Strong!'; className = 'strong'; }
            }
            // Temporarily removed feedbackDivReg reference as it's not in the HTML
            // feedbackDivReg.textContent = feedback;
            // feedbackDivReg.className = 'password-feedback ' + className;
        });
    }

    if (regTogglePassword && regPasswordInput) {
        regTogglePassword.addEventListener('click', function() {
            const type = regPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            regPasswordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    if (regToggleConfirmPassword && regConfirmPasswordInput) {
        regToggleConfirmPassword.addEventListener('click', function() {
            const type = regConfirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            regConfirmPasswordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    if (registrationForm) {
        registrationForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const password = regPasswordInput.value;
            const confirmPassword = regConfirmPasswordInput.value;

            if (password !== confirmPassword) {
                welcomeMessageRegDiv.textContent = 'Error: Passwords do not match!';
                welcomeMessageRegDiv.style.color = 'red';
                welcomeMessageRegDiv.classList.add('show');
                return;
            }

            // Temporarily removed feedbackDivReg reference as it's not in the HTML
            // if (feedbackDivReg && feedbackDivReg.textContent === 'Weak') {
            //     welcomeMessageRegDiv.textContent = 'Error: Password is too weak. Please choose a stronger password.';
            //     welcomeMessageRegDiv.style.color = 'red';
            //     welcomeMessageRegDiv.classList.add('show');
            //     return;
            // }

            fetch('../api/register.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.text())
            .then(data => {
                welcomeMessageRegDiv.textContent = data;
                welcomeMessageRegDiv.classList.add('show');
                
                if (data.includes('successful')) {
                    welcomeMessageRegDiv.style.color = 'green';
                    Array.from(registrationForm.children).forEach(child => {
                        if (child.id !== 'welcomeMessageRegister') {
                            child.style.display = 'none';
                        }
                    });
                    setTimeout(() => {
                        showForm(loginFormContainer);
                        welcomeMessageLoginDiv.textContent = "You can now log in with your new account.";
                        welcomeMessageLoginDiv.style.color = 'green';
                        welcomeMessageLoginDiv.classList.add('show');
                        welcomeMessageRegDiv.textContent = "";
                    }, 2000); 
                } else {
                    welcomeMessageRegDiv.style.color = 'red';
                    regPasswordInput.value = '';
                    regConfirmPasswordInput.value = '';
                    Array.from(registrationForm.children).forEach(child => {
                        child.style.display = '';
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                welcomeMessageRegDiv.textContent = 'An error occurred. Please try again.';
                welcomeMessageRegDiv.style.color = 'red';
                welcomeMessageRegDiv.classList.add('show');
            });
        });
    }

    // --- Forgot Password Form Functionality ---
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    const forgotPasswordMessageDiv = document.getElementById('forgotPasswordMessage');
    const forgotEmailInput = document.getElementById('forgot-email');

    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const email = forgotEmailInput.value;
            if (email.trim() !== '') {
                // Use a custom modal instead of the alert() function
                showCustomAlert('A password reset link has been sent to ' + email + '.');
                Array.from(forgotPasswordForm.children).forEach(child => {
                    if (child.id !== 'forgotPasswordMessage' && child.tagName !== 'SPAN') {
                        child.style.display = 'none';
                    }
                });
                forgotPasswordMessageDiv.textContent = `A password reset link has been sent to ${email}.`;
                forgotPasswordMessageDiv.style.color = 'green';
                forgotPasswordMessageDiv.classList.add('show');
                forgotPasswordForm.querySelector('h1').style.display = 'none';
                forgotPasswordForm.style.height = 'auto';
                forgotPasswordForm.style.paddingBottom = '50px';
            } else {
                showCustomAlert('Please enter your email address.');
            }
        });
    }

    // Custom alert function to replace window.alert()
    function showCustomAlert(message) {
        // Create an overlay and a modal
        const overlay = document.createElement('div');
        overlay.style.position = 'fixed';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        overlay.style.zIndex = '1000';
        overlay.style.display = 'flex';
        overlay.style.justifyContent = 'center';
        overlay.style.alignItems = 'center';

        const modal = document.createElement('div');
        modal.style.backgroundColor = '#fff';
        modal.style.padding = '20px';
        modal.style.borderRadius = '8px';
        modal.style.textAlign = 'center';
        modal.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
        modal.innerHTML = `
            <p>${message}</p>
            <button onclick="document.body.removeChild(this.parentNode.parentNode)">OK</button>
        `;
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
    }
});
