  // Look for .hamburger
  var hamburger = document.querySelector(".hamburger");
  // On click
  hamburger.addEventListener("click", function() {
    // Toggle class "is-active"
    hamburger.classList.toggle("is-active");
    // Do something else, like open/close menu
  });

  document.addEventListener('DOMContentLoaded', function() {
    function addRecaptchaLabel() {
        // Check if the textarea exists
        const textarea = document.querySelector('textarea#g-recaptcha-response-100000');
        if (textarea && !document.querySelector('label[for="g-recaptcha-response-100000"]')) {
            // Create the label element
            const label = document.createElement('label');
            label.setAttribute('for', 'g-recaptcha-response-100000');
            label.className = 'visually-hidden'; // Use 'sr-only' if using Bootstrap 4
            label.textContent = 'reCAPTCHA Response';

            // Insert the label before the textarea
            textarea.parentNode.insertBefore(label, textarea);
        }
    }

    // Run the function periodically to check if the textarea has been added
    const intervalId = setInterval(addRecaptchaLabel, 500);

    // Stop the interval once the label is added
    function stopInterval() {
        if (document.querySelector('label[for="g-recaptcha-response-100000"]')) {
            clearInterval(intervalId);
        }
    }

    // Continue checking until the label is added
    setInterval(stopInterval, 500);
});
