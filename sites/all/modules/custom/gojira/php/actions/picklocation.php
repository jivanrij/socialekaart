<?php

/**
 * gives a location picker
 */
function picklocation() {
    $oUser = helper::getUser();
    
    // get the group the user is linked to and link the new location to it
    $oUser->field_selected_location = array(LANGUAGE_NONE => array(0 => array('nid' => $_GET['nid'])));
    user_save($oUser);
    
    $location = Location::getCurrentLocationObjectOfUser(false);
    echo json_encode(array('latitude'=>$location->latitude, 'longitude'=>$location->longitude));
    exit;
}
