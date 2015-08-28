<?php
/**
 * Edit page of a location
 *
 * @global type $user
 * @return type
 */
function locationedit() {
  global $user;
  $id = false;
  $location = false;

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $output['type'] = 'edit';
    $location = Location::getLocationObjectOfNode($id);
  } else {
    $output['type'] = 'add';
    $location = Search::getInstance()->getCenterMap();
  }

  drupal_add_js(array('gojira' => array('page' => 'locationedit')), 'setting');
  drupal_add_js(array('gojira' => array('location_id' => $id)), 'setting');

  $output['template'] = 'locationedit';
  $output['form'] = drupal_get_form('gojira_locationedit_form');
  
  return theme('locationedit', array('output' => $output));
}