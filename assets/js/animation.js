import 'aos/dist/aos.css';
import AOS from 'aos';

AOS.init({
    offset: 200,
    duration: 600,
    easing: 'ease-in-sine',
    delay: 100,
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

    function animateProgressBars() {
        progressBars.forEach(bar => {
            if (isElementInViewport(bar)) {
                const percentage = bar.getAttribute('data-percentage');
                if (percentage !== null) {
                    console.log(`Animating progress bar to ${percentage}%`);
                    bar.style.width = percentage + '%';
                } else {
                    console.error('No data-percentage attribute found on element:', bar);
                }
            }
        });
    }

    // Initial check
    animateProgressBars();

    // Check on scroll
    window.addEventListener('scroll', animateProgressBars);
});


