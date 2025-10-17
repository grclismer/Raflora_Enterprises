/**
 * Terms & Conditions and Privacy Policy Modal System
 * Usage: Include this file and modal.css to enable modal functionality
 */

class TermsAndPrivacyModal {
    constructor() {
        this.modalOverlay = null;
        this.modalContainer = null;
        this.modalContent = null;
        this.closeModalBtn = null;
        this.termsContent = null;
        this.privacyContent = null;
        
        this.isInitialized = false;
        this.init();
    }

    init() {
        // Create modal elements if they don't exist
        this.createModalElements();
        
        // Initialize event listeners
        this.initializeEventListeners();
        
        this.isInitialized = true;
        console.log('Terms & Privacy Modal System initialized');
    }

    createModalElements() {
        // Create modal overlay
        this.modalOverlay = document.getElementById('modalOverlay');
        if (!this.modalOverlay) {
            this.modalOverlay = document.createElement('div');
            this.modalOverlay.id = 'modalOverlay';
            this.modalOverlay.className = 'modal-overlay';
            document.body.appendChild(this.modalOverlay);
        }

        // Create modal container
        this.modalContainer = document.getElementById('modalContainer');
        if (!this.modalContainer) {
            this.modalContainer = document.createElement('div');
            this.modalContainer.id = 'modalContainer';
            this.modalContainer.className = 'modal-container';
            document.body.appendChild(this.modalContainer);
        }

        // Create modal content
        this.modalContent = document.getElementById('modalContent');
        if (!this.modalContent) {
            this.modalContent = document.createElement('div');
            this.modalContent.id = 'modalContent';
            this.modalContent.className = 'modal-content';
            this.modalContainer.appendChild(this.modalContent);
        }

        // Create close button
        this.closeModalBtn = document.getElementById('closeModalBtn');
        if (!this.closeModalBtn) {
            this.closeModalBtn = document.createElement('button');
            this.closeModalBtn.id = 'closeModalBtn';
            this.closeModalBtn.className = 'close-btn';
            this.closeModalBtn.innerHTML = '&times;';
            this.closeModalBtn.setAttribute('aria-label', 'Close modal');
            this.modalContent.appendChild(this.closeModalBtn);
        }

        // Create terms content container
        this.termsContent = document.getElementById('termsandCondition');
        if (!this.termsContent) {
            this.termsContent = document.createElement('div');
            this.termsContent.id = 'termsandCondition';
            this.termsContent.className = 'modal-text hidden';
            this.modalContent.appendChild(this.termsContent);
        }

        // Create privacy content container
        this.privacyContent = document.getElementById('privacyPolicy');
        if (!this.privacyContent) {
            this.privacyContent = document.createElement('div');
            this.privacyContent.id = 'privacyPolicy';
            this.privacyContent.className = 'modal-text hidden';
            this.modalContent.appendChild(this.privacyContent);
        }

        // Set the content if not already set
        this.setModalContent();
    }

