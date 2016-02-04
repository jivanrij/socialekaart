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

    // so the user IS imported, and has agreed to the conditions, then: give all the Haweb privileges
    $discountGiven = db_query("select discount_given from users where uid = " . $user->uid)->fetchField();
    if ($discountGiven != 1) {
        db_query("UPDATE `users` SET `discount_given`=1 WHERE uid=" . $user->uid);
        Subscriptions::giveNewUserDiscount($user); // Adds information/roles/stuff to give the user a 3 months period for free
        Mailer::newAccountWithFreePeriod($user); // sends email to inform the user he/she has got a free period
    }
    
    if (array_key_exists('subscribe_newsletter', $_POST) && $_POST['subscribe_newsletter'] == '1') {
        Mailer::subscribeToMailchimp($user->mail);
    }    

    header('Location: /');
    exit;
}
