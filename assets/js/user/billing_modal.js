/**
 * Billing Page Payment Modal Functionality
 * Handles showing/hiding the payment reference modal
 */

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('paymentModal');
    const closeBtn = document.querySelector('.payment-modal-close');
    const referenceForm = document.getElementById('referenceForm');
    
    // Function to hide modal
    function hideModal() {
        if (modal) {
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
        }
    }
    
    // Function to show modal (added for external access)
    function showModal() {
        if (modal) {
            modal.style.display = 'block';
            document.body.classList.add('modal-open');
            
            // Auto-focus on reference input when modal is shown
            const referenceInput = document.getElementById('referenceCode');
            if (referenceInput) {
                setTimeout(() => {
                    referenceInput.focus();
                }, 100);
            }
        }
    }
    
    // Make functions globally accessible
    window.showPaymentModal = showModal;
    window.hidePaymentModal = hideModal;
    
    // Close modal when X is clicked
    if (closeBtn) {
        closeBtn.addEventListener('click', hideModal);
    }
    
    // Close modal when clicking outside
    if (modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                hideModal();
            }
        });
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal && modal.style.display === 'block') {
            hideModal();
        }
    });
    
    // Form submission handling
    if (referenceForm) {
        referenceForm.addEventListener('submit', function(e) {
            const referenceInput = document.getElementById('referenceCode');
            const submitBtn = this.querySelector('button[type="submit"]');
            
            // Basic validation
            if (referenceInput && referenceInput.value.trim() === '') {
                e.preventDefault();
                referenceInput.focus();
                referenceInput.style.borderColor = '#dc3545';
                return;
            }
            
            // Add loading state
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';
                submitBtn.style.opacity = '0.7';
            }
        });
    }
    
    // Auto-focus on reference input when modal is shown
    if (modal && modal.style.display === 'block') {
        const referenceInput = document.getElementById('referenceCode');
        if (referenceInput) {
            setTimeout(() => {
                referenceInput.focus();
            }, 100);
        }
        document.body.classList.add('modal-open');
    }
});