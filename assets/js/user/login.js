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

            fetch('../api/login.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.text())
            .then(data => {
                welcomeMessageLoginDiv.textContent = data;
                welcomeMessageLoginDiv.classList.add('show');
                if (data.includes('successful')) {
                    welcomeMessageLoginDiv.style.color = 'green';
                    Array.from(loginForm.children).forEach(child => {
                        if (child.id !== 'welcomeMessageLogin' && child.tagName !== 'SPAN') {
                            child.style.display = 'none';
                        }
                    });
                    loginForm.querySelector('h1').style.display = 'none';
                    loginForm.style.height = 'auto';
                    loginForm.style.paddingBottom = '50px';
                } else {
                    welcomeMessageLoginDiv.style.color = 'red';
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
    const feedbackDivReg = document.getElementById('password-strength-feedback-reg');
    const registrationForm = document.getElementById('registrationForm');
    const welcomeMessageRegDiv = document.getElementById('welcomeMessageRegister');

    // This is the password strength check logic for the REGISTRATION form only
    if (regPasswordInput && feedbackDivReg) {
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
            feedbackDivReg.textContent = feedback;
            feedbackDivReg.className = 'password-feedback ' + className;
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

            // This is the new check that prevents registration with a weak password
            if (feedbackDivReg && feedbackDivReg.textContent === 'Weak') {
                welcomeMessageRegDiv.textContent = 'Error: Password is too weak. Please choose a stronger password.';
                welcomeMessageRegDiv.style.color = 'red';
                welcomeMessageRegDiv.classList.add('show');
                return;
            }

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
                alert('Please enter your email address.');
            }
        });
    }
});