<?php
/**
 * This class acts as a function wrapper for all kinds of category related tasks.
 */
class Category {

  /**
   * Get's the first label and put's it in the category field.
   * If there is nog category with the same name, it makes one.
   *
   * @param stdClass $node
   * @param boolean $overwrite
   */
  public static function fillCategory($node, $overwrite = false){
    if(is_numeric($node)){
      $node = node_load($node);
    }

    // first label of the node
    $labelsArray = Labels::getLabels($node);
    $pop = array_pop($labelsArray);
    $category_name = trim($pop);

    if(trim($category_name) == ''){
      return;
    }

    $category_nid = self::getCategoryNID($category_name);

    $node_category_nid = helper::value($node, 'field_category', 'nid');

    // fill it if the overwrite is true, or the field is empty
    if(!$overwrite || trim($node_category_nid) == ''){
      $node->field_category[LANGUAGE_NONE][0]['nid'] = $category_nid;
    }

    node_save($node);
  }

  /**
   * Get's the category node id based on the name
   *
   * @param string $category_name
   * @return integer
   */
  public static function getCategoryNID($category_name){
    // get the category node
    $nid = db_query("select nid from node where title = '{$category_name}' and type = 'category' and status = 1;")->fetchField(0);

    if(!$nid){
      // create the node with the thanks text after user registration
      $node = new stdClass();
      $node->type = GojiraSettings::CONTENT_TYPE_CATEGORY;
      node_object_prepare($node);
      $node->title = $category_name;
      $node->language = LANGUAGE_NONE;
      $node->uid = 1;
      if ($node = node_submit($node)) {
        node_save($node);
      }
      $nid = $node->nid;
    }
    return $nid;
  }



  /**
   * Removes the given nid/category and linked all the related locations of that category to a new category that is given in the string.
   * Also checks if the new category in the given string is existing, if it is, uses that one.
   *
   * @param integer $category_nid
   * @param string $new_category
   * @return stdClass|Array changed locations
   */
  public static function moveCategory($category_nid, $new_category){
    $locations = array();
    $locations_return = array();

    $new_category_nid = self::getCategoryNID(strtolower(trim($new_category)));
    $locations = self::getAllLocationsFromCategory($category_nid);

    $counter = 0;
    foreach($locations as $location){
      $location->field_category[LANGUAGE_NONE][0]['nid'] = $new_category_nid;
      node_save($location);
      $locations_return[] = $location;
      $counter++;
      if($counter > 500){
        return false;
      }
    }
    node_delete($category_nid);

    return $locations_return;
  }

  /**
   * Removes the given category and all the related locations.
   *
   * @param integer $nid
   */
  public static function cleanupCategory($category_nid){
    $locations = self::getAllLocationsFromCategory($category_nid);
    foreach($locations as $location){
      node_delete($location->nid);
    }
    node_delete($category_nid);
    Importer::cleanFieldTables();
  }

  /**
   * Get's you all the locations related to this category
   *
   * @param integer $nid
   */
  public static function getAllLocationsFromCategory($category_nid){
    $locations_return = array();
    $locations = db_query("select node.nid from node join field_data_field_category on (node.nid = field_data_field_category.entity_id) where field_data_field_category.field_category_nid = '{$category_nid}'")->fetchAll();

    foreach($locations as $location){
      $locations_return[] = node_load($location->nid);
    }

    return $locations_return;
  }

  /**
   * Get's the category name of the given node
   *
   * @param stdClass $location
   * @return string
   */
  public static function getCategoryName($oNode){
    if($oNode){
      $oCategory = self::getCategoryOfLocation($oNode);
      if($oCategory){
        return $oCategory->title;
      }
    }
    return '';
  }

  /**
   * Get's the category of the given location
   *
   * @param object $category
   */
  public static function getCategoryOfLocation($oLocation){
      $iCategory = helper::value($oLocation, GojiraSettings::CONTENT_TYPE_CATEGORY_FIELD, 'nid');
      $oCategory = node_load($iCategory);
      if($oCategory){
          return $oCategory;
      }
      return false;
  }
}