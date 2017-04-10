<?php
/**
 * This page generates a form to crud a employee
 *
 * @return string
 */
function employeeedit() {
  
  global $user;
  
  // nice that your role has the permissions for this page, but you also need to have a payed role
  if (!helper::hasSubscribedMasterPrivileges()) {
     helper::redirectTo404();
  }
  
  if (isset($_GET['id'])) {
    $output['type'] = 'edit';
    if ($_GET['id'] == $user -> uid) {
      $output['create_employee_help_text'] = helper::getText('EDIT_SELF_HELP_TEXT');
    } else {
      $output['create_employee_help_text'] = helper::getText('EDIT_EMPLOYEE_HELP_TEXT');
    }
  } else {
    $output['type'] = 'create';
    $output['create_employee_help_text'] = helper::getText('CREATE_EMPLOYEE_HELP_TEXT');
  }

  drupal_add_js(array('gojira' => array('page' => 'employeeedit')), 'setting');

  $output['template'] = 'employeeedit';
  
  $output['gojira_employeeedit_form'] = drupal_get_form('gojira_employeeedit_form');
  return theme('employeeedit', array('output' => $output));
}