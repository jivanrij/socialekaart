<?php
/**
 * Ajax action to switch the users show_hints field
 *
 * @global type $user
 */
function locationinfo() {
    
    
  if(is_numeric($_GET['nid'])){
    $nLocation = node_load($_GET['nid']);
    $oLocation = Location::getLocationObjectOfNode($nLocation->nid);
    $aLabels = Labels::getLabels($nLocation);
    $sLabels = implode(', ', $aLabels);
    $sImproveLink = t('Is this information incorrect of incomplete?').' <a href="/?q=inform&nid='.$nLocation->nid.'" title="'.t('Report it here.').'">'.t('Report it here.').'</a>';

    
    echo json_encode(array(
        'sImproveLink' => $sImproveLink,
        'longitude' => $oLocation->longitude,
        'latitude'=>$oLocation->latitude,
        'nLocation'=> $nLocation,
        'iNode'=> $nLocation->nid,
        'sLabels'=> $sLabels,
        'aLabels'=> $aLabels,
        'sTitle' => $nLocation->title,
        'sCity' => helper::value($nLocation,  GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD),
        'sPostcode' => helper::value($nLocation,  GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD),
        'sStreetnumber' => helper::value($nLocation,  GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD),
        'sStreet' => helper::value($nLocation,  GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD),
        'sCategory' => Category::getCategoryName($nLocation),
            ));
    exit;
  }
  

}