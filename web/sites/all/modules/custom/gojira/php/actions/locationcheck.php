<?php
/**
 * Page to display if there is no locations available
 * 
 * @return string
 */
function locationcheck(){

  drupal_add_js(array('gojira' => array('page' => 'content_big')), 'setting');
  
  if(count(Location::getUsersLocations(true))==0){
    drupal_add_js(array('gojira' => array('zoom' => GojiraSettings::MAP_ZOOMLEVEL_COUNTRY)), 'setting');
  }
  
  return theme('locationcheck', array());
}