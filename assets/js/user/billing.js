document.addEventListener('DOMContentLoaded', function() {
    const modalContainer = document.getElementById('modalContainer');
    const modalOverlay = document.getElementById('modalOverlay');
    const termsModalContent = document.getElementById('termsandCondition');
    const privacyModalContent = document.getElementById('privacyPolicy');
    const feedbackModalContent = document.getElementById('FeedbackCondition');
    const showPrivacyPolicyLink = document.getElementById('showPrivacyPolicy');
    const showTermsConditionLink = document.getElementById('showTermsCondition');
    const showFeedbackConditionLink = document.getElementById('showFeedbackCondition');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const body = document.body;

    function showModal(contentElement) {
        termsModalContent.classList.add('hidden');
        privacyModalContent.classList.add('hidden');
        feedbackModalContent.classList.add('hidden');
        contentElement.classList.remove('hidden');
        modalContainer.classList.add('modal-active');
        modalOverlay.classList.add('modal-active');
        body.classList.add('modal-open');
    }

    function hideModal() {
        modalContainer.classList.remove('modal-active');
        modalOverlay.classList.remove('modal-active');
        body.classList.remove('modal-open');
    }

    if (showPrivacyPolicyLink) {
        showPrivacyPolicyLink.addEventListener('click', function(event) {
            event.preventDefault();
            showModal(privacyModalContent);
        });
    }

    if (showTermsConditionLink) {
        showTermsConditionLink.addEventListener('click', function(event) {
            event.preventDefault();
            showModal(termsModalContent);
        });
    }
    if (showFeedbackConditionLink) {
        showFeedbackConditionLink.addEventListener('click', function(event) {
            event.preventDefault();
            showModal(feedbackModalContent);
        });
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', hideModal);
    }

    if (modalOverlay) {
        modalOverlay.addEventListener('click', hideModal);
    }


});