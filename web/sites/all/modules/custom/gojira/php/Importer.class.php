<?php

/**
 * Description of Importer
 *
 * 
 * Set everything rdy to be imported:
 UPDATE `locations` SET `imported`=0, `double`=0, `notallowed`=0 WHERE  `id`>0;
 UPDATE `adhocdata_addresses` SET `ready_to_import`=1, `imported`=0, `double`=0, `attempts`=0 WHERE  `id`>0;
 * 
 * 
 * @author jivanrij
 */
class Importer {

    /**
     * Try's to get the coordinates from the database. If he finds them, set's the correct flags and stores them. This includes ready_to_import.
     * 
     * @param stdClass location information
     */
    public static function AdhocdataFindCoordsInDtb($adhocdata_object) {
        $id = $adhocdata_object->id;
        $city = $adhocdata_object->city;
        $housnumber = $adhocdata_object->housnumber;
        $postcode = $adhocdata_object->postcode;
        $street = $adhocdata_object->street;

        $sql = "SELECT longitude, latitude FROM locations WHERE city = :city AND housnumber = :housnumber AND street = :street";

        $location_coords = db_query($sql, array(':city' => $city, 'housnumber' => $housnumber, ':street' => $street))->fetchObject();
        if ($location_coords) {
            $longitude = $location_coords->latitude;
            $latitude = $location_coords->longitude;
            if (trim($longitude) != '' && trim($latitude) != '') {
                db_query("UPDATE `adhocdata_addresses` SET `longitude`='{$longitude}', `latitude`='{$latitude}', `coords_checked_with_dtb`=1, `ready_to_import`=1 WHERE  `id`={$id}");
                return true;
            }
        }

        // check the address cache table
        $address_formatted1 = Location::formatAddress($city, $street, $housnumber, $postcode);
        $address_formatted2 = str_replace(' ', '', strtoupper($postcode)) . $housnumber;
        $location_coords = db_query("select X(point) as longitude, Y(point) as latitude from address_cache where address = :address_formatted1 OR address = :address_formatted2", array(':address_formatted1' => $address_formatted1, ':address_formatted2' => $address_formatted2))->fetchObject();
        if ($location_coords) {
            $longitude = $location_coords->longitude;
            $latitude = $location_coords->latitude;
            if (trim($longitude) != '' && trim($latitude) != '') {
                db_query("UPDATE `adhocdata_addresses` SET `longitude`='{$longitude}', `latitude`='{$latitude}', `coords_checked_with_dtb`=1, `ready_to_import`=1 WHERE  `id`={$id}");
                return true;
            }
        }

//        echo $adhocdata_object->title.' niet gevonden in address_cache<br />';
        // nothing found, set flag
        db_query("UPDATE `adhocdata_addresses` SET `coords_checked_with_dtb`=1 WHERE `id`={$id}");
        return false;
    }

    /**
     * Try's to get the coordinates from google. If he finds them, set's the correct flags and stores them.
     * 
     * Google can handle 2.500 requests a day, 5 per second.
     * 
     * @param stdClass location information
     */
    public static function AdhocdataFindCoordsAtGoogle($adhocdata_object) {

        // check how much time google is called this day
        $amount = variable_get('gojira_amount_calls_to_google'); // format -> 'dd-mm-yy,AMOUNT'
        $amountinfo = explode(',', $amount);


        if (array_key_exists(1, $amountinfo)) {
            $date = $amountinfo[0];
            $amount = $amountinfo[1];

            if ($date == date('d-m-Y')) {
                // last entry is today, let's check
                if ($amount >= 2500) {
                    return false;
                }
            } else {
                $amount = 0;
            }
        } else {
            $amount = 0;
        }
        $amount++;
        variable_set('gojira_amount_calls_to_google', date('d-m-Y') . ',' . $amount);


        $id = $adhocdata_object->id;
        $city = $adhocdata_object->city;
        $housnumber = $adhocdata_object->housnumber;
        $postcode = $adhocdata_object->postcode;
        $street = $adhocdata_object->street;

        // get coordinates from google
        $location_coords = Location::getLocationForAddress(Location::formatAddress($city, $street, $housnumber, $postcode));

        if ($location_coords) {
            $longitude = $location_coords->longitude;
            $latitude = $location_coords->latitude;
            if (trim($longitude) != '' && trim($latitude) != '') {
                // found them, set the flags
                db_query("UPDATE `adhocdata_addresses` SET `longitude`='{$longitude}', `latitude`='{$latitude}', `coords_checked_with_google`=1, `ready_to_import`=1 WHERE  `id`={$id}");
                return true;
            }
        }

        // nothing found
        db_query("UPDATE `adhocdata_addresses` SET `coords_checked_with_google`=1 WHERE `id`={$id}");
        return false;
    }

