<?php
/**
 * Page to display if there is no locations available
 * 
 * @return string
 */
function locationcheck(){

  drupal_add_js(array('gojira' => array('page' => 'content_big')), 'setting');
  
  if(count(Location::getUsersLocations())==0){
    drupal_add_js(array('gojira' => array('zoom' => GojiraSettings::MAP_ZOOMLEVEL_COUNTRY)), 'setting');
  }
  
  $output['txt'] = t('You need to have a location to be able to use the system.');
  return theme('locationcheck', array('output' => $output));
}