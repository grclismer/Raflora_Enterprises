/**
 * Booking Page Functionality (Vanilla JS Refactor)
 * Handles: Theme/Package logic, Phone number validation, File Upload feedback, and Custom Payment Modal.
 */

document.addEventListener('DOMContentLoaded', function () {
    // --- Booking Form Elements ---
    const eventSelect = document.getElementById('theme');
    const packagesSelect = document.getElementById('packages');
    const packagesField = document.getElementById('packages-field');
    const mobileNumberInput = document.getElementById('mobile_number');
    const designDocumentInput = document.getElementById('design_document_upload');
    const fileUploadBox = document.getElementById('fileUploadBox');
    
    // --- Custom Modal Elements (Look for these IDs in your HTML) ---
    const modal = document.getElementById('paymentModal');
    const submitReferenceBtn = document.getElementById('submitReferenceBtn');
    const referenceCodeInput = document.getElementById('referenceCode');
    const orderIdElement = document.getElementById('modal-order-id');

    // =======================================================
    // 1. Phone Number Input Logic
    // =======================================================
    if (mobileNumberInput) {
        mobileNumberInput.addEventListener('keypress', function(e) {
            // Get the character code of the key pressed
            const charCode = (e.which) ? e.which : e.keyCode;

            // Allow numbers (48-57) and control keys (like backspace, delete, etc.)
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                // Prevent the key from being typed if it's not a number
                e.preventDefault();
            }
        });
    }


    // =======================================================
    // 2. Theme/Package Selection Logic
    // =======================================================
    
    // Initial setup for packages
    if (packagesField && packagesSelect) {
        packagesField.classList.add('hidden');
        packagesSelect.querySelectorAll('option').forEach(option => {
            option.style.display = 'none';
        });
        // Ensure the default "Select Packages" option is always visible
        const defaultPackageOption = packagesSelect.querySelector('option[value=""]');
        if(defaultPackageOption) defaultPackageOption.style.display = 'block';

        if (eventSelect) {
            eventSelect.addEventListener('change', function () {
                const selectedEvent = this.value;
                // Reset the package selection
                packagesSelect.value = '';

                // Hide all options first
                packagesSelect.querySelectorAll('option').forEach(option => {
                    option.style.display = 'none';
                });

                if (selectedEvent) {
                    packagesField.classList.remove('hidden');
                    if(defaultPackageOption) defaultPackageOption.style.display = 'block';
                    
                    const selectedGroup = packagesSelect.querySelector(`optgroup[data-event="${selectedEvent}"]`);
                    if (selectedGroup) {
                        // Show only the options within the selected optgroup
                        selectedGroup.querySelectorAll('option').forEach(option => {
                            option.style.display = 'block';
                        });
                    }
                } else {
                    packagesField.classList.add('hidden');
                }
            });
        }
    }


    // =======================================================
    // 3. File Upload Visual Feedback
    // =======================================================
    function handleFileUploadChange() {
        if (designDocumentInput && fileUploadBox) {
            if (designDocumentInput.files.length > 0) {
                fileUploadBox.classList.remove('error');
                fileUploadBox.classList.add('uploaded');
            } else {
                fileUploadBox.classList.remove('uploaded');
            }
        }
    }
    
    designDocumentInput?.addEventListener('change', handleFileUploadChange);
    handleFileUploadChange(); 


    // =======================================================
    // 4. Custom Modal Logic (Replaces Bootstrap JS)
    // =======================================================
    if (modal) {
        // Functions to open and close the modal
        const openModal = () => {
            modal.classList.add('is-visible');
            document.body.style.overflow = 'hidden'; // Prevent scrolling the body
        };

        const closeModal = () => {
            modal.classList.remove('is-visible');
            document.body.style.overflow = ''; // Restore body scrolling
        };
        
        // Find the button that opens the modal 
        // IMPORTANT: The button to open the modal MUST have the ID 'openPaymentModalBtn'
        const openButton = document.getElementById('openPaymentModalBtn'); 
        if (openButton) {
            openButton.addEventListener('click', (event) => {
                event.preventDefault(); 
                openModal();
            });
        }

        // Event listener for the close button inside the modal
        const closeButton = modal.querySelector('.modal-close-btn');
        if (closeButton) {
            closeButton.addEventListener('click', closeModal);
        }

        // Event listener to close the modal when clicking outside the content box
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        // Event listener to close the modal when the Escape key is pressed
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal.classList.contains('is-visible')) {
                closeModal();
            }
        });

        // =======================================================
        // 5. Reference Submission Logic (Refactored to Vanilla JS)
        // =======================================================
        
        if (submitReferenceBtn) {
            submitReferenceBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Get values
                const referenceCode = referenceCodeInput ? referenceCodeInput.value.trim() : '';
                const orderId = orderIdElement ? orderIdElement.textContent.trim() : null; // Use textContent for the span

                if (!orderId || orderId.startsWith('YOUR_PHP')) {
                    console.error("Order ID not found or is placeholder. Cannot submit reference code.");
                    // In a real application, display a user-friendly error message here.
                    return;
                }

                if (referenceCode === '') {
                    console.error('Validation Error: Please enter a Reference ID/Code.');
                    // In a real application, display a user-friendly error message here.
                    return;
                }

                // Create and submit the form dynamically
                const form = document.createElement('form');
                form.action = "../config/reference.php";
                form.method = "post";
                form.style.display = 'none'; // Keep the form hidden

                // Append Order ID input
                const orderIdInput = document.createElement('input');
                orderIdInput.type = 'hidden';
                orderIdInput.name = 'order_id';
                orderIdInput.value = orderId;
                form.appendChild(orderIdInput);

                // Append Reference Code input
                const referenceCodeHiddenInput = document.createElement('input');
                referenceCodeHiddenInput.type = 'hidden';
                referenceCodeHiddenInput.name = 'reference_code';
                referenceCodeHiddenInput.value = referenceCode;
                form.appendChild(referenceCodeHiddenInput);

                document.body.appendChild(form);
                form.submit();
            });
        }
    }
});


