<?php
/**
 * check is address exists via ajax
 */
function api_locations() {
exit;
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Origin:*');
header('Cache-Control:no-cache');
header('Connection:keep-alive');
header('Content-Length:0');

  $values = array();
  
  $locations = db_query("select nid from node where type = 'location' limit 10");
  
  foreach($locations as $location){
      $node = node_load($location->nid);
      $values[$location->nid] = array('title'=>$node->title, 'adres'=>Location::getAddressString($node), 'labels'=>Labels::getLabels($node));
  }
  
  echo json_encode($values);
  exit;
}

/**
 * Get's the locations based on a locationset id, search & api key
 * example: /api/mapsearch?id=884322&key=diafnadi3849547adfn9xn8f&search=Psychologie
 */
function api_mapsearch() {
    $nid = $_GET['id'];
    $key = $_GET['key'];
    $search = null;
    if(trim($_GET['search']) !== ''){
        $search = htmlentities($_GET['search']);
    } else{
        $search = null;
    }

    header('Access-Control-Allow-Credentials:true');
    header('Access-Control-Allow-Origin:*');
    header('Cache-Control:no-cache');
    header('Connection:keep-alive');
    header('Content-Length:0');


    $values = array();

    $nid = db_query("select nid from node where nid = :nid and type = 'locationset'", array('nid' => $nid))->fetchField();

    $locationset = \Models\Locationset::load($nid);

    if($locationset->getAPIKey() !== $key) {
        return 'Do. Or do not. There is no try';
    }

    $locations = Locationsets::getInstance()->getLocations($nid, null, $search);

    $lonLow = null;
    $lonHigh = null;
    $latLow = null;
    $latHigh = null;

    $locs = array();
    foreach($locations as $location) {

        $locObject = Location::getLocationObjectOfNode($location->nid);

        if(empty($locObject->longitude) || empty($locObject->latitude)) {
            continue;
        }

        $loc = array();
        $loc['title'] = $location->title;
        $loc['mail'] = $location->field_email[LANGUAGE_NONE][0]['value'];
        $loc['city'] = $location->field_address_city[LANGUAGE_NONE][0]['value'];
        $loc['street'] = $location->field_address_street[LANGUAGE_NONE][0]['value'];
        $loc['number'] = $location->field_address_streetnumber[LANGUAGE_NONE][0]['value'];
        $loc['postcode'] = $location->field_address_postcode[LANGUAGE_NONE][0]['value'];
        $loc['telephone'] = $location->field_telephone[LANGUAGE_NONE][0]['value'];
        $loc['url'] = $location->field_url[LANGUAGE_NONE][0]['value'];
        $category = Category::getCategoryOfLocation($location);
        $loc['category'] = $category->title;
        $loc['category_id'] = $category->nid;
        $loc['longitude'] = $locObject->longitude;
        $loc['latitude'] = $locObject->latitude;
        $locs[] = $loc;

        if ((is_null($latLow) || $loc['latitude'] <= $latLow) && !is_null($loc['latitude'])) {
            $latLow = $loc['latitude'];
        }
        if ((is_null($lonLow) || $loc['longitude'] <= $lonLow)  && !is_null($loc['longitude'])) {
            $lonLow = $loc['longitude'];
        }
        if ((is_null($latHigh) || $loc['latitude'] >= $latHigh)  && !is_null($loc['latitude'])) {
            $latHigh = $loc['latitude'];
        }
        if ((is_null($lonHigh) || $loc['longitude'] >= $lonHigh)  && !is_null($loc['longitude'])) {
            $lonHigh = $loc['longitude'];
        }
    }

    $returnLocations['bounds'] = array(
        'latLow' => $latLow,
        'lonLow' => $lonLow,
        'latHigh' => $latHigh,
        'lonHigh' => $lonHigh,
    );
    $returnLocations['locations'] = $locs;

    echo json_encode($returnLocations);
    exit;
}