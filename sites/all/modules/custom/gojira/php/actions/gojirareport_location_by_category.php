<?php
function gojirareport_location_by_category(){
  set_time_limit(300);
  
  $category_nid = -1;
  $locations = array();
  if(isset($_GET['category'])){
    $category_nid = $_GET['category'];
    //$locations = db_query("select * from node join field_data_field_category on (node.nid = field_data_field_category.entity_id) join node as category on (category.nid = field_data_field_category.field_category_nid) where category.title = '{$category}'")->fetchAll();
    $locations = db_query("select * from node join field_data_field_category on (node.nid = field_data_field_category.entity_id) where field_data_field_category.field_category_nid = '{$category_nid}'")->fetchAll();
  }
  
  $catagory_type = GojiraSettings::CONTENT_TYPE_CATEGORY;
  $categories = db_query("select node.nid, node.title from {node} where type = '{$catagory_type}'")->fetchAll();
  
  return theme('gojirareport_location_by_category', array('locations' => $locations, 'categories' => $categories));
}