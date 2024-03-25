"use strict";

import Cookies from 'js-cookie';

const modeToggler = document.getElementById('darkmode');
const documentBody = document.getElementsByTagName('body')[0];
const toggleName = document.querySelector('.toggle-name'); // Select the title element

// Function to set the theme from the cookie
function setThemeFromCookie() {
    // If the cookie is set and is 'dark-mode', or if no cookie is set (default to dark mode)
    if (Cookies.get('mode') === 'dark-mode' || typeof Cookies.get('mode') === "undefined") {
        documentBody.classList.add('dark-mode');
        modeToggler.checked = true;
        toggleName.innerHTML = '<i class="fas fa-adjust me-1"></i>Dark Mode'; // Set to Dark Mode
        // If no cookie is set, set it for dark mode by default
        if (typeof Cookies.get('mode') === "undefined") {
            Cookies.set('mode', 'dark-mode', { expires: 7 });
        }
    } else {
        documentBody.classList.remove('dark-mode');
        modeToggler.checked = false;
        toggleName.innerHTML = '<i class="fas fa-adjust me-1"></i>Light Mode'; // Set to Light Mode
    }
}

setThemeFromCookie();

// Event listener for the toggle change
modeToggler.addEventListener('change', () => {
    if (modeToggler.checked) {
        documentBody.classList.add('dark-mode');
        Cookies.set('mode', 'dark-mode', { expires: 7 });
        toggleName.innerHTML = '<i class="fas fa-adjust me-1"></i>Dark Mode'; // Change to Dark Mode
    } else {
        documentBody.classList.remove('dark-mode');
        Cookies.remove('mode');
        toggleName.innerHTML = '<i class="fas fa-adjust me-1"></i>Light Mode'; // Change to Light Mode
    }
});
