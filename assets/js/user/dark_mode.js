// --- 1. TAILWIND CONFIGURATION (Required by the Tailwind CDN) ---
tailwind.config = {
    // CRITICAL: This tells Tailwind to look for the 'dark' class on the <html> tag
    darkMode: 'class', 
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
            },
        }
    }
};

// --- 2. DARK MODE JAVASCRIPT LOGIC (Global) ---

const DARK_MODE_KEY = 'global_dark_mode'; // Key used in localStorage

/**
 * Reads preference from localStorage and sets initial state.
 */
function initializeDarkMode() {
    const savedMode = localStorage.getItem(DARK_MODE_KEY);
    // Check saved preference, otherwise default to 'disabled' (Light Mode)
    const isDark = savedMode === 'enabled'; 
    applyDarkMode(isDark);
    
    // Attach listener to the toggle button
    const toggleButton = document.getElementById('dark-mode-icon-toggle'); // Use the fixed ID
    if (toggleButton) {
        toggleButton.addEventListener('click', toggleDarkMode);
    }
}

/**
 * Applies the 'dark' class to the <html> element and saves preference.
 * @param {boolean} isDark - True to enable Dark Mode.
 */
function applyDarkMode(isDark) {
    const html = document.documentElement;
    
    if (isDark) {
        html.classList.add('dark');
    } else {
        html.classList.remove('dark');
    }
    
    // Update the visual icon (Moon/Sun)
    updateToggleIcon(isDark);
    
    // Save preference to local storage
    localStorage.setItem(DARK_MODE_KEY, isDark ? 'enabled' : 'disabled');
}

/**
 * Toggles the current state.
 */
function toggleDarkMode() {
    const currentMode = document.documentElement.classList.contains('dark');
    applyDarkMode(!currentMode);
}

/**
 * Updates the icon to reflect the opposite of the current state.
 * @param {boolean} isDark - The current state.
 */
function updateToggleIcon(isDark) {
    const icon = document.getElementById('dark-mode-icon');
    if (!icon) return; 

    // Clear previous classes for safety (Moon/Sun)
    icon.classList.remove('fa-moon', 'fa-sun');

    // If it's dark (isDark=true), we show the sun icon to switch to light mode
    if (isDark) {
        icon.classList.add('fa-sun');
    } else {
        // If it's light, we show the moon icon to switch to dark mode
        icon.classList.add('fa-moon');
    }
}


// Start the process when the entire page is loaded
document.addEventListener('DOMContentLoaded', initializeDarkMode);
