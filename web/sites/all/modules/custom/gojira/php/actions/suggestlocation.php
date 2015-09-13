<?php
function suggestlocation() {
  global $user;
  $id = false;
  $location = false;

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $location = Location::getLocationObjectOfNode($id);
    drupal_add_js(array('gojira' => array('draw_point' => 1)), 'setting');
  } else {
    drupal_add_js(array('gojira' => array('draw_point' => 0)), 'setting');
    $location = Search::getInstance()->getCenterMap();
  }

  if (!$location) {
    $location = new Location(variable_get('CENTER_COUNTRY_LONGITUDE'), variable_get('CENTER_COUNTRY_LATITUDE'));
  }

  drupal_add_js(array('gojira' => array('page' => 'suggestlocation')), 'setting');
  
  return theme('suggestlocation', array('fForm' => drupal_get_form('gojira_suggestlocation_form')));
}