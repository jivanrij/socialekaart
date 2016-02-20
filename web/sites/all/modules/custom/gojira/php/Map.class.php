<?php

class Map {

  public static function setMapInfo($route = 'gojirasearch') {
    global $user;
    $user = user_load($user->uid);
    
    // set defaults
    $longitude = variable_get('CENTER_COUNTRY_LONGITUDE');
    $latitude = variable_get('CENTER_COUNTRY_LATITUDE');
    $zoom = GojiraSettings::MAP_ZOOMLEVEL_COUNTRY;
    $show_self = 1;
    
    // if we have a user with a selected location, focus on the region
    $location = Location::getCurrentLocationObjectOfUser(true);
    
    if($location){
      $longitude = $location->longitude;
      $latitude = $location->latitude;
      $zoom = GojiraSettings::MAP_ZOOMLEVEL_REGION;
    }

    // user has no locations
    if(count(Location::getUsersLocations(true))==0){
      $zoom = GojiraSettings::MAP_ZOOMLEVEL_COUNTRY;
      $show_self = 0;
    }
    
    switch ($route) {
        case 'ownlist':
            $zoom = GojiraSettings::MAP_ZOOMLEVEL_STREET;
            break;
      
    }
    
    drupal_add_js(array('gojira' => array('longitude' => $longitude)), 'setting');
    drupal_add_js(array('gojira' => array('latitude' => $latitude)), 'setting');
    drupal_add_js(array('gojira' => array('zoom' => $zoom)), 'setting');
    drupal_add_js(array('gojira' => array('show_self' => $show_self)), 'setting');
  }

}
