<?php
/**
 * This page generates the settings form
 *
 * @return string
 */
function settings() {

  global $user;
  $user = user_load($user->uid);

  drupal_add_js(array('gojira' => array('page' => 'settings')), 'setting');
  drupal_add_js(array('gojira' => array('delete_warning' => t('Are you sure you want to delete this location?'))), 'setting');

  if (user_access(helper::PERM_HUISARTS_MORE_PRACTICES)) {
      $output['multiple_locations'] = helper::value($user, GojiraSettings::CONTENT_TYPE_HAS_MULTIPLE_LOCATIONS_FIELD);
  }else{
      $output['multiple_locations'] = false;
  }

  $output['template'] = 'settings';

  $output['user_locations'] = Location::getUsersLocations(false);
  $output['gojira_settings_form'] = drupal_get_form('gojira_settings_form');

  $output['subscribed'] = in_array(helper::ROLE_HUISARTS_PLUS, array_values($user->roles));

  return theme('settings', array('output' => $output));
}

function gojira_to_subscribe_page_form($form, &$form_state) {

  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array('#type' => 'submit', '#value' => t('To subscribe page'));

  $form['actions']['submit']['#prefix'] = '<span class="gbutton rounded noshadow left gbutton_wide">';
  $form['actions']['submit']['#suffix'] = '</span>';

  return $form;
}
function gojira_to_subscribe_page_form_submit($form, &$form_state) {
    drupal_goto('subscribe');
}
