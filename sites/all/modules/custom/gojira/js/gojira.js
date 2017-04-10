jQuery(function () {
    if (Drupal.settings.gojira) {

        window.zoomlevel_region = 12;
        window.zoomlevel_street = 16;
        window.zoomlevel_country = 8;

        bindGlobal();
        bindFaq();
        bindMobileMenu();

        if (Drupal.settings.gojira.ask_default_location) {
            askDefaultLocation();
        }

        if (Drupal.settings.gojira.show_tutorial) {
            showTutorial();
        }

        if (Drupal.settings.gojira.page == 'inform') {
            bindInformForm();
        }

        if (Drupal.settings.gojira.page == 'locationset') {
            bindLocationset();
            bindGojirasearch();
        }

        if (Drupal.settings.gojira.page == 'showlocation') {
            bindGojirasearch();
        }

        if (Drupal.settings.gojira.page == 'suggestlocation') {
            bindLocationFinder();
            bindSuggestlocation();
        }

        if (Drupal.settings.gojira.page == 'locationcorrect') {
            bindLocationFinder();
        }

        if (Drupal.settings.gojira.page == 'locationedit' || Drupal.settings.gojira.page == 'unownedlocationedit') {
            bindLocationFinder();
        }

        if (Drupal.settings.gojira.page == 'locationlist' || Drupal.settings.gojira.page == 'unownedlocationlist') {
            bindLocationlist();
        }

        if (Drupal.settings.gojira.page == 'settings') {
            bindLocationFinder();
            bindSettings();
        }

        jQuery(window).resize(function () {
            if (onMobileView()) {
                jQuery('#map').css('height', getHeightPx() + 5);
                jQuery('#map').css('top', (parseInt(jQuery(window).height()) - getHeightPx()) - 5);
                jQuery('#map').css('width', '100%');
            } else {
                jQuery('#map').css('height', getHeightPx());
                jQuery('#map').css('top', 0);
                jQuery('#map').css('width', '100%');
            }
        });
        if (onMobileView()) {
            jQuery(window).trigger('resize');
        }

        if (Drupal.settings.gojira.doSearch != 0) {
            doSearchCall(Drupal.settings.gojira.doSearch, 0);
        } else if (Drupal.settings.gojira.showLoc != 0) {
            doSearchCall(Drupal.settings.gojira.showLoc, 0);
        }
    }
});
