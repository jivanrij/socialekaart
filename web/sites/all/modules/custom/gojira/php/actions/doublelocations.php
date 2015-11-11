<?php
function doublelocations(){
    
  $ids_from_mail = filter_input(INPUT_GET, 'ids_from_mail');
  
  $aDoubleLocations = array();
  
  if(is_null($ids_from_mail)){
  
    set_time_limit(3000000);

    //HW links boven
    //51.8406974
    //4.2369379
    //HW rechts onder
    //51.7026117
    //4.6240819
    
    $limitArea = " (X(point) BETWEEN 4.2369379 AND 4.6240819) AND (Y(point) BETWEEN 51.8406974 AND 51.7026117) AND ";
    
    $limitArea = "";
    
    $rResult = db_query("select X(point) as x, Y(point) as y from node where {$limitArea} type = 'location' and source != 'spider' and point is not null and double_checked != 1 group by point having count(nid)>1 limit 50")->fetchAll();
    
    foreach($rResult as $oResult){
        $x = $oResult->x;
        $y = $oResult->y;
        $aDoubleLocations[str_replace('.','',$oResult->x.$oResult->y)] = db_query("select nid, title, source, X(point) as x, Y(point) as y from node where status = 1 AND source != 'spider' and type = 'location' and X(point) = {$x} and Y(point) = {$y}")->fetchAll();
    }
    
  }else{
      $aDoubleLocations[123456] = db_query("select nid, title, source, X(point) as x, Y(point) as y from node where nid in({$ids_from_mail})")->fetchAll();
  }
  
  return theme('doublelocations', array('aDoubleLocations'=>$aDoubleLocations));
}