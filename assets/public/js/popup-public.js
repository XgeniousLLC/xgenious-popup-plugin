(function($) {
    'use strict';

    $(document).ready(function() {
        $('.xgenious-popup').each(function() {
            var $popup = $(this);
            var popupId = $popup.data('popup-id');
            var delay = $popup.data('delay') * 1000;
            var autoClose = $popup.data('auto-close');
            var autoCloseTime = $popup.data('auto-close-time') * 1000;
            var endTime = $popup.data('end-time');

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

            $popup.find('.xgenious-popup-close').on('click', function() {
                $popup.hide();
            });
        });
    });

})(jQuery);