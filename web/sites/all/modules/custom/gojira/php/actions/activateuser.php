<?php

/**
 * check is address exists via ajax
 */
function activateuser() {

    $rUsers = db_query("select uid from {users} where status = 0 and uid not in (0,1)")->fetchAll();
    $aUsers = array();
    foreach ($rUsers as $iUser) {
        $aUsers[] = user_load($iUser->uid);
    }

    if (isset($_GET['uid']) && is_numeric($_GET['uid'])) {
        $oUser = user_load($_GET['uid']);
        $oUser->status = 1;
        user_save($oUser);
        Mailer::accountActivatedByAdmin($oUser);

        Subscriptions::giveNewUserDiscount($oUser); // Adds information/roles/stuff to give the user a 3 months period for free
        Mailer::newAccountWithFreePeriod($oUser); // sends email to inform the user he/she has got a free period
        
        drupal_set_message(t('Just activated user ' . $oUser->name), 'status');
        drupal_goto('admin/config/system/gojiraactivateuser');
        exit;
    }

    return theme('activateuser', array('aUsers' => $aUsers));
}
