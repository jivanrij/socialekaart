<?php
/**
 * check is address exists via ajax
 */
function checklocation() {
   
  $location = Location::getLocationForAddress(Location::formatAddress($_POST['city'], $_POST['street'], $_POST['pcnumber'], $_POST['pc']));
  
  if ($location) {
    $location->zoom = GojiraSettings::MAP_ZOOMLEVEL_STREET;
    echo json_encode($location);
  } else {
    $location->zoom = GojiraSettings::MAP_ZOOMLEVEL_COUNTRY;
    echo json_encode('fail');
  }
  exit;
}