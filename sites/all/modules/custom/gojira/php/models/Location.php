<?php
namespace Models;

class Location
{

    public $object = null;
    public $title = '';
    public $nid = '';
    public $latitude = '';
    public $longitude = '';
    public $labels = array();

    const ADDRESS_CITY_FIELD = 'field_address_city';
    const EMAIL_FIELD = 'field_email';
    const ADDRESS_STREET_FIELD = 'field_address_street';
    const ADDRESS_HOUSENUMBER_FIELD = 'field_address_streetnumber';
    const ADDRESS_POSTCODE_FIELD = 'field_address_postcode';
    const TELEPHONE_FIELD = 'field_telephone';
    const FAX_FIELD = 'field_fax';
    const NOTE_FIELD = 'field_note';
    const SHOW_LOCATION_FIELD = 'field_visible_to_other_user';
    const LOCATION_VOCABULARY_FIELD = 'field_location_labels';
    const URL_FIELD = 'field_url';
    const CATEGORY_FIELD = 'field_category';

    // loads the object of a Locationset from the factory
    public static function load($nid)
    {
        return Factory::getInstance()->getModel($nid, 'Location');
    }

    /**
     * Creates a new location with a title and a status of 0. If a group is given, this one is also saved. This is used for practices.
     *
     * Returns false if there is a location with this title.
     *
     * @param $title
     * @param null $groupId
     * @return Location | false
     */
    public static function create($title, $groupId = null)
    {
        $knownTitle = db_query("select title from {node} where title = :t1", array(':t1' => $title))->fetchField();
        if ($knownTitle) {
            return false;
        }

        $node = new \stdClass();
        $node->title = $title;
        $node->type = \GojiraSettings::CONTENT_TYPE_LOCATION;
        node_object_prepare($node);
        $node->language = LANGUAGE_NONE;
        $node->status = 0;
        $node->promote = 0;
        $node->comment = 0;

        // be default, no group id is set, if a numeric is given, it is used.
        if (is_numeric($groupId)) {
            $node->field_gojira_group = array(LANGUAGE_NONE => array(0 => array('nid' => $groupId)));
        }

        $node = node_submit($node);

        node_save($node);

        return Location::load($node->nid);
    }

    public function __construct($nid)
    {
        $this->init($nid);
    }

    /**
    * Initiates all the needed information of the locationset
    **/
    public function init($nid)
    {
        $node = node_load($nid);

        if (isset($node->type)) {
            if ($node->type == \GojiraSettings::CONTENT_TYPE_LOCATION) {
                $this->object = $node;

                // set init data
                $this->title = $node->title;
                $this->nid = $node->nid;
                $this->url = '/?loc=' . $node->nid;
                $this->labels = \Labels::getLabels($this->object);

                $locationInfo = db_query("select X(point) as x, Y(point) as y from {node} where nid = :nid", array(':nid' => $this->nid))->fetchObject();
                if (!$locationInfo || is_null($locationInfo->x) || is_null($locationInfo->y)) {
                    $this->object->status = 0;
                } else {
                    $this->longitude = $locationInfo->x;
                    $this->latitude = $locationInfo->y;
                }
            } else {
                throw new \Exception(sprintf('The given nid %s is nod of the correct type.', $nid));
            }
        }
    }

    /**
     * Saves the Model to the database node, puts status on 1 & passes it to the search index
     */
    public function save()
    {
        $this->object->status = 1;
        node_save($this->object);

        db_query("UPDATE `node` SET point = GeomFromText('POINT(" . $this->longitude . " " . $this->latitude . ")'), `source`=:source WHERE  `nid`=:nid", array(':nid' => $this->nid, ':source' => 'gojira'));

        \Search::getInstance()->updateSearchIndex($this->nid);
    }

    /*
    * Get's you the value of a field
    * If the field can contian multiple values, it returns an array
    */
    public function get($field)
    {
        $objectField = $this->object->$field;
        switch ($field) {
            case self::CATEGORY_FIELD:
                return $objectField[LANGUAGE_NONE][0]['nid'];
            break;
            default:
                return $objectField[LANGUAGE_NONE][0]['value'];
            break;
        }
    }

    /**
     * Returns the Category name of this location
     *
     * @return string
     */
    public function getCategoryName()
    {
        $category = \helper::value($this->object, self::CATEGORY_FIELD, 'nid');
        $category = node_load($category);
        if($category){
            return $category->title;
        }
        return '';
    }

    /**
     * Set's the labels, based on given tid's
     *
     * @param $labelTids
     * @throws \Exception
     */
    public function setLabelTids($labelTids)
    {
        $saveTids = array();
        if (is_array($labelTids)) {
            foreach ($labelTids as $labelTid) {
                if (!is_numeric($labelTid)) {
                    throw new \Exception('An array with incorrect values given in the setLabelIds function.');
                } else {
                    $saveTids[] = array('tid'=> $labelTid);
                }
            }
        } else {
            throw new \Exception('No array with correct content given on the setLabelIds function.');
        }


        $this->object->field_location_labels[LANGUAGE_NONE] = $saveTids;
    }

    /**
     * Set's the visible to other users field
     *
     * @param $value
     */
    public function setVisible($value)
    {
        if (is_bool($value)) {
            if ($value) {
                $value = 1;
            } else {
                $value = 0;
            }
        } else {
            if ($value) {
                $value = 1;
            } else {
                $value = 0;
            }
        }

        $this->object->field_visible_to_other_user[LANGUAGE_NONE][0]['value'] = $value;
    }

    /**
     * Set's tje category field
     *
     * @param $categoryNid
     * @throws \Exception
     */
    public function setCategory($categoryNid)
    {
        if (is_numeric($categoryNid)) {
            $this->object->field_category[LANGUAGE_NONE][0] = array('nid'=>$categoryNid);
        } else {
            throw new \Exception('No numeric value given in the category setter.');
        }

    }

    /**
     * Set's the email
     *
     * @param $email
     */
    public function setEmail($email)
    {
        $this->object->field_email[LANGUAGE_NONE][0]['value'] = $email;
    }

    /**
     * Set's the url
     *
     * @param $url
     */
    public function setUrl($url)
    {
        $this->object->field_url[LANGUAGE_NONE][0]['value'] = $url;
        $this->object->field_url[LANGUAGE_NONE][0]['safe_value'] = $url;
    }

    /**
     * Set's the telephone field
     *
     * @param $telephone
     */
    public function setTelephone($telephone)
    {
        $this->object->field_telephone[LANGUAGE_NONE][0]['value'] = $telephone;
    }

    /**
     * Set's the fax field
     *
     * @param $fax
     */
    public function setFax($fax)
    {
        $this->object->field_fax[LANGUAGE_NONE][0]['value'] = $fax;
    }

    /**
     * Set's the street
     *
     * @param $street
     */
    public function setStreet($street)
    {
        $this->object->field_address_street[LANGUAGE_NONE][0]['value'] = $street;
    }

    /**
     * Set's the city
     *
     * @param $city
     */
    public function setCity($city)
    {
        $this->object->field_address_city[LANGUAGE_NONE][0]['value'] = $city;
    }

    /**
     * Set's the housenumber
     *
     * @param $housenumber
     */
    public function setHousenumber($housenumber)
    {
        $this->object->field_address_streetnumber[LANGUAGE_NONE][0]['value'] = $housenumber;
    }

    /**
     * Set's the postcode
     *
     * @param $postcode
     */
    public function setPostcode($postcode)
    {
        $this->object->field_address_postcode[LANGUAGE_NONE][0]['value'] = $postcode;
    }

}
