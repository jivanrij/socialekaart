<?php
function list_double(){
    
    
  $ids_from_mail = filter_input(INPUT_GET, 'ids_from_mail');
  
  if(is_null($ids_from_mail)){
  
    set_time_limit(3000000);

    $rResult = db_query("select count(nid) as count, X(point) as x, Y(point) as y, source from node where status = 1 AND source != 'spider' AND source != 'double' and type = 'location' group by point limit 1000")->fetchAll();

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
        $aDoubleLocations[$sKey] = db_query("select nid, title, source, X(point) as x, Y(point) as y from node where status = 1 AND source != 'spider' AND source != 'double' and type = 'location' and X(point) = {$x} and Y(point) = {$y}")->fetchAll();
    }
  }else{
      $aDoubleLocations[123456] = db_query("select nid, title, source, X(point) as x, Y(point) as y from node where nid in({$ids_from_mail})")->fetchAll();
  }
  
  return theme('list_double', array('aDoubleLocations'=>$aDoubleLocations));
}