<?php
function ownlist() {
  $oCurrentLocation = Location::getCurrentLocationObjectOfUser();
  $favorites = Favorite::getInstance()->getAllFavoriteLocations(true, $oCurrentLocation->nid);
  
  $ordered_categorys = array();
  $ordered_locations = array();
  
  foreach($favorites as $favorite){
    $category_nid = helper::value($favorite, GojiraSettings::CONTENT_TYPE_CATEGORY_FIELD, 'nid');
    $category_name = Category::getCategoryName($favorite);
    
    $favorite->labels = implode(', ', Labels::getLabels($favorite));
    $favorite->category_nid = $category_nid;
    $favorite->category_name = $category_name;
    
    $ordered_categorys[$category_name] = $favorite;
    $ordered_locations[$favorite->title] = $favorite;
  }
  
  ksort($ordered_categorys);
  ksort($ordered_locations);  
    
  drupal_add_js(array('gojira' => array('page' => 'ownlist')), 'setting');
  
  return theme('ownlist', array('ordered_locations'=>$ordered_locations, 'ordered_categorys'=>$ordered_categorys, 'has_locations' => count($ordered_locations)));
}
