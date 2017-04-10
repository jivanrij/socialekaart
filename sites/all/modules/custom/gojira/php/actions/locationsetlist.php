<?php

/**
 * Shows the locationssets that are managed by this user
 */
function locationsetlist() {
    $sets = Locationsets::getInstance()->getMapSetsForCurrentUser();
    foreach($sets as $set) {
        $setModels[] = \Models\Locationset::load($set->nid);
    }


    return theme('locationsetlist', array('setModels'=>$setModels));
}
