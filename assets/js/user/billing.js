// FILE: assets/js/user/billing.js

document.addEventListener('DOMContentLoaded', () => {
    const modalOverlay = document.getElementById('modalOverlay');
    const modalContainer = document.getElementById('modalContainer');
    const closeModalBtn = document.getElementById('closeModalBtn');
    
    // Elements for Terms, Privacy, and Feedback
    const showTermsCondition = document.getElementById('showTermsCondition');
    const termsandCondition = document.getElementById('termsandCondition');
    
    const showPrivacyPolicy = document.getElementById('showPrivacyPolicy');
    const privacyPolicy = document.getElementById('privacyPolicy');
    
    const showFeedbackCondition = document.getElementById('showFeedbackCondition');
    const feedbackCondition = document.getElementById('FeedbackCondition');

    // Function to show the modal with specific content
    function showModal(contentElement) {
        // Hide all modal content first
        document.querySelectorAll('.modal-text').forEach(el => {
            el.classList.add('hidden');
        });

        // Show the selected content
        if (contentElement) {
            contentElement.classList.remove('hidden');
        }

        // Show the overlay and container
        modalOverlay.classList.add('modal-active');
        modalContainer.classList.add('modal-active');
        document.body.classList.add('modal-open'); // Prevent body scroll
    }

    // Function to hide the modal
    function hideModal() {
        modalOverlay.classList.remove('modal-active');
        modalContainer.classList.remove('modal-active');
        document.body.classList.remove('modal-open'); // Re-enable body scroll
    }

    // --- Event Listeners for Opening Modals ---
    if (showTermsCondition) {
        showTermsCondition.addEventListener('click', (e) => {
            e.preventDefault();
            showModal(termsandCondition);
        });
    }

    if (showPrivacyPolicy) {
        showPrivacyPolicy.addEventListener('click', (e) => {
            e.preventDefault();
            showModal(privacyPolicy);
        });
    }

    if (showFeedbackCondition) {
        showFeedbackCondition.addEventListener('click', (e) => {
            e.preventDefault();
            showModal(feedbackCondition);
        });
    }

    // --- Event Listeners for Closing Modals ---
    
    // 1. Close button (X)
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', hideModal);
    }

    // 2. Clicking outside the modal (on the overlay)
    if (modalOverlay) {
        modalOverlay.addEventListener('click', hideModal);
    }

    // 3. Escape key press
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modalContainer.classList.contains('modal-active')) {
            hideModal();
        }
    });

    // NOTE: Adding an event listener to stop clicks inside the modal from closing it
    // This is optional but good for UX. The 'modal-container' handles this implicitly,
    // but if you had clicked an element inside the content, it might bubble up to the body.
    if (modalContainer) {
         modalContainer.addEventListener('click', (e) => {
            // Stop propagation to the overlay if the click originated inside the modal content
            if (e.target !== modalContainer && e.target !== modalOverlay) {
                e.stopPropagation();
            }
        });
    }
});