    setModalContent() {
        // Terms and Conditions Content
        if (this.termsContent && !this.termsContent.innerHTML.trim()) {
            this.termsContent.innerHTML = `
                <h2>TERMS AND CONDITIONS:</h2>
                <p class="subtitle">Contractor: RAFLORA ENTERPRISES</p>
                <div class="section"><p>Contractor shall be given independence as stylist to choose colors, materials, and accessories that will be compatible with the theme and inspiration
                    <span class="highlighted">AS REQUIRED BY THE CLIENT</span>. Client accepts that flowers and materials shape, form and colors may vary from attached sketches or photo pegs. As stylist, Contractor shall be given
                    <span class="highlighted">artistic license</span> in dealing with the available materials as they see fit, adhering as close as possible to the approved presentation.</p></div>
                <div class="section"><p class="section-title">A. Meals:</p><p>Client shall provide (3) three meals and continuous supply of clean drinking water for
                    <span class="highlighted">Contractor's crew</span> during the whole duration of the installation until final conclusion. In the event that client fails to provide meals, client shall be invoiced for the provision of meals.</p></div>
                <div class="section"><p class="section-title">B. Electrician:</p><p>Raflora Enterprises shall arrange for an electrician to tap any Décor that has electrical elements to a power source compatible with the electrical requirements of the materials concerned. This is to avoid and keep in control electrical fire hazards.</p></div>
                <div class="section"><p class="section-title">C. Raflora Enterprises</p><p>shall have recourse for assistance from Event Venue for tools and equipment, (e.g., tall ladders or scaffolding) necessary for the completion of certain tasks, and should
                    <span class="highlighted">assist in providing personnel</span> who can install that which may be inaccessible under normal conditions.</p></div>
                <div class="section"><p class="section-title">D. Holding Room:</p><p>Hotel Venue, in coordination with Client, should make available a holding room where delivered materials and accessories for installation can be stored and are easily accessible. The holding room will also serve as preparation area for the finished product is installed and should be furnished with tables and chairs. This should also be protected from the elements to safeguard the integrity of the materials for installation, and shall serve as private and rest area for Crew to take their meals and completion of installation until Egress.</p></div>
                <div class="section"><p class="section-title">E. Contractor</p><p>shall not be liable for any consequential damages that may arise due to unforeseen events such as malfunction of any equipment,
                    <span class="highlighted">forces of nature (typhoon, strong winds and rain)</span> that may cause damage to installed materials and decorations, acts of war, accidents, unexpected governmental acts that may cause unforeseen traffic or any delay, and political/social unrest.</p></div>
                <div class="section"><p class="section-title">F. All goods, accessories and decorative materials from RAFLORA'S INVENTORY</p><p>from their warehouse are considered as
                    <span class="highlighted">RENTALS (Inclusive in the Proposal Costs)</span> for the duration of the event. EX: Vases, Votives, Accent Decors, Electronic Votives and Candles, Floating Battery-Operated Candles, Tassels, Faux Flowers, Avatar Lights, Gold Metal Structures, Bubble Lights & Tube Lights, etc.</p></div>
            `;
        }

        // Privacy Policy Content
        if (this.privacyContent && !this.privacyContent.innerHTML.trim()) {
            this.privacyContent.innerHTML = `
                <h2>DISCLAIMER:</h2>
                <p class="subtitle">All presentation photos in this proposal have been gathered/generated from public design requirements of the Client. Actual installations may have variations in sizes, colors and design form but we will remain faithful to the Client's theme and requirements.</p>
                <div class="section"><p class="section-title">1.Publicly Available:</p><p>The photo pegs presented in this proposal have been gathered/generated from publicly available sources, such as websites, publications, and other open platforms. These images remain in the public domain or under their respective ownership.</p></div>
                <div class="section"><p class="section-title">2.Fresh Perspectives:</p><p>Our intention is to bring innovative and unique viewpoints to the project. While these photo pegs serve as an initial starting point, we are dedicated to creating original installations that goes beyond referenced materials. Our goal is fresh and inventive interpretations that align with your vision.</p></div>
                <div class="section"><p class="section-title">3.Fresh Interpretations:</p><p>Our primary objective is to offer innovative and distinct perspectives based on the influence of the client's Mood Board, Color Theme, etc. These photo pegs are intended to ignite creativity and provide an initial visual reference, but they do not entirely define our original content nor represents a fresh take on the project.</p></div>
                <div class="section"><p class="section-title">4.Collaboration Focus:</p><p>We aim to offer innovative and distinct perspectives. They are intended to spark creativity and set a tone for the project, but the final results may differ due to factors such as the project's evolving nature and client support. Instead, we focus final availability of seasonal flowers, accessories at any given time.</p></div>
                <div class="section"><p class="section-title">5.Open Collaboration:</p><p>We encourage an open and collaborative environment. We value our client's input, feedback, and ideas throughout the project's development, which may lead to further innovative and creative perspectives.</p></div>
                <div class="section"><p>By reviewing this proposal and the associated photo pegs, you acknowledge and accept the terms outlined in this disclaimer. If you have any inquiries, concerns, or need further clarification, please do not hesitate to reach out. We are enthusiastic about the opportunity to work together and keep the project to life with fresh and unique perspectives.</p></div>
                <div class="payment-terms">
                    <h3>CLIENT PRIORITY AND TERMS OF PAYMENT</h3>
                    <div class="payment-details">
                        <p><span class="highlighted">First-Come, First-Serve Policy:</span> Services are provided on a first-come, first-serve basis, based on the receipt of a down-payment. Raflora Enterprises reserves the right to prioritize clients, whose event occurs on the same date with other client/ clients, who have confirmed their event by submitting their down-payment first and who have applied for this priority status shall be in the last confirmed in this interim, and will not be liable from accepting another client who has provided their down payment first. Raflora Enterprises is not responsible for any potential inconvenience or impact on a client's event due to the first-come, first-serve policy.</p>
                        <div class="payment-row">
                            <span>⮚ 50 % DOWNPAYMENT UPON APPROVAL & SIGNING OF CONTRACT</span>
                        </div>
                        <div class="payment-row">
                            <span>⮚ 50 % BALANCE OF PAYMENT 30 DAYS BEFORE THE EVENT</span>
                        </div>
                        <div class="payment-row">
                            <span>RAFLORA ENTERPRISES - BIR TIN: 944-328-187-000 (NON-VAT)</span>
                        </div>
                        <div class="payment-row">
                            <span>BDO SAVINGS ACCOUNT: 0013 - 9018 - 3937</span>
                        </div>
                        <p style="margin-top: 15px;"><span class="highlighted">A. Proposal</span> is based on above-given areas and quantity. No variation of costs less than the approved and confirmed Grand Total shall be allowed for any regional and confirmation of this Contract.</p>
                        <p><span class="highlighted">B. Overdue accounts</span> are subject to interest based on prevailing bank rates from the time it becomes overdue until full payment.</p>
                        <p><span class="highlighted">C. This formal quotation</span> also serves as the formal contract of confirmation. All contents, values, rates and other particulars of this Formal Quotation is <span class="highlighted">strictly confidential and only for the perusal of the intended client.</span></p>
                    </div>
                </div>
            `;
        }
    }

