<?php
class Add
{

    /**
   * Get's a html image tag of the given add in the given size
   * 
   * @param stdClass $node
   * @param string $size
   * @return string
   */
  public static function getImageUrl($node, $size = null)
  {
      if ($node->type != GojiraSettings::CONTENT_TYPE_ADD) {
          return false;
      }

      if ($size == null) {
          $size = GojiraSettings::IMAGE_STYLE_ADD_SMALL;
      }

      $info = self::getImageInfoFromField($node, GojiraSettings::CONTENT_TYPE_ADD_IMAGE_FIELD);

      return theme('image_style', array(
        'style_name' => $size, 'path' => $info['uri'], 'title' => $info['title'], 'alt' => $info['alt']
    ));
  }

  /**
   * Get's you the useable information of a image field, returns false if none if found.
   * 
   * @param stdClass $node
   * @param string $field
   * @return array|boolean
   */
  public static function getImageInfoFromField($node, $field)
  {
      if (isset($node->$field)) {
          $field = $node->$field;
          if (isset($field[LANGUAGE_NONE][0])) {
              return $field[LANGUAGE_NONE][0];
          }
      }
      return false;
  }


  /**
   * Get's you a random add from all available adds
   * 
   * @return stdClass
   */
  public static function getRandomAdd()
  {
      $now = helper::getTime();

      $query = 'select nid from node 
              join field_data_field_showuntill on (node.nid = field_data_field_showuntill.entity_id)
              join field_data_field_showfrom on (node.nid = field_data_field_showfrom.entity_id)
              where node.type =  \'add\' and node.status = 1 
              and field_data_field_showuntill.field_showuntill_value >= ' . $now . '
              and field_data_field_showfrom.field_showfrom_value <= ' . $now;

      $result = db_query($query);
      $nodes = array();
      foreach ($result as $entity) {
          $nodes[] = $entity->nid;
      }

      $key = array_rand($nodes);
      if (array_key_exists($key, $nodes)) {
          return node_load($nodes[$key]);
      }
    
      return false;
  }
}
