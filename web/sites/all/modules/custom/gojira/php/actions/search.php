<?php
// ajax action that is called when searching
function search(){
  global $user;
  $user = user_load($user->uid);

  $user_is_admin = false;
  if (in_array('administrator', array_values($user->roles))) {
    $user_is_admin = true;
  }
  
  $check_city = true;
  if(isset($_GET['check_city']) && $_GET['check_city'] == '0'){
    $check_city = false;
  }
  
  $single_location = (isset($_GET['tags']) && is_numeric($_GET['tags']));
  
  $output['tags'] = '';
  $searchResults = array();
  $foundNodes = array();
  $output['has_tags'] = true;
  $output['by_id'] = false;
  if (isset($_GET['tags']) && ($_GET['tags'] == 'favorites')) {
    $foundNodes = Favorite::getInstance()->getAllFavoriteLocations();
  }else if (isset($_GET['tags']) && ($_GET['tags'] == 'ownlist')) {
    $foundNodes = Favorite::getInstance()->getAllFavoriteLocations();
  }else if(isset($_GET['tags']) && strstr($_GET['tags'], 'allwithtag:')){
    $tag = str_replace('allwithtag:', '', $_GET['tags']);
    $foundNodes = Search::getInstance()->doSearch(array($tag), false, true, false);    
  } else if($single_location){
    // we have been given a nid as a tag, let's show a single location
    $output['by_id'] = $_GET['tags'];
    $foundNodes[$_GET['loc']] = node_load($_GET['tags']);
  }else if(isset($_GET['tags']) && $_GET['tags'] != ''){
    $tags = explode(' ', urldecode($_GET['tags']));

    $filteredTags = array();
    foreach ($tags as $tag) {
      $tag = trim($tag);
      if ($tag != "") {        
        $filteredTags[] = $tag;
      }
    }
    
    // get all the nodes based on the normal tags
    $foundNodes = Search::getInstance()->doSearch($filteredTags, $check_city);

    $output['tags'] = implode(', ', $filteredTags);
  }else{
    $output['has_tags'] = false;
  }
  
  $popupHtml = '';

  usort($foundNodes, "sort_search_results_on_score");
 
  // format the results
  foreach ($foundNodes as $key => $foundNode) {
      
    $location = Location::getLocationObjectOfNode($foundNode->nid);
    
    if($location){

        $title = $foundNode->title;

        $searchResults[] = array(
            'd' => $foundNode->distance, 
            's' => $foundNode->score,
            'n' => $foundNode->nid, 
            'x'=>$foundNode->self, 
            't' => $title, 
            'lo' => $location->getLongitude(), 
            'la' => $location->getLatitude()
        );
    }
  }
  
  $searchResultsJavascript = _merge_and_strip_searchresults_for_js($searchResults, $output['has_tags']);
  
  $output['searchResults'] = $searchResults;
  $output['resultcounttotal'] = count($searchResults); // minus one because of self
  
  // give the no results found message
  $output['nothing_found_message'] = t('No results found based on the given terms.');
  if (helper::value($user, GojiraSettings::CONTENT_TYPE_SEARCH_FAVORITES_FIELD)) {
    $output['nothing_found_message'] = t('No results found based on the given terms within your favorite locations. ');
  }else if(isset($_GET['tags']) && $_GET['tags'] == 'favorites'){
    $output['nothing_found_message'] = t('You have no favorites. ');
  }

  $mobileDetect = new Mobile_Detect();
  if($mobileDetect->isTablet()){
    $output['page_length'] = 5;
  }else{
    $output['page_length'] = 10;
  }
  
  $output['city_in_tag'] = Search::getInstance()->getCityNameFromTags();
  $output['check_city'] = $check_city;
  $output['to_much_results_found'] = Search::getInstance()->toMuchResults;
  $output['user_is_admin'] = $user_is_admin;
  
  if($output['by_id']){
    $location = Location::getLocationObjectOfNode($output['by_id']);
  }else{
    $location = Search::getInstance()->getCenterMap($check_city);  
  }
  
  $output['longitude'] = $location->longitude;
  $output['latitude'] = $location->latitude;
  
  $output['mapSearchResults'] =  array_values($searchResultsJavascript);
  $output['mapSearchResultsCount'] = count($searchResultsJavascript);
  $output['page'] =  'gojirasearch';
  $output['url'] =  '/';
  if ($output['by_id']) {
    $output['loc'] =  $_GET['loc'];
  } else {
    $output['loc'] =  0;
  }
  $output['has_tags'] = $output['has_tags'];
  $output['tags_changed_message'] = t('Tags successfully changed');
  $output['tags_not_changed_message'] = t('Failed to modify tags');
  $output['your_location'] = t('This is your location');

  if(helper::value($user, GojiraSettings::CONTENT_TYPE_SEARCH_GLOBAL_FIELD) && !Search::getInstance()->getCityNameFromTags()){
    $output['longitude'] = variable_get('CENTER_COUNTRY_LONGITUDE');
    $output['latitude'] = variable_get('CENTER_COUNTRY_LATITUDE');
  }
  
  if(helper::value($user, GojiraSettings::CONTENT_TYPE_SEARCH_GLOBAL_FIELD) && !Search::getInstance()->getCityNameFromTags()){
    if (isset($_GET['tags']) && ($_GET['tags'] == 'ownlist')){
        $output['zoom'] = GojiraSettings::MAP_ZOOMLEVEL_REGION;
    }else{
        $output['zoom'] = GojiraSettings::MAP_ZOOMLEVEL_COUNTRY;
    }    
  }else{
    $output['zoom'] = GojiraSettings::MAP_ZOOMLEVEL_STREET;
  }
  
  if(isset($_GET['tags']) && ($_GET['tags'] == 'favorites')) {
    $output['search_favorites'] = 1;
  }
  
  $output['results_html'] = Search::getInstance()->getResultListHtml($output);
  
  $output['single_location'] = $single_location;
  
  echo json_encode($output,true);
  exit;
}

