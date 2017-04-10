<?php
namespace Models;

class Locationset {

    private $object = null;
    public $moderators = array();
    public $moderatorids = array();
    public $users = array();
    public $userids = array();
    public $locations = array();
    public $locationids = array();
    public $title = '';
    public $nid = '';
    public $url = '';

    // loads the object of a Locationset from the factory
    public static function load($nid) {
        return Factory::getInstance()->getModel($nid,'Locationset');
    }


    /**
     * Get's you an array with Locationsets connected to the given Location by the field_setlocation field.
     *
     * @param $locationNid
     * @return array
     * @throws \Exception
     */
    public static function getLocationsetsConnectedToLocation($locationNid)
    {
        if(!is_numeric($locationNid)) {
            throw new \Exception(sprintf('The given nid %s is nod incorrect.', $locationNid));
        }

        $return = array();
        $results = db_query('select entity_id from field_data_field_setlocations where field_setlocations_nid = :nid', array('nid'=>$locationNid));
        foreach($results as $result) {
            $return[$result->entity_id] = \Models\Locationset::load($result->entity_id);
        }

        return $return;
    }


    public function __construct($nid) {
        $this->init($nid);
    }

    /**
    * Initiates all the needed information of the locationset
    **/
    public function init($nid) {

        $node = node_load($nid);

        if (isset($node->type)) {
            if ($node->type == \GojiraSettings::CONTENT_TYPE_SET_OF_LOCATIONS) {

                $this->title = $node->title;
                $this->nid = $node->nid;
                $this->url = url('node/'. $this->nid);

                // load all the moderators
                $moderators = array();
                $moderatorsfield = $node->field_setmoderators[LANGUAGE_NONE];
                foreach($moderatorsfield as $moderator) {
                    $moderators[] = $moderator['uid'];
                }
                $moderatorids = implode(',',$moderators);
                if($moderatorids !== '') {
                    $moderators = db_query(sprintf('select name, uid, mail from users where uid in(%s)', $moderatorids));
                    foreach($moderators as $object) {
                        $this->moderatorids[$object->uid] = $object->uid;
                        $this->moderators[] = array(
                            'name' => $object->name,
                            'mail' => $object->mail,
                            'uid' => $object->uid
                        );
                    }
                }

                // load all the users who can view
                $users = array();
                $usersfield = $node->field_setusers[LANGUAGE_NONE];
                foreach($usersfield as $moderator) {
                    $users[] = $moderator['uid'];
                }
                $userids = implode(',',$users);
                if($userids !== '') {
                    $users = db_query(sprintf('select name, uid from users where uid in(%s)', $userids));
                    foreach($users as $object) {
                        $this->userids[$object->uid] = $object->uid;
                        $this->users[] = array(
                            'name' => $object->name,
                            'uid' => $object->uid
                        );
                    }
                }

                // load all the locations that belong to this set
                $locations = array();
                $locationsfield = $node->field_setlocations[LANGUAGE_NONE];
                foreach($locationsfield as $location) {
                    if(trim($location['nid']) !== '') {
                        $locations[] = $location['nid'];
                    }
                }
                $locationids = implode(',',$locations);
                if($locationids !== '') {
                    $locations = db_query(sprintf('select title, nid from node where nid in(%s)', $locationids));
                    foreach($locations as $object) {
                        $this->locationids[$object->nid] = $object->nid;
                        $this->locations[] = array(
                            'title' => $object->title,
                            'nid' => $object->nid
                        );
                    }
                }

            } else {
                throw new \Exception(sprintf('The given nid %s is nod of the correct type.', $nid));
            }
        }
        $this->object = $node;
    }

    // Tells you if a location is a part of this locationset
    public function hasLocation($locationNid){
        return array_key_exists($locationNid, $this->locationids);
    }

    // Tells you if a user is allowed to see this locationset
    public function hasUser($userId){
        return in_array($userId, $this->userids);
    }

    // Tells you if a user is allowed to moderate this locationset
    public function hasModerator($userId){
        return in_array($userId, $this->moderatorids);
    }

    /**
     * Get's the url of this location
     *
     * @return string
     */
    public function getUrl()
    {
        return url(drupal_get_path_alias('node/' . $this->object->nid));
    }

    /**
     * Adds the given Location to this Locationset
     *
     * @param $locationModel
     * @throws \Exception
     */
    public function addLocation($locationModel)
    {
        if(is_null($locationModel) || get_class($locationModel) !== 'Models\Location') {
            throw new \Exception(sprintf('The given param in addLocation is not of the correct type.'));
        }

        global $user;
        if(!$this->hasModerator($user->uid)) {
            $user = \Models\User::load($user->uid);
            // so we need to e-mail the moderators, the user is not a moderator
            foreach($this->moderators as $moderator) {
                \MailerHtml::sendUserInformMapmoderatorUseraction($moderator['mail'], $user, $locationModel, $this, 'add');
            }
        }

        $this->object->field_setlocations[LANGUAGE_NONE][]['nid'] = $locationModel->nid;

        $this->save();
    }

    // Removes the Location to this Locationset
    public function removeLocation($location)
    {
        if(is_null($location) || get_class($location) !== 'Models\Location') {
            throw new \Exception(sprintf('The given param in removeLocation is not of the correct type.'));
        }

        global $user;
        if(!$this->hasModerator($user->uid)) {
            $user = \Models\User::load($user->uid);

            // so we need to e-mail the moderators, the user is not a moderator
            foreach($this->moderators as $moderator) {
                \MailerHtml::sendUserInformMapmoderatorUseraction($moderator['mail'], $user, $location, $this, 'remove');
            }
        }

        $locations = $this->object->field_setlocations[LANGUAGE_NONE];

        $newLocations = array();
        foreach($locations as $loc) {
            if($location->nid !== $loc['nid']) {
                $newLocations[]['nid'] = $loc['nid'];
            }
        }
        $this->object->field_setlocations[LANGUAGE_NONE] = $newLocations;

        $this->save();
    }

    /**
     * Returns the amount of locations on this set
     *
     * @return int
     */
    public function getLocationsCount()
    {
        if(!empty($this->object->field_setlocations['und'])) {
            return count($this->object->field_setlocations['und']);
        }
        return 0;
    }

    /**
     * Returns all the location models in a array
     *
     * @return array
     */
    public function getLocations()
    {
        $return = array();
        if(!empty($this->object->field_setlocations['und'])) {
            foreach($this->object->field_setlocations['und'] as $loc) {
                $return[] = Location::load($loc['nid']);
            }
        }
        return $return;
    }

    /**
     * Returns the amount of users on this set
     *
     * @return int
     */
    public function getAPIKey()
    {

        if(!empty($this->object->field_api_key['und'][0]['value'])) {
            return $this->object->field_api_key['und'][0]['value'];
        }
        return '';
    }

    /**
     * Returns the amount of users on this set
     *
     * @return int
     */
    public function getUsersCount()
    {
        if(!empty($this->object->field_setusers['und'])) {
            return count($this->object->field_setusers['und']);
        }
        return 0;
    }

    /**
     * Returns the amount of moderators on this set
     *
     * @return int
     */
    public function getModeratorsCount()
    {
        if(!empty($this->object->field_setmoderators['und'])) {
            return count($this->object->field_setmoderators['und']);
        }
        return 0;
    }

/**
     * Saves the Location
     */
    public function save()
    {
        node_save($this->object);
    }
}
