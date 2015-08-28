<?php

/**
 * This class is rasponsible for all the Postcode lookup related tasks. Mosly used to get the postcode info for a location.
 */
class Postcode {

  public static $instance = null;

  public static function getInstance() {
    if (is_null(self::$instance)) {
      self::$instance = new Postcode();
    }
    return self::$instance;
  }

  /**
   * Get's a Location object for the given addres info.
   * Return's null if nothing is found.
   * 
   * @param String $postcode
   * @param String $number
   * @param String $addition
   * @return \Location|null
   */
  public function getGeoInfo($postcode, $number) {

    $postcode = trim(str_replace(' ', '', strtoupper($postcode)));
    $number = trim($number);

    $addressKey = $this->getUniqueKeyFormat($postcode, $number);
    $coords = db_query("select coordinates_x as x, coordinates_y as y, street, houseNumber, city from address_cache where address = :address", array(':address' => $addressKey))->fetch();
    
    if ($coords) {
      return new Location($coords->x, $coords->y, $coords->street, $coords->houseNumber, $coords->city);
    } else {

      $numberInfo = $this->splitAddressNumber($number);

      $response = $this->postcodeNLcall($postcode, $numberInfo['number'], $numberInfo['addition']);
      
      if($response === false){
        return null;
      }
      
      if (array_key_exists('exception', $response)) {
        watchdog('postcode', $response['exception']);
        return null;
      }

      if (is_null($response['houseNumberAddition'])) {
        $response['houseNumberAddition'] = '';
      }
      if(trim($response['longitude']) != '' && trim($response['latitude']) != ''){
        $params = array(':address' => $addressKey, ':x' => $response['longitude'], ':y' => $response['latitude'], ':street' => $response['street'], ':houseNumber' => $response['houseNumber'], ':houseNumberAddition' => $response['houseNumberAddition'], ':postcode' => $response['postcode'], ':city' => $response['city'], ':municipality' => $response['municipality'], ':addressType' => $response['addressType']);
        db_query("INSERT INTO `address_cache` (`address`, `coordinates_x`, `coordinates_y`, `street`, `houseNumber`, `houseNumberAddition`, `postcode`, `city`, `municipality`, `addressType`) VALUES (:address, :x, :y, :street, :houseNumber, :houseNumberAddition, :postcode, :city, :municipality, :addressType)", $params);
        return new Location($response['longitude'], $response['latitude'], $response['street'], $response['houseNumber'], $response['city']);
      }
      return null;
    }
  }

  public function postcodeNLcall($postcode, $number, $addition) {

    $serviceUrl = 'https://api.postcode.nl/rest/addresses/';
    $serviceKey = variable_get('postocde_nl_key');
    $serviceSecret = variable_get('postocde_nl_secret');

    $url = $serviceUrl . $postcode . '/' . $number . '/' . $addition;
    
    try{
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_USERPWD, $serviceKey . ':' . $serviceSecret);
      curl_setopt($ch, CURLOPT_USERAGENT, 'idaniel');
      $jsonResponse = curl_exec($ch);
      $curlError = curl_error($ch);
      curl_close($ch);
    } catch (Exception $ex) {
      watchdog('postcode', "PostcodeNL curl call error: ".$ex->getMessage());
      return false;
    }
    
    if($curlError !== ""){
      watchdog('postcode', "PostcodeNL curl call error: ".$curlError);
      return false;
    }
    
    return json_decode($jsonResponse, true);
  }

  private function splitAddressNumber($number) {
    $numberInfo = str_split($number);
    $number = '';
    $numberAddition = '';
    $skipInteger = false;
    foreach ($numberInfo as $part) {
      if (is_numeric($part) && !$skipInteger) {
        $number .= $part;
      } else if (!is_integer($part)) {
        $skipInteger = true;
        $numberAddition .= $part;
      }
    }

    return array('number' => $number, 'addition' => $numberAddition);
  }

  private function getUniqueKeyFormat($postcode, $number) {
    return $postcode . '' . $number;
  }

}
