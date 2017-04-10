<?php
/**
 * Saves a new label
 */
function savenewlabel() {
  if(Labels::addAndScoreLabel($_GET['label'], $_GET['nid'])){
      echo json_encode(array('success'=>true));
  }else{
      echo json_encode(array('success'=>false));
  }
}