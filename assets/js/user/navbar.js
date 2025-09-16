document.addEventListener('DOMContentLoaded', function() {
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    const navLinks = document.querySelector('.nav-links');
    const userDropdownToggle = document.querySelector('.user-dropdown-toggle');

    // Toggle for Hamburger Menu
    if (hamburgerMenu) {
        hamburgerMenu.addEventListener('click', (event) => {
            navLinks.classList.toggle('active');
            event.stopPropagation();
        });
    }

    // Toggle for User Dropdown
    if (userDropdownToggle) {
        userDropdownToggle.addEventListener('click', (event) => {
            userDropdownToggle.classList.toggle('active');
            event.stopPropagation();
        });
    }

    // Close menus when clicking outside
    document.addEventListener('click', (event) => {
        // Close user dropdown if clicking outside of it
        if (userDropdownToggle && userDropdownToggle.classList.contains('active') && !userDropdownToggle.contains(event.target)) {
            userDropdownToggle.classList.remove('active');
        }
        // Close hamburger menu if clicking outside of it
        if (navLinks && navLinks.classList.contains('active') && !navLinks.contains(event.target) && hamburgerMenu && !hamburgerMenu.contains(event.target)) {
            navLinks.classList.remove('active');
        }
    });
});