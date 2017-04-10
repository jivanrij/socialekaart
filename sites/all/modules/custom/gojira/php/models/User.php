<?php
namespace models;

class User
{

    public $object = null;
    public $name = '';
    public $uid = '';
    public $mail = '';
    public $practices = null;
    public $groupId = null;
    public $lastSelectedMap = null;

    const TITLE_FIELD = 'field_user_title';
    const CONDITIONS_AGREE_FIELD = 'field_agree_conditions'; // the user has agreed to the conditions
    const SEARCH_GLOBAL_FIELD = 'field_search_global'; // user want's to search on a global level
    const HAS_MULTIPLE_LOCATIONS_FIELD = 'field_has_multiple_locations'; // user want's to handle multiple locations on his account
    const BIG_FIELD = 'field_big'; // users big number
    const TUTORIAL_FIELD = 'field_seen_tutorial'; // the user has seen the tutorial
    const USER_LAST_SELECTED_LOCATION = 'field_selected_location'; // the last selected location of the user
    const USER_LAST_SELECTED_MAP = 'field_selected_map'; // the last selected map of the user
    const CITY = 'field_user_city'; // Given city of the user @ registering
    const STREET = 'field_user_street'; // Given street of the user @ registering
    const HOUSENUMBER = 'field_user_housenumber'; // Given housenumber of the user @ registering
    const POSTCODE = 'field_user_postcode'; // Given postcode of the user @ registering

    // loads the object of a User from the factory
    public static function load($uid)
    {
        return Factory::getInstance()->getModel($uid, 'User');
    }

    /**
     * Returns the current user
     *
     * @return \Models\User
     */
    public static function loadCurrent()
    {
        global $user;
        return Factory::getInstance()->getModel($user->uid, 'User');
    }

    public function __construct($uid)
    {
        $this->init($uid);
    }

    /**
    * Initiates all the needed information of the locationset
    **/
    public function init($uid)
    {
        $user = user_load($uid);
        $this->object = $user;

        if (!empty($user)) {
            $this->name = $this->object->name;
            $this->uid = $this->object->uid;
            $this->mail = $this->object->mail;
            $this->getGroupId();
            $this->getPractices();
            $this->getLastSelectedMap();
            $this->object = $user;
        } else {
            throw new \Exception(sprintf('The given uid %s is not of a user.', $uid));
        }

    }

    // Get's the users group id
    private function getGroupId()
    {
        if (empty($this->groepId)) {
            $groupField = \GojiraSettings::CONTENT_TYPE_GROUP_FIELD;
            $groupField = $this->object->$groupField;

            if (!empty($groupField[LANGUAGE_NONE][0]['nid'])) {
                $this->groepId = $groupField[LANGUAGE_NONE][0]['nid'];
            }
        }
        return $this->groepId;
    }

    // Get's all the practices of this user
    private function getPractices()
    {
        if (is_null($this->practices)) {
            $query = new \EntityFieldQuery();
            $query->entityCondition('entity_type', 'node')
                    ->entityCondition('bundle', \GojiraSettings::CONTENT_TYPE_LOCATION)
                    ->fieldCondition('field_gojira_group', 'nid', $this->getGroupId(), '=');

            $result = $query->execute();

            $practices = array();
            if (isset($result['node'])) {
                foreach ($result['node'] as $node) {
                    $practices[$node->nid] = \Models\Location::load($node->nid);
                }
            }
            $this->practices = $practices;
        }

        return $this->practices;
    }

    /**
     * Returns the last selected map of the user
     *
     * @return null|Integer
     */
    private function getLastSelectedMap()
    {
        if (is_null($this->lastSelectedMap)) {
            if (!empty($this->object->field_selected_map[LANGUAGE_NONE][0]['nid'])) {
                $this->lastSelectedMap = $this->object->field_selected_map[LANGUAGE_NONE][0]['nid'];
            }
        }

        return $this->lastSelectedMap;
    }

    /**
     * Tells you if a user has a connection with a practice
     *
     * @param $practiceId
     * @return bool
     */
    public function hasConnectionWithPractice($practiceId)
    {
        if (array_key_exists($practiceId, $this->practices)) {
            return true;
        }
        return false;
    }

