<?php
/**
 * This is  a custom login form made for none SSO purposes. Not more then the original drupal login form with some layout changes
 * 
 * @param Array $form
 * @param Array $form_state
 * @return Array
 */
function gojira_login_form($form, &$form_state) {
  // Display login form:
  $form['name'] = array('#type' => 'textfield',
    '#title' => t('Username'),
    '#size' => 60,
    '#maxlength' => USERNAME_MAX_LENGTH,
    '#required' => TRUE,
  );

  $form['pass'] = array('#type' => 'password',
    '#title' => t('Password'),
    '#description' => t('Enter the password that accompanies your username.'),
    '#required' => TRUE,
    '#description' => '<a href="/passwordreset" title="'.t('Click here if you forgot your password.').'">'.t('Click here if you forgot your password.').'</a>',
  );
  
  $form['#validate'] = user_login_default_validators();
  $form['#validate'][] = 'gojira_validate_login';
  
  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array('#type' => 'submit', '#value' => t('Log in'));
  
  $form['actions']['submit']['#prefix'] = '<span class="gbutton rounded noshadow left gbutton_small">';
  $form['actions']['submit']['#suffix'] = '</span>';
  
  return $form;
}
