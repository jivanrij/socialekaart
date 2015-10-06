<?php
function list_double(){
  set_time_limit(3000000);

//  $rResult = db_query("select count(nid) as count, X(point) as x, Y(point) as y from node where type = 'location' group by point limit 10000")->fetchAll();

  $rResult = array();
  
  $aDoublesCoordinates = array();
  foreach($rResult as $oResult){
      if($oResult->count > 1 && ($oResult->x.$oResult->y !== '')){
          $aDoublesCoordinates[$oResult->x.$oResult->y]['x'] = $oResult->x;
          $aDoublesCoordinates[$oResult->x.$oResult->y]['y'] = $oResult->y;
      }
  }

  $aDoubleLocations = array();
  foreach($aDoublesCoordinates as $sKey=>$aDoublesCoordinate){
      $x = $aDoublesCoordinate['x'];
      $y = $aDoublesCoordinate['y'];
      $aDoubleLocations[$sKey] = db_query("select nid, title from node where type = 'location' and X(point) = {$x} and Y(point) = {$y}")->fetchAll();
  }
  
  return theme('list_double', array('aDoubleLocations'=>$aDoubleLocations));
}