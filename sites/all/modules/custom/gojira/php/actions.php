<?php

/**
 * Implements the theme hook
 * Every action needs a theme defined
 *
 * @return array
 */
function gojira_theme()
{
    return array('welcome' => array('template' => 'templates/welcome'),
        'grouplist' => array('template' => 'templates/grouplist'),
        'locationcrud' => array('template' => 'templates/locationcrud'),
        'locationsetlist' => array('template' => 'templates/locationsetlist'),
        'groupdetail' => array('template' => 'templates/groupdetail'),
        'locationedit' => array('template' => 'templates/locationedit'),
        'locationcorrect' => array('template' => 'templates/locationcorrect'),
        'linkhaweb' => array('template' => 'templates/linkhaweb'),
        'editnote' => array('template' => 'templates/editnote'),
        'error' => array('template' => 'templates/error'),
        'inform' => array('template' => 'templates/inform'),
        'introduction' => array('template' => 'templates/introduction'),
        'informthanks' => array('template' => 'templates/informthanks'),
        'activateuser' => array('template' => 'templates/activateuser'),
        'locationlist' => array('template' => 'templates/locationlist'),
        'suggestlocation' => array('template' => 'templates/suggestlocation'),
        'suggestlocationthanks' => array('template' => 'templates/suggestlocationthanks'),
        'questions' => array('template' => 'templates/questions'),
        'settings' => array('template' => 'templates/settings'),
        'settings_thanks' => array('template' => 'templates/settings_thanks'),
        'gojirasearch' => array('template' => 'templates/gojirasearch'),
        'showlocation' => array('template' => 'templates/showlocation'),
        'doublelocations' => array('template' => 'templates/doublelocations'),
        'favorites' => array('template' => 'templates/favorites'),
        'passwordthanks' => array('template' => 'templates/passwordthanks'),
        'tools' => array('template' => 'templates/tools'),
        'idealreport' => array('template' => 'templates/idealreport'),
        'docu' => array('template' => 'templates/docu'),
        'websites' => array('template' => 'templates/websites'),
        'configuration' => array('template' => 'templates/configuration'),
        'conditions' => array('template' => 'templates/conditions'),
        'paymentconditions' => array('template' => 'templates/paymentconditions'),
        'locationcheck' => array('template' => 'templates/locationcheck'),
        'subscribe' => array('template' => 'templates/subscribe'),
        'idealpay' => array('template' => 'templates/idealpay'),
        'idealreturn' => array('template' => 'templates/idealreturn'),
        'idealfail' => array('template' => 'templates/idealfail'),
        'idealsuccess' => array('template' => 'templates/idealsuccess'),
        'register' => array('template' => 'templates/register'),
        'crudtest' => array('template' => 'templates/crudtest'),
        'passwordreset' => array('template' => 'templates/passwordreset')
    );
}

/**
 * Implements the menu hook
 * Every action needs a menu defined
 *
 * @return array
 */
