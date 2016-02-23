<?php

/**
 * Implements the theme hook
 * Every action needs a theme defined
 *
 * @return array
 */
function gojira_theme() {


    return array('welcome' => array('template' => 'templates/welcome'),
        'locationedit' => array('template' => 'templates/locationedit'),
        'locationcorrect' => array('template' => 'templates/locationcorrect'),
        'linkhaweb' => array('template' => 'templates/linkhaweb'),
        'editnote' => array('template' => 'templates/editnote'),
        'inform' => array('template' => 'templates/inform'),
        'informthanks' => array('template' => 'templates/informthanks'),
        'activateuser' => array('template' => 'templates/activateuser'),
        'locationlist' => array('template' => 'templates/locationlist'),
        'suggestlocation' => array('template' => 'templates/suggestlocation'),
        'suggestlocationthanks' => array('template' => 'templates/suggestlocationthanks'),
        'employeelist' => array('template' => 'templates/employeelist'),
        'employeeedit' => array('template' => 'templates/employeeedit'),
        'questions' => array('template' => 'templates/questions'),
        'settings' => array('template' => 'templates/settings'),
        'settings_thanks' => array('template' => 'templates/settings_thanks'),
        'gojirasearch' => array('template' => 'templates/gojirasearch'),
        'showlocation' => array('template' => 'templates/showlocation'),
        'doublelocations' => array('template' => 'templates/doublelocations'),
        'favorites' => array('template' => 'templates/favorites'),
        'passwordthanks' => array('template' => 'templates/passwordthanks'),
//      'gojirareport_suggested_inactive_locations' => array('template' => 'templates/gojirareport_suggested_inactive_locations'),
//      'gojirareport_suggested_active_locations' => array('template' => 'templates/gojirareport_suggested_active_locations'),
//      'gojirareport_double_locations' => array('template' => 'templates/gojirareport_double_locations'),
//      'gojirareport_location_by_tag' => array('template' => 'templates/gojirareport_location_by_tag'),
//      'gojirareport_location_by_category' => array('template' => 'templates/gojirareport_location_by_category'),
        'tools' => array('template' => 'templates/tools'),
        'idealreport' => array('template' => 'templates/idealreport'),
        'docu' => array('template' => 'templates/docu'),
        'websites' => array('template' => 'templates/websites'),
        'configuration' => array('template' => 'templates/configuration'),
        'ownlist' => array('template' => 'templates/ownlist'),
        'conditions' => array('template' => 'templates/conditions'),
        'paymentconditions' => array('template' => 'templates/paymentconditions'),
        'locationcheck' => array('template' => 'templates/locationcheck'),
        'subscribe' => array('template' => 'templates/subscribe'),
        //'practicecheck' => array('template' => 'templates/practicecheck'),
        'idealpay' => array('template' => 'templates/idealpay'),
        'idealreturn' => array('template' => 'templates/idealreturn'),
        'idealfail' => array('template' => 'templates/idealfail'),
        'idealsuccess' => array('template' => 'templates/idealsuccess'),
        'register' => array('template' => 'templates/register'),
        'passwordreset' => array('template' => 'templates/passwordreset')
    );
}

/**
 * Implements the menu hook
 * Every action needs a menu defined
 *
 * @return array
 */
