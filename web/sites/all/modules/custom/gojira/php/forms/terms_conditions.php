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
        '#title' => t('I agree with the terms & conditions'),
        '#type' => 'checkbox',
        '#default_value' => 0
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
  if(array_key_exists('agree', $_POST) && $_POST['agree'] == '1'){
    return;
  }
  Messages::setNeedToAgreeMessage();
}

function gojira_terms_conditions_form_submit($form, &$form_state) {
  $user = user_load($form['uid']['#value']);
  $fieldName = GojiraSettings::CONTENT_TYPE_CONDITIONS_AGREE_FIELD;
  $user->$fieldName = array('und' => array(0 => array('value' => 1)));
  user_save($user);
  
  if($user->field_user_not_imported[LANGUAGE_NONE][0]['value'] == 0){
      // so the user IS imported, and has agreed to the conditions, then: give all the Haweb privileges
        $iHawebSetupDone = db_query("select haweb_sso_setup_done from users where uid = " . $user->uid)->fetchField();
        if ($iHawebSetupDone == 0) {
            Haweb::setNewSSOUser($user); // Adds crucial information/roles/stuff to a new created user from the HAWeb SSO
            Mailer::newAccountThroughSSO($user); // Informs the user he has logged on to SocialeKaart for the first time through SSO from haweb
            Mailer::subscribeToMailchimp($user->mail); // Add given e-mail to the mailchimp list
            db_query("UPDATE `users` SET `haweb_sso_setup_done`=1 WHERE uid=" . $user->uid);
        }
  }  
  
  
  header('Location: /');
  exit;
}
