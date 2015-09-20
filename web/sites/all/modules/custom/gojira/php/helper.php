<?php

/**
 * This class implemente some functions as helper functions.
 * Also defines the names of the permissions and roles in gojira.
 */
class helper {

    // Permissions
    const PERMISSION_ACCESS_CONTENT = 'access content';
    const PERMISSION_GOJIRA_ADMIN = 'administer gojira';
    const PERMISSION_ACCESS_LOCATION_CONTENT = 'gojira access location info';
    const PERMISSION_MODERATE_LOCATION_CONTENT = 'gojira moderate location info';
    const PERMISSION_HIDE_ADDS = 'remove adds';
    const PERMISSION_MANAGE_USERS = 'manage gojira group users';
    const PERMISSION_DO_PAYMENTS = 'do payments for a gojira group';
    const PERMISSION_MULTIPLE_LOCATIONS = 'multiple locations';
    const PERMISSION_MANAGE_MULTIPLE_LOCATIONS = 'manage multiple locations';
    const PERMISSION_SHOW_DEBUG = 'show debug data';
    // we have 3 types of users:
    // 1. the original master of the group, the practitioner
    // 2. a user added by the practitioner that can change data
    // 3. a user added by the practitioner that can not change data
    // all types of users can also have the subscribed role, this role gives the users the subscribed functionality
    // Roles
    const ROLE_AUTHENTICATED = 'authenticated user';
    const ROLE_EMPLOYEE = 'gojira employee (subscribed)';
    const ROLE_EMPLOYER = 'gojira employer (subscribed)';
    const ROLE_EMPLOYER_MASTER = 'gojira master employer';
    const ROLE_SUBSCRIBED_MASTER = 'gojira master employer (subscribed)';

    public static function redirectTo404() {
        header('Location: /404');
        exit;
    }

    /**
     * Gives you the title of the current node
     * 
     * @return string
     */
    public static function getTitleCurrentNode() {
        if (arg(0) == 'node' && is_numeric(arg(1))) {
            $nid = arg(1);
            $node = node_load($nid);
            return $node->title;
        }
        return '';
    }

    /**
     * Get's you the type of the current node
     * 
     * @return string
     */
    public static function getCurrentNodeType() {
        if (arg(0) == 'node' && is_numeric(arg(1))) {
            $nid = arg(1);
            $node = node_load($nid);
            return $node->type;
        }
        return false;
    }

    public static function restoreBackup($amount, $cron = false){
        $rLocations = db_query("select id, source, title, telephone, city, street, number, postcode, category, email, longitude, latitude, url, labels from practices_backup where import_it = 1 limit {$amount}");
        foreach ($rLocations as $o) {
            $aLabels = explode('|', $o->labels);
            if(!$aLabels){
                $aLabels = array();
            }
            Importer::restoreLocationFromBackup($o->source, $o->title, $o->telephone, $o->city, $o->street, $o->number, $o->postcode, $o->category, $o->email, $o->longitude, $o->latitude, $o->url, $aLabels, $o->id);
        }
        if(!$cron){
            drupal_set_message(t('Restored some locations!'), 'status');
            header('Location: /?q=admin/config/system/gojiratools');
            exit;
        }
    }
    
    /**
     * Get's a array with words and removes all the blacklisted words
     * 
     * @param type $words 
     * @return type
     */
    public static function cleanArrayWithBlacklist($words) {
        $blacklist = explode(',', variable_get('gojira_blacklist_search_words'));
        $clean = array();
        foreach ($words as $word) {
            if (!in_array($word, $blacklist)) {
                $clean[] = $word;
            }
        }
        return $clean;
    }

    /**
     * Tells you if a string is on the blacklist
     * 
     * @param String $sString 
     * @return boolean
     */
    public static function inBlacklist($sString) {
        $aBlacklist = explode(',', variable_get('gojira_blacklist_search_words'));
        $aClean = array();
        if (in_array($sString, $aBlacklist)) {
            return true;
        }
        return false;
    }

