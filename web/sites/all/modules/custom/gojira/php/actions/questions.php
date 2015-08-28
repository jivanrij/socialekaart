<?php
function questions(){

  drupal_add_js(array('gojira' => array('page' => 'questions')), 'setting');
  
  $sCurrentUrl = 'questions';
  if(isset($_GET['topic'])){
      $sCurrentUrl = $_GET['topic'];
  }
  
  
  return theme('questions', array('sCurrentUrl'=>$sCurrentUrl));
}