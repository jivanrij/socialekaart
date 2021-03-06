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
    private static $_userLocationsWithCheck = null;
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
            watchdog(GojiraSettings::WATCHDOG_LOCATION, "Location {$nid} has no coordinates.");
            return false;
        }
        return new Location($locationInfo->x, $locationInfo->y);
    }

    /**
     * Tells you if the current user has multiple locations stored in the dtb
     *
     * @return boolean
     */
    public static function userHasMultipleLocationsStored() {
        $aLocations = Location::getUsersLocations(true);
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

        $aLocations = Location::getUsersLocations(true);

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
            if (is_integer($iSelectedLocation)) {
                $oSelectedLocation = Location::getLocationObjectOfNode($iSelectedLocation);
                if ($oSelectedLocation) {
                    $oSelectedLocation->nid = $iSelectedLocation;
                    return $oSelectedLocation;
                }
            }
        }

        // let's just return one if there is just one found or no known preference for one
        $oLocationNode = array_shift($aLocations);
        $oLocation = Location::getLocationObjectOfNode($oLocationNode->nid);
        if($oLocation){
            $oLocation->nid = $oLocationNode->nid;
            return $oLocation;
        }

        return false;
    }

    /**
     * Get's the current location node of the logged in user.
     *
     * @return Location
     */
    public static function getCurrentLocationNodeObjectOfUser() {
        $nid = self::getCurrentLocationObjectOfUser();
        $locationModel = \Models\Location::load($nid->nid);
        return $locationModel->object;
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

            $coordinates = self::getCoordinatesCustom($address);

            if (is_array($coordinates)) {
                $locationObject = new Location($coordinates['latitude'], $coordinates['longitude']);
                if ($locationObject) {
                    db_query("INSERT INTO `address_cache` (`address`, `coordinates_x`, `coordinates_y`) VALUES (:address, :x, :y)", array(':address' => $address, ':x' => $locationObject->longitude, ':y' => $locationObject->latitude));
                    return $locationObject;
                }
            }
            watchdog(GojiraSettings::WATCHDOG_LOCATION, sprintf('Unable to find coords for location %s', $address));
            return false;
        }
    }

    public static function getCoordinatesCustom($address) {
        $address = str_replace(" ", "+", $address); // replace all the white space with "+" sign to match with google search pattern
        $url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=$address";

        $response = file_get_contents($url);

        $json = json_decode($response, TRUE); //generate array object from the response from the web

        if(($json['status'] !== 'ZERO_RESULTS') && isset($json['results'][0])){
            return array('latitude'=>$json['results'][0]['geometry']['location']['lng'], 'longitude'=>$json['results'][0]['geometry']['location']['lat']);
        }
        return false;

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

        foreach (self::getAddressFields() as $field) {
            $thisField = $node->$field;
            if (!array_key_exists('und', $thisField) || !array_key_exists(0, $thisField['und']) || !array_key_exists('value', $thisField['und'][0])) {
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
            return true;
        }
        return false;
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
     * Return's all the linked location nodes from the given/current user.
     * By default without a status check. Use the boolean to change this.
     *
     * @global type $user
     * @param boolean $bCheckPublished
     * @return array|Locations
     */
    public static function getUsersLocations($bCheckPublished = true) {

//        $userModel = \Models\User::loadCurrent();
//        $userModel->assureLocation();
        
        if (self::$_userLocations === null && self::$_userLocationsWithCheck === null) {

            self::$_userLocations = array();
            self::$_userLocationsWithCheck = array();

            global $user;
            $uid = $user->uid;

            $user = user_load($uid);
            $groupField = GojiraSettings::CONTENT_TYPE_GROUP_FIELD;
            $groupField = $user->$groupField;

            if (isset($groupField[LANGUAGE_NONE]) && isset($groupField[LANGUAGE_NONE][0]) && isset($groupField[LANGUAGE_NONE][0]['nid'])) {
                $gid = $groupField[LANGUAGE_NONE][0]['nid'];
            } else {
                $gid = 0;
            }

            $query = new EntityFieldQuery();
            $query->entityCondition('entity_type', 'node')
                    ->entityCondition('bundle', GojiraSettings::CONTENT_TYPE_LOCATION)
                    ->fieldCondition('field_gojira_group', 'nid', $gid, '=');

            $result = $query->execute();

            if (isset($result['node'])) {
                foreach ($result['node'] as $node) {
                    $oNode = node_load($node->nid);
                    self::$_userLocations[$node->nid] = $oNode;
                    if ($oNode->status == 1) {
                        self::$_userLocationsWithCheck[$node->nid] = $oNode;
                    }
                }
            }
        }

        if ($bCheckPublished) {
            return self::$_userLocationsWithCheck;
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
        node_delete($nid);
        unset(self::$_userLocations[$nid]);
        db_query("delete from group_location_favorite where pid = :nid",array(':nid'=>$nid));
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

        if(($a_long == $b_long) && ($a_lat == $b_lat)){
            return true;
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

    /**
     * Get's the note a user's group has on a location
     *
     * @param integer $nid
     * @return boolean
     */
    public static function getNote($nid, $postfix = '', $default = ''){
        $gid = Group::getGroupId();

        $note = db_query("select note from group_location_note where nid = :nid and gid = :gid", array(':nid' => $nid, ':gid' => $gid))->fetchField();

        if(trim($note) !== ''){
            return $note.$postfix;
        }
        return $default.$postfix;
    }

    /**
     * Adds a note for a group on a location
     *
     * @param integer $nid
     * @param string $note
     */
    public static function setNote($nid, $note){
        $gid = Group::getGroupId();

        db_query("delete from group_location_note where nid = :nid and gid = :gid", array(':nid' => $nid, ':gid' => $gid));

        db_query("INSERT INTO `group_location_note` (`nid`, `gid`, `note`) VALUES (:nid,:gid,:note)", array(':nid' => $nid, ':gid' => $gid, ':note' => $note));
    }

    public static function removeUselessDumbLocations() {
        $result = db_query('SELECT id, nid FROM {remove_locations} limit 500');
        foreach ($result as $entity) {
            node_delete($entity->nid);
            db_query('delete from {remove_locations} where id = '.$entity->id);
        }
    }
}
