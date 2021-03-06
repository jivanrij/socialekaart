<?php
function doublelocations(){
  
  $sql = '';
    
  $ids_from_mail = filter_input(INPUT_GET, 'ids_from_mail');
  
  $specific_map = filter_input(INPUT_GET, 'map');
  
  $aDoubleLocations = array();
  
  if(is_null($ids_from_mail)){
  
    set_time_limit(3000000);

    //HW links boven
    //51.8406974
    //4.2369379
    //HW rechts onder
    //51.7026117
    //4.6240819
    
    //Epe links boven
    //52.530148
    //5.7489782
    //Epe rechts onder
    //52.2312446
    //6.2384336
    
    $limitArea = "";
    if($specific_map == 'hw'){
        $limitArea = " (X(point) > 4.2369379 AND X(point) < 4.6240819) AND (Y(point) > 51.7026117 AND Y(point) < 51.8406974) AND ";
    }else if($specific_map == 'epe'){
        $limitArea = " (X(point) > 5.7489782 AND X(point) < 6.2384336) AND (Y(point) > 52.2312446 AND Y(point) < 52.530148) AND ";
    }

    $criteria = " {$limitArea} type = 'location' and source != 'spider' and point is not null and double_checked != 1 ";
    
    $sql = "select X(point) as x, Y(point) as y from node where {$criteria} group by point having count(nid)>1 limit 250";
    
    $rResult = db_query($sql)->fetchAll();
    
    foreach($rResult as $oResult){
        $x = $oResult->x;
        $y = $oResult->y;
        $aDoubleLocations[str_replace('.','',$oResult->x.$oResult->y)] = db_query("select nid, title, source, X(point) as x, Y(point) as y from node where {$criteria} and X(point) = {$x} and Y(point) = {$y}")->fetchAll();
    }
    
  }else{
      $aDoubleLocations[123456] = db_query("select nid, title, source, X(point) as x, Y(point) as y from node where nid in({$ids_from_mail})")->fetchAll();
  }
  
  return theme('doublelocations', array('aDoubleLocations'=>$aDoubleLocations, 'query'=>$sql));

}