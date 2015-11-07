<?php

/**
 * This class acts as a function wrapper for all kinds of favorite related tasks. 
 */
class Favorite {

    public static $instance = null;

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new Favorite();
        }
        return self::$instance;
    }

    /**
     * Tells you if the given location is a favorite of the user
     * 
     * @param integer $nid
     * @param integer optional practice to filter on
     * @return boolean
     */
    public static function isFavorite($nid, $iLocationPractice = null) {
        $favorites = Favorite::getInstance()->getAllFavoriteLocations(false, $iLocationPractice);

        if (array_key_exists($nid, $favorites)) {
            return true;
        }
        return false;
    }

    private $favoriteLocations = array();

    /**
     * Get's you all the favorite locations of the current logged in user's group
     * If you order by title it get's ordered, else the keys will be the nid's
     * 
     * @param boolean $order_by_title
     * @return Array
     */
    public function getAllFavoriteLocations($order_by_title = false, $iLocationPractice = null) {
        $favorites = array();

        if (count($this->favoriteLocations) == 0) {
            if (is_null($iLocationPractice)) {
                $sPracticeString = '';
            } else {
                $sPracticeString = ' and group_location_favorite.pid = ' . $iLocationPractice . ' ';
            }

            $results = db_query("select group_location_favorite.nid from group_location_favorite join node on (node.nid = group_location_favorite.nid) where group_location_favorite.gid = :gid {$sPracticeString} order by node.title asc", array(':gid' => Group::getGroupId()))->fetchAll();
            foreach ($results as $nid) {
                if ($order_by_title) {
                    $this->favoriteLocations[] = node_load($nid->nid);
                } else {
                    $this->favoriteLocations[$nid->nid] = node_load($nid->nid);
                }
            }
        }

        return $this->favoriteLocations;
    }

    /**
     * Saves the personalized info of the given location (nid) for the given user's group
     * 
     * @param integer $nid
     * @param boolean $favorite
     */
    public function setFavorite($nid, $gid = null, $pid = null) {
        
        if($gid === null){
            $gid = Group::getGroupId();
        }
        
        $favoriteLocations = self::getInstance()->getAllFavoriteLocations();

        if (array_key_exists($nid, $favoriteLocations)) {
            return;
        } else {
            if($pid = null){
                $oCurrentLocation = Location::getCurrentLocationObjectOfUser();
                $pid = $oCurrentLocation->nid;
            }            
            $params = array(':gid' => $gid, ':nid' => $nid, ':pid' => $pid);
            return db_query("INSERT INTO `group_location_favorite` (`gid`, `nid`, `pid`) VALUES (:gid, :nid, :pid)", $params);
        }
    }

    /**
     * This function removes a locations from the favorites of the current selected group of the user
     * 
     * @param integer $nid
     */
    public function removeFromFavorite($nid) {
        $params = array(':nid' => $nid, ':gid' => Group::getGroupId());
        db_query("DELETE FROM `group_location_favorite` WHERE `nid`=:nid AND `gid`=:gid;", $params);
    }

    /**
     * Turns filtering on favorites for the user on or off
     * 
     * @global uid $user
     * @param boolean $status
     */
    public function turnFavoriteFilter($status) {
        global $user;
        $user = user_load($user->uid);

        $field = GojiraSettings::CONTENT_TYPE_SEARCH_FAVORITES_FIELD;
        $fieldValue = $user->$field;

        if ($status) {
            $fieldValue[LANGUAGE_NONE][0]['value'] = 1;
        } else {
            $fieldValue[LANGUAGE_NONE][0]['value'] = 0;
        }

        $user->$field = $fieldValue;

        user_save($user);
    }

    /**
     * Gets all the groups that have favorited this locations
     * 
     * returns array with combined gid & pid
     */
    public function getAllFaviritedGroupsByPractices($nid) {
        $return = array();
        $results = db_query('select gid, pid from group_location_favorite where nid = :nid', array(':nid' => $nid));
        foreach($results as $result) {
            $return[$result->gid.$result->pid] = array(
                'gid'=>$result->gid,
                'pid'=>$result->pid
            );
        }
        return $return;
    }

}
