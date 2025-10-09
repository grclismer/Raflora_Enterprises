/* ===============================================
GLOBAL STYLES & TRANSITIONS 
===============================================
*/

html {
    /* Optional: Add smooth transition to the entire page background */
    transition: background-color 0.5s ease; 
}
/* Use this class on cards, headers, or buttons that change color */
.transition-colors-theme {
    transition: background-color 0.5s, color 0.5s, border-color 0.5s, box-shadow 0.5s;
}
body {
    /* Ensure body inherits size but not the color, container handles card color */
    transition: background-color 0.5s;
}
.card-bg {
             transition: background-color 0.5s;
        }
/* Ensure icon pointers don't interfere with clicks */
/* Dark Mode Toggle */
.dark-mode-toggle {
    position: fixed;
    top: 6rem;
    right: 2rem;
    background: #1f2937;
    color: white;
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    z-index: 999;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.dark-mode-toggle:hover {
    background: #374151;
    transform: scale(1.1);
}
.form-group-icon i {
    pointer-events: none;
}
#dark-mode-icom{
    color: #2563eb;
    transition: color 0.3s ease;
}
.html.dark #dark-mode-icon-toggle:hover {
    background-color: #1f2937; 
}
html.dark #dark-mode-icon{
    color: #facc15;
}
.input-dark:focus {
    border-color: #3b82f6; /* Blue for focus */
    outline: none;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}
        
        

/* ===============================================
BODY AND GLOBAL TEXT
===============================================
*/

/* Overrides for the <html> element and default <body> */
html.dark, 
html.dark body {
    background-color: #111827; 
    color: #f3f4f6; 
}

/* ===============================================
NAVBAR STYLES (Common to all pages)
===============================================
*/

html.dark .navbar {
    background-color: #0d121c; 
    box-shadow: 0 4px 6px rgba(255, 255, 255, 0.05);
}

html.dark .nav-links a,
html.dark .user-dropdown-toggle i {
    color: #d1d5db; 
}

 html.dark .nav-links a:hover {
    color: #ffffff;
} 

html.dark .user-dropdown-menu {
    background-color: #1f2937; 
    border: 1px solid #374151; 
    box-shadow: 0 4px 6px rgba(255, 255, 255, 0.1);
}

html.dark .user-dropdown-menu a {
    color: #f3f4f6;
}

html.dark .user-dropdown-menu a:hover {
    background-color: #374151; 
}
.html.dark {
    /* Full dark background */
    background-color: #1f2937; 
}
.html:not(.dark) {
        /* Default background (yung dark blue na ginamit mo sa original) */
    background-color: #111827; 
}

/* Dark Mode Toggle Icon (Visible) */
html.dark #dark-mode-icon {
    color: #facc15;
}

/* Light Mode Toggle Icon (Default) */
#dark-mode-icon {
    color: #2563eb; 
}

/* ===============================================
LANDING PAGE SECTIONS (Existing styles from prompt)
===============================================
*/

html.dark .Home-bg h1 {
    color: #ffffff;
}

html.dark .Home-bg p {
    color: #d1d5db; 
}

html.dark .Set-img {
    background: #1f2937; 
    box-shadow: 0 2px 5px rgba(255, 255, 255, 0.1);
}

html.dark .Set-img .Section-Heading {
    color: #ffffff;
}

html.dark .Set-img .description {
    color: #d1d5db;
}

html.dark .Review {
    background: #374151; 
    box-shadow: 0 2px 5px rgba(255, 255, 255, 0.1);
}

html.dark .Review h2 {
    color: #ffffff;
}

html.dark .Review h4 {
    color: #9ca3af; 
}

html.dark .Review h3 {
    color: #d1d5db;
}

/* ===============================================
GALLERY PAGE STYLES
===============================================
*/

/* Gallery Header Section */


html.dark .Gallery-Page .Header-Bg h1,
html.dark .Gallery-Page .Header-Bg h3 {
    color: #ffffff; 
    /* text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);  */
}

/* Individual Image Sets (Treated like cards/sections) */
html.dark .Gallery-Collage div[class^="Image-Set"] {
    background: #1f2937;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.08);
}

html.dark .Gallery-Collage div[class^="Image-Set"] h1 {
    color: #ffffff;
}

/* Gallery Modal */
html.dark .modal {
    background-color: rgba(0, 0, 0, 0.9); 
}