function gojira_menu()
{
    $items = array();

    // ideal related actions
    $items['idealcallback'] = array('access arguments' => array('access content'), 'page callback' => 'idealcallback', 'title' => t('Callback page for iDeal'), 'type' => MENU_NORMAL_ITEM);
    $items['subscribe'] = array('access arguments' => array(helper::PERM_HUISARTS_PAYMENT), 'page callback' => 'subscribe', 'title' => t('Subscribe'), 'type' => MENU_NORMAL_ITEM);
    $items['idealpay'] = array('access arguments' => array(helper::PERM_HUISARTS_PAYMENT), 'page callback' => 'idealpay', 'title' => t('Ideal pay'), 'type' => MENU_NORMAL_ITEM);
    $items['idealreturn'] = array('access arguments' => array(helper::PERM_HUISARTS_PAYMENT), 'page callback' => 'idealreturn', 'title' => t('Ideal return'), 'type' => MENU_NORMAL_ITEM);
    $items['idealfail'] = array('access arguments' => array(helper::PERM_HUISARTS_PAYMENT), 'page callback' => 'idealfail', 'title' => t('Ideal fail page'), 'type' => MENU_NORMAL_ITEM);
    $items['idealsuccess'] = array('access arguments' => array(helper::PERM_HUISARTS_PAYMENT), 'page callback' => 'idealsuccess', 'title' => t('Ideal success page'), 'type' => MENU_NORMAL_ITEM);

    // users related
    $items['linkhaweb'] = array('access arguments' => array('access content'), 'page callback' => 'linkhaweb', 'title' => t('Links a Haweb user to this account'), 'type' => MENU_NORMAL_ITEM);

    // crud pages
    $items['locationcrud'] = array('access arguments' => array(helper::PERM_CORRECT_LOCATION), 'page callback' => 'locationcrud', 'title' => t('Edit location'), 'type' => MENU_NORMAL_ITEM);

    // locationset
    $items['locationsetlist'] = array('access arguments' => array(helper::PERM_MANAGE_MAPS), 'page callback' => 'locationsetlist', 'title' => 'Alle beschikbare kaarten', 'type' => MENU_NORMAL_ITEM);

    $items['inform'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'inform', 'title' => t('Inform us about some information that needs changing.'), 'type' => MENU_NORMAL_ITEM);
    $items['informthanks'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'informthanks', 'title' => t('Thank you for informing us.'), 'type' => MENU_NORMAL_ITEM);
    $items['gojirasearch'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'gojirasearch', 'title' => t('Search page'), 'type' => MENU_NORMAL_ITEM, 'description' => t('Search page'));
    $items['showlocation'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'showlocation', 'title' => t('Show location'), 'type' => MENU_NORMAL_ITEM, 'description' => t('Show one location'));
    $items['ownlist'] = array('access arguments' => array(helper::PERM_MY_MAP), 'page callback' => 'ownlist', 'title' => t('Own list'), 'type' => MENU_NORMAL_ITEM, 'description' => t('The personal list of the docter'));
    $items['favorites'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'favorites', 'title' => t('Favorites'), 'type' => MENU_NORMAL_ITEM, 'description' => t('Shows all the favorites'));
    $items['register'] = array('access arguments' => array('access content'), 'page callback' => 'register', 'title' => t('Register'), 'description' => t('Register an account.'));
    $items['conditions'] = array('access arguments' => array('access content'), 'page callback' => 'conditions', 'title' => 'Algemene voorwaarden', 'description' => t('Terms & conditions page.'));
    $items['paymentconditions'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'paymentconditions', 'title' => t('Payment terms & conditions'), 'description' => t('Payment terms & conditions page.'));
    $items['locationcheck'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'locationcheck', 'title' => t('Check location'), 'description' => t('Check the availibility of a location.'));
    $items['welcome'] = array('access arguments' => array('access content'), 'page callback' => 'welcome', 'title' => t('Welcome'), 'type' => MENU_NORMAL_ITEM);
    $items['introduction'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'introduction', 'title' => t('Introduction'), 'type' => MENU_NORMAL_ITEM);
    $items['registered'] = array('access arguments' => array('access content'), 'page callback' => 'registered', 'title' => t('Registered thanks page'), 'type' => MENU_NORMAL_ITEM);
    $items['passwordreset'] = array('access arguments' => array('access content'), 'page callback' => 'passwordreset', 'title' => t('Password reset page'), 'type' => MENU_NORMAL_ITEM);
    $items['passwordmailsend'] = array('access arguments' => array('access content'), 'page callback' => 'passwordmailsend', 'title' => t('Thanks page after password rest mail is send'), 'type' => MENU_NORMAL_ITEM);
    $items['location/edit'] = array('access arguments' => array(helper::PERM_HUISARTS_MORE_PRACTICES), 'page callback' => 'locationedit', 'title' => t('Edit location page'), 'type' => MENU_NORMAL_ITEM);
    $items['location/correct'] = array('access arguments' => array(helper::PERM_CORRECT_LOCATION), 'page callback' => 'locationcorrect', 'title' => t('Change location'), 'type' => MENU_NORMAL_ITEM);
    $items['location/list'] = array('access arguments' => array(helper::PERM_HUISARTS_MORE_PRACTICES), 'page callback' => 'locationlist', 'title' => t('Manage locations'), 'type' => MENU_NORMAL_ITEM);
    $items['passwordthanks'] = array('access arguments' => array('access content'), 'page callback' => 'passwordthanks', 'title' => t('Password reset send'), 'type' => MENU_NORMAL_ITEM);
    $items['suggestlocation'] = array('access arguments' => array(helper::PERM_ADD_LOCATION), 'page callback' => 'suggestlocation', 'title' => t('Add a missing location.'), 'type' => MENU_NORMAL_ITEM);
    $items['suggestlocationthanks'] = array('access arguments' => array(helper::PERM_ADD_LOCATION), 'page callback' => 'suggestlocationthanks', 'title' => t('Thanks for adding a location.'), 'type' => MENU_NORMAL_ITEM);
    $items['location/delete'] = array('access arguments' => array(helper::PERM_HUISARTS_MORE_PRACTICES), 'page callback' => 'locationdelete', 'title' => t('Delete location'), 'type' => MENU_NORMAL_ITEM);
    $items['settings'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'settings', 'title' => t('Settings'), 'type' => MENU_NORMAL_ITEM);
    $items['settings_thanks'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'settings_thanks', 'title' => t('Settings thanks'), 'type' => MENU_NORMAL_ITEM);
    $items['questions'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'questions', 'title' => t('Questions'), 'type' => MENU_NORMAL_ITEM);
    $items['editnote'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'editnote', 'title' => t('Edit note'), 'type' => MENU_NORMAL_ITEM);
    $items['error'] = array('access arguments' => array('access content'), 'page callback' => 'error', 'title' => t('Error page'), 'type' => MENU_NORMAL_ITEM);

    // AJAX
    $items['ajax/search'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'search', 'title' => t('Search'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/locationtags'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'locationtags', 'title' => t('Set tags'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/postcodesuggest'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'postcodesuggest', 'title' => t('Postcode suggest'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/checklocation'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'checklocation', 'title' => t('Check location info'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/showtutorial'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'showtutorial', 'title' => t('Show the tutorial'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/picklocation'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'picklocation', 'title' => t('Pick a default location'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/setfavorite'] = array('access arguments' => array(helper::PERM_MY_MAP), 'page callback' => 'setfavorite', 'title' => t('Switch favorite.'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/setonlocationset'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'setonlocationset', 'title' => t('Link a location on a locationset.'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/likelabel'] = array('access arguments' => array(helper::PERM_HUISARTS_LABELS), 'page callback' => 'likelabel', 'title' => t('Likelabel'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/unlikelabel'] = array('access arguments' => array(helper::PERM_HUISARTS_LABELS), 'page callback' => 'unlikelabel', 'title' => t('Unlikelabel'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/savenewlabel'] = array('access arguments' => array(helper::PERM_HUISARTS_LABELS), 'page callback' => 'savenewlabel', 'title' => t('Save new label'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/removelabel'] = array('access arguments' => array(helper::PERM_HUISARTS_LABELS), 'page callback' => 'removelabel', 'title' => t('Save new label'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/singlesearchresult'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'singlesearchresult', 'title' => t('A single search result'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/locationinfo'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'locationinfo', 'title' => t('Get basic location info'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/reportdouble'] = array('access arguments' => array(helper::PERM_BASIC_ACCESS), 'page callback' => 'reportdouble', 'title' => t('Report double locations'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/doublehandler_checked'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'doublehandler_checked', 'title' => t('Make double location checked'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/doublehandler_merge'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'doublehandler_merge', 'title' => t('Merge double location'), 'type' => MENU_NORMAL_ITEM);
    $items['ajax/doublehandler_remove'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'doublehandler_remove', 'title' => t('Remove double location'), 'type' => MENU_NORMAL_ITEM);

    $items['api/locations'] = array('access arguments' => array('access content'), 'page callback' => 'api_locations', 'title' => t('Gives some locations in JSON'), 'type' => MENU_NORMAL_ITEM);
    $items['api/mapsearch'] = array('access arguments' => array('access content'), 'page callback' => 'api_mapsearch', 'title' => t('External search a specific map'), 'type' => MENU_NORMAL_ITEM);

    // ADMIN
    $items['admin/config/system/docu'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'docu', 'title' => t('Gojira documentation'), 'type' => MENU_NORMAL_ITEM, 'description' => t('Documentation of Gojira.'));
    $items['admin/config/system/websites'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'websites', 'title' => t('Gojira documentation'), 'type' => MENU_NORMAL_ITEM, 'description' => t('List of websites'));
    $items['admin/config/system/doublelocations'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'doublelocations', 'title' => t('Double locations in the system'), 'type' => MENU_NORMAL_ITEM);
    $items['admin/config/system/idealreport'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'idealreport', 'title' => t('SocialeKaart.care ideal betalingen overzicht'), 'type' => MENU_NORMAL_ITEM, 'description' => t('Report of the payments'));
    $items['admin/config/system/gojiratools'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'tools', 'title' => t('SocialeKaart.care tools'), 'type' => MENU_NORMAL_ITEM, 'description' => t('Several tools for Gojira.'));
    $items['admin/config/system/gojiraconfiguration'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'configuration', 'title' => t('SocialeKaart.care configuratie'), 'type' => MENU_NORMAL_ITEM, 'description' => t('Several configurations for Gojira.'));
    $items['admin/config/system/gojiraactivateuser'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'activateuser', 'title' => t('Gojira activate user'), 'type' => MENU_NORMAL_ITEM, 'description' => t('Activate a user trough this form.'));
    $items['admin/config/system/grouplist'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'grouplist', 'title' => t('Gojira group info'), 'type' => MENU_NORMAL_ITEM, 'description' => t('A list of groupes with a link to a detail page.'));
    $items['admin/config/system/groupdetail'] = array('access arguments' => array('administer site configuration'), 'page callback' => 'groupdetail', 'title' => t('Gojira group detail'), 'type' => MENU_NORMAL_ITEM, 'description' => t('Detail page of a group.'));
    return $items;
}
