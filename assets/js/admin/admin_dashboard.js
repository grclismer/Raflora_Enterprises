document.addEventListener('DOMContentLoaded', () => {

    // 1. Dynamic Sidebar Active State
    // This function automatically highlights the current page in the sidebar menu.
    function setActiveSidebarLink() {
        const sidebarLinks = document.querySelectorAll('.sidebar-menu a');
        const currentPath = window.location.pathname.split('/').pop();

        sidebarLinks.forEach(link => {
            const linkPath = link.getAttribute('href').split('/').pop();
            // Compare the filename (e.g., 'inventory.php' or 'update.html')
            if (linkPath === currentPath) {
                // Remove existing active class and set it on the parent <li>
                const parentLi = link.closest('li');
                if (parentLi) {
                    const allLi = document.querySelectorAll('.sidebar-menu li');
                    allLi.forEach(li => li.classList.remove('active'));
                    parentLi.classList.add('active');
                }
            }
        });
    }

    setActiveSidebarLink();

    // 2. Client Updates Page Functionality (on update.html)
    // This code handles theme selection and the "Place Order" button.
    const placeOrderBtn = document.querySelector('.placeorder-btn');
    const themeItems = document.querySelectorAll('.theme-item');

    if (themeItems.length > 0) {
        let selectedTheme = null;

        themeItems.forEach(item => {
            item.addEventListener('click', () => {
                // Deselect previous theme
                if (selectedTheme) {
                    selectedTheme.classList.remove('selected');
                }
                
                // Select the new theme
                item.classList.add('selected');
                selectedTheme = item;
                console.log('Theme selected!');
            });
        });
    }

    if (placeOrderBtn) {
        placeOrderBtn.addEventListener('click', (event) => {
            // Prevent the default form submission (if it's in a form)
            event.preventDefault(); 
            
            // Here you would add the logic to process the order
            // e.g., collect form data and send it to your backend via fetch()
            console.log('Place Order button clicked!');

            alert('Order successfully placed!'); // Simple confirmation
        });
    }

    // 3. Invoice/Inventory Table Search Functionality (Placeholder)
    // This is a common feature. You can expand this to filter the table rows.
    function setupTableSearch(tableId, inputId) {
        const searchInput = document.getElementById(inputId);
        const table = document.getElementById(tableId);

        if (searchInput && table) {
            searchInput.addEventListener('keyup', () => {
                const filter = searchInput.value.toLowerCase();
                const rows = table.getElementsByTagName('tr');

                for (let i = 1; i < rows.length; i++) { // Start from 1 to skip the header
                    const rowText = rows[i].textContent.toLowerCase();
                    if (rowText.includes(filter)) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            });
        }
    }

    // Example Usage (You can add a search input field to your HTML)
    // setupTableSearch('tools-table', 'tools-search-input');
    // setupTableSearch('invoice-table', 'invoice-search-input');
});