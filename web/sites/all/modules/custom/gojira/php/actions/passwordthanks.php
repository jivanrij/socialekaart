<?php
/**
 * Page for password thanks
 * 
 * @return string
 */
function passwordthanks(){
  
  drupal_add_js(array('gojira' => array('page' => 'passwordthanks')), 'setting');

  return theme('passwordthanks', array());
}