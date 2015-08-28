<?php

/**
 * This class acts as a function wrapper for all kinds of location related tasks with all the static functions.
 * The not static part of the class represents a bundle of geo information of a location.
 */
class Location {

    public $longitude;
    public $latitude;
    public $city;
    public $houseNumber;
    public $street;
    public $nid;
    private static $_userLocations = null;
    private static $_knownLocations = null;

    /**
     * 
     * @param Float $x
     * @param Float $y
     * @param String $street
     * @param Integer $houseNumber
     * @param String $city
     */
    public function __construct($longitude, $latitude, $street = null, $houseNumber = null, $city = null, $nid = null) {
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->city = $city;
        $this->street = $street;
        $this->houseNumber = $houseNumber;
        $this->nid = $nid;
    }

    public function getLongitude() {
        return $this->longitude;
    }

    public function getNodeId() {
        return $this->nid;
    }

    public function getLatitude() {
        return $this->latitude;
    }

    public function getStreet() {
        return $this->street;
    }

    public function getHouseNumber() {
        return $this->houseNumber;
    }

    public function getCity() {
        return $this->city;
    }

    /**
     * Get's a Location object of the linked node
     * 
     * @param integer $nid
     * @return \Location
     */
    public static function getLocationObjectOfNode($nid) {
        $locationInfo = db_query("select X(point) as x, Y(point) as y from {node} where nid = :nid", array(':nid' => $nid))->fetchObject();

        if (!$locationInfo || is_null($locationInfo->x) || is_null($locationInfo->y)) {
            watchdog('location', "Location {$nid} has no coordinates.");
            return false;
        }
        return new Location($locationInfo->x, $locationInfo->y);
    }

    /**
     * Tells you if the current user has multiple locations stored in the dtb
     * 
     * @return boolean
     */
    public static function userHasMultipleLocationsStored(){
        $aLocations = Location::getUsersLocations();
        $iAmount = count($aLocations);
        if ($iAmount > 1) { // we have multiple locations, let's check if there is a preference
            return true;
        }
        return false;
    }

    /**
     * Get's you a location object to use as a base location of the currently logged in user.
     * if fallback is true, you will get a default Location if there is no location found
     * 
     * @param boolean $fallback
     * @return \Location|boolean
     */
    public static function getCurrentLocationObjectOfUser($bFallback = false) {

        $aLocations = Location::getUsersLocations();
        $iAmount = count($aLocations);

        if ($iAmount == 0) { // no location found, let's check fallback option
            if ($bFallback) {
                return new Location(variable_get('CENTER_COUNTRY_LONGITUDE'), variable_get('CENTER_COUNTRY_LATITUDE'));
            }
            return false;
        } else if ($iAmount > 1) { // we have multiple locations, let's check if there is a preference
            // get selected location and return it.
            $oUser = helper::getUser();
            $iSelectedLocation = (int) helper::value($oUser, GojiraSettings::CONTENT_TYPE_USER_LAST_SELECTED_LOCATION, 'nid');
            if(is_integer($iSelectedLocation)){
                $oSelectedLocation = Location::getLocationObjectOfNode($iSelectedLocation);
                if($oSelectedLocation){
                    $oSelectedLocation->nid = $iSelectedLocation;
                    return $oSelectedLocation;
                }
            }
        }

        // let's just return one if ther is just one found or no known preference for one
        $oLocationNode = array_shift($aLocations);
        $oLocation = Location::getLocationObjectOfNode($oLocationNode->nid);
        $oLocation->nid = $oLocationNode->nid;
        return $oLocation;
    }

    /**
     * Get's the current location node of the logged in user.
     * 
     * @return Location
     */
    public static function getCurrentLocationNodeObjectOfUser() {
        $nid = self::getCurrentLocationObjectOfUser();
        return node_load($nid->nid);
    }

    /**
     * This function will return you a Location Class that belongs to the given address.
     * If it can not find it, it try's to get the address from google.
     *
     * @param string address
     * @return Location
     */
    public static function getLocationForAddress($address) {

        $address = trim($address);
        $coords = db_query("select coordinates_x as x, coordinates_y as y from address_cache where address = :address", array(':address' => $address))->fetch();

        if ($coords) {
            return new Location($coords->x, $coords->y);
        } else {
            $point = geocoder('google', $address);
            if ($point) {
                try {
                    $address_std = json_decode($point->out('json'));
                    $location = new Location($address_std->coordinates[0], $address_std->coordinates[1]);
                    db_query("INSERT INTO `address_cache` (`address`, `coordinates_x`, `coordinates_y`) VALUES (:address, :x, :y)", array(':address' => $address, ':x' => $location->longitude, ':y' => $location->latitude));
                } catch (Exception $e) {
                    return false;
                }
            } else {
                return false;
            }
            return $location;
        }
    }

    /**
     * Gives you the 4 fields used for getting the location/geo information in gojira
     * 
     * @return Array
     */
    public static function getAddressFields() {
        return array(GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD, GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD, GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD, GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD);
    }

    /**
     * Format's the address to search with in the google api
     *
     * @param string $city
     * @param string $street
     * @param string $streetnumber
     * @return string
     */
    public static function formatAddress($city, $street, $streetnumber, $postcode) {
        return $street . ' ' . $streetnumber . ', ' . $city . ', ' . str_replace(' ', '', strtolower($postcode));
    }

