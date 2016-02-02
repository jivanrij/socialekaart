<?php

class Locationsets {

    public static $instance = null;
    public $mapsAvailable = null;

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new Locationsets();
        }
        return self::$instance;
    }

    /**
     * Tells you if a user can access predefined maps
     *
     * @return boolean
     */
    public function userHasRightToLocationssets() {
        if (user_access(helper::PERMISSION_LOCATIONSETS)) {
            return true;
        }
        return false;
    }

    /**
     * Get's you the current node
     *
     * @return node
     */
    public function getCurrentLocationset(){
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
     * Get's the locations belonging to the current locationset page.
     * Only works on a locationsset page node
     * 
     * @param integer set id | optional, default is the current set
     * @param integer category to filter on | optional
     * @return type
     */
    public function getLocations($nid = null, $iFilterCategoryId = null){

        if(is_null($nid)){
            $oSet = $this->getCurrentLocationset();
        }else{
            $oSet = node_load($nid);
        }

        $aReturn = array();

        if($oSet){
            $sFieldname = GojiraSettings::CONTENT_TYPE_LOCATIONSET_LOCATIONS;
            $aField = $oSet->$sFieldname;
            foreach($aField[LANGUAGE_NONE] as $location){
                $oNode = node_load($location['nid']);
                if($oNode){
                    if($iFilterCategoryId){ // filter on category
                        $iThisCategoryId = helper::value($oNode, GojiraSettings::CONTENT_TYPE_CATEGORY_FIELD, 'nid');
                        if($iFilterCategoryId == $iThisCategoryId){
                            $aReturn[] = $oNode;
                        }
                    }else{
                        $aReturn[] = $oNode;
                    }
                }
            }
        }
        return $aReturn;
    }

    /**
     * Get's a set of maps to use for the user
     */
    public function getMapSetsForCurrentUser() {

        if(is_null($this->mapsAvailable)){
            $return = array();
            $rLocationsets = array();
            if ($this->userHasRightToLocationssets()) {
                $oLocation = Location::getCurrentLocationNodeObjectOfUser();

                $sPostcodeNumber = substr(trim(helper::value($oLocation, GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD)), 0, 2);
                if(is_numeric($sPostcodeNumber)){
                    $iPostcodearea = db_query("select field_data_field_postcodenumber.entity_id as nid from node join field_data_field_postcodenumber on (field_data_field_postcodenumber.entity_id = node.nid) where node.type = 'postcodearea' and field_data_field_postcodenumber.bundle = 'postcodearea' and field_data_field_postcodenumber.field_postcodenumber_value = {$sPostcodeNumber}")->fetchField();
                    if(is_numeric($iPostcodearea)){
                        $rLocationsets = db_query("select entity_id as nid from field_data_field_postcodeareas where bundle = 'locationsset' and field_postcodeareas_nid = {$iPostcodearea}")->fetchAll();
                    }
                }
                foreach ($rLocationsets as $oLocationset) {
                    if (self::userCanAssessLocationset($oLocationset->nid)) {
                        $return[] = node_load($oLocationset->nid);
                    }
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
    public function userCanAssessLocationset($nid) {
        return true;
    }

    /**
     * Get's the categories from an set of locations in an array
     */
    public function getCategoriesFromLocationsArray($aLocations){
        $aCategories = array();
        foreach($aLocations as $oLocation){
            $oCatagory = Category::getCategoryOfLocation($oLocation);
            $aCategories[$oCatagory->nid] = $oCatagory;
        }
        return $aCategories;
    }

}
