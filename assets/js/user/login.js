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
        loginForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            welcomeMessageLoginDiv.textContent = ''; // Clear previous message
            welcomeMessageLoginDiv.style.color = 'red';
            
            try {
                const response = await fetch('/raflora_enterprises/api/login.php', {
                    method: 'POST',
                    body: new FormData(this)
                });
                
                // Get the response as text first
                const responseText = await response.text();
                
                let data;
                try {
                    // Try to parse as JSON
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    throw new Error('Server returned invalid response. Please try again.');
                }
                
                if (data.status === 'success') {
                    welcomeMessageLoginDiv.textContent = 'Login successful! Redirecting...';
                    welcomeMessageLoginDiv.style.color = 'green';
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1000);
                } else {
                    welcomeMessageLoginDiv.textContent = data.message || 'Login failed. Please try again.';
                    welcomeMessageLoginDiv.style.color = 'red';
                    document.getElementById('password').value = '';
                }
            } catch (error) {
                welcomeMessageLoginDiv.textContent = error.message || 'An error occurred. Please try again.';
                welcomeMessageLoginDiv.style.color = 'red';
            }
        });
    }

    // --- Registration Form Functionality ---
    const regPasswordInput = document.getElementById('reg-password');
    const regConfirmPasswordInput = document.getElementById('reg-confirm-password');
    const regTogglePassword = document.getElementById('reg-togglePassword');
    const regToggleConfirmPassword = document.getElementById('reg-toggleConfirmPassword');
    const registrationForm = document.getElementById('registrationForm');
    const welcomeMessageRegDiv = document.getElementById('welcomeMessageRegister');

    if (regPasswordInput) {
        regPasswordInput.addEventListener('keyup', function() {
            const password = this.value;
            let strength = 0;
            if (password.length > 0) {
                if (password.length >= 8) { strength += 1; }
                if (/[A-Z]/.test(password)) { strength += 1; }
                if (/[a-z]/.test(password)) { strength += 1; }
                if (/[0-9]/.test(password)) { strength += 1; }
                if (/[^A-Za-z0-9]/.test(password)) { strength += 1; }
            }
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

            // Updated path for registration
            fetch('/raflora_enterprises/api/register.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => {
                return response.json(); 
            })
            .then(data => {
                if (data.status === 'success') {
                    welcomeMessageRegDiv.textContent = data.message;
                    welcomeMessageRegDiv.style.color = 'green';
                    welcomeMessageRegDiv.classList.add('show');
                    
                    // Hide the form fields on success
                    Array.from(registrationForm.querySelectorAll('.input-box, .btn, .register-link')).forEach(child => {
                         child.style.display = 'none';
                    });
                    
                    setTimeout(() => {
                        showForm(loginFormContainer);
                        welcomeMessageLoginDiv.textContent = data.message;
                        welcomeMessageLoginDiv.style.color = 'green';
                        welcomeMessageLoginDiv.classList.add('show');
                        welcomeMessageRegDiv.textContent = "";
                    }, 2000); 
                } else {
                    welcomeMessageRegDiv.textContent = data.message;
                    welcomeMessageRegDiv.style.color = 'red';
                    welcomeMessageRegDiv.classList.add('show');
                    regPasswordInput.value = '';
                    regConfirmPasswordInput.value = '';
                    
                    // Show the form fields again on error
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
                forgotPasswordMessageDiv.textContent = 'Please enter your email address.';
                forgotPasswordMessageDiv.style.color = 'red';
                forgotPasswordMessageDiv.classList.add('show');
            }
        });
    }
});

// QR Code Scanner Functions
let qrScanner = null;

console.log("🔄 QR Scanner functions loaded");
console.log("📚 jsQR available:", typeof jsQR !== 'undefined');

function openQRScanner() {
    console.log("🎬 Opening QR scanner...");
    const modal = document.getElementById('qrScannerModal');
    if (!modal) {
        console.error("❌ QR scanner modal not found!");
        return;
    }
    modal.classList.remove('hidden');
    startQRScanner();
}

function closeQRScanner() {
    console.log("🛑 Closing QR scanner...");
    const modal = document.getElementById('qrScannerModal');
    modal.classList.add('hidden');
    stopQRScanner();
}

async function startQRScanner() {
    try {
        console.log("📷 Starting camera...");
        const stream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: "environment" } 
        });
        const video = document.getElementById('qrVideo');
        video.srcObject = stream;
        
        video.onloadedmetadata = function() {
            console.log("✅ Camera stream loaded");
        };
        
        console.log("🔍 Starting QR scan loop...");
        qrScanner = setInterval(scanQRFrame, 500); // Scan every 500ms
        
    } catch (err) {
        console.error('❌ Camera error:', err);
        showMessage('Cannot access camera. Please upload QR code image instead.', 'error');
    }
}

function stopQRScanner() {
    console.log("⏹️ Stopping scanner...");
    if (qrScanner) {
        clearInterval(qrScanner);
        qrScanner = null;
    }
    
    const video = document.getElementById('qrVideo');
    if (video.srcObject) {
        video.srcObject.getTracks().forEach(track => {
            console.log("🛑 Stopping camera track");
            track.stop();
        });
    }
}

