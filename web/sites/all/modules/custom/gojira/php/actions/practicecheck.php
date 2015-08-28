<?php

/**
 * This page generates a form to crud a employee
 *
 * @return string
 */
function practicecheck() {

    $aResults = array();

    if (isset($_POST['terms']) && trim($_POST['terms']) != '') {
        $aTerms = explode(' ', $_POST['terms']);

        $aCleanTerms = array();
        foreach ($aTerms as $sTerm) {
            $aCleanTerms[] = helper::cleanSearchTag($sTerm);
        }

        $aSql = array();
        $aParams = array();
        for ($i = 0; $i < count($aCleanTerms); $i++) {
            $aSql[] = "(title like :term{$i} or field_data_field_address_street.field_address_street_value like :term{$i} or field_data_field_address_city.field_address_city_value like :term{$i})";
            $aParams[':term'.$i] = '%'.$aCleanTerms[$i].'%';
        }

        $sSqlPart = '(' . implode(' and ', $aSql) . ')';

        $sSql = <<<EOT
select node.nid, node.title, field_data_field_address_city.field_address_city_value as city from node 
join field_data_field_address_street on (field_data_field_address_street.entity_id = node.nid) 
join field_data_field_address_city on (field_data_field_address_city.entity_id = node.nid) 
where type = 'location'
and {$sSqlPart} group by node.title order by node.title
EOT;
//
//echo $sSql;
//die;

        $oResults = db_query($sSql, $aParams)->fetchAll();

        foreach($oResults as $oResult){
            $aCitys[$oResult->city] = $oResult->city;
        }
        
        foreach($aCitys as $sCity){
            $aCityFiller = array();
            foreach($oResults as $oResult){
                if($oResult->city == $sCity){
                    $aCityFiller[] = node_load($oResult->nid);
                }
            }
            $aResults[$sCity] = array('sCity' => $sCity, 'aLocations'=> $aCityFiller);
        }
        
        ksort($aResults);
    }
    
    $sTerms = '';
    if (isset($_POST['terms']) && trim($_POST['terms']) != '') {
        $sTerms = trim($_POST['terms']);
    }
    
    return theme('practicecheck', array('aResults' => $aResults,'sTerms'=>$sTerms));
}
