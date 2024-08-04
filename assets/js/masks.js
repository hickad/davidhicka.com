document.addEventListener('DOMContentLoaded', function() {
    var phoneInput = document.getElementById('your-phone');

    if(phoneInput != null) {

        phoneInput.addEventListener('input', function(e) {
            var value = phoneInput.value.replace(/\D/g, '');
            var formattedValue = '';

            if (value.length > 0) {
                formattedValue += '(' + value.substring(0, 3);
            }
            if (value.length >= 4) {
                formattedValue += ') ' + value.substring(3, 6);
            }
            if (value.length >= 7) {
                formattedValue += '-' + value.substring(6, 10);
            }

            phoneInput.value = formattedValue;
        });

        phoneInput.addEventListener('blur', function() {
            var value = phoneInput.value.replace(/\D/g, '');
            if (value.length !== 10) {
                phoneInput.value = '';
            }
        });

    }
});