    /**
     * Get's you the IE version is IE is used
     * 
     * @return int
     */
    public static function getIEVersion() {
        $matches = 0;
        if(isset($_SERVER['HTTP_USER_AGENT'])){
            preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $matches);
        }
        if (count($matches) < 2) {
            if(isset($_SERVER['HTTP_USER_AGENT'])){
                preg_match('/Trident\/\d{1,2}.\d{1,2}; rv:([0-9]*)/', $_SERVER['HTTP_USER_AGENT'], $matches);
            }
        }
        if (count($matches) > 1) {
            $version = $matches[1];

            switch (true) {
                case ($version <= 8):
                    return 8;
                case ($version == 9):
                    return 9;
                case ($version == 10):
                    return 10;
                case ($version == 11):
                    return 11;
            }
        }
    }

    /**
     * Talls you if the user has subscribe && master docter privileges
     * 
     * @global stdClass $user
     * @return boolean
     */
    public static function hasSubscribedMasterPrivileges() {
        if (user_access('administer')) {
            return true;
        }

        global $user;
        $user = user_load($user->uid);
        if (Subscriptions::currentGroupHasPayed() && in_array(helper::ROLE_EMPLOYER_MASTER, array_values($user->roles))) {
            return true;
        }
        return false;
    }

    /**
     * Get's you the available terms or city's that are like $term
     * 
     * @param string $term
     * @return array
     */
    public static function getAvailableTerms($term) {
        $return = array();
        $sql = "select taxonomy_term_data.name as term from taxonomy_term_data where taxonomy_term_data.name like :term and vid in (select vid from taxonomy_vocabulary where machine_name in ('" . GojiraSettings::VOCABULARY_LOCATION . "'))";
        $results = db_query($sql, array(':term' => '' . $term . '%'));
        foreach ($results as $result) {
            $return[$result->term] = $result->term;
        }

        sort($return);

        $return = array_slice($return, 0, 15);

        return $return;
    }

    /**
     * Get's you the terms connected to the node
     * 
     * @param integer $nid
     * @return array
     */
    public static function getTermsOfNode($nid) {
        $sql = "select taxonomy_term_data.name as name from taxonomy_term_data where tid in (select field_location_labels_tid from field_data_field_location_labels where bundle = '" . GojiraSettings::CONTENT_TYPE_LOCATION . "' and entity_id = :nid)";

        $results = db_query($sql, array(':nid' => $nid));
        $return = array();
        foreach ($results as $result) {
            $return[] = $result->name;
        }
        return $return;
    }

    /**
     * Tell's you if the current user has agreed with the conditions
     * 
     * @global stdClass $user
     * @return boolean
     */
    public static function agreedToConditions() {
        global $user;
        $user = user_load($user->uid);
//    if (in_array('administrator', array_values($user->roles))) {
//      return true;
//    }
        return (bool) helper::value($user, GojiraSettings::CONTENT_TYPE_CONDITIONS_AGREE_FIELD);
    }

    /**
     * Tell's you if the current user has seen the tutorial
     * 
     * @global stdClass $user
     * @return boolean
     */
    public static function hasSeenTutorial() {
        global $user;
        $user = user_load($user->uid);
        return (bool) helper::value($user, GojiraSettings::CONTENT_TYPE_TUTORIAL_FIELD);
    }

    /**
     * Tells you if a location that needs to be changed by a user is connected to the same location and has employer rights and has a moderator
     * 
     * @param integer uid of the so called admin user
     * @param integer nid of the location that is going to be changed
     * @return boolean
     */
    public static function canChangeLocation($uidSoCalledAdmin, $nidToChange) {
        global $user;
        $user = user_load($user->uid);
        if (in_array('administrator', array_values($user->roles))) {
            return true;
        }

        $employer = user_load($uidSoCalledAdmin);
        $location = node_load($nidToChange);

//    if(helper::value($location, GojiraSettings::CONTENT_TYPE_LOCATION_HAS_MODERATOR) != 1){
//      return false;
//    }

        if (!user_access(helper::PERMISSION_MODERATE_LOCATION_CONTENT)) {
            return false;
        }

        // check the same group id
        $employerGroupId = helper::value($employer, GojiraSettings::CONTENT_TYPE_GROUP_FIELD, 'nid');
        $locationGroupId = helper::value($location, GojiraSettings::CONTENT_TYPE_GROUP_FIELD, 'nid');

        if (!is_numeric($employerGroupId) || !is_numeric($locationGroupId) || ($employerGroupId !== $locationGroupId)) {
            return false;
        }

        return true;
    }

    /**
     * Tells you if a user that needs to be changed by an other user is connected to the same location and has employer rights
     * 
     * @param integer uid of the so called admin user
     * @param integer uid of the user that is going to be changed
     * @return boolean
     */
    public static function canChangeOtherUser($uidSoCalledAdmin, $uidToChange) {
        // editing my self
        if ($uidSoCalledAdmin == $uidToChange) {
            return true;
        }

        $admin = user_load($uidSoCalledAdmin);
        $employee = user_load($uidToChange);

        if (!user_access(helper::PERMISSION_MODERATE_LOCATION_CONTENT)) {
            return false;
        }

        // check the same group id
        $employerGroupId = helper::value($admin, GojiraSettings::CONTENT_TYPE_GROUP_FIELD, 'nid');
        $employeeGroupId = helper::value($employee, GojiraSettings::CONTENT_TYPE_GROUP_FIELD, 'nid');

        if (!is_numeric($employerGroupId) || !is_numeric($employeeGroupId) || ($employerGroupId !== $employeeGroupId)) {
            return false;
        }

        return true;
    }

    /**
     * Returns the gojira role of the given user
     * 
     * @param integer $iUser
     * @return string
     */
    public static function getGojiraRole($iUser) {
        $oUser = user_load($iUser);
        if (in_array(helper::ROLE_EMPLOYER, $oUser->roles)){
            return 'Employer';
        }else if (in_array(helper::ROLE_EMPLOYER_MASTER, $oUser->roles)){
            return 'Main employer';
        }else if (in_array('administrator', $oUser->roles)){
            return 'Administrator';
        }else{
            return 'Employee';
        }
    }

    public static function getSystemnameRole() {
        global $user;
        $user = user_load($user->uid);
        $role = helper::ROLE_EMPLOYEE;
        if (in_array(helper::ROLE_EMPLOYER, $user->roles)) {
            $role = helper::ROLE_EMPLOYER;
        }
        if (in_array(helper::ROLE_EMPLOYER_MASTER, $user->roles)) {
            $role = helper::ROLE_EMPLOYER_MASTER;
        }
        if (in_array('administrator', $user->roles)) {
            $role = helper::ROLE_EMPLOYER_MASTER;
        }
        return $role;
    }

    /**
     * Get's you the text of the text field of the text node found by the given code.
     * 
     * @param string $code
     * @return string
     */
    public static function getText($code, $getTitle = false) {

        if (isset($_GET['translation']) || isset($_GET['t'])) {
            return $code;
        }


        // get existing location the belongs to this e-mail address
        $query = new EntityFieldQuery();
        //https://drupal.org/node/1343708
        $query->entityCondition('entity_type', 'node')
                ->entityCondition('bundle', GojiraSettings::CONTENT_TYPE_TEXT)
                ->propertyCondition('status', 1)
                //->propertyCondition('mail', $user->mail)
                ->fieldCondition('field_code', 'value', $code, '=')
                ->range(0, 1);
        $result = $query->execute();

        if (isset($result['node']) && is_array($result['node'])) {
            $nodeinfo = array_pop($result['node']);
            $node = node_load($nodeinfo->nid);
            if ($getTitle) {
                return $node->title;
            }
            return $node->field_text[LANGUAGE_NONE][0]['value'];
        } else {
            if ($getTitle) {
                return t('Cannot find the title of text node with code %code%.', array('%code%' => $code));
            }
            return t('Cannot find text node with code %code%.', array('%code%' => $code));
        }
    }

    /**
     * A field value wrapper because i got sick of those array's
     * 
     * @param stdClass $node
     * @param string $field
     * @return string
     */
    public static function value($oNode, $sField, $sFieldValueKey = 'value') {
        if (isset($oNode->$sField)) {
            $aField = $oNode->$sField;
            if (isset($aField[LANGUAGE_NONE][0][$sFieldValueKey])) {
                return $aField[LANGUAGE_NONE][0][$sFieldValueKey];
            }
        }
        return '';
    }

    /**
     * Adds the addressfields to the given form
     * 
     * @param array $form
     * @param stdClass $node
     */
    public static function addAddressFormPart(&$form, $node = false) {
        
        $form[GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD] = array(
            '#title' => t('Street'),
            '#type' => 'textfield',
            '#default_value' => ($node ? helper::value($node, GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD) : ''),
            '#required' => TRUE,
        );

        $form[GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD] = array(
            '#title' => t('Streetnumber'),
            '#type' => 'textfield',
            '#default_value' => ($node ? helper::value($node, GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD) : ''),
            '#required' => TRUE,
        );
        
        $form[GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD] = array(
            '#title' => t('Postcode'),
            '#type' => 'textfield',
            '#default_value' => ($node ? helper::value($node, GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD) : ''),
            '#required' => TRUE,
        );

        $form[GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD] = array(
            '#title' => t('City'),
            '#type' => 'textfield',
            '#default_value' => ($node ? helper::value($node, GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD) : ''),
            '#required' => TRUE,
        );
        
        // this field is used as an validation error wrapper for the location
        $form['location'] = array(
            '#type' => 'hidden'
        );
    }

    private static function removeAllHuisartsen() {
        exit;
        $result = db_query("select node.nid from node join taxonomy_index on (taxonomy_index.nid = node.nid) join taxonomy_term_data on (taxonomy_term_data.tid = taxonomy_index.tid) where taxonomy_term_data.name = 'huisartsenpost' OR taxonomy_term_data.name = 'huisartsenpraktijk'");
        $counter = 1;
        foreach ($result as $row) {
            node_delete($row->nid);
            //$node = node_load($row->nid);
            //echo $node->title.'<br />';
            $counter++;
        }
        echo $counter . '<br />';
        exit;
    }

    public static function formatMoney($number) {
        return str_replace('.', ',', number_format($number, 2));
    }

    /**
     * Get's you the current time.
     * This function is used so we can change the 'current' moment for testing
     */
    public static function getTime() {
        return time();
    }

    /**
     * Get's you all the Faq pages.
     * 
     * @return type
     */
    public static function getAllFaqPages() {
        $aReturn = array();
        $rResult = db_query("select node.nid as nid, title from {node} where type = '" . GojiraSettings::CONTENT_TYPE_FAQ . "' order by node.title")->fetchAll();
        if ($rResult) {
            foreach($rResult as $oResult){
                $aReturn[] = node_load($oResult->nid);
            }
        }
        return $aReturn;
    }

    /**
     * Get current user
     * 
     * @global stdClass $user
     * @return stdClass;
     */
    public static function getUser(){
        global $user;
        return user_load($user->uid);
    }
    
    /**
     * Get's the tags from the query string
     * 
     * return Array
     */
    public static function getTagsFromQuery(){
        return explode(' ', urldecode($_GET['tags']));
    }
}

?>
