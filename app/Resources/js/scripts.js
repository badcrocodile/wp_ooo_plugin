(function($) {
    $(document).ready(function() {
        disableACFPopup();
    });

    function disableACFPopup() {
        // disable the ACF js navigate away pop up
        acf.unload.active = false;
    }

})(jQuery);