// CORRECT Payment method handling - FIXED VERSION
// Payment channel handling for modal
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodSelect = document.getElementById('payment-method-modal');
    const paymentChannelSection = document.getElementById('payment-channel-section');
    const paymentDetailsSelect = document.getElementById('payment-details-select');
    
    const paymentChannels = {
        'Online Bank': ['BDO Bank', 'BPI Bank', 'Metrobank', 'UnionBank', 'Landbank', 'Security Bank', 'Other'],
        'E-Wallet': ['GCash', 'PayMaya', 'Other']
    };
    
    if (paymentMethodSelect) {
        paymentMethodSelect.addEventListener('change', function() {
            const selectedMethod = this.value;
            
            // Reset payment channel
            paymentDetailsSelect.innerHTML = '<option value="">Select channel</option>';
            paymentChannelSection.style.display = 'none';
            paymentDetailsSelect.required = false;
            
            if (selectedMethod && paymentChannels[selectedMethod]) {
                paymentChannelSection.style.display = 'block';
                paymentDetailsSelect.required = true;
                
                // Populate channels
                paymentChannels[selectedMethod].forEach(channel => {
                    const option = document.createElement('option');
                    option.value = channel;
                    option.textContent = channel;
                    paymentDetailsSelect.appendChild(option);
                });
            }
        });
    }
});

// Payment channel handling for modal
document.addEventListener('DOMContentLoaded', function() {
    const paymentDetailsSelect = document.getElementById('payment-details-select');
    const customChannelInput = document.getElementById('custom-payment-channel');
    
    if (paymentDetailsSelect && customChannelInput) {
        paymentDetailsSelect.addEventListener('change', function() {
            const selectedValue = this.value;
            
            if (selectedValue === 'Other') {
                // Show and enable custom input
                customChannelInput.style.display = 'block';
                customChannelInput.disabled = false;
                customChannelInput.required = true;
                customChannelInput.placeholder = 'Enter payment channel name';
            } else {
                // Hide and disable custom input
                customChannelInput.style.display = 'none';
                customChannelInput.disabled = true;
                customChannelInput.required = false;
                customChannelInput.value = ''; // Clear value
            }
        });
        
        // Initialize on page load
        if (paymentDetailsSelect.value === 'Other') {
            customChannelInput.style.display = 'block';
            customChannelInput.disabled = false;
            customChannelInput.required = true;
        }
    }
});
