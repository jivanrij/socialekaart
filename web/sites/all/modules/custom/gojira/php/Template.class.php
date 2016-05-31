<?php

class Template {

    const VIEWTYPE_AJAX = 'ajax';
    const VIEWTYPE_ERROR = 'error';
    const VIEWTYPE_FRONT = 'front';
    const VIEWTYPE_CRUD = 'crud'; // shows no title on global level
    const VIEWTYPE_CRUD_TITLE = 'crud_title'; // shows title on global level
    const VIEWTYPE_BIG = 'big'; // big dynamic pages, same a contant_big but with the page title in it
    const VIEWTYPE_SEARCH = 'search';
    const VIEWTYPE_BIG_TITLE = 'big_title'; // random content on a wide page without a global title
    const VIEWTYPE_LOCATIONSSET = 'locationsset'; // just content, with a title

    /**
     * Gives you the type of rendering needed for the page
     *
     * @return string
     */

    public static function getView() {

        global $user;

        if ($_GET['q'] == 'error') {
            return Template::VIEWTYPE_FRONT;
        }

        if (self::statusNotFound() || self::statusForbidden()) {
            if ($user->uid == 0) {
                return Template::VIEWTYPE_FRONT;
            } else {
                return Template::VIEWTYPE_CRUD_TITLE;
            }
        }

        $front_pages[] = 'passwordreset';
        $front_pages[] = 'onesignin/response';
        $front_pages[] = 'user/blank';
        $front_pages[] = 'user';
        $front_pages[] = 'register';
        $front_pages[] = 'conditions';
        $front_pages[] = '/user';
        $front_pages[] = 'practicecheck';
        foreach ($front_pages as $url) {
            if ($_GET['q'] == $url) {
                return Template::VIEWTYPE_FRONT;
            }
        }
        if (strstr($_GET['q'], 'user/reset/')) { //$_GET['q'] == 'user/reset/83/1426884583/45N9962GK4v-9OKxhSVSIrYF_FY_Zeh3i5yrKatcU9w'){
            return Template::VIEWTYPE_FRONT;
        }
        if (!user_access(helper::PERMISSION_ACCESS_CONTENT)) {
            return Template::VIEWTYPE_FRONT;
        }

        if ($user->uid == 0) {
            return Template::VIEWTYPE_FRONT;
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return Template::VIEWTYPE_AJAX;
        }
        if ($_GET['q'] == 'idealcallback') {
            return Template::VIEWTYPE_AJAX;
        }

        // Check to see if $user has the administrator role.
        if (strstr($_GET['q'], 'user') && isset($_GET['pass-reset-token']) && in_array('authenticated user', array_values($user->roles))) {
            drupal_add_js(array('gojira' => array('page' => 'passwordresetform')), 'setting');
            return Template::VIEWTYPE_CRUD;
        }
        $crud_pages = array();
        $crud_pages[] = 'location/list';
        $crud_pages[] = 'location/edit';
        $crud_pages[] = 'location/correct';
        $crud_pages[] = 'settings';
        $crud_pages[] = 'settings_thanks';
        $crud_pages[] = 'passwordthanks';
        $crud_pages[] = 'unownedlocation/list';
        $crud_pages[] = 'unownedlocation/edit';
        $crud_pages[] = 'employee/list';
        $crud_pages[] = 'employee/edit';
        $crud_pages[] = 'change';
        $crud_pages[] = 'suggestlocation';
        $crud_pages[] = 'suggestlocationthanks';
        $crud_pages[] = 'wronginfo';
        $crud_pages[] = 'inform';
        $crud_pages[] = 'editnote';
        $crud_pages[] = 'informthanks';
        $crud_pages[] = 'subscribe';
        $crud_pages[] = 'idealpay';
        $crud_pages[] = 'idealreturn';
        $crud_pages[] = 'idealfail';
        $crud_pages[] = 'linkhaweb';
        $crud_pages[] = 'idealsuccess';

        foreach ($crud_pages as $url) {
            if ($_GET['q'] == $url) {
                return Template::VIEWTYPE_CRUD;
            }
        }
        if (strstr($_GET['q'], 'user/') && strstr($_GET['q'], '/edit')) { // user/UID/edit
            return Template::VIEWTYPE_CRUD;
        }
        if (arg(0) == 'node' && is_numeric(arg(1))) {
            $nid = arg(1);
            $node = node_load($nid);
            if (isset($node->type)) {
                if ($node->type == GojiraSettings::CONTENT_TYPE_SET_OF_LOCATIONS) {
                    return Template::VIEWTYPE_LOCATIONSSET;
                }
                if ($node->type == GojiraSettings::CONTENT_TYPE_PAGE) {
                    return Template::VIEWTYPE_CRUD_TITLE;
                }
                if ($node->type == GojiraSettings::CONTENT_TYPE_PAGE_PUBLIC) {
                    return Template::VIEWTYPE_FRONT;
                }
                if ($node->type == GojiraSettings::CONTENT_TYPE_PAGE_BIG) {
                    return Template::VIEWTYPE_BIG_TITLE;
                }
                if ($node->type == GojiraSettings::CONTENT_TYPE_CATEGORY) {
                    return Template::VIEWTYPE_SEARCH;
                }
                if ($node->type == GojiraSettings::CONTENT_TYPE_TEXT) {
                    return Template::VIEWTYPE_SEARCH;
                }
            }
        }

        $locationsset_pages[] = 'ownlist';
        foreach ($locationsset_pages as $url) {
            if ($_GET['q'] == $url) {
                return Template::VIEWTYPE_LOCATIONSSET;
            }
        }

        // custom pages that need to be rendered like the page big nodes
        $big_pages[] = 'locationcheck';
        $big_pages[] = 'conditions';
        $big_pages[] = 'paymentconditions';
        $big_pages[] = 'questions';
        foreach ($big_pages as $url) {
            if ($_GET['q'] == $url) {
                return Template::VIEWTYPE_BIG;
            }
        }

        if (strstr($_GET['q'], 'user/') && $_GET['q'] != 'user/logout' && !strstr($_GET['q'], 'admin/')) {
            helper::redirectTo404();
        }



        return Template::VIEWTYPE_SEARCH;

        // JRI todo we need to do something with a forbidden redirect for pages the user can't get to.
    }

