// mobile-menu.js
document.addEventListener('DOMContentLoaded', function() {
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    const sidebar = document.querySelector('.sidebar');
    const sidebarMenu = document.querySelector('.sidebar-menu');
    
    if (hamburgerMenu && sidebar) {
        // Toggle mobile menu
        hamburgerMenu.addEventListener('click', function() {
            this.classList.toggle('active');
            sidebar.classList.toggle('menu-open');
            
            // Toggle aria-expanded for accessibility
            const isExpanded = this.classList.contains('active');
            this.setAttribute('aria-expanded', isExpanded);
        });
        
        // Close menu when clicking on a link (for single page applications)
        const menuLinks = sidebarMenu.querySelectorAll('a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    hamburgerMenu.classList.remove('active');
                    sidebar.classList.remove('menu-open');
                    hamburgerMenu.setAttribute('aria-expanded', 'false');
                }
            });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768 && 
                sidebar.classList.contains('menu-open') &&
                !sidebar.contains(event.target) &&
                !hamburgerMenu.contains(event.target)) {
                hamburgerMenu.classList.remove('active');
                sidebar.classList.remove('menu-open');
                hamburgerMenu.setAttribute('aria-expanded', 'false');
            }
        });
        
        // Close menu on window resize if it becomes desktop view
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                hamburgerMenu.classList.remove('active');
                sidebar.classList.remove('menu-open');
                hamburgerMenu.setAttribute('aria-expanded', 'false');
            }
        });
        
        // Escape key to close menu
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && sidebar.classList.contains('menu-open')) {
                hamburgerMenu.classList.remove('active');
                sidebar.classList.remove('menu-open');
                hamburgerMenu.setAttribute('aria-expanded', 'false');
            }
        });
    }
});