<?php

/**
 * This class acts as a function wrapper for all kinds of haweb related stuff
 */
class Haweb {

    /**
     * Sends a Gojira user a email that someone with the same email want's to login from Haweb
     * 
     * @param String $sMail
     */
    public static function dublicateWarning($sMail) {

        $iUser = db_query("select uid from {users} where mail = '{$sMail}'")->fetchField();
        
        $iDublicte = db_query("select haweb_sso_dublicate_warning_send from users where uid = " . $iUser)->fetchField();
        
        if ($iDublicte == 0) {
            watchdog('gojira', 'haweb_sso_dublicate_warning_send for user '.$iUser.' is 0 -> dublicateWarning(SENDING!)');
            Mailer::sendDoubleAccountWarning($sMail);
            db_query("UPDATE `users` SET `haweb_sso_dublicate_warning_send`=1 WHERE uid=" . $iUser);
        }
    }

    /**
     * Adds crucial information/roles/stuff to a new created user from the HAWeb SSO
     * 
     * Do NOT run this function on allready existing accounts
     * 
     * @param stdClass $account
     */
    public static function setNewSSOUser(&$account) {
        $group = Group::createNewGroup($account, 1);
        $groupField = GojiraSettings::CONTENT_TYPE_GROUP_FIELD;
        $account->$groupField = array(LANGUAGE_NONE => array(0 => array('nid' => $group->nid)));

        $roles = array();
        $activeRoles = user_roles(true);
        foreach ($activeRoles as $key => $role) {
            if ($role == helper::ROLE_AUTHENTICATED || $role == helper::ROLE_EMPLOYER_MASTER) {
                $roles[$key] = $role;
            }
        }
        $account->roles = $roles;

        $sTitleField = GojiraSettings::CONTENT_TYPE_USER_TITLE;
        $user->$sTitleField = array(LANGUAGE_NONE => array(0 => array('value' => $account->name)));
        $searchFavoritesField = GojiraSettings::CONTENT_TYPE_SEARCH_FAVORITES_FIELD;
        $account->$searchFavoritesField = array(LANGUAGE_NONE => array(0 => array('value' => 0)));
        $searchGlobalField = GojiraSettings::CONTENT_TYPE_SEARCH_GLOBAL_FIELD;
        $account->$searchGlobalField = array(LANGUAGE_NONE => array(0 => array('value' => 0)));
        $tutorialField = GojiraSettings::CONTENT_TYPE_TUTORIAL_FIELD;
        $account->$tutorialField = array(LANGUAGE_NONE => array(0 => array('value' => 0)));
        $docField = GojiraSettings::CONTENT_TYPE_IS_DOCTOR_FIELD;
        $account->$docField = array(LANGUAGE_NONE => array(0 => array('value' => 1)));
        $importedField = GojiraSettings::CONTENT_TYPE_USER_NOT_IMPORTED;
        $account->$importedField = array(LANGUAGE_NONE => array(0 => array('value' => 0)));

        user_save($account);

        Subscriptions::setRolesForPayed($group, false);

        // add a payment log so the group will have a payed period of 3 months
        $sql = "INSERT INTO `gojira_payments` (`uid`, `name`, `description`, `amount`, `gid`, `ideal_id`, `ideal_code`, `period_start`, `status`, `period_end`,`discount`,`tax`,`payed`) VALUES ({$account->uid}, '{$account->name}', 'Intro Period', 0, " . $group->nid . ", '0', '0', " . helper::getTime() . ", 1, " . strtotime("+3 months", helper::getTime()) . ",0,0,0)";
        db_query($sql);
    }

}