    /**
     * Try's to get the coordinates from google. If he finds them, set's the correct flags and stores them.
     * 
     * @param stdClass location information
     */
    private static function AdhocdataFindCoordsAtPostcodeNL($adhocdata_object) {
        $id = $adhocdata_object->id;
        $housnumber = $adhocdata_object->housnumber;
        $postcode = $adhocdata_object->postcode;

        // find coordinates at postcodenl
        $location_coords = Postcode::getInstance()->getGeoInfo($postcode, $housnumber);
        if ($location_coords) {
            $longitude = $location_coords->longitude;
            $latitude = $location_coords->latitude;
            if (trim($longitude) != '' && trim($latitude) != '') {
                // found some
                db_query("UPDATE `adhocdata_addresses` SET `longitude`='{$longitude}', `latitude`='{$latitude}', `coords_checked_with_postcodenl`=1, `ready_to_import`=1 WHERE  `id`={$id}");
                return true;
            }
        }

        // found nothing, let's just set the flags
        db_query("UPDATE `adhocdata_addresses` SET `coords_checked_with_postcodenl`=1 WHERE `id`={$id}");
        return false;
    }

    /**
     * Get's the given amount of adhocdata addresses that have no coordinates and try's to find them in the locations or address_cache table, google or postcodeNL. 
     * If found, stores the coordinates and set's the corresponding flags. If not found, raises the attempts counter.
     * 
     * @param integer $max_amount
     */
    public static function AdhocdataGetCoordinates($max_amount = 250, $dtb = true, $google = true, $postcodeNL = true) {
        $result = db_query("SELECT id, title, city, housnumber, postcode, street, coords_checked_with_postcodenl, coords_checked_with_dtb, coords_checked_with_google, attempts FROM {adhocdata_addresses} WHERE ready_to_import = 0 AND attempts <= 2 LIMIT {$max_amount}")->fetchAll();
        foreach ($result as $location) {
            $id = $location->id;
            $city = $location->city;
            $housnumber = $location->housnumber;
            $postcode = $location->postcode;
            $street = $location->street;
            $checked_dtb = $location->coords_checked_with_dtb;
            $checked_postcodeNL = $location->coords_checked_with_postcodenl;
            $checked_google = $location->coords_checked_with_google;
            $attempts = $location->attempts;
            $attempts++;

            $found = false;
            if ($dtb && !$found && ($attempts <= 2)) { // have found nothing & never checked the dtb
                $found = self::AdhocdataFindCoordsInDtb($location);
            }
            if ($google && !$found && ($attempts <= 2)) { // have found nothing & never checked google
                $found = self::AdhocdataFindCoordsAtGoogle($location);
            }
            if ($postcodeNL && !$found && ($attempts <= 2)) { // have found nothing & never checked postcodeNL
                $found = self::AdhocdataFindCoordsAtPostcodeNL($location);
            }

            db_query("UPDATE `adhocdata_addresses` SET `attempts`={$attempts} WHERE `id`={$id}");
        }
    }