function scanQRFrame() {
    const video = document.getElementById('qrVideo');
    const canvas = document.getElementById('qrCanvas');
    
    if (!video || !canvas) {
        console.error("❌ Video or canvas element not found");
        return;
    }
    
    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        
        // Check if jsQR is available
        if (typeof jsQR === 'undefined') {
            console.error("❌ jsQR library not loaded!");
            stopQRScanner();
            return;
        }
        
        const code = jsQR(imageData.data, imageData.width, imageData.height);
        
        if (code) {
            console.log("✅ QR Code detected:", code.data);
            stopQRScanner(); // Stop scanning once we found a code
            handleScannedQR(code.data);
        }
    }
}

function handleQRFileUpload(event) {
    console.log("📁 File upload triggered");
    const file = event.target.files[0];
    if (!file) {
        console.log("❌ No file selected");
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        console.log("🖼️ File loaded, processing image...");
        const img = new Image();
        img.onload = function() {
            const canvas = document.getElementById('qrCanvas');
            const context = canvas.getContext('2d');
            canvas.width = img.width;
            canvas.height = img.height;
            context.drawImage(img, 0, 0);
            
            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            
            if (typeof jsQR === 'undefined') {
                console.error("❌ jsQR library not loaded!");
                showMessage('QR scanner library not loaded', 'error');
                return;
            }
            
            const code = jsQR(imageData.data, imageData.width, imageData.height);
            
            if (code) {
                console.log("✅ QR Code found in file:", code.data);
                handleScannedQR(code.data);
            } else {
                console.log("❌ No QR code found in image");
                showMessage('No QR code found in the image', 'error');
            }
        };
        img.onerror = function() {
            console.error("❌ Error loading image");
            showMessage('Error loading image', 'error');
        };
        img.src = e.target.result;
    };
    reader.onerror = function() {
        console.error("❌ Error reading file");
        showMessage('Error reading file', 'error');
    };
    reader.readAsDataURL(file);
}

async function handleScannedQR(qrData) {
    console.log("🔍 Handling scanned QR data:", qrData);
    
    try {
        const qrObject = JSON.parse(qrData);
        console.log("✅ Parsed QR object:", qrObject);
        
        if (qrObject.method === 'qr_login' && qrObject.system === 'raflora_enterprises') {
            console.log("🚀 Sending to verification API...");
            
            const apiUrl = '/Raflora_Enterprises/api/verify_qr_login.php';
            console.log("📡 API URL:", apiUrl);
            
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ qr_data: qrData })
            });
            
            console.log("📊 API Response status:", response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log("📨 API Response data:", result);
            
            if (result.status === 'success') {
                // Use safe message display
                safeShowMessage('Login successful! Redirecting...', 'success');
                console.log('✅ Login successful! Redirecting...');
                
                // Close scanner
                closeQRScanner();
                
                // Redirect based on role
                setTimeout(() => {
                    console.log("🔄 Redirecting to:", result.user.role);
                    if (result.user.role === 'admin_type') {
                        window.location.href = '/Raflora_Enterprises/admin_dashboard/inventory.php';
                    } else {
                        window.location.href = '/Raflora_Enterprises/user/landing.php';
                    }
                }, 1500);
                
            } else {
                console.log("❌ Login failed:", result.message);
                safeShowMessage('QR code login failed: ' + result.message, 'error');
            }
        } else {
            console.log("❌ Invalid QR format for this system");
            safeShowMessage('This QR code is not for Raflora Enterprises login', 'error');
        }
    } catch (error) {
        console.error('💥 Error processing QR code:', error);
        safeShowMessage('Error: ' + error.message, 'error');
    }
}

// Safe message display with fallback
function safeShowMessage(message, type = 'info') {
    // Try to use existing showMessage function
    if (typeof showMessage === 'function') {
        showMessage(message, type);
        return;
    }
    
    // Fallback: Use browser alert for critical messages
    console.log(`[${type.toUpperCase()}] ${message}`);
    
    // Try to use existing message containers in your form
    const messageContainers = [
        document.getElementById('welcomeMessageLogin'),
        document.getElementById('welcomeMessageRegister'),
        document.getElementById('forgotPasswordMessage')
    ];
    
    const validContainer = messageContainers.find(container => container !== null);
    
    if (validContainer) {
        validContainer.textContent = message;
        validContainer.style.display = 'block';
        validContainer.style.color = type === 'error' ? 'red' : 'green';
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            validContainer.style.display = 'none';
        }, 5000);
    } else {
        // Final fallback: alert
        if (type === 'error') {
            alert('Error: ' + message);
        } else if (type === 'success') {
            alert('Success: ' + message);
        }
    }
}

// Test function to simulate QR scan
function testQRScan() {
    console.log("🧪 Testing QR scan with manual data...");
    const testData = '{"user_id":5,"system":"raflora_enterprises","method":"qr_login"}';
    handleScannedQR(testData);
}


// Add this at the top of your QR scanner functions
console.log("🔍 Checking jsQR library:", typeof jsQR);