jQuery(function () {
    if (Drupal.settings.gojira) {
        if (Drupal.settings.gojira.browserwarning) {
            jQuery(".hidden-sm").css('display','none');
        }
    }
});