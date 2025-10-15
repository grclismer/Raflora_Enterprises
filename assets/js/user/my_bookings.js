/**
 * --------------------------------------------------------
 * NEW LOGIC FOR MY BOOKINGS PAGE - Payment Reference Modal
 * --------------------------------------------------------
 */

/**
 * Shows the payment reference modal and populates it with booking data.
 * This function is triggered by the "Submit Payment" button on the my_bookings.php page.
 * * @param {string} orderId - The unique ID of the order/booking.
 * @param {string} amountDue - The total price/amount due for the order.
 */
function showPaymentModal(orderId, amountDue) {
    const modal = document.getElementById('paymentRefModal');
    const amountDisplay = document.getElementById('amountDueDisplay');
    
    // 1. Populate the hidden form fields with the data needed for submission
    document.getElementById('modalOrderId').value = orderId;
    
    // 2. Display the amount for the user's reference
    amountDisplay.textContent = amountDue;

    // 3. Show the modal
    if (modal) {
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
    }
}

/**
 * Closes the payment reference modal.
 */
function closePaymentModal() {
    const modal = document.getElementById('paymentRefModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
    }
}

/**
 * Handles the AJAX submission of the payment reference form.
 * * NOTE: This function prevents the default form submission and sends data to 
 * the PHP endpoint api/submit_payment_ref.php instead.
 */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('paymentRefForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Stop the form from doing a standard page refresh submission
            
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            
            // Temporarily disable button and show loading state
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';

            // Use the fetch API to send the data asynchronously
            fetch('api/submit_payment_ref.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Payment reference submitted successfully! The status will update shortly after admin verification.');
                    closePaymentModal();
                    // Optional: Automatically reload the window to show the new status (pending verification)
                    window.location.reload(); 
                } else {
                    alert('Error: ' + (data.message || 'Could not submit payment reference.'));
                }
            })
            .catch(error => {
                console.error('Submission Error:', error);
                alert('An unexpected network error occurred.');
            })
            .finally(() => {
                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Reference';
            });
        });
    }
});
