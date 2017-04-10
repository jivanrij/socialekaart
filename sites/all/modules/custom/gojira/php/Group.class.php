<?php
/**
 * This class acts as a function wrapper for all kinds of group related tasks.
 */
class Group
{

    /**
   * Get's the group id of the current or given user;
   *
   * @global integer $uis
   * @return integer the group id
   */
  public static function getGroupId($uid = null)
  {
      if (is_null($uid)) {
          global $user;
          $user = user_load($user->uid);
      } else {
          $user = user_load($uid);
      }
      $groupField = GojiraSettings::CONTENT_TYPE_GROUP_FIELD;
      $groupField = $user->$groupField;

      if (count($groupField) == 0) {
          $group = Group::createNewGroup($user);
          $groupField = GojiraSettings::CONTENT_TYPE_GROUP_FIELD;
          $user->$groupField = array(LANGUAGE_NONE => array(0 => array('nid' => $group->nid)));
          user_save($user);
          return $group->nid;
      }

      if (isset($groupField[LANGUAGE_NONE]) && $groupField[LANGUAGE_NONE][0] && $groupField[LANGUAGE_NONE][0]['nid']) {
          return $groupField[LANGUAGE_NONE][0]['nid'];
      }
      return;
  }

  /**
   * Get's all the users of the gives group, or the current group
   *
   * @param integer group's id | optional
   * @return resultset
   */
  public static function getAllUsers($nid = null)
  {
      if (is_null($nid)) {
          $nid = self::getGroupId();
      }
      $result = db_query("select entity_id from field_data_field_gojira_group where bundle = 'user' and field_gojira_group_nid = {$nid}");
      if (!$result) {
          return array();
      }

      $return = array();
      foreach ($result as $user) {
          $return[] = user_load($user->entity_id);
      }

      return $return;
  }

  /**
   * Get's all the groups
   *
   * @return resultset
   */
  public static function getAllGroups()
  {
      $return = array();
      $group = GojiraSettings::CONTENT_TYPE_GROUP;
      $result = db_query("select nid from {node} where type = '{$group}' order by title");
      if (!$result) {
          return array();
      }
      foreach ($result as $item) {
          $return[] = node_load($item->nid);
      }
      return $return;
  }

  /**
   * Creates a new group for the given user
   *
   * @param stdClass $user
   * @return stdClass
   */
  public static function createNewGroup($user)
  {
      // create new group to link new user to
    $group = new stdClass();
      $group->type = GojiraSettings::CONTENT_TYPE_GROUP;
      node_object_prepare($group);
      $group->language = LANGUAGE_NONE;
      $group->uid = $user->uid;
      $group->status = 0;
      $group->promote = 0;
      $group->comment = 0;
      if ($title == '') {
          $group->title = 'Group made by '.$user->mail;
      } else {
          $group->title = $title;
      }
      $group = node_submit($group);
      $field = GojiraSettings::CONTENT_TYPE_ORIGINAL_DOCTOR;
      $group->$field = array(LANGUAGE_NONE=>array(0=>array('uid'=>$user->uid))); // [LANGUAGE_NONE][0]['value'] = $user->uid;

    node_save($group);

      return $group;
  }
}
