(function($) {
    'use strict';

    $(document).ready(function() {
        $('.xgenious-popup-close').on('click', function() {
            $(this).closest('.xgenious-popup').hide();
        });
    });

})(jQuery);