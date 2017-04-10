<?php
function inform(){
  
  drupal_add_js(array('gojira' => array('page' => 'inform')), 'setting');
  
  $output['form'] = drupal_get_form('gojira_inform_form');
  
  $node = false;
  if(isset($_GET['nid'])){
    $node = node_load($_GET['nid']);
  }
  $output['location'] = $node;
  
  $location = Location::getCurrentLocationObjectOfUser(true);
  
  drupal_add_js(array('gojira' => array('url' => '/')), 'setting');
  
  return theme('inform', array('output' => $output));
}