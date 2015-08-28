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
        $before_html = '<div id="search_result_info"><div id="selected_location_info" class="rounded"><div id="location_info_'.$locationNode->nid.'"><div class="search_result_wrapper" id="location_'.$locationNode->nid.'">';    
        $after_html = '</div></div></div>';
    }
    
    
    $html = Search::getInstance()->getResultItemTableHtml($locationNode);
    
    
    $location = Location::getLocationObjectOfNode($locationNode->nid);
    
    echo json_encode(array('html'=>$before_html.$html.$after_html,'longitude'=>$location->longitude,'latitude'=>$location->latitude, 'zoom'=>  GojiraSettings::MAP_ZOOMLEVEL_STREET));
    exit;
  }
  

}