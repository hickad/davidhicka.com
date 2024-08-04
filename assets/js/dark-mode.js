"use strict";

import Cookies from 'js-cookie';

const modeToggler = document.getElementById('darkmode');
const documentBody = document.body;
const toggleName = document.querySelector('.toggle-name');

// Function to set the theme from the cookie
function setThemeFromCookie() {
    const mode = Cookies.get('mode');

    if (mode === 'dark-mode') {
        documentBody.classList.add('dark-mode');
        modeToggler.checked = true;
        toggleName.innerHTML = '<i class="fas fa-adjust me-1"></i>Dark Mode';
    } else {
        documentBody.classList.remove('dark-mode');
        modeToggler.checked = false;
        toggleName.innerHTML = '<i class="fas fa-adjust me-1"></i>Light Mode';
    }
}

// Call the function immediately to set the theme
setThemeFromCookie();

// Event listener for the toggle change
modeToggler.addEventListener('change', () => {
    if (modeToggler.checked) {
        documentBody.classList.add('dark-mode');
        Cookies.set('mode', 'dark-mode', { expires: 7 });
        toggleName.innerHTML = '<i class="fas fa-adjust me-1"></i>Dark Mode';
    } else {
        documentBody.classList.remove('dark-mode');
        Cookies.set('mode', 'light-mode', { expires: 7 }); // Explicitly set to 'light-mode'
        toggleName.innerHTML = '<i class="fas fa-adjust me-1"></i>Light Mode';
    }
});