    /**
     * Get's the address string of a node to search with based on the correct format
     * This node needs to have the fields defined in the config & self::getAddressFields();
     *
     * @param stdClass $node
     * @return string
     */
    public static function getAddressString($node) {
        //array(4) { [0]=> string(18) "field_address_city" [1]=> string(20) "field_address_street" [2]=> string(26) "field_address_streetnumber" [3]=> string(22) "field_address_postcode" }
//        var_dump(self::getAddressFields(), $node);
//        die;
        
        foreach (self::getAddressFields() as $field) {
            $thisField = $node->$field;
            if (!array_key_exists('und', $thisField) || !array_key_exists(0, $thisField['und']) || !array_key_exists('value', $thisField['und'][0])) {
                watchdog('location', 'The node with nid ' . $node->nid . ' passed to GojiraFunctions::getAddressString() has no correct address.');
                return false;
            }
        }
        $cityfield = GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD;
        $cityfield = $node->$cityfield;
        $streetfield = GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD;
        $streetfield = $node->$streetfield;
        $streetnumberfield = GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD;
        $streetnumberfield = $node->$streetnumberfield;
        $postcodefield = GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD;
        $postcodefield = $node->$postcodefield;

        return self::formatAddress(
                        helper::value($node, GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD), helper::value($node, GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD), helper::value($node, GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD), helper::value($node, GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD));
    }

    /**
     * stores the location coordinates found in the node fields address in the node table
     * 
     * @param stdClass $node
     */
    public static function checkAndSaveLocation($node) {
        
        $location = self::GetLocationForAddress(
                        self::getAddressString($node)
        );
        if ($location) {
            self::StoreLocatioInNode($location, $node->nid);
        }
    }

    /**
     * Stores a location in a node
     *
     * @param Location
     * @param integer $node_id
     */
    public static function storeLocatioInNode(Location $location, $node_id) {
        db_query("UPDATE `node` SET point = GeomFromText('POINT(" . $location->longitude . " " . $location->latitude . ")'), `source`=:source WHERE  `nid`=:nid", array(':nid' => $node_id, ':source' => 'gojira'));
    }

    /**
     * Return's all the linked location nodes from the given/current user
     * 
     * @global stdClass $user
     * @param integer $uid
     * @return array of stdClass
     */
    public static function getUsersLocations() {

        if (self::$_userLocations === null) {

            self::$_userLocations = array();

            global $user;
            $uid = $user->uid;

            $user = user_load($uid);
            $groupField = GojiraSettings::CONTENT_TYPE_GROUP_FIELD;
            $groupField = $user->$groupField;

            if(isset($groupField[LANGUAGE_NONE]) && isset($groupField[LANGUAGE_NONE][0]) && isset($groupField[LANGUAGE_NONE][0]['nid'])){
                $gid = $groupField[LANGUAGE_NONE][0]['nid'];
            }else{
                $gid = 0;
            }
            
            $query = new EntityFieldQuery();
            $query->entityCondition('entity_type', 'node')
                    ->entityCondition('bundle', GojiraSettings::CONTENT_TYPE_LOCATION)
                    ->fieldCondition('field_gojira_group', 'nid', $gid, '=');
            //->fieldCondition(GojiraSettings::CONTENT_TYPE_MODERATED_STATUS_FIELD, 'value', array(1,4), 'in');

            $result = $query->execute();

            if (isset($result['node'])) {
                foreach ($result['node'] as $node) {
                    self::$_userLocations[$node->nid] = node_load($node->nid);
                }
            }
        }
        return self::$_userLocations;
    }

    /**
     * Removes a user location from database and userlocation array in static location object
     * 
     * @param type $nid
     */
    public static function removeUserLocation($nid) {
        global $user;
        if (helper::canChangeLocation($user->uid, $nid)) {
            node_delete($nid);
            unset(self::$_userLocations[$nid]);
        }
    }

    /**
     * This function tells you if the given coordinates are close to eachother
     * 
     * @param float $a_long
     * @param float $a_lat
     * @param float $b_long
     * @param float $b_lat
     * @return boolean
     */
    public static function locationsAreClose($a_long, $a_lat, $b_long, $b_lat) {

        $differance = 0.0001;

        $diff_long = abs($a_long - $b_long);
        $diff_lat = abs($a_lat - $b_lat);

//    if($a_long == $b_long && $a_lat == $b_lat){
//      return true;
//    }

        if ($diff_long == 0 && $diff_lat == 0) {
            return true;
        }

        if (is_numeric($diff_lat) && is_numeric($diff_long) && $diff_lat != 0 && $diff_long != 0) {
            if (($diff_long < $differance) && ($diff_lat < $differance)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get's you all the known Location objects of all the known city's
     * The key in the array is the name of the city in lowercase
     * 
     * @return array of city names
     */
    public static function getKnownCitys() {
        if (self::$_knownLocations === null) {
            self::$_knownLocations = array();
            $return = array();
            $result = db_query('select field_address_city_value from {field_data_field_address_city} group by field_address_city_value');
            foreach ($result as $entity) {
                $return[strtolower($entity->field_address_city_value)] = strtolower($entity->field_address_city_value);
            }
            self::$_knownLocations = $return;
        }
        return self::$_knownLocations;
    }

    /**
     * Tels you if the given name is a city
     * 
     * @param string $name
     * @return boolean
     */
    public static function isKnownCity($name) {
        if (array_key_exists(strtolower($name), self::getKnownCitys())) {
            return true;
        }
        return false;
    }

}
