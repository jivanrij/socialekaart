<?php

class Locationsets
{

    public static $instance = null;
    public $mapsAvailable = null;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Locationsets();
        }
        return self::$instance;
    }

    /**
     * Get's you the current Locationset node
     *
     * @return stdClass
     */
    public function getCurrentLocationset()
    {
        if (arg(0) == 'node' && is_numeric(arg(1))) {
            $nid = arg(1);
            $node = node_load($nid);
            if (isset($node->type)) {
                if ($node->type == GojiraSettings::CONTENT_TYPE_SET_OF_LOCATIONS) {
                    return $node;
                }
            }
        }
        return null;
    }

    /**
     * Tells you if you are on your own map
     *
     * @return boolean
     */
    public static function onOwnMap()
    {
        if (arg(0) == 'ownlist') {
            return true;
        }
        return false;
    }

    /**
     * Tells you if you are on a locationset
     *
     * @return boolean
     */
    public static function onLocationset()
    {
        if (arg(0) == 'node' && is_numeric(arg(1))) {
            $nid = arg(1);
            $node = node_load($nid);
            if (isset($node->type)) {
                if ($node->type == GojiraSettings::CONTENT_TYPE_SET_OF_LOCATIONS) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Gives you the current locationset title of false if you are not on a locationset
     *
     * @return boolean
     */
    public function getCurrentLocationsetTitle()
    {
        if (self::onLocationset()) {
            if (arg(0) == 'node' && is_numeric(arg(1))) {
                $nid = arg(1);
                $node = node_load($nid);
                return $node->title;
            }
        }
        return false;
    }

    /**
     * Get's the locations belonging to the current locationset page.

     * Only works on a locationset page node
     *
     * @param integer set id | optional, default is the current set
     * @param integer category to filter on | optional
     * @return type
     */
    public function getLocations($nid = null, $iFilterCategoryId = null, $sFilterWithTags = '')
    {
        $filteredNodes = array();
        if (trim($sFilterWithTags) !== '') {
            $tags = explode(' ', urldecode($sFilterWithTags));
            $filteredTags = array();
            foreach ($tags as $tag) {
                $tag = trim($tag);
                if ($tag !== '') {
                    $filteredTags[] = $tag;
                }
            }

            $filteredNodes = Search::getInstance()->doSearch($filteredTags, helper::SEARCH_TYPE_COUNTRY, true);
        }

        if (is_null($nid)) {
            $oSet = $this->getCurrentLocationset();
        } else {
            $oSet = node_load($nid);
        }

        $aReturn = array();

        if ($oSet) {
            $sFieldname = GojiraSettings::CONTENT_TYPE_LOCATIONSET_LOCATIONS;
            $aField = $oSet->$sFieldname;

            foreach ($aField[LANGUAGE_NONE] as $location) {
                $oNode = node_load($location['nid']);
                if ($oNode) {
                    if ($sFilterWithTags) {
                        if (array_key_exists($oNode->nid, $filteredNodes)) {
                            $aReturn[$oNode->nid] = $oNode;
                        }
                    } else {
                        if ($iFilterCategoryId) { // filter on category
                            $iThisCategoryId = helper::value($oNode, GojiraSettings::CONTENT_TYPE_CATEGORY_FIELD, 'nid');
                            if ($iFilterCategoryId == $iThisCategoryId) {
                                $aReturn[$oNode->nid] = $oNode;
                            }
                        } else {
                            $aReturn[$oNode->nid] = $oNode;
                        }
                    }
                }
            }
        }

        return $aReturn;
    }

    /**
     * Get's a set of maps to use for the user
     */
    public function getMapSetsForCurrentUser($uid = false)
    {

        if (!$uid) {
            global $user;
            $uid = $user->uid;
        }

        if (is_null($this->mapsAvailable)) {
            $return = array();

            $locationsets = db_query("select entity_id as nid from field_data_field_setusers where bundle = 'locationset' and field_setusers_uid = :uid", array('uid'=>$uid))->fetchAll();
            foreach ($locationsets as $locationset) {

                if (self::userCanAssessLocationset($locationset->nid)) {
                    $return[$locationset->nid] = node_load($locationset->nid);
                }
            }

            $locationsets = db_query("select entity_id as nid from field_data_field_setmoderators where bundle = 'locationset' and field_setmoderators_uid = :uid", array('uid'=>$uid))->fetchAll();
            foreach ($locationsets as $locationset) {

                if (self::userCanAssessLocationset($locationset->nid)) {
                    $return[$locationset->nid] = node_load($locationset->nid);
                }
            }

            $this->mapsAvailable = $return;
        }

        return $this->mapsAvailable;
    }

    /**
     * Tells you if a user can access a specific map
     *
     * @param type $nid
     */
    public function userCanAssessLocationset($nid)
    {
        return true;
    }

    /**
     * Get's the categories from an set of locations in an array
     */
    public function getCategoriesFromLocationsArray($aLocations)
    {
        $aCategories = array();
        foreach ($aLocations as $oLocation) {
            $oCatagory = Category::getCategoryOfLocation($oLocation);
            $aCategories[$oCatagory->nid] = $oCatagory;
        }
        return $aCategories;
    }

    /**
     * Get's the locations on the current practice based own map
     *
     * @return array
     */
    public function getOwnMapLocations()
    {
        if (is_null($this->ownMapLocations)) {
            $ownMapLocations = array();
            $currentPractice = Location::getCurrentLocationNodeObjectOfUser();

            if (is_null($currentPractice)) {
                $sPracticeString = '';
            } else {
                $sPracticeString = ' and group_location_favorite.pid = ' . $currentPractice->nid . ' ';
            }

            $results = db_query("select group_location_favorite.nid from group_location_favorite join node on (node.nid = group_location_favorite.nid) where group_location_favorite.gid = :gid {$sPracticeString} order by node.title asc", array(':gid' => Group::getGroupId()))->fetchAll();
            foreach ($results as $nid) {
                if ($order_by_title) {
                    $ownMapLocations[] = node_load($nid->nid);
                } else {
                    $ownMapLocations[$nid->nid] = node_load($nid->nid);
                }
            }
            $this->ownMapLocations = $ownMapLocations;
        }
        return $this->ownMapLocations;
    }

    /*
    * Returns a resultset of all locationsets the user can manage
    */
    public function getModeratedLocationsets()
    {
        global $user;

        $locationsets = array();

        $resultset = db_query("select node.nid from node join field_data_field_setmoderators on (field_data_field_setmoderators.entity_id = node.nid) where field_setmoderators_uid = :uid and node.status = 1", array(':uid'=>$user->uid));
        foreach ($resultset as $result) {
            $locationsets[] = \Models\Locationset::load($result->nid);
        }
        return $locationsets;
    }

    /*
    * Returns a resultset of all locationsets the user can view
    */
    public function getViewableLocationsets()
    {
        global $user;
        $resultset = db_query("select node.nid, node.title from node join field_data_field_setusers on (field_data_field_setusers.entity_id = node.nid) where field_setusers_uid = :uid", array(':uid'=>$user->uid));
        foreach ($resultset as $result) {
            $locationsets[] = \Models\Locationset::load($result->nid);
        }
        return $locationsets;
    }

    /*
    * Returns all the locationsets the current user is moderator or viewer of
    */
    public function getViewableOrModeratedLocationsets()
    {
        $return = array();
        $viewable = $this->getViewableLocationsets();
        $moderatable = $this->getModeratedLocationsets();
        foreach ($viewable as $view) {
            $return[$view->nid] = $view;
        }
        foreach ($moderatable as $moderate) {
            $return[$moderate->nid] = $moderate;
        }
        return $return;
    }
}
