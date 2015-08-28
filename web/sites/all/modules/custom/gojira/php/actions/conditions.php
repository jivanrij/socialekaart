<?php
/**
 * Page to display to ask confirmation of the conditions
 * 
 * @return string
 */
function conditions(){

  drupal_add_js(array('gojira' => array('page' => 'content_big')), 'setting');
  $output['button'] = '';
  if(!helper::agreedToConditions()){
    $output['form'] = drupal_get_form('gojira_terms_conditions_form');
  }
    
  $output['txt'] = helper::getText('TERMS_CONDITIONS');
  
  return theme('conditions', array('output' => $output));
}