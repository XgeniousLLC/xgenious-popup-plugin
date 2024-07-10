(function($) {
    'use strict';

    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    $(document).ready(function() {
        $('.xgenious-popup').each(function() {
            var $popup = $(this);
            var popupId = $popup.data('popup-id');
            var delay = $popup.data('delay') * 1000;
            var autoClose = $popup.data('auto-close');
            var autoCloseTime = $popup.data('auto-close-time') * 1000;
            var endTime = $popup.data('end-time');

            // Check if the popup was closed within the last 24 hours
            if (getCookie('popup_closed_' + popupId) !== 'true') {
                setTimeout(function() {
                    if (new Date() < new Date(endTime) || !endTime) {
                        $popup.show();
                        if (autoClose && autoCloseTime > 0) {
                            setTimeout(function() {
                                $popup.hide();
                            }, autoCloseTime);
                        }
                    }
                }, delay);
            }

            $popup.find('.xgenious-popup-close').on('click', function() {
                $popup.hide();
                // Set a cookie to remember that this popup was closed
                setCookie('popup_closed_' + popupId, 'true', 1); // 1 day
            });
        });
    });
})(jQuery);