<?php
add_action('wp_footer', function () {
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if the user has already accepted the cookie
            if (!getCookie('cookieAccepted')) {
                // Show the modal
                document.querySelector('.premium-modal-box-modal-dialog').style.display = 'block';
            }

            // Handle the "Accept" button click
            document.querySelector('.premium-modal-box-modal-footer button').addEventListener('click', function() {
                // Set a cookie named "cookieAccepted" with value "true" that expires in 24 hours
                setCookie('cookieAccepted', true, 1);

                // Hide the modal
                document.querySelector('.premium-modal-box-modal-dialog').style.display = 'none';
            });

            // Check if the user has already accepted the cookie (after page reload)
            if (getCookie('cookieAccepted')) {
                // Hide the modal
                document.querySelector('.premium-modal-box-modal-dialog').style.display = 'none';
            }
        });

        function setCookie(name, value, days) {
            var expires = '';
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + date.toUTCString();
            }
            document.cookie = name + '=' + value + expires + '; path=/';
        }

        function getCookie(name) {
            var nameEQ = name + '=';
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = cookies[i];
                while (cookie.charAt(0) == ' ') {
                    cookie = cookie.substring(1, cookie.length);
                }
                if (cookie.indexOf(nameEQ) == 0) {
                    return cookie.substring(nameEQ.length, cookie.length);
                }
            }
            return null;
        }
    </script>

<?php
});
