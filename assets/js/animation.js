import 'aos/dist/aos.css';
import AOS from 'aos';

AOS.init({
    offset: 20,
    duration: 300,
    easing: 'ease-out',
    delay: 0,
  });

  document.addEventListener('DOMContentLoaded', function () {
    const progressBars = document.querySelectorAll('.progress-bar');

    function isElementInViewport(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    function animateProgressBar(bar) {
        const percentage = parseInt(bar.getAttribute('data-percentage'), 10);
        let width = 0;
        const increment = 2; // Increase the width by 2% each step
        const interval = setInterval(() => {
            if (width >= percentage) {
                clearInterval(interval);
            } else {
                width += increment;
                if (width > percentage) width = percentage; // Ensure it doesn't exceed the percentage
                bar.style.width = width + '%';
                bar.querySelector('strong').textContent = width + '%';
            }
        }, 10); // Decrease the interval to 10ms
    }

    function animateProgressBars() {
        progressBars.forEach(bar => {
            if (isElementInViewport(bar) && !bar.classList.contains('animated')) {
                animateProgressBar(bar);
                bar.classList.add('animated');
            }
        });
    }

    // Initial check after the page is fully loaded
    window.addEventListener('load', animateProgressBars);

    // Check on scroll
    window.addEventListener('scroll', animateProgressBars);
});
