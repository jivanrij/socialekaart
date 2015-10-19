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
        
        if (Drupal.settings.gojira.page == 'ownlist') {
            bindOwnlist();
        }

        if (Drupal.settings.gojira.page == 'favorites') {
            bindGojirasearch();
            doSearchCall('favorites', 0);
        }
        
        if (Drupal.settings.gojira.page == 'showlocation') {
            bindGojirasearch();
        }

        if (Drupal.settings.gojira.page == 'suggestlocation') {
            bindLocationFinder();
            bindSuggestlocation();
        }

        if (Drupal.settings.gojira.page == 'locationedit' || Drupal.settings.gojira.page == 'unownedlocationedit') {
            bindLocationFinder();
        }

        if (Drupal.settings.gojira.page == 'locationlist' || Drupal.settings.gojira.page == 'unownedlocationlist') {
            bindLocationlist();
        }

        if (Drupal.settings.gojira.page == 'employeelist') {
            bindEmployeelist();
        }

        if (Drupal.settings.gojira.page == 'settings') {
            bindLocationFinder();
            bindSettings();
        }

        jQuery(window).resize(function () {
            //var mapHeight = jQuery(window).height() - 24;
            jQuery('#map').css('height', getHeightPx());
            jQuery('#map').css('width', '100%');
        });

        if (Drupal.settings.gojira.doSearch != 0) {
            doSearchCall(Drupal.settings.gojira.doSearch, 0);
        } else if (Drupal.settings.gojira.showLoc != 0) {
            doSearchCall(Drupal.settings.gojira.showLoc, 0);
        }
    }
});