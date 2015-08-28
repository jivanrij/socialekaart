<?php
function locationdelete() {
    
  global $user;
  $iDeleteLocation = $_GET['id'];

  $aLocations = Location::getUsersLocations();

  foreach ($aLocations as $oLocation) {
    if ($oLocation->nid == $iDeleteLocation) {

      Location::removeUserLocation($iDeleteLocation);
      drupal_set_message(t('Location %location sucesfully removed', array('%location' => $oLocation->title)), 'status');
     
      // if the user had the location selected as default to view, set another one
      $iSelectedLocation = helper::value(helper::getUser(), GojiraSettings::CONTENT_TYPE_USER_LAST_SELECTED_LOCATION, 'nid');
      if($iSelectedLocation == $iDeleteLocation){
        $aUsersLocations = Location::getUsersLocations();
        $oNewDefaultLocation = array_pop($aUsersLocations);
        $oUser = helper::getUser();
        // get the group the user is linked to and link the new location to it
        $oUser->field_selected_location = array(LANGUAGE_NONE => array(0 => array('nid' => $oNewDefaultLocation->nid)));
        user_save($oUser);
      }

      drupal_goto('settings');
      return;
    }
  }

  drupal_set_message(t('Location @location not removed, please contact administrator if this problem re occurs.', array('@location' => $deleteLocationId)), 'error');
  watchdog('gojira', 'Failed to remove location (' . $deleteLocationId . ') by user (' . $user->uid . ').');
  drupal_goto('settings');
  return;
}
