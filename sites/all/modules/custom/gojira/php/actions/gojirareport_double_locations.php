<?php
function gojirareport_double_locations(){
  set_time_limit(300);
  
  $result = db_query("select nid, title, X(point) as x, Y(point) as y from {node} where type = 'location'")->fetchAll();
  
  $doubleCoordinates = array();
  foreach ($result as $entry) {
    $doubleCoordinates[$entry->x . '_' . $entry->y] = array();
  }
  
  foreach ($result as $key => $entry) {
    $doubleCoordinates[$entry->x . '_' . $entry->y][] = $entry;
  }
  
  foreach ($doubleCoordinates as $key => $lookup) {
    if (count($lookup) == 1) {
      unset($doubleCoordinates[$key]);
    }
  }
  
  $noCoordinates = array();
  if(isset($doubleCoordinates['_'])){
    $noCoordinates = $doubleCoordinates['_'];
  }
  
  unset($doubleCoordinates['_']);
  
  return theme('gojirareport_double_locations', array('locations' => $doubleCoordinates, 'no_coordinates'=>$noCoordinates));
}