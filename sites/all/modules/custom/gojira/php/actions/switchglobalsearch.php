<?php
/**
 * Ajax action to switch the users show_hints field
 *
 * @global type $user
 */
function switchglobalsearch() {
  global $user;
  $user = user_load($user->uid);

  $field = GojiraSettings::CONTENT_TYPE_SEARCH_GLOBAL_FIELD;
  $fieldValue = $user->$field;

  if ($_GET['turn'] == 'on') {
    $fieldValue[LANGUAGE_NONE][0]['value'] = 1;
    $zoom = GojiraSettings::MAP_ZOOMLEVEL_COUNTRY;
  } else {
    $fieldValue[LANGUAGE_NONE][0]['value'] = 0;
    $zoom = GojiraSettings::MAP_ZOOMLEVEL_REGION;
  }

  $user->$field = $fieldValue;

  user_save($user);
  
  if ($_GET['turn'] == 'on') {
    $latitude = variable_get('CENTER_COUNTRY_LATITUDE');
    $longitude = variable_get('CENTER_COUNTRY_LONGITUDE');
  }else{
    $location = Location::getCurrentLocationObjectOfUser(); 
    $latitude =  $location->latitude;
    $longitude =  $location->longitude;
  }
  
  echo json_encode(array('longitude'=>$longitude,'latitude'=>$latitude,'zoom'=>  $zoom));
  exit;
}