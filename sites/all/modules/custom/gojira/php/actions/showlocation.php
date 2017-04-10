<?php
/**
 * Thanks for registering page
 *
 * @return string
 */
function showlocation() {
  drupal_add_js(array('gojira' => array('page' => 'showlocation')), 'setting');
  drupal_add_js(array('gojira' => array('loc' => $_GET['loc'])), 'setting');
  return theme('showlocation');
}