html.dark .modal .close-btn,
html.dark .modal .prev,
html.dark .modal .next {
    color: #f3f4f6; /* White controls */
    text-shadow: 0 0 5px #000000;
}

html.dark #caption {
    color: #d1d5db;
}

/* ===============================================
ABOUT PAGE STYLES
===============================================
*/

/* Profile Sections (The card/block containing the image and text) */
html.dark .profile-section {
    background: #1f2937; /* Gray-800 for the profile card */
    box-shadow: 0 5px 15px rgba(255, 255, 255, 0.1);
    border: 1px solid #374151;
}

html.dark .profile-content h1 {
    color: #ffffff; 
}

html.dark .profile-content h3 {
    color: #9ca3af; 
}

html.dark .profile-content p {
    color: #d1d5db; 
}


/* ===============================================
FOOTER STYLES (Common to all pages)
===============================================
*/

html.dark .footer {
    background-color: #0d121c; 
    color: #f3f4f6;
    border-top: 1px solid #374151;
}

html.dark .Contact {
    color: #ffffff;
}

/* Social Icon Blocks */
html.dark .Social-Facebook,
html.dark .Social-Email {
    border: 1px solid #374151;
    background-color: #1f2937;
    box-shadow: 0 0 5px rgba(255, 255, 255, 0.1);
}

html.dark .Social-Facebook h3,
html.dark .Social-Email h3 {
    color: #ffffff;
}

html.dark .Social-Facebook p,
html.dark .Social-Email p {
    color: #9ca3af;
}

html.dark .hyper-link-facebook,
html.dark .hyper-link-gmail {
    color: #60a5fa;
}





/* BOOKING PAGE STYLES */

 /* Main Form Container  */
html.dark .main-container {
background-color: #111827;
}

html.dark .page-title {
color: #ffffff;
}

html.dark .form-container {
background-color: #1f2937; 
box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
border: 1px solid #374151; 
}

 /* Labels  */
html.dark .form-container label {
color: #f3f4f6; 
}

 /* Text Inputs, Textareas, and Selects  */
html.dark .form-container input[type="text"],
html.dark .form-container input[type="tel"],
html.dark .form-container input[type="email"],
html.dark .form-container input[type="date"],
html.dark .form-container input[type="time"],
html.dark .form-container textarea,
html.dark .form-container select {
background-color: #374151;
color: #f3f4f6; 
border: 1px solid #4b5563;
}

/* Placeholder color  */
html.dark .form-container input::placeholder,
html.dark .form-container textarea::placeholder {
color: #9ca3af; 
}

 /* File Upload Box  */
html.dark .file-upload-box {
background-color: #1f2937;
border: 2px dashed #4b5563;
}

html.dark .file-upload-box i {
color: #93c5fd; 
}

/* Event Date/Time Icons (filter applied for dark theme) */
html.dark .date-time-group img {
filter: brightness(0.9) grayscale(10%);
opacity: 0.8;
}

/* /Payment Radio Buttons Text / */
html.dark .payment-type,
html.dark .payment-type label,
html.dark .payment-type div {
color: #f3f4f6;  
}

html.dark .submit-button {
background-color: #2563eb; 
color: #ffffff;
box-shadow: 0 4px #1e40af;
}

html.dark .submit-button:hover {
background-color: #1e40af; 
box-shadow: 0 2px #1e40af;
}

 /* Modal Styling (for #paymentModal)  */
html.dark .modal-content {
background-color: #1f2937; 
color: #f3f4f6;
border: 1px solid #374151;
}

html.dark .modal-header {
border-bottom: 1px solid #374151;
}

html.dark .modal-title {
color: #ffffff;
}

html.dark .modal-body {
color: #d1d5db; 
}

/* Alert inside modal  */
html.dark .alert-info {
background-color: #1d4ed8;
color: #eff6ff; 
border-color: #1e40af; 
}

/* Bootstrap form control inside modal */
html.dark .modal-body .form-control {
background-color: #374151;
color: #f3f4f6;
border: 1px solid #4b5563;
}

 /* Modal Submit Button (Bootstrap success green) */
html.dark #submitReferenceBtn {
background-color: #10b981; 
border-color: #059669; 
color: #ffffff;
}
html.dark #submitReferenceBtn:hover {
background-color: #059669; 
border-color: #047857; 
}

