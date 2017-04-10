<?php
/**
 * Page to display to ask confirmation of the conditions
 * 
 * @return string
 */
function paymentconditions(){

  drupal_add_js(array('gojira' => array('page' => 'content_big')), 'setting');
  
  $conditions = helper::getText('PAYMENT_TERMS_CONDITIONS');
  
  return theme('paymentconditions', array('conditions' => $conditions));
}