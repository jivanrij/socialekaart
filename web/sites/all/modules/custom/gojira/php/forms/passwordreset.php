<?php

/**
 * Form builder; Request a password reset.
 *
 * @ingroup forms
 * @see user_pass_validate()
 * @see user_pass_submit()
 */
function gojira_passwordreset_form() {
    global $user;

    $form['name'] = array(
        '#type' => 'textfield',
        '#title' => t('Username or e-mailaddress'),
        '#size' => 60,
        '#maxlength' => max(USERNAME_MAX_LENGTH, EMAIL_MAX_LENGTH),
        '#required' => true,
        '#default_value' => isset($_GET['name']) ? $_GET['name'] : '',
    );
    // Allow logged in users to request this also.
    if ($user->uid > 0) {
        $form['name']['#type'] = 'value';
        $form['name']['#value'] = $user->mail;
        $form['mail'] = array(
            '#prefix' => '<p>',
            '#markup' => t('Password reset instructions will be mailed to %email. You must log out to use the password reset link in the e-mail.', array('%email' => $user->mail)),
            '#suffix' => '</p>',
        );
    }
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array('#type' => 'submit', '#value' => t('E-mail new password'));

    $form['#prefix'] = '<p>' . t('Enter your e-mailadres in the form below so we can send you a link to login with. Then you will be able to set a new password.') . '</p>';
    $form['actions']['submit']['#prefix'] = '<span class="gbutton rounded noshadow left gbutton_widest">';
    $form['actions']['submit']['#suffix'] = '</span>';
    $form['name']['#title'] = t('E-mailaddress');
//    $form['#validate'][] = 'gojira_validate_password_reset';
//    $form['#submit'][] = '_password_recovermail_submit';

    return $form;
}

function gojira_passwordreset_form_validate($form, &$form_state) {
    if ($form_state['values']['name'] == '') {
//        form_set_error('incomplete', t('You have not filled in all the needed form fields.'));
    } else {
        $found_mail = db_query("SELECT mail, uid FROM {users} where mail = :mail", array(':mail' => $form_state['values']['name']))->fetchObject();
        if (!$found_mail) {
            form_set_error('mail', t('The e-mail adres you gave is unknown.'));
        } else {
            $form_state['uid'] = $found_mail->uid;
        }
    }
}

function gojira_passwordreset_form_submit($form, &$form_state) {
    _user_mail_notify('password_reset', user_load($form_state['uid']));
    drupal_goto('passwordmailsend');
}
