<?php

function gojira_terms_conditions_form($form, &$form_state) {

    global $user;

    $form['uid'] = array(
        '#title' => 'uid',
        '#type' => 'hidden',
        '#required' => true,
        '#default_value' => $user->uid,
    );

    $form['agree'] = array(
        '#prefix' => '<div class="wide_form_label">',
        '#suffix' => '</div>',
        '#title' => t('I agree with the terms & conditions'),
        '#type' => 'checkbox',
        '#default_value' => 0
    );

    $form['subscribe_newsletter'] = array(
        '#prefix' => '<div class="wide_form_label">',
        '#suffix' => '</div>',
        '#title' => t('Subscribe to our newsletter'),
        '#type' => 'checkbox',
        '#default_value' => 1
    );

    $form['submit'] = array(
        '#type' => 'submit',
        '#prefix' => '<div class="gbutton_wrapper"><span class="gbutton rounded noshadow left">',
        '#value' => t('Submit'),
        '#suffix' => '</span></div>'
    );
    return $form;
}

function gojira_terms_conditions_form_validate($form, &$form_state) {
    if (array_key_exists('agree', $_POST) && $_POST['agree'] == '1') {
        return;
    }
    Messages::setNeedToAgreeMessage();
}

function gojira_terms_conditions_form_submit($form, &$form_state) {
    
    $user = user_load($form['uid']['#value']);
    $fieldName = GojiraSettings::CONTENT_TYPE_CONDITIONS_AGREE_FIELD;
    $user->$fieldName = array('und' => array(0 => array('value' => 1)));
    user_save($user);

    if ($user->field_user_not_imported[LANGUAGE_NONE][0]['value'] == 0) {
        // so the user IS imported, and has agreed to the conditions, then: give all the Haweb privileges
        $iHawebSetupDone = db_query("select haweb_sso_setup_done from users where uid = " . $user->uid)->fetchField();
        if ($iHawebSetupDone == 0) {
            Haweb::setNewSSOUser($user); // Adds crucial information/roles/stuff to a new created user from the HAWeb SSO
            Mailer::newAccountThroughSSO($user); // Informs the user he has logged on to SocialeKaart for the first time through SSO from haweb
            db_query("UPDATE `users` SET `haweb_sso_setup_done`=1 WHERE uid=" . $user->uid);
        }
    }else{
        // we have decided that we will give the first x amount of users that have registered themself a free period of 3 months
        
        // get amount of users that have registered themself and have agreed with the conditions
        $iAmount = db_query("select count(users.uid) amount from users join field_data_field_user_not_imported on (field_data_field_user_not_imported.entity_id = users.uid) join field_data_field_agree_conditions on (field_data_field_agree_conditions.entity_id = users.uid) where field_data_field_agree_conditions.field_agree_conditions_value = 1 and field_data_field_user_not_imported.field_user_not_imported_value = 1")->fetchField();
        
        if($iAmount < variable_get('gojira_user_amount_with_discount', 300)){
            watchdog(WATCHDOG_SUBSCRIPTIONS, 'Giving user '.$user->uid.' a discount because there are still '.$iAmount.' users registered.');
            Haweb::setNewFreePeriodUser($user); // Adds crucial information/roles/stuff to a new created user from the HAWeb SSO
            Mailer::newAccountWithFreePeriod($user); // sends email to inform the user he/she has got a free period
        }
        die;
    }


    if (array_key_exists('subscribe_newsletter', $_POST) && $_POST['subscribe_newsletter'] == '1') {
        Mailer::subscribeToMailchimp($user->mail);
    }    

    header('Location: /');
    exit;
}
