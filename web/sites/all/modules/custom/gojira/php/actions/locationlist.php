<?php
/**
 * This page generates a list of locations
 *
 * @return string
 */
function locationlist() {
    
  drupal_add_js(array('gojira' => array('page' => 'locationlist')), 'setting');
  drupal_add_js(array('gojira' => array('delete_warning' => t('Are you sure you want to delete this location?'))), 'setting');

  $output['template'] = 'locationlist';
  $output['locations'] = Location::getUserLocations();
  return theme('locationlist', array('output' => $output));
}