<?php
function gojirareport_location_by_tag(){
  set_time_limit(300);
  
  $tag = '';
  if(isset($_GET['tag'])){
    $tag = $_GET['tag'];
  }
  
  $result = db_query("select node.nid, node.title from {node} join field_data_field_location_labels on (field_data_field_location_labels.entity_id = node.nid) join taxonomy_term_data on (taxonomy_term_data.tid = field_data_field_location_labels.field_location_labels_tid) where node.type = 'location' and taxonomy_term_data.name = '".$tag."'")->fetchAll();
  
  $nodes = array();
  foreach ($result as $node) {
    $nodes[] = $node;
  }
  
  $labels = db_query("select taxonomy_term_data.name from taxonomy_term_data order by name");
  
  return theme('gojirareport_location_by_tag', array('locations' => $nodes, 'labels' => $labels));
}