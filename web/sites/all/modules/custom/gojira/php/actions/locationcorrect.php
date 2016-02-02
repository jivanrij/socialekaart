<?php
/**
 * Edit page of a location
 *
 * @global type $user
 * @return type
 */
function locationcorrect() {
  global $user;
  $id = false;
  $location = false;

  if (isset($_GET['nid'])) {
    $id = $_GET['nid'];
    $output['type'] = 'edit';
    $location = Location::getLocationObjectOfNode($id);
  }
  
  drupal_add_js(array('gojira' => array('page' => 'locationcorrect')), 'setting');
  drupal_add_js(array('gojira' => array('location_id' => $id)), 'setting');
  drupal_add_js(array('gojira' => array('longitude' => $location->longitude)), 'setting');
  drupal_add_js(array('gojira' => array('latitude' => $location->latitude)), 'setting');
  drupal_add_js(array('gojira' => array('zoom' => GojiraSettings::MAP_ZOOMLEVEL_STREET)), 'setting');
  drupal_add_js(array('gojira' => array('show_self' => true)), 'setting');
  
  
  $output['template'] = 'locationcorrect';
  $output['form'] = drupal_get_form('gojira_locationcorrect_form');
  $output['nid'] = $id;
  
  return theme('locationcorrect', array('output' => $output));
}