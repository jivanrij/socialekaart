<?php
/**
 * check is address exists via ajax
 */
function api_locations() {
    
  $values = array();
  
  $locations = db_query("select nid from node where type = 'location' limit 10");
  
  foreach($locations as $location){
      $node = node_load($location->nid);
      $values[$location->nid] = array('title'=>$node->title);
  }
  
  echo json_encode($values);
  exit;
}