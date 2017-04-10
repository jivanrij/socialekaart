<?php
/**
 * This page generates a list of employees
 *
 * @return string
 */
function employeelist() {
    
  global $user; 
    
  drupal_add_js(array('gojira' => array('page' => 'employeelist')), 'setting');
  drupal_add_js(array('gojira' => array('delete_warning' => t('Are you sure you want to delete this employee?'))), 'setting');

  return theme('employeelist', array('employees' => Group::getEmployees(),'uid'=>$user->uid));
}