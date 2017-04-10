<?php
/**
* Ajax action to switch if a location belongs ot a locationset boolean for a user
*
* @global type $user
*/
function setonlocationset() {

    $locationset = \Models\Locationset::load($_GET['locationset']);
    $location = \Models\Location::load($_GET['location']);

    if ($_GET['turn'] == 'on') {
        $locationset->addLocation($location);
    } else {
        $locationset->removeLocation($location);
    }

    echo 'success';
    exit;
}