    /**
     * Gives true if the current splashpage needs to render the content
     *
     * @return boolean
     */
    public static function getFrontPage() {

        if (self::statusNotFound() || self::statusForbidden()) {
            return 'front/page.tpl.php';
        }

        if ($_GET['q'] == 'passwordmailsend') {
            return 'front/passwordmailsend.tpl.php';
        }

        if ($_GET['q'] == 'error') {
            return 'front/error.tpl.php';
        }

        if ($_GET['q'] == 'passwordreset') {
            return 'front/passwordreset.tpl.php';
        }
        if ($_GET['q'] == 'register') {
            return 'front/register.tpl.php';
        }
        if ($_GET['q'] == 'practicecheck') {
            return 'front/page.tpl.php';
        }
        if ($_GET['q'] == 'conditions') {
            return 'front/page.tpl.php';
        }

        if ($_GET['q'] == 'registered') {
            return 'front/registered.tpl.php';
        }

        if (strstr($_GET['q'], 'user/reset/')) {
            return 'front/resetlink.tpl.php';
        }

        if (strstr($_GET['q'], 'user/login')) {
            return 'front/user_login.tpl.php';
        }

        if ($_GET['q'] == 'user' || $_GET['q'] == '/user') {
            return 'front/page.tpl.php';
        }

        if (strstr($_GET['q'], 'introduction')) {
            return 'front/introduction.tpl.php';
        }

        if (arg(0) == 'node' && is_numeric(arg(1))) {
            $nid = arg(1);
            $node = node_load($nid);
            if ($node->type == GojiraSettings::CONTENT_TYPE_PAGE_PUBLIC) {
                return 'front/page.tpl.php';
            }
        }

        return 'front/login.tpl.php';
    }

    /**
     * Tells you if you need to include the frontend css/js files for the map
     *
     * @return boolean
     */
    public static function shouldWeIncludeMapFrontendFiles() {
        if (isset($_GET['pass-reset-token'])) {
            return true;
        }
        if (Template::getView() == Template::VIEWTYPE_FRONT) {
            return false;
        }
        if (path_is_admin(current_path())) {
            return false;
        }
        return true;
    }

    /**
     * Returns true if the page is 404
     *
     * @return boolean
     */
    public static function statusNotFound() {
        $aStatus = drupal_get_http_header("status");
        if ($aStatus == '404 Not Found') {
            return true;
        }
    }

    /**
     * Returns true if the page is 403
     *
     * @return boolean
     */
    public static function statusForbidden() {
        $aStatus = drupal_get_http_header("status");
        if ($aStatus == '403 Forbidden') {
            return true;
        }
    }

    /**
     * Get the correct class for the body so the mobile css can hook on to it
     *
     * @return string
     */
    public static function getMobileType() {

        // exceptions
        if ($_GET['q'] == 'ownlist' || $_GET['q'] == 'mijn-kaart') {
            return 'mobile-search';
        }
        if ($_GET['q'] == 'questions') {
            return 'mobile-form';
        }
        if ($_GET['q'] == 'conditions') {
            return 'mobile-form';
        }



        // default return values
        switch (Template::getView()) {
            case Template::VIEWTYPE_AJAX:
                return '';
                break;
            case Template::VIEWTYPE_FRONT:
                return '';
                break;
            case Template::VIEWTYPE_CRUD:
                return 'mobile-form';
                break;
            case Template::VIEWTYPE_CRUD_TITLE:
                return 'mobile-form';
                break;
            case Template::VIEWTYPE_SEARCH:
                return 'mobile-search';
                break;
            case Template::VIEWTYPE_BIG:
                return 'mobile-content';
                break;
            case Template::VIEWTYPE_BIG_TITLE:
                return 'mobile-content';
                break;
        }
    }

}