    /**
     * Stores a nid as a last saved selected map
     *
     * @param $nid
     */
    public function storeLastSelectedMap($nid)
    {
        $this->lastSelectedMap = $nid;
        $this->object->field_selected_map = array(LANGUAGE_NONE => array(0 => array('nid' => $nid)));
        user_save($this->object);
    }


    /**
     * Assures that a user has a linked location. Can be used if there is no known location.
     * Tries to make a location/practice to work with based on the given information @ the register form.
     *
     * @return bool
     */
    public function assureLocation() {

        $location = \Location::getLocationForAddress(
            \Location::formatAddress(
                $this->get(User::CITY), $this->get(User::STREET), $this->get(User::HOUSENUMBER), $this->get(User::POSTCODE)
            )
        );

        if($location) {
            // no existing location found, let's creat one
            $node = new \stdClass();
            $node->type = \GojiraSettings::CONTENT_TYPE_LOCATION;
            node_object_prepare($node);
            $node->language = LANGUAGE_NONE;
            $node->status = 1;
            $node->promote = 0;
            $node->comment = 0;

            // get the group the user is linked to and link the new location to it
            $groupField = \GojiraSettings::CONTENT_TYPE_GROUP_FIELD;
            $groupFieldUser = $this->object->$groupField;

            if (count($groupFieldUser) == 0) {
                $group = \Group::createNewGroup($this->object);
                $groupField = \GojiraSettings::CONTENT_TYPE_GROUP_FIELD;
                $this->object->$groupField = array(LANGUAGE_NONE => array(0 => array('nid' => $group->nid)));
                $this->save();
                $node->$groupField = array(LANGUAGE_NONE => array(0 => array('nid' => $group->nid)));
            } else {
                $node->$groupField = array(LANGUAGE_NONE => array(0 => array('nid' => $groupFieldUser[LANGUAGE_NONE][0]['nid'])));
            }

            $node = node_submit($node); // Prepare node for saving

            $visiblefield = \GojiraSettings::CONTENT_TYPE_SHOW_LOCATION_FIELD;
            $node->$visiblefield = array(LANGUAGE_NONE => array(0 => array('value' => 0)));

            $category_nid = \Category::getCategoryNID('Huisarts');
            $catfield = \GojiraSettings::CONTENT_TYPE_CATEGORY_FIELD;
            $node->$catfield = array('und' => array(0 => array('nid' => $category_nid)));

            $node->uid = $this->uid;
            $node->title = $this->name;

            $cityField = \GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD;
            $node->$cityField = array(LANGUAGE_NONE => array(0 => array('value' => $this->get(User::CITY))));
            $streetField = \GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD;
            $node->$streetField = array(LANGUAGE_NONE => array(0 => array('value' => $this->get(User::STREET))));
            $numberField = \GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD;
            $node->$numberField = array(LANGUAGE_NONE => array(0 => array('value' => $this->get(User::HOUSENUMBER))));
            $postcodeField = \GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD;
            $node->$postcodeField = array(LANGUAGE_NONE => array(0 => array('value' => $this->get(User::POSTCODE))));

            node_save($node);

            $this->setCurrentPractice($node->nid);

            return true;
        }
        return false;
    }

    /**
     * This function sets the current practice of the user
     */
    public function setCurrentPractice($nid)
    {
        $this->object->field_selected_location = array(LANGUAGE_NONE => array(0 => array('nid' => $nid)));
        $this->save();
    }

    /**
     * Returns the Location modal of the current active location practice
     *
     * @return \Models\Location
     */
    public function getCurrentPractice()
    {
        if(!empty($this->object->field_selected_location[LANGUAGE_NONE][0]['nid'])) {
            return \Models\Location::load($this->object->field_selected_location[LANGUAGE_NONE][0]['nid']);
        }
    }

    /**
     * Saves the user to the database
     */
    public function save()
    {
        user_save($this->object);
    }

    /**
     * Get's you the value of a field
     * If the field can contain multiple values, it returns an array
     *
     * @param $field
     * @return mixed
     */
    public function get($field)
    {
        $objectField = $this->object->$field;
        switch ($field) {
            default:
                if (!empty($objectField[LANGUAGE_NONE][0]['value'])) {
                    return $objectField[LANGUAGE_NONE][0]['value'];
                }
                break;
        }
        return '';
    }

}
