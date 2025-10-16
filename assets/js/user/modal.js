/**
 * Payment Modal Functionality - COMPLETE VERSION
 * Handles payment selection, validation, amount calculation, and payment guides
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Modal.js loaded - initializing all payment functionality');
    initializePaymentSelection();
    initializePaymentGuides();
    initializeRealTimeValidation();
    initializePaymentAmountCalculation();
});

// Payment channels data
const paymentChannels = {
    'Online Bank': ['BDO Bank', 'BPI Bank', 'Metrobank', 'UnionBank', 'Landbank', 'Security Bank'],
    'E-Wallet': ['GCash', 'PayMaya']
};

// Payment instructions data
const paymentMethods = {
    'BDO Bank': {
        steps: [
            'Log in to your BDO Online Banking',
            'Go to "Transfer Funds"',
            'Send {amount} to: BDO Account # 123-456-7890',
            'Account Name: RAFLORA ENTERPRISES',
            'Copy the reference number from the transaction'
        ]
    },
    'BPI Bank': {
        steps: [
            'Log in to BPI Online',
            'Select "Send Money"',
            'Send {amount} to: BPI Account # 9876-5432-10',
            'Account Name: RAFLORA ENTERPRISES', 
            'Save the transaction reference number'
        ]
    },
    'Metrobank': {
        steps: [
            'Access Metrobank Online',
            'Choose "Transfer to Other Banks"',
            'Send {amount} to: Metrobank Account # 0123-4567-89',
            'Account Name: RAFLORA ENTERPRISES',
            'Note down the reference code'
        ]
    },
    'GCash': {
        steps: [
            'Open your GCash app',
            'Tap "Send Money"',
            'Send {amount} to: 0917-123-4567',
            'Account Name: RAFLORA ENTERPRISES',
            'Copy the reference number from transaction details'
        ]
    },
    'PayMaya': {
        steps: [
            'Open your PayMaya app',
            'Tap "Send Money"', 
            'Send {amount} to: 0918-765-4321',
            'Account Name: RAFLORA ENTERPRISES',
            'Save the transaction reference'
        ]
    },
    'Other': {
        steps: [
            'Complete your payment using your preferred method',
            'Ensure you get a reference/transaction number',
            'Enter that reference code in the field above'
        ]
    }
};

// =======================================================
// 1. PAYMENT SELECTION FUNCTIONALITY
// =======================================================

function initializePaymentSelection() {
    console.log('Initializing payment selection...');
    
    // Initialize booking modal
    setupPaymentHandler('payment-method-modal', 'payment-details-select', 'custom-payment-channel', 'payment-channel-placeholder');
    
    // Initialize billing modal
    setupPaymentHandler('billing-payment-method', 'billing-payment-details', 'billing-custom-payment-channel', 'billing-channel-placeholder');
}

function setupPaymentHandler(methodId, detailsId, customId, placeholderId) {
    const methodSelect = document.getElementById(methodId);
    const detailsSelect = document.getElementById(detailsId);
    const customInput = document.getElementById(customId);
    const placeholder = document.getElementById(placeholderId);
    
    if (methodSelect && detailsSelect && customInput && placeholder) {
        console.log(`Setting up payment handler for ${methodId}`);
        
        methodSelect.addEventListener('change', function() {
            const selectedMethod = this.value;
            console.log(`Payment method changed to: ${selectedMethod}`);
            
            // Reset everything first
            detailsSelect.style.display = 'none';
            customInput.style.display = 'none';
            placeholder.style.display = 'block';
            detailsSelect.innerHTML = '<option value="">Select channel</option>';
            customInput.value = '';
            
            if (selectedMethod && paymentChannels[selectedMethod]) {
                // Show payment channel dropdown
                detailsSelect.style.display = 'block';
                detailsSelect.required = true;
                placeholder.style.display = 'none';
                
                // Populate with channels
                paymentChannels[selectedMethod].forEach(channel => {
                    detailsSelect.innerHTML += `<option value="${channel}">${channel}</option>`;
                });
                
                // Add "Other" option
                detailsSelect.innerHTML += '<option value="Other">Other / Unlisted</option>';
                
                console.log(`Populated ${detailsId} with:`, paymentChannels[selectedMethod]);
            }
            
            // Update any visible tooltips
            updateAllTooltips();
        });
        
        // Handle channel selection change
        detailsSelect.addEventListener('change', function() {
            const selectedChannel = this.value;
            
            if (selectedChannel === 'Other') {
                // Show custom input for other channels
                customInput.style.display = 'block';
                customInput.required = true;
                detailsSelect.required = false;
            } else {
                // Hide custom input
                customInput.style.display = 'none';
                customInput.required = false;
                detailsSelect.required = true;
            }
            
            updateAllTooltips();
        });
        
        // Update tooltips when custom channel changes
        customInput.addEventListener('input', function() {
            updateAllTooltips();
        });
        
        // Trigger initial setup
        if (methodSelect.value) {
            methodSelect.dispatchEvent(new Event('change'));
        }
    }
}

// =======================================================
// 2. PAYMENT AMOUNT CALCULATION
// =======================================================

function initializePaymentAmountCalculation() {
    console.log('Initializing payment amount calculation...');
    
    // Initialize for booking modal
    const bookingRadios = document.querySelectorAll('.payment-type-radio');
    bookingRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            calculatePaymentAmount('total-package-price', 'amount-due-now', this.value);
        });
    });
    
    // Initialize for billing modal
    const billingRadios = document.querySelectorAll('.billing-payment-type-radio');
    billingRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            calculatePaymentAmount('billing-total-package-price', 'billing-amount-due-now', this.value);
        });
    });
    
    // Set initial amounts
    if (document.getElementById('total-package-price')) {
        calculatePaymentAmount('total-package-price', 'amount-due-now', 'Down Payment');
    }
    if (document.getElementById('billing-total-package-price')) {
        calculatePaymentAmount('billing-total-package-price', 'billing-amount-due-now', 'Down Payment');
    }
}

function calculatePaymentAmount(totalPriceId, amountDueId, paymentType) {
    const totalPriceElement = document.getElementById(totalPriceId);
    const amountDueElement = document.getElementById(amountDueId);
    
    if (!totalPriceElement || !amountDueElement) {
        console.log('Elements not found for calculation');
        return;
    }
    
    const totalPrice = parseFloat(totalPriceElement.value);
    
    let amountDue;
    if (paymentType === 'Full Payment') {
        amountDue = totalPrice; // 100%
    } else {
        amountDue = totalPrice * 0.50; // 50% down payment
    }
    
    // Format the amount with Philippine Peso symbol
    const formattedAmount = '₱' + amountDue.toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    amountDueElement.textContent = formattedAmount;
    
    console.log(`Payment calculation: ${paymentType}, Total: ${totalPrice}, Due: ${amountDue}`);
}

// =======================================================
// 3. PAYMENT GUIDE FUNCTIONALITY
// =======================================================

function initializePaymentGuides() {
    console.log('Initializing payment guides...');
    
    const guideIcons = document.querySelectorAll('.payment-guide-icon');
    console.log(`Found ${guideIcons.length} guide icons`);
    
    guideIcons.forEach((icon, index) => {
        console.log(`Setting up guide icon ${index + 1}`);
        
        // Find the tooltip - it should be the next sibling element
        let tooltip = icon.nextElementSibling;
        
        // If not found as immediate sibling, try to find by ID
        if (!tooltip || !tooltip.classList.contains('payment-guide-tooltip')) {
            const tooltipId = icon.id === 'paymentGuideIcon' ? 'paymentGuideTooltip' : 
                            icon.id === 'billingPaymentGuideIcon' ? 'billingPaymentGuideTooltip' : null;
            if (tooltipId) {
                tooltip = document.getElementById(tooltipId);
            }
        }
        
        if (!tooltip) {
            console.log('No tooltip found for icon', icon);
            return;
        }

        console.log('Tooltip found:', tooltip);

        let hoverTimer;
        let isClickOpen = false;

        // Hover show
        icon.addEventListener('mouseenter', function(e) {
            console.log('Mouse entered guide icon');
            clearTimeout(hoverTimer);
            hoverTimer = setTimeout(() => {
                if (!isClickOpen) {
                    updateTooltipContent(tooltip);
                    tooltip.classList.add('show');
                    console.log('Tooltip shown on hover');
                }
            }, 300);
        });

        // Hover hide
        icon.addEventListener('mouseleave', function(e) {
            console.log('Mouse left guide icon');
            clearTimeout(hoverTimer);
            if (!isClickOpen) {
                hoverTimer = setTimeout(() => {
                    tooltip.classList.remove('show');
                    console.log('Tooltip hidden on mouse leave');
                }, 300);
            }
        });

        // Click functionality
        icon.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Guide icon clicked');
            isClickOpen = !isClickOpen;
            updateTooltipContent(tooltip);
            
            if (isClickOpen) {
                tooltip.classList.add('show');
            } else {
                tooltip.classList.remove('show');
            }
            console.log('Tooltip toggled via click, isClickOpen:', isClickOpen);
        });

        // Close on click outside
        document.addEventListener('click', function(e) {
            if (!icon.contains(e.target) && !tooltip.contains(e.target)) {
                tooltip.classList.remove('show');
                isClickOpen = false;
                console.log('Tooltip hidden on outside click');
            }
        });

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && tooltip.classList.contains('show')) {
                tooltip.classList.remove('show');
                isClickOpen = false;
                console.log('Tooltip hidden on escape key');
            }
        });

        // Prevent tooltip from closing when clicking inside it
        tooltip.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    
    console.log('Payment guides initialization complete');
}

function updateAllTooltips() {
    console.log('Updating all tooltips...');
    const tooltips = document.querySelectorAll('.payment-guide-tooltip');
    tooltips.forEach(tooltip => {
        updateTooltipContent(tooltip);
    });
}

function updateTooltipContent(tooltip) {
    console.log('Updating tooltip content...');
    
    const guideChannel = tooltip.querySelector('#guide-channel, [id$="guide-channel"]');
    const instructions = tooltip.querySelector('#payment-instructions, [id$="payment-instructions"]');
    
    if (!guideChannel || !instructions) {
        console.log('Tooltip elements not found');
        return;
    }

    // Get selected payment channel
    let paymentChannel = 'Other';
    
    // Check both modals for selected channel
    const bookingChannel = document.getElementById('payment-details-select');
    const billingChannel = document.getElementById('billing-payment-details');
    const bookingCustom = document.getElementById('custom-payment-channel');
    const billingCustom = document.getElementById('billing-custom-payment-channel');
    
    // Check booking modal first
    if (bookingChannel && bookingChannel.value) {
        paymentChannel = bookingChannel.value;
    } else if (bookingCustom && bookingCustom.value.trim()) {
        paymentChannel = bookingCustom.value.trim();
    } 
    // Then check billing modal
    else if (billingChannel && billingChannel.value) {
        paymentChannel = billingChannel.value;
    } else if (billingCustom && billingCustom.value.trim()) {
        paymentChannel = billingCustom.value.trim();
    }
    
    console.log('Selected payment channel for tooltip:', paymentChannel);

    // Get amount - try different selectors
    let amount = 'the amount';
    const amountSelectors = [
        '.text-success',
        '[style*="color:#28a745"]',
        '#amount-due-now',
        '#billing-amount-due-now'
    ];
    
    for (const selector of amountSelectors) {
        const amountElement = document.querySelector(selector);
        if (amountElement && amountElement.textContent.trim()) {
            amount = amountElement.textContent.trim();
            break;
        }
    }

    console.log('Amount for tooltip:', amount);

    // Update content
    const method = paymentMethods[paymentChannel] || paymentMethods['Other'];
    let stepsHTML = '<ol>';
    
    method.steps.forEach(step => {
        const stepWithAmount = step.replace('{amount}', amount);
        stepsHTML += `<li>${stepWithAmount}</li>`;
    });
    
    stepsHTML += '</ol>';
    instructions.innerHTML = stepsHTML;
    guideChannel.textContent = paymentChannel;
    
    console.log('Tooltip content updated for:', paymentChannel);
}

// =======================================================
// 4. REAL-TIME VALIDATION
// =======================================================

function initializeRealTimeValidation() {
    console.log('Initializing real-time validation...');
    
    // Add real-time validation for all payment fields
    const paymentFields = document.querySelectorAll('.payment-field');
    
    paymentFields.forEach(field => {
        field.addEventListener('blur', function() {
            validateField(this);
        });
        
        field.addEventListener('input', function() {
            // Clear error when user starts typing
            if (this.classList.contains('error')) {
                this.classList.remove('error');
                const errorId = this.id + '-error';
                hideError(errorId);
            }
        });
    });
}

function validateField(field) {
    const fieldId = field.id;
    const value = field.value.trim();
    
    switch(fieldId) {
        case 'payment-method-modal':
        case 'billing-payment-method':
            if (!value) {
                showFieldError(field, 'method-error', 'Please select a payment method');
            } else {
                clearFieldError(field, 'method-error');
            }
            break;
            
        case 'payment-details-select':
        case 'billing-payment-details':
        case 'custom-payment-channel':
        case 'billing-custom-payment-channel':
            if (!value) {
                showFieldError(field, 'channel-error', 'Please select or enter a payment channel');
            } else {
                clearFieldError(field, 'channel-error');
            }
            break;
            
        case 'referenceCode':
        case 'billingReferenceCode':
            if (!value) {
                showFieldError(field, 'reference-error', 'Please enter a reference code');
            } else if (value.length < 12 || value.length > 30) {
                showFieldError(field, 'reference-error', 'Reference code must be 12-30 characters long');
            } else if (!/^[A-Za-z0-9]+$/.test(value)) {
                showFieldError(field, 'reference-error', 'Reference code can only contain letters and numbers');
            } else {
                clearFieldError(field, 'reference-error');
            }
            break;
    }
}

function showFieldError(field, errorId, message) {
    field.classList.add('error');
    showError(errorId, message);
}

function clearFieldError(field, errorId) {
    field.classList.remove('error');
    hideError(errorId);
}

// =======================================================
// 5. FORM VALIDATION FUNCTIONS
// =======================================================

function validatePaymentForm() {
    console.log('Validating payment form...');
    
    const methodSelect = document.getElementById('payment-method-modal');
    const detailsSelect = document.getElementById('payment-details-select');
    const customInput = document.getElementById('custom-payment-channel');
    const referenceInput = document.getElementById('referenceCode');
    
    let isValid = true;
    
    // Validate each field and show visual errors
    validateField(methodSelect);
    if (!methodSelect.value) isValid = false;
    
    const selectedChannel = detailsSelect.value;
    const customChannel = customInput.value.trim();
    if (!selectedChannel && !customChannel) {
        showFieldError(detailsSelect, 'channel-error', 'Please select or enter a payment channel');
        isValid = false;
    } else {
        clearFieldError(detailsSelect, 'channel-error');
        clearFieldError(customInput, 'channel-error');
    }
    
    validateField(referenceInput);
    if (!referenceInput.value || referenceInput.value.length < 12 || referenceInput.value.length > 30 || !/^[A-Za-z0-9]+$/.test(referenceInput.value)) {
        isValid = false;
    }
    
    if (!isValid) {
        // Scroll to first error
        const firstError = document.querySelector('.field-error:not([style*="display: none"])');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    return isValid;
}

function validateBillingPaymentForm() {
    console.log('Validating billing payment form...');
    
    const methodSelect = document.getElementById('billing-payment-method');
    const detailsSelect = document.getElementById('billing-payment-details');
    const customInput = document.getElementById('billing-custom-payment-channel');
    const referenceInput = document.getElementById('billingReferenceCode');
    
    let isValid = true;
    
    // Validate each field and show visual errors
    validateField(methodSelect);
    if (!methodSelect.value) isValid = false;
    
    const selectedChannel = detailsSelect.value;
    const customChannel = customInput.value.trim();
    if (!selectedChannel && !customChannel) {
        showFieldError(detailsSelect, 'billing-channel-error', 'Please select or enter a payment channel');
        isValid = false;
    } else {
        clearFieldError(detailsSelect, 'billing-channel-error');
        clearFieldError(customInput, 'billing-channel-error');
    }
    
    validateField(referenceInput);
    if (!referenceInput.value || referenceInput.value.length < 12 || referenceInput.value.length > 30 || !/^[A-Za-z0-9]+$/.test(referenceInput.value)) {
        isValid = false;
    }
    
    return isValid;
}

function showError(errorId, message) {
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'flex';
    }
}

function hideError(errorId) {
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        errorElement.style.display = 'none';
    }
}

// =======================================================
// 6. GLOBAL FUNCTIONS FOR BILLING PAGE
// =======================================================

function openPaymentModal() {
    console.log('Opening payment modal...');
    const modal = document.getElementById('paymentModal');
    if (modal) {
        modal.style.display = 'block';
        document.body.classList.add('modal-open');
        
        // Re-initialize everything
        setTimeout(() => {
            initializePaymentSelection();
            initializePaymentGuides();
            initializeRealTimeValidation();
            initializePaymentAmountCalculation();
        }, 100);
    }
}

function closePaymentModal() {
    console.log('Closing payment modal...');
    const modal = document.getElementById('paymentModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
        
        // Hide tooltips
        document.querySelectorAll('.payment-guide-tooltip').forEach(tooltip => {
            tooltip.classList.remove('show');
        });
        
        // Clear interrupted transaction flags
        handleInterruptedTransaction();
    }
}

// Function to handle interrupted transaction cleanup
function handleInterruptedTransaction() {
    console.log('Handling interrupted transaction cleanup...');
    
    // Clear any session flags via AJAX
    fetch('../config/clear_session_flags.php')
        .then(response => response.json())
        .then(data => {
            console.log('Session flags cleared:', data);
        })
        .catch(error => {
            console.error('Error clearing session flags:', error);
        });
}

// Make functions globally available
window.openPaymentModal = openPaymentModal;
window.closePaymentModal = closePaymentModal;
window.validatePaymentForm = validatePaymentForm;
window.validateBillingPaymentForm = validateBillingPaymentForm;
window.paymentMethods = paymentMethods;
window.paymentChannels = paymentChannels;

// =======================================================
// DEBUG FUNCTION (You can remove this after testing)
// =======================================================

function debugPaymentGuide() {
    console.log('=== PAYMENT GUIDE DEBUG INFO ===');
    
    const guideIcons = document.querySelectorAll('.payment-guide-icon');
    console.log('Guide icons found:', guideIcons.length);
    
    guideIcons.forEach((icon, index) => {
        console.log(`Icon ${index + 1}:`, icon);
        console.log(`Icon ID:`, icon.id);
        
        let tooltip = icon.nextElementSibling;
        if (!tooltip || !tooltip.classList.contains('payment-guide-tooltip')) {
            const tooltipId = icon.id === 'paymentGuideIcon' ? 'paymentGuideTooltip' : 
                            icon.id === 'billingPaymentGuideIcon' ? 'billingPaymentGuideTooltip' : null;
            if (tooltipId) {
                tooltip = document.getElementById(tooltipId);
            }
        }
        
        console.log(`Tooltip for icon ${index + 1}:`, tooltip);
        console.log(`Tooltip visibility:`, tooltip ? window.getComputedStyle(tooltip).display : 'N/A');
    });
    
    // Check if tooltips are positioned correctly
    const tooltips = document.querySelectorAll('.payment-guide-tooltip');
    console.log('All tooltips:', tooltips);
    
    console.log('=== END DEBUG INFO ===');
}

// Make it globally available for testing
window.debugPaymentGuide = debugPaymentGuide;