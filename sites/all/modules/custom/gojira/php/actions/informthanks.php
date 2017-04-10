<?php
function informthanks(){
  drupal_add_js(array('gojira' => array('page' => 'informthanks')), 'setting');
  return theme('informthanks', array());
}