function gojira_menu() {
    $items = array();

    // ideal related actions
    $items['idealcallback'] = array('access arguments' => array('access content'), 'page callback' => 'idealcallback', 'title' => t('Callback page for iDeal'), 'type' => MENU_NORMAL_ITEM);
    $items['subscribe'] = array('access arguments' => array(helper::PERMISSION_DO_PAYMENTS), 'page callback' => 'subscribe', 'title' => t('Subscribe'), 'type' => MENU_NORMAL_ITEM);
    $items['idealpay'] = array('access arguments' => array(helper::PERMISSION_DO_PAYMENTS), 'page callback' => 'idealpay', 'title' => t('Ideal pay'), 'type' => MENU_NORMAL_ITEM);
    $items['idealreturn'] = array('access arguments' => array(helper::PERMISSION_DO_PAYMENTS), 'page callback' => 'idealreturn', 'title' => t('Ideal return'), 'type' => MENU_NORMAL_ITEM);
    $items['idealfail'] = array('access arguments' => array(helper::PERMISSION_DO_PAYMENTS), 'page callback' => 'idealfail', 'title' => t('Ideal fail page'), 'type' => MENU_NORMAL_ITEM);
    $items['idealsuccess'] = array('access arguments' => array(helper::PERMISSION_DO_PAYMENTS), 'page callback' => 'idealsuccess', 'title' => t('Ideal success page'), 'type' => MENU_NORMAL_ITEM);

    // users related
    $items['employee/list'] = array('access arguments' => array(helper::PERMISSION_MANAGE_USERS), 'page callback' => 'employeelist', 'title' => t('Manage employees'), 'type' => MENU_NORMAL_ITEM);
    $items['employee/delete'] = array('access arguments' => array(helper::PERMISSION_MANAGE_USERS), 'page callback' => 'employeedelete', 'title' => t('Delete employer'), 'type' => MENU_NORMAL_ITEM);
    $items['employee/edit'] = array('access arguments' => array(helper::PERMISSION_MANAGE_USERS), 'page callback' => 'employeeedit', 'title' => t('Edit/Add employee'), 'type' => MENU_NORMAL_ITEM);
    $items['linkhaweb'] = array('access arguments' => array(helper::PERMISSION_ACCESS_CONTENT), 'page callback' => 'linkhaweb', 'title' => t('Links a Haweb user to this account'), 'type' => MENU_NORMAL_ITEM);


    $items['inform'] = array('access arguments' => array(helper::PERMISSION_ACCESS_LOCATION_CONTENT), 'page callback' => 'inform', 'title' => t('Inform us about some information that needs changing.'), 'type' => MENU_NORMAL_ITEM);
    $items['informthanks'] = array('access arguments' => array(helper::PERMISSION_ACCESS_LOCATION_CONTENT), 'page callback' => 'informthanks', 'title' => t('Thank you for informing us.'), 'type' => MENU_NORMAL_ITEM);
    $items['gojirasearch'] = array('access arguments' => array(helper::PERMISSION_ACCESS_LOCATION_CONTENT), 'page callback' => 'gojirasearch', 'title' => t('Search page'), 'type' => MENU_NORMAL_ITEM, 'description' => t('Search page'));
    $items['showlocation'] = array('access arguments' => array(helper::PERMISSION_ACCESS_LOCATION_CONTENT), 'page callback' => 'showlocation', 'title' => t('Show location'), 'type' => MENU_NORMAL_ITEM, 'description' => t('Show one location'));
    $items['ownlist'] = array('access arguments' => array(helper::PERMISSION_PERSONAL_LIST), 'page callback' => 'ownlist', 'title' => t('Own list'), 'type' => MENU_NORMAL_ITEM, 'description' => t('The personal list of the docter'));
    $items['favorites'] = array('access arguments' => array(helper::PERMISSION_ACCESS_LOCATION_CONTENT), 'page callback' => 'favorites', 'title' => t('Favorites'), 'type' => MENU_NORMAL_ITEM, 'description' => t('Shows all the favorites'));
    $items['register'] = array('access arguments' => array(helper::PERMISSION_ACCESS_CONTENT), 'page callback' => 'register', 'title' => t('Register'), 'description' => t('Register an account.'));
    //$items['practicecheck'] = array('access arguments' => array(helper::PERMISSION_ACCESS_CONTENT), 'page callback' => 'practicecheck', 'title' => t('Practice check'), 'description' => t('Check if your practice is known in socialekaart.care.'));
    $items['conditions'] = array('access arguments' => array(helper::PERMISSION_ACCESS_CONTENT), 'page callback' => 'conditions', 'title' => t('Terms & Conditions'), 'description' => t('Terms & conditions page.'));
    $items['paymentconditions'] = array('access arguments' => array(helper::PERMISSION_ACCESS_CONTENT), 'page callback' => 'paymentconditions', 'title' => t('Payment terms & conditions'), 'description' => t('Payment terms & conditions page.'));
    $items['locationcheck'] = array('access arguments' => array(helper::PERMISSION_ACCESS_CONTENT), 'page callback' => 'locationcheck', 'title' => t('Check location'), 'description' => t('Check the availibility of a location.'));
    $items['welcome'] = array('access arguments' => array(helper::PERMISSION_ACCESS_CONTENT), 'page callback' => 'welcome', 'title' => t('Welcome'), 'type' => MENU_NORMAL_ITEM);
    $items['registered'] = array('access arguments' => array(helper::PERMISSION_ACCESS_CONTENT), 'page callback' => 'registered', 'title' => t('Registered thanks page'), 'type' => MENU_NORMAL_ITEM);
    $items['passwordreset'] = array('access arguments' => array(helper::PERMISSION_ACCESS_CONTENT), 'page callback' => 'passwordreset', 'title' => t('Password reset page'), 'type' => MENU_NORMAL_ITEM);
    $items['passwordmailsend'] = array('access arguments' => array(helper::PERMISSION_ACCESS_CONTENT), 'page callback' => 'passwordmailsend', 'title' => t('Thanks page after password rest mail is send'), 'type' => MENU_NORMAL_ITEM);
    $items['location/edit'] = array('access arguments' => array(helper::PERMISSION_MANAGE_MULTIPLE_LOCATIONS), 'page callback' => 'locationedit', 'title' => t('Edit location page'), 'type' => MENU_NORMAL_ITEM);
    $items['location/correct'] = array('access arguments' => array(helper::PERMISSION_CORRECT_EXISTING_LOCATIONS), 'page callback' => 'locationcorrect', 'title' => t('Change location'), 'type' => MENU_NORMAL_ITEM);
    $items['location/list'] = array('access arguments' => array(helper::PERMISSION_MANAGE_MULTIPLE_LOCATIONS), 'page callback' => 'locationlist', 'title' => t('Manage locations'), 'type' => MENU_NORMAL_ITEM);
    $items['passwordthanks'] = array('access arguments' => array(helper::PERMISSION_ACCESS_CONTENT), 'page callback' => 'passwordthanks', 'title' => t('Password reset send'), 'type' => MENU_NORMAL_ITEM);
    $items['suggestlocation'] = array('access arguments' => array(helper::PERMISSION_MODERATE_LOCATION_CONTENT), 'page callback' => 'suggestlocation', 'title' => t('Add a missing location.'), 'type' => MENU_NORMAL_ITEM);
    $items['suggestlocationthanks'] = array('access arguments' => array(helper::PERMISSION_MODERATE_LOCATION_CONTENT), 'page callback' => 'suggestlocationthanks', 'title' => t('Thanks for adding a location.'), 'type' => MENU_NORMAL_ITEM);
    $items['location/delete'] = array('access arguments' => array(helper::PERMISSION_MANAGE_MULTIPLE_LOCATIONS), 'page callback' => 'locationdelete', 'title' => t('Delete location'), 'type' => MENU_NORMAL_ITEM);
    $items['settings'] = array('access arguments' => array(helper::PERMISSION_ACCESS_LOCATION_CONTENT), 'page callback' => 'settings', 'title' => t('Settings'), 'type' => MENU_NORMAL_ITEM);
    $items['settings_thanks'] = array('access arguments' => array(helper::PERMISSION_ACCESS_LOCATION_CONTENT), 'page callback' => 'settings_thanks', 'title' => t('Settings thanks'), 'type' => MENU_NORMAL_ITEM);
    $items['questions'] = array('access arguments' => array(helper::PERMISSION_ACCESS_LOCATION_CONTENT), 'page callback' => 'questions', 'title' => t('Questions'), 'type' => MENU_NORMAL_ITEM);
    $items['editnote'] = array('access arguments' => array(helper::PERMISSION_MODERATE_LOCATION_CONTENT), 'page callback' => 'editnote', 'title' => t('Edit note'), 'type' => MENU_NORMAL_ITEM);

    // AJAX
    $items['ajax/search'] = array('access arguments' => array(helper::PERMISSION_ACCESS_LOCATION_CONTENT), 'page callback' => 'search', 'title' => t('Search'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/locationtags'] = array('access arguments' => array(helper::PERMISSION_ACCESS_LOCATION_CONTENT), 'page callback' => 'locationtags', 'title' => t('Set tags'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/switchfavorites'] = array('access arguments' => array(helper::PERMISSION_PERSONAL_LIST), 'page callback' => 'switchfavorites', 'title' => t('Switch only show favorites'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/postcodesuggest'] = array('access arguments' => array(helper::PERMISSION_ACCESS_LOCATION_CONTENT), 'page callback' => 'postcodesuggest', 'title' => t('Postcode suggest'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/checklocation'] = array('access arguments' => array(helper::PERMISSION_ACCESS_LOCATION_CONTENT), 'page callback' => 'checklocation', 'title' => t('Check location info'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/showtutorial'] = array('access arguments' => array(helper::PERMISSION_ACCESS_LOCATION_CONTENT), 'page callback' => 'showtutorial', 'title' => t('Show the tutorial'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/picklocation'] = array('access arguments' => array(helper::PERMISSION_ACCESS_LOCATION_CONTENT), 'page callback' => 'picklocation', 'title' => t('Pick a default location'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/setfavorite'] = array('access arguments' => array(helper::PERMISSION_PERSONAL_LIST), 'page callback' => 'setfavorite', 'title' => t('Switch favorite.'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/likelabel'] = array('access arguments' => array(helper::PERMISSION_MODERATE_LOCATION_CONTENT), 'page callback' => 'likelabel', 'title' => t('Likelabel'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/unlikelabel'] = array('access arguments' => array(helper::PERMISSION_MODERATE_LOCATION_CONTENT), 'page callback' => 'unlikelabel', 'title' => t('Unlikelabel'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/savenewlabel'] = array('access arguments' => array(helper::PERMISSION_MODERATE_LOCATION_CONTENT), 'page callback' => 'savenewlabel', 'title' => t('Save new label'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/removelabel'] = array('access arguments' => array(helper::PERMISSION_MODERATE_LOCATION_CONTENT), 'page callback' => 'removelabel', 'title' => t('Save new label'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/switchglobalsearch'] = array('access arguments' => array(helper::PERMISSION_ACCESS_LOCATION_CONTENT), 'page callback' => 'switchglobalsearch', 'title' => t('Switch global search'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/singlesearchresult'] = array('access arguments' => array(helper::PERMISSION_ACCESS_LOCATION_CONTENT), 'page callback' => 'singlesearchresult', 'title' => t('A single search result'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/locationinfo'] = array('access arguments' => array(helper::PERMISSION_ACCESS_LOCATION_CONTENT), 'page callback' => 'locationinfo', 'title' => t('Get basic location info'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/reportdouble'] = array('access arguments' => array(helper::PERMISSION_ACCESS_LOCATION_CONTENT), 'page callback' => 'reportdouble', 'title' => t('Report double locations'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/doublehandler_checked'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'doublehandler_checked', 'title' => t('Make double location checked'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/doublehandler_merge'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'doublehandler_merge', 'title' => t('Merge double location'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/doublehandler_remove'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'doublehandler_remove', 'title' => t('Remove double location'), 'type' => MENU_NORMAL_ITEM);
    
    $items['api/locations'] = array('access arguments' => array('access content'), 'page callback' => 'api_locations', 'title' => t('Gives some locations in JSON'), 'type' => MENU_NORMAL_ITEM);

    // ADMIN
    $items['admin/config/system/docu'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'docu', 'title' => t('Gojira documentation'), 'type' => MENU_NORMAL_ITEM, 'description' => t('Documentation of Gojira.'));
    $items['admin/config/system/websites'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'websites', 'title' => t('Gojira documentation'), 'type' => MENU_NORMAL_ITEM, 'description' => t('List of websites'));
    $items['admin/config/system/doublelocations'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'doublelocations', 'title' => t('Double locations in the system'), 'type' => MENU_NORMAL_ITEM);
    $items['admin/config/system/idealreport'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'idealreport', 'title' => t('SocialeKaart.care ideal betalingen overzicht'), 'type' => MENU_NORMAL_ITEM, 'description' => t('Report of the payments'));
    $items['admin/config/system/gojiratools'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'tools', 'title' => t('SocialeKaart.care tools'), 'type' => MENU_NORMAL_ITEM, 'description' => t('Several tools for Gojira.'));
    $items['admin/config/system/gojiraconfiguration'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'configuration', 'title' => t('SocialeKaart.care configuratie'), 'type' => MENU_NORMAL_ITEM, 'description' => t('Several configurations for Gojira.'));
    $items['admin/config/system/gojiraactivateuser'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'activateuser', 'title' => t('Gojira activate user'), 'type' => MENU_NORMAL_ITEM, 'description' => t('Activate a user trough this form.'));
    return $items;
}
