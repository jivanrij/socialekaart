<?php
/**
 * check is address exists via ajax
 */
function api_locations() {
    exit;
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Origin:*');
header('Cache-Control:no-cache');
header('Connection:keep-alive');
header('Content-Length:0');

  $values = array();
  
  $locations = db_query("select nid from node where type = 'location' limit 10");
  
  foreach($locations as $location){
      $node = node_load($location->nid);
      $values[$location->nid] = array('title'=>$node->title, 'adres'=>Location::getAddressString($node), 'labels'=>Labels::getLabels($node));
  }
  
  echo json_encode($values);
  exit;
}