    public static function AdhocdataImportCSV() {

        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        set_time_limit(999999999999999);
        /*
          CREATE TABLE `adres_import` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `titel` VARCHAR(100) NOT NULL DEFAULT '0',
          `adres` VARCHAR(100) NOT NULL DEFAULT '0',
          `postcode` VARCHAR(100) NOT NULL DEFAULT '0',
          `city` VARCHAR(100) NOT NULL DEFAULT '0',
          `phone` VARCHAR(100) NOT NULL DEFAULT '0',
          `note` VARCHAR(100) NOT NULL DEFAULT '0',
          `tag` VARCHAR(100) NOT NULL DEFAULT '0',
          `longitude` VARCHAR(100) NOT NULL DEFAULT '0',
          `latitude` VARCHAR(100) NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`),
          INDEX `id` (`id`)
          );
         */
//14119
        $row = 0;
        $doubles = array();
        $double_count = 0;
        $first = true;


        $result = db_query('select nr, description from {sbi}')->fetchAll();
        $sbi = array();
        foreach ($result as $item) {
            $sbi[$item->nr] = str_replace(',', '', $item->description);
        }

        if (($handle = fopen(str_replace('/web', '/data', getcwd()) . '/locations.csv', "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 100000, ",")) !== FALSE) {

                $data = array_map("gojira_set_encode", $data);

                if ($first) {
                    $first = false;
                    continue;
                }
//  0 => string 'HOOFDNEVEN' (length=10)
//  1 => string 'ORGANISATIE_NAAM' (length=16)
//  2 => string 'ORGANISATIE_STRAAT' (length=18)
//  3 => string 'ORGANISATIE_HUISNR' (length=18)
//  4 => string 'ORGANISATIE_HUISNRTOEV' (length=22)
//  5 => string 'ORGANISATIE_PC' (length=14)
//  6 => string 'ORGANISATIE_WOONPLAATS' (length=22)
//  7 => string 'ORGANISATIE_VSI' (length=15)
//  8 => string 'ORGANISATIE_STRAAT_CA' (length=21)
//  9 => string 'ORGANISATIE_HUISNR_CA' (length=21)
//  10 => string 'ORGANISATIE_HUISNRTOEV_CA' (length=25)
//  11 => string 'ORGANISATIE_PC_CA' (length=17)
//  12 => string 'ORGANISATIE_WOONPLAATS_CA' (length=25)
//  13 => string 'ORGANISATIE_PROV' (length=16)
//  14 => string 'ORGANISATIE_LAND' (length=16)
//  15 => string 'ORGANISATIE_TEL' (length=15)
//  16 => string 'ORGANISATIE_TELNET' (length=18)
//  17 => string 'ORGANISATIE_TELABN' (length=18)
//  18 => string 'ORGANISATIE_TELMOB' (length=18)
//  19 => string 'DOMEINNAAM' (length=10)
//  20 => string 'EMAIL' (length=5)
//  21 => string 'RECHTSVORM' (length=10)
//  22 => string 'SBIHOOFDACT' (length=11)
//  23 => string 'SBINEVENACT1' (length=12)
//  24 => string 'SBINEVENACT2' (length=12)
//  25 => string 'KWP_TOTAAL' (length=10)
//  26 => string 'KWP_FULLT' (length=9)
//  27 => string 'CONTACT_FUNCTIE' (length=15)
//  28 => string 'CONTACT_GESLACHT' (length=16)
//  29 => string 'CONTACT_VRLT' (length=12)
//  30 => string 'CONTACT_VRVOEG' (length=14)
//  31 => string 'CONTACT_ACHTERNM' (length=16)
//  32 => string 'DATUMOPRICHTING' (length=15)
//  33 => string 'ECOACTIEF' (length=9)
//  34 => string 'GESCHAT' (length=7)
//  35 => string 'IND_FAILL' (length=9)
//  36 => string 'IND_SURS' (length=8)
//  37 => string 'IND_OPH' (length=7)
//  38 => string 'NMI' (length=3)


                $title = $data[1];
                $email = $data[20];
                $city = ucfirst(strtolower($data[6]));
                $street = $data[2];
                $url = $data[19];
                $housnumber = $data[3] . $data[4];
                $postcode = $data[5];
                $telephone = $data[15];

                if (trim($data[22]) != '') {
                    if (array_key_exists($data[22], $sbi)) {
                        $category = $sbi[$data[22]];
                    }
                }

//                echo '$title'.$title.'<br />';
//                echo '$email'.$email.'<br />';
//                echo '$street'.$street.'<br />';
//                echo '$url'.$url.'<br />';
//                echo '$housnumber'.$housnumber.'<br />';
//                echo '$postcode'.$postcode.'<br />';
//                echo '$telephone'.$telephone.'<br />';
//                echo '$category'.$category.'<br />';
//                echo '$city'.$city.'<br />';
//                echo '<br /><br />';

                $sql = "INSERT INTO `adhocdata_addresses` (`title`, `email`, `city`, `street`, `url`, `housnumber`, `postcode`, `telephone`, `category`) VALUES (:title, :email, :city, :street, :url, :housnumber, :postcode, :telephone, :category)";

                //drupal_set_message('Inserted: '.$title.' '.$street.' '.$housnumber.' '.$postcode.' '.$city.' '.$telephone.' '.$email.' '.$url, 'status');
                try {
                    if (!array_key_exists($title . $postcode . $housnumber, $doubles)) {
                        db_query($sql, array(':title' => $title, ':email' => $email, ':city' => $city, ':street' => $street, ':url' => $url, ':housnumber' => $housnumber, ':postcode' => $postcode, ':telephone' => $telephone, ':category' => $category));
                        $doubles[$title . $postcode . $housnumber] = $title . $postcode . $housnumber;
                    } else {
                        $double_count++;
                        drupal_set_message('Found double location: ' . $title . $postcode . $housnumber . ' on row nr: ' . $row, 'error');
                    }
                } catch (Exception $e) {
                    drupal_set_message($e->getMessage() . '   SQL: ' . $sql, 'error');
                }

                $row++;

//                if($row > 50000){
//                    return;
//                }
            }
            fclose($handle);
        }
        drupal_set_message($row . ' inserted with ' . $double_count . ' double locations.', 'status');
    }

    /**
     * Save a new location with the given information
     * 
     * @global type $user
     * @param type $source
     * @param type $title
     * @param type $phone
     * @param type $city
     * @param type $street
     * @param type $housenumber
     * @param type $postcode
     * @param type $category
     * @param type $email
     * @param type $latitude
     * @param type $longitude
     * @param type $url
     * @param type $labels
     * @return boolean
     */
    public static function addLocation($source, $title, $phone, $city, $street, $housenumber, $postcode, $category, $email, $latitude, $longitude, $url, $labels, $source_table_id, $source_table) {
        // check title
        $existing = db_query("select nid, title from {node} where title = :title", array(':title' => $title))->fetchObject();

        if ($existing && $existing->title == $title) {
            // there is one with the same title, now check if it's the same address, then return false
            $existing_node = node_load($existing->nid);
            $existing_city = helper::value($existing_node, GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD);
            $existing_street = helper::value($existing_node, GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD);
            $existing_number = helper::value($existing_node, GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD);
//            $existing_category = Category::getCategoryName($existing_node);
// && $category == $existing_category
            if ($existing_city == $city && $existing_street == $street && $existing_number == $housenumber) {
                db_query("UPDATE `" . $source_table . "` SET `double` = 1  WHERE  `id`=" . $source_table_id);
                watchdog('import_error', 'Double location title & address: ' . $title . ' - found in Importer::addLocation');
                drupal_set_message('Double location title & address: ' . $title . ' - found in Importer::addLocation', 'error');
                return false;
            } else {
                // if there is a location with 2 the same names, we will ad the city (that makes the differance) to the title and the set of labels.
                $title = $title . ' - ' . $city;
                watchdog('import_info', 'Double location title: ' . $title . ' but another address, adding city to name. - found in Importer::addLocation');
                drupal_set_message('Double location title: ' . $title . ' but another address, adding city to name. - found in Importer::addLocation', WATCHDOG_INFO);
            }
        }
        try {
            // get needed data
            global $user;
            $user = user_load($user->uid);
            $category_nid = Category::getCategoryNID($category);

            // create location node
            $node = new stdClass();
            $node->type = GojiraSettings::CONTENT_TYPE_LOCATION;
            node_object_prepare($node);
            $node->language = LANGUAGE_NONE;
            $node->uid = $user->uid;
            $node->promote = 0;
            $node->comment = 0;
            $node = node_submit($node); // Prepare node for saving
            // fill fields
            $node->field_moderated_status = array(LANGUAGE_NONE => array(0 => array('value' => 2)));
            $node->status = 1;
            $node->type = GojiraSettings::CONTENT_TYPE_LOCATION;
            $node->uid = $user->uid;
            $node->title = $title;
            $node->field_email = array('und' => array(0 => array('value' => $email)));
            $node->field_telephone = array('und' => array(0 => array('value' => $phone)));
            $node->field_url = array('und' => array(0 => array('value' => $url)));
            $node->field_address_city = array('und' => array(0 => array('value' => $city)));
            $node->field_address_street = array('und' => array(0 => array('value' => $street)));
            $node->field_address_streetnumber = array('und' => array(0 => array('value' => $housenumber)));
            $node->field_address_postcode = array('und' => array(0 => array('value' => $postcode)));
            $node->field_visible_to_other_user = array('und' => array(0 => array('value' => 1)));
            $node->field_category[LANGUAGE_NONE][0]['nid'] = $category_nid;
            //$node->field_location_labels[LANGUAGE_NONE][0]['tid'] = $label_tid;
            // save node
            node_save($node);

            if (!is_array($labels)) {
                if (strstr($labels, ',')) {
                    $labels_tmp = explode(',', $labels);
                    $labels = array();
                    foreach ($labels_tmp as $label_tmp) {
                        $labels[] = trim($label_tmp);
                    }
                } else {
                    $labels = array($labels);
                }
            }

            // index node
            //Search::getInstance()->updateSearchIndex($node);
            Labels::saveArrayOfLabelsOnNode($labels, $node->nid); // this function does the updateSearchIndex
            //save coordinates
            db_query("UPDATE `node` SET point = GeomFromText('POINT(" . $longitude . " " . $latitude . ")'), `source` = '" . $source . "'  WHERE  `nid`=" . $node->nid);
            return $node->nid;
        } catch (Exception $ex) {
            return false;
        }
    }
    
    
    public static function restoreLocationFromBackup($source, $title, $phone, $city, $street, $housenumber, $postcode, $category, $email, $latitude, $longitude, $url, $labels, $id) {
        try {
            // get needed data
            global $user;
            $user = user_load($user->uid);
            $category_nid = Category::getCategoryNID($category);

            // create location node
            $node = new stdClass();
            $node->type = GojiraSettings::CONTENT_TYPE_LOCATION;
            node_object_prepare($node);
            $node->language = LANGUAGE_NONE;
            $node->uid = $user->uid;
            $node->promote = 0;
            $node->comment = 0;
            $node = node_submit($node); // Prepare node for saving
            // fill fields
            $node->field_moderated_status = array(LANGUAGE_NONE => array(0 => array('value' => 2)));
            $node->status = 1;
            $node->type = GojiraSettings::CONTENT_TYPE_LOCATION;
            $node->uid = $user->uid;
            $node->title = $title;
            $node->field_email = array('und' => array(0 => array('value' => $email)));
            $node->field_telephone = array('und' => array(0 => array('value' => $phone)));
            $node->field_url = array('und' => array(0 => array('value' => $url)));
            $node->field_address_city = array('und' => array(0 => array('value' => $city)));
            $node->field_address_street = array('und' => array(0 => array('value' => $street)));
            $node->field_address_streetnumber = array('und' => array(0 => array('value' => $housenumber)));
            $node->field_address_postcode = array('und' => array(0 => array('value' => $postcode)));
            $node->field_visible_to_other_user = array('und' => array(0 => array('value' => 1)));
            $node->field_category[LANGUAGE_NONE][0]['nid'] = $category_nid;
            //$node->field_location_labels[LANGUAGE_NONE][0]['tid'] = $label_tid;
            // save node
            node_save($node);

            if (!is_array($labels)) {
                if (strstr($labels, ',')) {
                    $labels_tmp = explode(',', $labels);
                    $labels = array();
                    foreach ($labels_tmp as $label_tmp) {
                        $labels[] = trim($label_tmp);
                    }
                } else {
                    $labels = array($labels);
                }
            }

            // index node
            //Search::getInstance()->updateSearchIndex($node);
            Labels::saveArrayOfLabelsOnNode($labels, $node->nid); // this function does the updateSearchIndex
            //save coordinates
            db_query("UPDATE `node` SET `indexed` = 0, point = GeomFromText('POINT(" . $latitude . " " . $longitude . ")'), `source` = '" . $source . "'  WHERE  `nid`=" . $node->nid);
            db_query("UPDATE `practices_backup` SET `import_it`=0 WHERE `id`=".$id);
            
            Search::getInstance()->updateSearchIndex($node);
            
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
    /*
     * Get's the locations from the locations table and adds them to the system.
     */
    public static function importLocations($amount = 250) {

        $bChangeSetting = false;
        if (variable_get('gojira_check_coordinates_on_update_node', 1)) {
            variable_set('gojira_check_coordinates_on_update_node', 0);
            $bChangeSetting = true;
        }

        set_time_limit(999999999999999);
        $result = db_query("SELECT `gojira_category`, `gojira_labels`, `id`, `nid`, `title`, `email`, `city`, `street`, `housnumber`, `properties`, `postcode`, `telephone`, `fax`, `note`, `status`, `visible`, `published`, `longitude`, `latitude`, `url` FROM `locations` WHERE (imported = 0 AND notallowed = 0 AND `double` = 0) LIMIT {$amount};")->fetchAll();

        foreach ($result as $location) {

            $not[] = 'De door u gezochte pagina kon niet worden gevonden.';
            $not[] = 'Huisartsenpraktijk';
            $not[] = 'Huisartsenpost';
            $not[] = 'Overige kliniek';

            foreach ($not as $label) {
                if (strtolower($location->properties) == strtolower($label)) {
                    // count not allowed category as imported
                    db_query("UPDATE `locations` SET `notallowed`=1 WHERE  `id`=" . $location->id);
                    continue 2;
                }
            }

            $titles = array();

            $new_nid = self::addLocation('spider', $location->title, $location->telephone, $location->city, $location->street, $location->housnumber, $location->postcode, $location->gojira_category, $location->email, $location->longitude, $location->latitude, $location->url, $location->gojira_labels, $location->id, 'locations');
            $titles[] = $location->title;
            if ($new_nid) {
                db_query("UPDATE `locations` SET `imported`=1, `nid`={$new_nid} WHERE  `id`=" . $location->id);
            }
        }

        if ($bChangeSetting) {
            variable_set('gojira_check_coordinates_on_update_node', 1);
        }
    }

    /*
     * Get's the locations from the adhocdata_addresses table and adds them to the system.
     */

    public static function AdhocdataImportLocations($amount = 250) {

        $bChangeSetting = false;
        if (variable_get('gojira_check_coordinates_on_update_node', 1)) {
            variable_set('gojira_check_coordinates_on_update_node', 0);
            $bChangeSetting = true;
        }

        set_time_limit(999999999999999);
        $result = db_query("SELECT `gojira_category`, `gojira_labels`, `id`, `title`, `email`, `city`, `street`, `url`, `housnumber`, `postcode`, `telephone`, `category`, `longitude`, `latitude`, `nid`, `coords_checked_with_dtb`, `coords_checked_with_google`, `coords_checked_with_postcodenl`, `ready_to_import`, `imported`, `attempts` FROM `adhocdata_addresses` WHERE `ready_to_import` = 1 AND `imported` = 0 AND `double` = 0 LIMIT {$amount};")->fetchAll();

        foreach ($result as $location) {
            $titles = array();
            $new_nid = self::addLocation('adhocdata', $location->title, $location->telephone, $location->city, $location->street, $location->housnumber, $location->postcode, $location->gojira_category, $location->email, $location->latitude, $location->longitude, $location->url, $location->gojira_labels, $location->id, 'adhocdata_addresses');
            if ($new_nid) {
                db_query("UPDATE `adhocdata_addresses` SET `imported`=1, `nid` = {$new_nid} WHERE  `id`=" . $location->id);
                $titles[] = $location->title;
            }
        }

        if ($bChangeSetting) {
            variable_set('gojira_check_coordinates_on_update_node', 1);
        }
    }

    /**
     * Remove all locations & categories & taxonomy
     */
    public static function emptyLocations() {
        if (user_access('administer')) {
            self::removeAllNodesOfType('location');
            self::removeAllNodesOfType('category');
            self::cleanFieldTables();
            db_query('DELETE FROM `taxonomy_index` WHERE `nid`>0');
            db_query('DELETE FROM `taxonomy_term_data` WHERE `tid`>0');
            db_query('DELETE FROM `taxonomy_term_hierarchy` WHERE  `tid`>0');
            // set all adhocdata locations to be imported again
//            db_query('UPDATE `adhocdata_addresses` SET `nid`=NULL, `imported`=0, `double`=0 WHERE  `id`>0');
        }
    }

    public static function cleanNames() {

        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        set_time_limit(999999999999999);

        $result = db_query("select nid from {node} where type = 'location' and title like '%-%'")->fetchAll();
        foreach ($result as $entry) {
            $node = node_load($entry->nid);

            $titleArray = explode('-', $node->title);
            $node->title = trim($titleArray[0]);
            node_save($node);
        }
    }

    /**
     * This function cleans all the field tables from data that is linked to nodes that are removed manually
     */
    public static function cleanFieldTables() {
        $result = db_query("select field_name from {field_config}");
        foreach ($result as $r) {
            if (db_table_exists('field_data_' . $r->field_name)) {
                if (strstr($r->field_name, 'field_')) {
                    $sql = "DELETE FROM `field_data_{$r->field_name}` WHERE  `entity_type`='node' AND `entity_id` not in (select nid from node)";
                    db_query($sql);
                    $sql = "DELETE FROM `field_revision_{$r->field_name}` WHERE  `entity_type`='node' AND `entity_id` not in (select nid from node)";
                    db_query($sql);
                }
            }
        }
    }

    /**
     * This function cleans all the field tables from data that is linked to nodes that are removed manually
     */
//    public static function AdhocdataFillCategoryLabels() {
//        $category_labels = array();
//        $result = db_query('select gojira_category, gojira_labels, category from adhocdata_addresses_coordinates group by category');
//        foreach($result as $info){
//          $category_labels[trim($info->category)] = array('category'=>$info->gojira_category,'labels'=>$info->gojira_labels);  
//        }
//        
//        foreach($category_labels as $key=>$category_label){
//            db_query("update adhocdata_addresses set gojira_category = '".$category_label['category']."', gojira_labels = '".$category_label['labels']."' where category = '".$key."'");
//            die('asd');
//        }
//    }

    /*
     * Removes all the nodes of the given type
     */

    public static function removeAllNodesOfType($type) {
        $sql = "DELETE FROM `node` WHERE  `type`='{$type}'";
        db_query($sql);
    }
    
    public static function removeZidbLocations() {
        return false;
        $result = db_query("select nid from node where source = 'zidb'");
        foreach ($result as $r) {
            node_delete($r->nid);
            echo 'remove node: '.$r->nid.'<br />';
        }
        self::cleanFieldTables();
    }
    
    public static function mergeZidbLocationsWithEchother() {
        return false;
        $results_original = db_query("select labels, postcode, title, city, street, number, category from practices_backup_original");
        
        foreach($results_original as $result_original){
           
            $key = str_replace(' ','',$result_original->title.$result_original->city.$result_original->street.$result_original->number.$result_original->category);
            
            echo $key.'<br />';

            $result = db_query("select id, labels, postcode, title, city, street, number, category from practices_backup where uid = '{$key}'")->fetchObject();
            if($result){
                // merge to existing location
                if($result_original->labels !== $result->labels){
                    $first = explode('|', $result_original->labels);
                    $second = explode('|', $result->labels);
                    $third = array_merge($first, $second);
                    $clean = array();
                    foreach($third as $thirdVal){
                        if(trim($thirdVal) != ''){
                            $clean[] = $thirdVal;
                        }
                    }
                    $forth = implode('|', $clean);
                    db_query("UPDATE `practices_backup` SET `labels`='{$forth}' WHERE  `id`={$result->id}");
                }
            }else{
                // add to new table
                db_query("INSERT INTO `practices_backup` (`uid`, `title`, `city`, `street`, `number`, `postcode`, `labels`, `category`, `source`) VALUES ('".$key."', '".$result_original->title."', '".$result_original->city."', '".$result_original->street."', '".$result_original->number."', '".$result_original->postcode."', '".$result_original->labels."', '".$result_original->category."', 'zidb')");
            }
        }
    }
    
    public static function setCoordinatesOnZIBD(){
        return false;
        $results = db_query("select id, labels, postcode, title, city, street, number, category from practices_backup where latitude is null and longitude is null");
        foreach($results as $result){
            $location = Location::getLocationForAddress($result->street.' '.$result->number.','.$result->postcode.' '.$result->city);
            if($location){
                db_query("UPDATE `practices_backup` SET `longitude`='{$location->longitude}', `latitude`='{$location->latitude}' WHERE  `id`={$result->id}");
            }
        }
    }
}

function gojira_set_encode($str) {
    return iconv("Windows-1252", "UTF-8", $str);
}
