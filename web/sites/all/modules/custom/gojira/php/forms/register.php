<?php
function gojira_register_form($form, &$form_state) {
  $id = 'new';
  $node = false;
  $user = false;
  if (isset($_GET['id']) && !is_null($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $user = user_load($id);
  }

  $form[GojiraSettings::CONTENT_TYPE_USER_TITLE] = array(
      '#title' => t('Name/Title'),
      '#type' => 'textfield',
      '#required' => true,
      '#default_value' => ($user ? $user->name : ''),
  );
  
  $form['email'] = array(
      '#title' => t('E-mailadres'),
      '#type' => 'textfield',
      '#required' => true,
      '#default_value' => ($user ? $user->email : ''),
  );
  
  $form['id'] = array(
      '#title' => t('id'),
      '#type' => 'hidden',
      '#default_value' => $id,
  );
  
  if ($user) {
    $bigfield = GojiraSettings::CONTENT_TYPE_BIG_FIELD;
    $bigfield = $user->$bigfield;
    $big = $bigfield['und'][0]['value'];
  }
  $form[GojiraSettings::CONTENT_TYPE_BIG_FIELD] = array(
      '#title' => t('BIG registration number'),
      '#type' => 'textfield',
      '#required' => true,
      '#default_value' => ($user ? $big : ''),
      '#description' => t('Fill in your BIG registration number.'),
  );

  if ($user) {
    $isdoctorfield = GojiraSettings::CONTENT_TYPE_IS_DOCTOR_FIELD;
    $isdoctorfield = $user->$isdoctorfield;
    $isdoctor = $isdoctorfield['und'][0]['value'];
  }
  $form[GojiraSettings::CONTENT_TYPE_IS_DOCTOR_FIELD] = array(
      '#title' => t('I am a medical practitioner'),
      '#type' => 'checkbox',
      '#default_value' => ($user ? $isdoctor : ''),
      '#description' => t('Confirm that you are a medical practitioner. Only medical practitioners are allowed to have access to socialekaart.care.'),
  );

  $form['submit'] = array(
      '#type' => 'submit',
      '#prefix' => '<span class="gbutton rounded noshadow left">',
      '#value' => t('Register'),
      '#suffix' => '</span>'
  );

  return $form;
}

function gojira_register_form_validate($form, &$form_state) {
  
  if($form[GojiraSettings::CONTENT_TYPE_IS_DOCTOR_FIELD]['#value'] != 1){
    form_set_error(GojiraSettings::CONTENT_TYPE_IS_DOCTOR_FIELD, t('You need to confirm to us that you are some sort of medical practitioner.'));
  }

  if (trim($form[GojiraSettings::CONTENT_TYPE_USER_TITLE]['#value']) == '') {
    form_set_error(GojiraSettings::CONTENT_TYPE_USER_TITLE, t('You have not filled in all the needed form fields.'));
  }
  
  if (trim($form['email']['#value']) == '') {
    form_set_error('incomplete', t('You have not filled in all the needed form fields.'));
  }else if ($error = user_validate_mail($form['email']['#value'])){
    form_set_error('email', t('The given e-mail address is already in use by a user or not correctly formed.'));
  }else if ($error = user_validate_name($form['email']['#value'])){
    form_set_error('email', t('The given e-mail address is already in use by a user or not correctly formed.'));
  }
  
  if ((bool) db_select('users')->fields('users', array('uid'))->condition('mail', db_like($form['email']['#value']), 'LIKE')->range(0, 1)->execute()->fetchField()) {
    form_set_error('email', t('The given e-mail address is already in use by a user or not correctly formed.'));
  }
  
//  if(trim($form[GojiraSettings::CONTENT_TYPE_BIG_FIELD]['#value']) != 'letmein'){
//    if ((bool) db_select('field_data_field_big')->fields('field_data_field_big', array('field_big_value'))->condition('field_big_value', $form[GojiraSettings::CONTENT_TYPE_BIG_FIELD]['#value'], '=')->execute()->fetchField()) {
//      form_set_error(GojiraSettings::CONTENT_TYPE_BIG_FIELD, t('We where unable to verify your BIG number. If you are convinced that you have entered a correct one, please contact the site administrator.'));
//    }else if(!BIG::getInstance()->verifyBIG(trim($form[GojiraSettings::CONTENT_TYPE_BIG_FIELD]['#value']))){
//      form_set_error(GojiraSettings::CONTENT_TYPE_BIG_FIELD, t('We where unable to verify your BIG number. If you are convinced that you have entered a correct one, please contact the site administrator.'));
//    }
//  }
}

function gojira_register_form_submit($form, &$form_state) {
  global $base_url;
  
  if (is_numeric($form['id']['#value'])) {
    //existing location
    $user = user_load($form['id']['#value']);
  } else {
    //set up the user fields
    $fields = array(
        'name' => $form['email']['#value'],
        'mail' => $form['email']['#value'],
        'status' => 0,
        'roles' => array(
            DRUPAL_AUTHENTICATED_RID => helper::ROLE_AUTHENTICATED
        ),
    );
    $user = user_save('', $fields);
  }

  $fieldsToSave = array(
      GojiraSettings::CONTENT_TYPE_BIG_FIELD,
      GojiraSettings::CONTENT_TYPE_IS_DOCTOR_FIELD,
      GojiraSettings::CONTENT_TYPE_USER_TITLE
  );
  
  foreach($fieldsToSave as $fieldName){
    $user->$fieldName = array(LANGUAGE_NONE => array(0 => array('value' => $form[$fieldName]['#value'])));
  }

  $searchGlobalField = GojiraSettings::CONTENT_TYPE_SEARCH_GLOBAL_FIELD;
  $user->$searchGlobalField = array(LANGUAGE_NONE => array(0 => array('value' => 0)));
  $conditionsField = GojiraSettings::CONTENT_TYPE_CONDITIONS_AGREE_FIELD;
  $user->$conditionsField = array(LANGUAGE_NONE => array(0 => array('value' => 0)));  
  $tutorialField = GojiraSettings::CONTENT_TYPE_TUTORIAL_FIELD;
  $user->$tutorialField = array(LANGUAGE_NONE => array(0 => array('value' => 0)));
  $importedField = GojiraSettings::CONTENT_TYPE_USER_NOT_IMPORTED;
  $user->$importedField = array(LANGUAGE_NONE => array(0 => array('value' => 1)));
  
  $group = Group::createNewGroup($user);
  
  $groupField = GojiraSettings::CONTENT_TYPE_GROUP_FIELD;
  $user->$groupField = array(LANGUAGE_NONE => array(0 => array('nid' => $group->nid)));
  
  $roles = array();
  $activeRoles = user_roles(true);
  foreach($activeRoles as $key=>$role){
    if($role == helper::ROLE_AUTHENTICATED || $role == helper::ROLE_EMPLOYER_MASTER){
      $roles[$key] = $role; 
    }
  }
  $user->roles = $roles;
  
  user_save($user);

  
  drupal_set_message(t('Account sucesfully created.'));
  
  drupal_mail('user', 'register_pending_approval', $form['email']['#value'], null, array('account' => $user), variable_get('site_mail', 'no@reply.com'));
  //register_no_approval_required
  
  Mailer::sendAccountNeedsValidation($user);
  
  Mailer::subscribeToMailchimp($user->mail);
  
  drupal_goto('registered');
}