    initializeEventListeners() {
        // Close modal events
        this.closeModalBtn.addEventListener('click', () => this.hideModal());
        this.modalOverlay.addEventListener('click', () => this.hideModal());

        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isModalVisible()) {
                this.hideModal();
            }
        });

        // Prevent modal container click from closing modal
        this.modalContainer.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // Auto-attach to links with specific IDs
        this.autoAttachToLinks();
    }

    autoAttachToLinks() {
        // Attach to Privacy Policy links
        const privacyLinks = document.querySelectorAll('#showPrivacyPolicy, [href*="privacy"], .privacy-policy-link');
        privacyLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.showModal('privacy');
            });
        });

        // Attach to Terms and Conditions links
        const termsLinks = document.querySelectorAll('#showTermsCondition, [href*="terms"], .terms-conditions-link');
        termsLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.showModal('terms');
            });
        });
    }

    showModal(contentType) {
        // Hide both contents first
        this.termsContent.classList.add('hidden');
        this.privacyContent.classList.add('hidden');
        
        // Show the requested content
        if (contentType === 'terms') {
            this.termsContent.classList.remove('hidden');
        } else if (contentType === 'privacy') {
            this.privacyContent.classList.remove('hidden');
        }
        
        // Show modal and overlay
        this.modalContainer.style.display = 'block';
        this.modalOverlay.style.display = 'block';
        
        // Prevent body scrolling
        document.body.style.overflow = 'hidden';
        
        // Focus the close button for accessibility
        this.closeModalBtn.focus();
        
        // Dispatch custom event
        this.dispatchEvent('modalShow', { contentType });
    }

    hideModal() {
        this.modalContainer.style.display = 'none';
        this.modalOverlay.style.display = 'none';
        document.body.style.overflow = 'auto';
        
        // Dispatch custom event
        this.dispatchEvent('modalHide');
    }

    isModalVisible() {
        return this.modalContainer.style.display === 'block';
    }

    // Method to manually attach to any element
    attachToElement(element, contentType) {
        element.addEventListener('click', (e) => {
            e.preventDefault();
            this.showModal(contentType);
        });
    }

    // Custom events support
    dispatchEvent(eventName, detail = {}) {
        const event = new CustomEvent(`termsPrivacyModal:${eventName}`, {
            detail: { ...detail, instance: this }
        });
        document.dispatchEvent(event);
    }

    // Public methods
    showTerms() {
        this.showModal('terms');
    }

    showPrivacy() {
        this.showModal('privacy');
    }

    // Destroy method for cleanup
    destroy() {
        if (this.modalOverlay && this.modalOverlay.parentNode) {
            this.modalOverlay.parentNode.removeChild(this.modalOverlay);
        }
        if (this.modalContainer && this.modalContainer.parentNode) {
            this.modalContainer.parentNode.removeChild(this.modalContainer);
        }
        this.isInitialized = false;
    }
}

// Initialize automatically when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.termsPrivacyModal = new TermsAndPrivacyModal();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = TermsAndPrivacyModal;
}