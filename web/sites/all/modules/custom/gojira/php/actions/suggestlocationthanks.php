<?php
function suggestlocationthanks() {
  $node = false;
  if(isset($_GET['nid']) && is_numeric($_GET['nid'])){
    $node = node_load($_GET['nid']);
  }
  return theme('suggestlocationthanks',array('oNewLocation'=>$node));
}