/**
 * merge results if they are to close, based on there coordinates
 * imagine: result 1,2,3 & 4 
 * and 1 & 2 are close to eachother this code will merge this array into the following: a(2=>(1,2),3,4). 2 will have the coordinates of 1
 * and 1, 2 & 3 are close to eachother this code will merge this array into the following: a(2=>(1,2,3),4). 2 will have the coordinates of 1
 * 
 * @param type $searchResults
 */
function _merge_and_strip_searchresults_for_js($searchResults, $hasTags) {
        
  // used array keys, made them shorter for load speed
  // d distance
  // s score
  // n nid
  // x self
  // t title
  // lo longitude
  // la latutude
  // c count_merged
  // m merged
  // h merged_html
    
    
  // always have your own location on the map
  //$self = Location::getCurrentLocationNodeObjectOfUser();
  $selfLocation = Location::getCurrentLocationObjectOfUser();
  
  $searchResults[] = array(
      'd' => 0, 
      's' => 100,
      'n' => 1, 
      'x'=>1, 
      't' => 'self', 
      'lo' => $selfLocation->getLongitude(), 
      'la' => $selfLocation->getLatitude()
  );
  
  $returnArray = array();

  $originalResults = $searchResults; // make a backup of the original data, don't change this info
  $adopted = array(); // this array holds nid's of locations that are merged with others

  foreach ($searchResults as $result) {
    
    unset($result['d']);
    unset($result['s']);
      
    // for each result
    // if i'm never merged before
    if (!array_key_exists($result['n'], $adopted)) {
      
      // check each all other results
      foreach ($originalResults as $originalResult) {
                
        if ($originalResult['n'] != $result['n'] 
                && !array_key_exists($originalResult['n'], $adopted) 
                && Location::locationsAreClose($result['lo'], $result['la'], $originalResult['lo'], $originalResult['la'])) {
          
          // now we know that $result[nid] and $originalResult[nid] belong together

          $mergeMasterNid = $result['n'];

          // merge them
          $returnArray[$mergeMasterNid]['m'][$originalResult['n']] = $originalResult;
          $returnArray[$mergeMasterNid]['m'][$result['n']] = $result;

          $returnArray[$mergeMasterNid]['c'] = count($returnArray[$mergeMasterNid]['m']);
          $returnArray[$mergeMasterNid]['lo'] = $result['lo'];
          $returnArray[$mergeMasterNid]['la'] = $result['la'];
          
          // let's remind them
          $adopted[$originalResult['n']] = $originalResult['n'];
          $adopted[$result['n']] = $result['n'];
        } else {
          // not close to anyone, let's store it if it's not stored allready
          if (!array_key_exists($result['n'], $adopted)) {
            $adopted[$result['n']] = $result['n'];
            $returnArray[$result['n']] = $result;
            $returnArray[$result['n']]['c'] = 0;
          }
        }
      }
    }
  }
  
  
  
  foreach ($returnArray as $key => $return) {
    $selfHtml = null;
    if ($return['c'] > 1) {
      $mergedHtml = '';
      foreach ($return['m'] as $mergedOne) {
        if(isset($mergedOne['x']) && $mergedOne['x'] == '1'){
          $selfHtml = '<li class="self_popup_link">'.t('Your own location is also on this spot').'</li>';
          $returnArray[$key]['x'] = '1';
        }else if ($hasTags) {
          $mergedHtml .= '<li id="map_link_to_' . $mergedOne['n'] . '" class="map_link_to "><a onClick="focusLocation(' . $mergedOne['n'] . ');return false;" href="#' . $mergedOne['n'] . '" title="' . $mergedOne['t'] . '">' . $mergedOne['t'] . '</a></li>';
        } else {
          $mergedHtml .= '<li id="map_link_to_' . $mergedOne['n'] . '" class="map_link_to "><a onClick="gotoLocation(' . $mergedOne['n'] . ');return false;" href="#' . $mergedOne['n'] . '" title="' . $mergedOne['t'] . '">' . $mergedOne['t'] . '</a></li>';
        }
        
      }
      
      if($selfHtml !== null){
          $mergedHtml = $selfHtml.$mergedHtml;
      }
      
      $mergedHtml = '<ul>' . $mergedHtml . '</ul>';
      
      $returnArray[$key]['h'] = $mergedHtml;
      
    }
  }
  
  return $returnArray;
}

function sort_search_results_on_score($a, $b)
{
    if($a->self){
      return 1;
    }
    if($a->self){
      return -1;
    }
  
    if ($a->score == $b->score) {
      
      if($a->distance > $b->distance){
        return 1;
      }else if ($a->distance > $b->distance){
        return -1;
      }else{
        return 0;
      }
    }
    return ($a->score > $b->score) ? -1 : 1;
}