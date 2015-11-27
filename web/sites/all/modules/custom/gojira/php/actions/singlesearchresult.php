<?php
/**
 * Ajax action to switch the users show_hints field
 *
 * @global type $user
 */
function singlesearchresult() {

  if(is_numeric($_GET['nid'])){
    $locationNode = node_load($_GET['nid']);

    $after_html = '';
    $before_html = '';
    if(isset($_GET['wrap_it'])){
        $before_html = '<div id="search_result_info"><div id="selected_location_info" class="rounded"><div id="location_info_'.$locationNode->nid.'">';
        $after_html = '</div></div>';
    }

    $html = Search::getInstance()->getResultItemTableHtml($locationNode);

    $location = Location::getLocationObjectOfNode($locationNode->nid);

//    $locationInfo = array(
//      'city'=>helper::value($locationNode, GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD),
//      'email'=>helper::value($locationNode, GojiraSettings::CONTENT_TYPE_EMAIL_FIELD),
//      'street'=>helper::value($locationNode, GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD),
//      'number'=>helper::value($locationNode, GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD),
//      'telephone'=>helper::value($locationNode, GojiraSettings::CONTENT_TYPE_TELEPHONE_FIELD),
//      'url'=>helper::value($locationNode, GojiraSettings::CONTENT_TYPE_URL_FIELD),
//      'labels'->Label
//
//    );

    echo json_encode(array('html'=>$before_html.$html.$after_html,'longitude'=>$location->longitude,'latitude'=>$location->latitude, 'zoom'=>  GojiraSettings::MAP_ZOOMLEVEL_STREET));
    exit;
  }


}