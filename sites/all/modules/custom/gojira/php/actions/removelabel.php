<?php
/**
 * Removes a new label from location
 */
function removelabel() {
  $nid = $_GET['nid'];
  $tid = $_GET['tid'];

  $node = node_load($nid);
  $new_labels = array();
  foreach($node->field_location_labels[LANGUAGE_NONE] as $label){
    if($label['tid'] != $tid){
      $new_labels[] = $label;
    }
  }
  
  $node->field_location_labels[LANGUAGE_NONE] = $new_labels;
  
  node_save($node);
  
  Search::getInstance()->updateSearchIndex($nid);
}