<?php

/**
 * Adds the therapist contenttype if it does not exists
 * 
 */
function gojira_add_locationnode_contenttype() {
  $type = GojiraSettings::CONTENT_TYPE_LOCATION;
  $exists = db_query("select type from node_type where type = '{$type}'")->rowCount();

  if (!$exists) {
    $type_values = array(
        'op' => 'Save content type',
        'type' => $type,
        'name' => 'Location',
        'orig_type' => '',
        'old_type' => '',
        'description' => 'These node type holds the practices',
        'help' => '',
        'title_label' => '',
        'body_label' => '',
        'base' => '',
        'custom' => '1',
        'locked' => '0',
        'modified' => '1'
    );

    $op = isset($type_values ['op']) ? $type_values ['op'] : '';

    $type = node_type_set_defaults();

    $type->type = trim($type_values ['type']);
    $type->name = trim($type_values ['name']);
    $type->orig_type = trim($type_values ['orig_type']);
    $type->old_type = isset($type_values ['old_type']) ? $type_values ['old_type'] : $type->type;

    $type->description = $type_values ['description'];
    $type->help = $type_values ['help'];
    $type->title_label = 'Title';
    $type->body_label = 'Body';

    $type->has_title = true;
    $type->has_body = false;

    $type->base = !empty($type_values ['base']) ? $type_values ['base'] : 'node_content';
    $type->custom = $type_values ['custom'];
    $type->modified = true;
    $type->locked = $type_values ['locked'];

    $status = node_type_save($type);

    node_types_rebuild();
    menu_rebuild();
    $t_args = array(
        '%name' => $type->name
    );

    drupal_set_message(t('Added content type Therapist.'));
    watchdog('Gojira', 'Added content type Therapist.');
  }
}

/**
 * Adds the therapist contenttype if it does not exists
 * 
 */
function gojira_add_gojira_group_contenttype() {
  $type = GojiraSettings::CONTENT_TYPE_GROUP;
  $exists = db_query("select type from node_type where type = '{$type}'")->rowCount();

  if (!$exists) {
    $type_values = array(
        'op' => 'Save content type',
        'type' => $type,
        'name' => 'Group',
        'orig_type' => '',
        'old_type' => '',
        'description' => 'Binds users with other users and locations',
        'help' => '',
        'title_label' => '',
        'body_label' => 'Description',
        'base' => '',
        'custom' => '1',
        'locked' => '0',
        'modified' => '1'
    );

    $op = isset($type_values ['op']) ? $type_values ['op'] : '';

    $type = node_type_set_defaults();

    $type->type = trim($type_values ['type']);
    $type->name = trim($type_values ['name']);
    $type->orig_type = trim($type_values ['orig_type']);
    $type->old_type = isset($type_values ['old_type']) ? $type_values ['old_type'] : $type->type;

    $type->description = $type_values ['description'];
    $type->help = $type_values ['help'];
    $type->title_label = 'Title';
    $type->body_label = 'Description';

    $type->has_title = true;
    $type->has_body = true;

    $type->base = !empty($type_values ['base']) ? $type_values ['base'] : 'node_content';
    $type->custom = $type_values ['custom'];
    $type->modified = true;
    $type->locked = $type_values ['locked'];

    $status = node_type_save($type);

    node_types_rebuild();
    menu_rebuild();
    $t_args = array(
        '%name' => $type->name
    );

    drupal_set_message(t('Added content type Group.'));
    watchdog('Gojira', 'Added content type Groupy.');
  }
}

/**
 * Adds the therapist contenttype if it does not exists
 * 
 */
function gojira_add_textnode_contenttype() {
  $type = GojiraSettings::CONTENT_TYPE_TEXT;
  $exists = db_query("select type from node_type where type = '{$type}'")->rowCount();

  if (!$exists) {
    $type_values = array(
        'op' => 'Save content type',
        'type' => $type,
        'name' => 'Text',
        'orig_type' => '',
        'old_type' => '',
        'description' => 'These node type holds text.',
        'help' => '',
        'title_label' => '',
        'body_label' => 'Text',
        'base' => '',
        'custom' => '1',
        'locked' => '0',
        'modified' => '1'
    );

    $op = isset($type_values ['op']) ? $type_values ['op'] : '';

    $type = node_type_set_defaults();

    $type->type = trim($type_values ['type']);
    $type->name = trim($type_values ['name']);
    $type->orig_type = trim($type_values ['orig_type']);
    $type->old_type = isset($type_values ['old_type']) ? $type_values ['old_type'] : $type->type;

    $type->description = $type_values ['description'];
    $type->help = $type_values ['help'];
    $type->title_label = 'Title';
    $type->body_label = 'Text';

    $type->has_title = true;
    $type->has_body = true;

    $type->base = !empty($type_values ['base']) ? $type_values ['base'] : 'node_content';
    $type->custom = $type_values ['custom'];
    $type->modified = true;
    $type->locked = $type_values ['locked'];

    $status = node_type_save($type);

    node_types_rebuild();
    menu_rebuild();
    $t_args = array(
        '%name' => $type->name
    );

    drupal_set_message(t('Added content type Text.'));
    watchdog('Gojira', 'Added content type Text.');
  }
}

function add_address_cache_table() {
  $sql = <<<EOT
CREATE TABLE `address_cache` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`address` VARCHAR(100) NOT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`street` VARCHAR(200) NULL DEFAULT NULL,
	`houseNumber` VARCHAR(10) NULL DEFAULT NULL,
	`houseNumberAddition` VARCHAR(10) NULL DEFAULT NULL,
	`postcode` VARCHAR(7) NULL DEFAULT NULL,
	`city` VARCHAR(200) NULL DEFAULT NULL,
	`municipality` VARCHAR(100) NULL DEFAULT NULL,
	`addressType` VARCHAR(100) NULL DEFAULT NULL,
	`cordinates_x` VARCHAR(32) NOT NULL,
	`cordinates_y` VARCHAR(32) NOT NULL,
	PRIMARY KEY (`id`)
)
EOT;
  if (!db_table_exists('address_cache')) {
    db_query($sql);
  }
}


function add_gojira_payments_table() {
  $sql = <<<EOT
CREATE TABLE `gojira_payments` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`uid` INT(11) NOT NULL DEFAULT '0',
	`name` VARCHAR(50) NOT NULL DEFAULT '0',
	`description` VARCHAR(50) NOT NULL,
	`amount` FLOAT NOT NULL DEFAULT '0',
	`gid` INT(11) NOT NULL,
	`ideal_id` VARCHAR(50) NOT NULL,
	`ideal_code` VARCHAR(50) NOT NULL,
	`status` INT(11) NULL DEFAULT NULL COMMENT '0=open, 1=completed, 2=failed',
	`period_start` INT(11) NOT NULL,
	`period_end` INT(11) NOT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`increment` INT(11) NULL DEFAULT NULL,
	`discount` FLOAT NOT NULL DEFAULT '0',
	`tax` FLOAT NOT NULL DEFAULT '0',
	`payed` FLOAT NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COMMENT='stores all the payments done'
EOT;
  if (!db_table_exists('gojira_payments')) {
    db_query($sql);
  }
}

function add_gojira_adhocdata_addresses_table() {
  $sql = <<<EOT
CREATE TABLE `adhocdata_addresses` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NULL DEFAULT NULL,
	`email` VARCHAR(255) NULL DEFAULT NULL,
	`city` VARCHAR(255) NULL DEFAULT NULL,
	`street` VARCHAR(255) NULL DEFAULT NULL,
	`url` VARCHAR(255) NULL DEFAULT NULL,
	`housnumber` VARCHAR(255) NULL DEFAULT NULL,
	`postcode` VARCHAR(255) NULL DEFAULT NULL,
	`telephone` VARCHAR(255) NULL DEFAULT NULL,
	`category` VARCHAR(255) NULL DEFAULT NULL,
	`gojira_category` VARCHAR(255) NULL DEFAULT NULL COMMENT 'category jonathan thinks it needs to be put in',
	`gojira_labels` VARCHAR(512) NULL DEFAULT NULL COMMENT 'labels jonathan thinks it needs to be applied',
	`longitude` VARCHAR(255) NULL DEFAULT NULL,
	`latitude` VARCHAR(255) NULL DEFAULT NULL,
	`nid` INT(11) NULL DEFAULT NULL COMMENT 'imported and saved in this node',
	`coords_checked_with_dtb` INT(11) NOT NULL DEFAULT '0' COMMENT 'we have checked if we have the coordinates in the database',
	`coords_checked_with_google` INT(11) NOT NULL DEFAULT '0' COMMENT 'we have checked if we have the coordinates from google',
	`coords_checked_with_postcodenl` INT(11) NOT NULL DEFAULT '0' COMMENT 'we have checked if we have the coordinates from postcodenl',
	`ready_to_import` INT(11) NOT NULL DEFAULT '0' COMMENT 'we have coordniates, so this entry can be imported',
	`imported` INT(11) NOT NULL DEFAULT '0' COMMENT 'is imported',
	`double` INT(11) NOT NULL DEFAULT '0' COMMENT 'not imported, double found',
	`attempts` INT(11) NOT NULL DEFAULT '0' COMMENT 'amount of times we have tryed to get the coordinates',
	PRIMARY KEY (`id`),
	INDEX `id` (`id`)
)
COMMENT='Stores all the adhocdata addresses'
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;

EOT;
  if (!db_table_exists('adhocdata_addresses')) {
    db_query($sql);
  }
}

function add_gojira_locations_table() {
  $sql = <<<EOT
CREATE TABLE `locations` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NULL DEFAULT NULL,
	`email` VARCHAR(255) NULL DEFAULT NULL,
	`city` VARCHAR(255) NULL DEFAULT NULL,
	`street` VARCHAR(255) NULL DEFAULT NULL,
	`url` VARCHAR(255) NULL DEFAULT NULL,
	`housnumber` VARCHAR(255) NULL DEFAULT NULL,
	`properties` VARCHAR(255) NULL DEFAULT NULL,
	`gojira_category` VARCHAR(255) NULL DEFAULT NULL COMMENT 'category jonathan thinks it needs to be put in',
	`gojira_labels` VARCHAR(255) NULL DEFAULT NULL COMMENT 'labels jonathan thinks that need to be applied',
	`postcode` VARCHAR(255) NULL DEFAULT NULL,
	`telephone` VARCHAR(255) NULL DEFAULT NULL,
	`fax` VARCHAR(255) NULL DEFAULT NULL,
	`note` VARCHAR(255) NULL DEFAULT NULL,
	`status` VARCHAR(255) NULL DEFAULT NULL,
	`visible` VARCHAR(255) NULL DEFAULT NULL,
	`published` VARCHAR(255) NULL DEFAULT NULL,
	`latitude` VARCHAR(255) NULL DEFAULT NULL,
	`longitude` VARCHAR(255) NULL DEFAULT NULL,
	`nid` INT(11) NULL DEFAULT NULL,
	`imported` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	INDEX `id` (`id`)
)
COMMENT='this table is to store all the locations for export purposes'
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;

EOT;
  if (!db_table_exists('locations')) {
    db_query($sql);
  }
}


function add_personaltags_table() {
  $sql = <<<EOT
CREATE TABLE IF NOT EXISTS `group_location_term` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `gid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'lnks to the group node representing a location group',
  `nid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'links to the node representing a location where the personal tags are for',
  `tid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'link to tag',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `FK_user_location_term_node` (`nid`),
  KEY `FK_user_location_term_taxonomy_term_data` (`tid`),
  KEY `FK_user_location_term_groups` (`gid`),
  CONSTRAINT `FK_user_location_term_groups` FOREIGN KEY (`gid`) REFERENCES `node` (`nid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_user_location_term_node` FOREIGN KEY (`nid`) REFERENCES `node` (`nid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_user_location_term_taxonomy_term_data` FOREIGN KEY (`tid`) REFERENCES `taxonomy_term_data` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links personal tems between a location & user.';
EOT;
  if (!db_table_exists('group_location_term')) {
    db_query($sql);
  }
}

function addIndexField() {


  if (!db_field_exists('node', 'indexed')) {
  $sql = <<<EOT
ALTER TABLE `node`
	ADD COLUMN `indexed` INT(11) NOT NULL DEFAULT '0' COMMENT 'The Unix timestamp of when the node was last indexed' AFTER `changed`;
EOT;
    db_query($sql);
  }
}

function addIndexTables() {
  $sql = <<<EOT
CREATE TABLE `searchword` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`word` VARCHAR(128) NOT NULL,
	UNIQUE INDEX `word` (`word`),
	INDEX `id` (`id`)
)
COMMENT='The search index holding all the words.'
ENGINE=InnoDB;
EOT;
    db_query($sql);
    
      $sql = <<<EOT
CREATE TABLE `searchword_nid` (
	`node_nid` INT(10) UNSIGNED NOT NULL,
	`searchword_id` INT(10) UNSIGNED NOT NULL,
	`score` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`node_nid`, `searchword_id`),
	INDEX `searchword link` (`searchword_id`),
	CONSTRAINT `location link` FOREIGN KEY (`node_nid`) REFERENCES `node` (`nid`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `searchword link` FOREIGN KEY (`searchword_id`) REFERENCES `searchword` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
)
COMMENT='Connection betweet a word in the index & the location'
ENGINE=InnoDB;
EOT;
    db_query($sql);
    
    
}


/**
 * Adds the required user fields
 * 
 * https://drupal.org/node/730554
 */
function gojira_add_fields() {

  $fields = array(
      array('required' => true, 'field_type' => 'text', 'widget_type' => 'textfield', 'bundle' => GojiraSettings::CONTENT_TYPE_TEXT, 'entity_type' => 'node', 'name' => GojiraSettings::CONTENT_TYPE_CODE_FIELD, 'label' => 'Code', 'description' => 'The code of this text node.'),
      array('required' => true, 'field_type' => 'text_with_summary', 'widget_type' => 'text_textarea_with_summary', 'bundle' => GojiraSettings::CONTENT_TYPE_TEXT, 'entity_type' => 'node', 'name' => GojiraSettings::CONTENT_TYPE_TEXT_FIELD, 'label' => 'Text', 'description' => 'Stored text.'),
      array('required' => true, 'field_type' => 'text', 'widget_type' => 'textfield', 'bundle' => GojiraSettings::CONTENT_TYPE_LOCATION, 'entity_type' => 'node', 'name' => GojiraSettings::CONTENT_TYPE_EMAIL_FIELD, 'label' => 'E-mail', 'description' => 'The contact e-mail address of this location.'),
      array('required' => true, 'field_type' => 'text', 'widget_type' => 'textfield', 'bundle' => GojiraSettings::CONTENT_TYPE_LOCATION, 'entity_type' => 'node', 'name' => GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD, 'label' => 'City', 'description' => 'The city this practice is located in.'),
      array('required' => true, 'field_type' => 'text', 'widget_type' => 'textfield', 'bundle' => GojiraSettings::CONTENT_TYPE_LOCATION, 'entity_type' => 'node', 'name' => GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD, 'label' => 'Street', 'description' => 'The street this practice is located on.'),
      array('required' => true, 'field_type' => 'text', 'widget_type' => 'textfield', 'bundle' => GojiraSettings::CONTENT_TYPE_LOCATION, 'entity_type' => 'node', 'name' => GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD, 'label' => 'House number', 'description' => 'The house number this practice is located on.'),
      array('required' => true, 'field_type' => 'text', 'widget_type' => 'textfield', 'bundle' => GojiraSettings::CONTENT_TYPE_LOCATION, 'entity_type' => 'node', 'name' => GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD, 'label' => 'Postcode', 'description' => 'The postcode this practice is located on.'),
      array('required' => false, 'field_type' => 'text', 'widget_type' => 'textfield', 'bundle' => GojiraSettings::CONTENT_TYPE_LOCATION, 'entity_type' => 'node', 'name' => GojiraSettings::CONTENT_TYPE_TELEPHONE_FIELD, 'label' => 'Telephone', 'description' => 'The telephone number of the location.'),
      array('required' => false, 'field_type' => 'text', 'widget_type' => 'textfield', 'bundle' => GojiraSettings::CONTENT_TYPE_LOCATION, 'entity_type' => 'node', 'name' => GojiraSettings::CONTENT_TYPE_FAX_FIELD, 'label' => 'Fax', 'description' => 'The fax number of the location.'),
      array('required' => true, 'field_type' => 'text', 'widget_type' => 'textfield', 'bundle' => GojiraSettings::CONTENT_TYPE_LOCATION, 'entity_type' => 'node', 'name' => GojiraSettings::CONTENT_TYPE_URL_FIELD, 'label' => 'Url', 'description' => 'Url of a locations.'),
      array(
          'required' => false,
          'field_type' => 'text_long',
          'widget_type' => 'text_textarea',
          'bundle' => GojiraSettings::CONTENT_TYPE_LOCATION,
          'entity_type' => 'node',
          'name' => GojiraSettings::CONTENT_TYPE_NOTE_FIELD,
          'label' => 'Note',
          'description' => 'Information about the location.'
      ),
      array(
          'required' => false,
          'field_type' => 'node_reference',
          'widget_type' => 'options_select',
          'bundle' => GojiraSettings::CONTENT_TYPE_LOCATION,
          'entity_type' => 'node',
          'name' => GojiraSettings::CONTENT_TYPE_GROUP_FIELD,
          'label' => 'Location group',
          'description' => 'Group the location is linked to.'
      ),
      array(
          'required' => false,
          'field_type' => 'node_reference',
          'widget_type' => 'options_select',
          'bundle' => 'user',
          'entity_type' => 'user',
          'name' => GojiraSettings::CONTENT_TYPE_GROUP_FIELD,
          'label' => 'User group',
          'description' => 'Group the user is linked to.'
      ),
      array(
          'required' => false, 
          'field_type' => 'list_integer', 
          'widget_type' => 'options_select', 
          'bundle' => GojiraSettings::CONTENT_TYPE_LOCATION,
          'entity_type' => 'node', 
          'name' => GojiraSettings::CONTENT_TYPE_MODERATED_STATUS_FIELD, 
          'label' => 'Moderation status', 
          'description' => 'Status of the moderation of this location.'
      ),
      array(
          'required' => false, 
          'field_type' => 'list_boolean', 
          'widget_type' => 'options_onoff', 
          'bundle' => 'user', 
          'entity_type' => 'user', 
          'name' => GojiraSettings::CONTENT_TYPE_IS_DOCTOR_FIELD, 
          'label' => 'Is doctor', 
          'description' => 'You confirm that you practice medicine.'
      ),
      array(
          'required' => false, 
          'field_type' => 'list_boolean', 
          'widget_type' => 'options_onoff', 
          'bundle' => 'user', 
          'entity_type' => 'user', 
          'name' => GojiraSettings::CONTENT_TYPE_SHOW_HINTS_FIELD,
          'label' => 'Show hints', 
          'description' => 'Show the hits for his user.'
      ),
      array(
          'required' => false, 
          'field_type' => 'list_boolean', 
          'widget_type' => 'options_onoff', 
          'bundle' => 'user', 
          'entity_type' => 'user', 
          'name' => GojiraSettings::CONTENT_TYPE_SEARCH_FAVORITES_FIELD,
          'label' => 'Search in favorites', 
          'description' => 'Determs if the user will by default search in favorites.'
      ),
      array(
          'required' => false, 
          'field_type' => 'list_boolean', 
          'widget_type' => 'options_onoff', 
          'bundle' => 'user', 
          'entity_type' => 'user', 
          'name' => GojiraSettings::CONTENT_TYPE_HAS_MULTIPLE_LOCATIONS_FIELD,
          'label' => 'Multiple locations', 
          'description' => 'Check this box if you want to manage multiple locations instead of one.'
      ),
      array('required' => false, 'field_type' => 'text', 'widget_type' => 'textfield', 'bundle' => 'user', 'entity_type' => 'user', 'name' => GojiraSettings::CONTENT_TYPE_BIG_FIELD, 'label' => 'BIG-number', 'description' => 'The BIG-number of the user.'),
  );

  foreach ($fields as $fieldInfo) {

    $field = field_info_field($fieldInfo['name']);
    if (!$field) {
      $field = array(
          'field_name' => $fieldInfo['name'],
          'type' => $fieldInfo['field_type'],
      );
      field_create_field($field);
    }
    
    // attache the field to the user
    $instance = array(
        'field_name' => $fieldInfo['name'],
        'entity_type' => $fieldInfo['entity_type'],
        'label' => $fieldInfo['label'],
        'bundle' => $fieldInfo['bundle'],
        'description' => $fieldInfo['description'],
        'required' => $fieldInfo['required'],
        'settings' => array(
            'user_register_form' => 1,
        ),
        'widget' => array(
            'type' => $fieldInfo['widget_type'],
        ),
    );
    field_create_instance($instance);
    $message = 'Added the ' . $fieldInfo['name'] . ' field to the entity ' . $fieldInfo['entity_type'] . '.';
    drupal_set_message(t($message));
    watchdog('Gojira', $message);
  }

  // can't seem to get the setting array of the is doctor field correct, so let's do it this way #biteme
  $field_config_instance_is_doctor = '0x613A373A7B733A353A226C6162656C223B733A393A22497320446F63746F72223B733A363A22776964676574223B613A353A7B733A363A22776569676874223B733A313A2239223B733A343A2274797065223B733A31333A226F7074696F6E735F6F6E6F6666223B733A363A226D6F64756C65223B733A373A226F7074696F6E73223B733A363A22616374697665223B693A313B733A383A2273657474696E6773223B613A313A7B733A31333A22646973706C61795F6C6162656C223B693A313B7D7D733A383A2273657474696E6773223B613A313A7B733A31383A22757365725F72656769737465725F666F726D223B693A313B7D733A373A22646973706C6179223B613A313A7B733A373A2264656661756C74223B613A353A7B733A353A226C6162656C223B733A353A2261626F7665223B733A343A2274797065223B733A31323A226C6973745F64656661756C74223B733A383A2273657474696E6773223B613A303A7B7D733A363A226D6F64756C65223B733A343A226C697374223B733A363A22776569676874223B693A323B7D7D733A383A227265717569726564223B693A303B733A31313A226465736372697074696F6E223B733A34333A224920636F6E6669726D2074686174204920616D2061206D65646963616C2070726163746974696F6E65722E223B733A31333A2264656661756C745F76616C7565223B613A313A7B693A303B613A313A7B733A353A2276616C7565223B693A303B7D7D7D';
  $field_config_is_doctor = '0x613A373A7B733A31323A227472616E736C617461626C65223B733A313A2230223B733A31323A22656E746974795F7479706573223B613A303A7B7D733A383A2273657474696E6773223B613A323A7B733A31343A22616C6C6F7765645F76616C756573223B613A323A7B693A303B733A313A2230223B693A313B733A313A2231223B7D733A32333A22616C6C6F7765645F76616C7565735F66756E6374696F6E223B733A303A22223B7D733A373A2273746F72616765223B613A353A7B733A343A2274797065223B733A31373A226669656C645F73716C5F73746F72616765223B733A383A2273657474696E6773223B613A303A7B7D733A363A226D6F64756C65223B733A31373A226669656C645F73716C5F73746F72616765223B733A363A22616374697665223B733A313A2231223B733A373A2264657461696C73223B613A313A7B733A333A2273716C223B613A323A7B733A31383A224649454C445F4C4F41445F43555252454E54223B613A313A7B733A32363A226669656C645F646174615F6669656C645F69735F646F63746F72223B613A313A7B733A353A2276616C7565223B733A32313A226669656C645F69735F646F63746F725F76616C7565223B7D7D733A31393A224649454C445F4C4F41445F5245564953494F4E223B613A313A7B733A33303A226669656C645F7265766973696F6E5F6669656C645F69735F646F63746F72223B613A313A7B733A353A2276616C7565223B733A32313A226669656C645F69735F646F63746F725F76616C7565223B7D7D7D7D7D733A31323A22666F726569676E206B657973223B613A303A7B7D733A373A22696E6465786573223B613A313A7B733A353A2276616C7565223B613A313A7B693A303B733A353A2276616C7565223B7D7D733A323A226964223B733A323A223135223B7D';
  db_query("UPDATE {field_config} SET `data`={$field_config_is_doctor} WHERE  `field_name`='" . GojiraSettings::CONTENT_TYPE_IS_DOCTOR_FIELD . "'");
  db_query("UPDATE {field_config_instance} SET `data`={$field_config_instance_is_doctor} WHERE  `field_name`='" . GojiraSettings::CONTENT_TYPE_IS_DOCTOR_FIELD . "'");

  $field_config_instance_moderation_status = '0x613A373A7B733A353A226C6162656C223B733A31373A224D6F6465726174696F6E20737461747573223B733A363A22776964676574223B613A353A7B733A363A22776569676874223B733A313A2235223B733A343A2274797065223B733A31343A226F7074696F6E735F73656C656374223B733A363A226D6F64756C65223B733A373A226F7074696F6E73223B733A363A22616374697665223B693A313B733A383A2273657474696E6773223B613A303A7B7D7D733A383A2273657474696E6773223B613A313A7B733A31383A22757365725F72656769737465725F666F726D223B623A303B7D733A373A22646973706C6179223B613A313A7B733A373A2264656661756C74223B613A353A7B733A353A226C6162656C223B733A353A2261626F7665223B733A343A2274797065223B733A31323A226C6973745F64656661756C74223B733A383A2273657474696E6773223B613A303A7B7D733A363A226D6F64756C65223B733A343A226C697374223B733A363A22776569676874223B693A383B7D7D733A383A227265717569726564223B693A303B733A31313A226465736372697074696F6E223B733A303A22223B733A31333A2264656661756C745F76616C7565223B4E3B7D';
  $field_config_moderation_status = '0x613A373A7B733A31323A227472616E736C617461626C65223B733A313A2230223B733A31323A22656E746974795F7479706573223B613A303A7B7D733A383A2273657474696E6773223B613A323A7B733A31343A22616C6C6F7765645F76616C756573223B613A343A7B693A313B733A393A226D6F64657261746564223B693A323B733A32393A226E6F74206D6F646572617465642C206E6F20696E766974652073656E64223B693A333B733A32363A226E6F74206D6F646572617465642C20696E766974652073656E64223B693A343B733A32333A226D6F646572617465642C20616674657220696E76697465223B7D733A32333A22616C6C6F7765645F76616C7565735F66756E6374696F6E223B733A303A22223B7D733A373A2273746F72616765223B613A353A7B733A343A2274797065223B733A31373A226669656C645F73716C5F73746F72616765223B733A383A2273657474696E6773223B613A303A7B7D733A363A226D6F64756C65223B733A31373A226669656C645F73716C5F73746F72616765223B733A363A22616374697665223B733A313A2231223B733A373A2264657461696C73223B613A313A7B733A333A2273716C223B613A323A7B733A31383A224649454C445F4C4F41445F43555252454E54223B613A313A7B733A33333A226669656C645F646174615F6669656C645F6D6F646572617465645F737461747573223B613A313A7B733A353A2276616C7565223B733A32383A226669656C645F6D6F646572617465645F7374617475735F76616C7565223B7D7D733A31393A224649454C445F4C4F41445F5245564953494F4E223B613A313A7B733A33373A226669656C645F7265766973696F6E5F6669656C645F6D6F646572617465645F737461747573223B613A313A7B733A353A2276616C7565223B733A32383A226669656C645F6D6F646572617465645F7374617475735F76616C7565223B7D7D7D7D7D733A31323A22666F726569676E206B657973223B613A303A7B7D733A373A22696E6465786573223B613A313A7B733A353A2276616C7565223B613A313A7B693A303B733A353A2276616C7565223B7D7D733A323A226964223B733A323A223230223B7D';
  db_query("UPDATE {field_config} SET `data`={$field_config_moderation_status} WHERE  `field_name`='" . GojiraSettings::CONTENT_TYPE_MODERATED_STATUS_FIELD . "'");
  db_query("UPDATE {field_config_instance} SET `data`={$field_config_instance_moderation_status} WHERE  `field_name`='" . GojiraSettings::CONTENT_TYPE_MODERATED_STATUS_FIELD . "'");
  
  $field_config_group = '0x613A373A7B733A31323A227472616E736C617461626C65223B733A313A2230223B733A31323A22656E746974795F7479706573223B613A303A7B7D733A383A2273657474696E6773223B613A323A7B733A31393A227265666572656E636561626C655F7479706573223B613A353A7B733A31323A22676F6A6972615F67726F7570223B733A31323A22676F6A6972615F67726F7570223B733A373A2261727469636C65223B693A303B733A343A2270616765223B693A303B733A383A226C6F636174696F6E223B693A303B733A343A2274657874223B693A303B7D733A343A2276696577223B613A333A7B733A393A22766965775F6E616D65223B733A303A22223B733A31323A22646973706C61795F6E616D65223B733A303A22223B733A343A2261726773223B613A303A7B7D7D7D733A373A2273746F72616765223B613A353A7B733A343A2274797065223B733A31373A226669656C645F73716C5F73746F72616765223B733A383A2273657474696E6773223B613A303A7B7D733A363A226D6F64756C65223B733A31373A226669656C645F73716C5F73746F72616765223B733A363A22616374697665223B733A313A2231223B733A373A2264657461696C73223B613A313A7B733A333A2273716C223B613A323A7B733A31383A224649454C445F4C4F41445F43555252454E54223B613A313A7B733A32393A226669656C645F646174615F6669656C645F676F6A6972615F67726F7570223B613A313A7B733A333A226E6964223B733A32323A226669656C645F676F6A6972615F67726F75705F6E6964223B7D7D733A31393A224649454C445F4C4F41445F5245564953494F4E223B613A313A7B733A33333A226669656C645F7265766973696F6E5F6669656C645F676F6A6972615F67726F7570223B613A313A7B733A333A226E6964223B733A32323A226669656C645F676F6A6972615F67726F75705F6E6964223B7D7D7D7D7D733A31323A22666F726569676E206B657973223B613A313A7B733A333A226E6964223B613A323A7B733A353A227461626C65223B733A343A226E6F6465223B733A373A22636F6C756D6E73223B613A313A7B733A333A226E6964223B733A333A226E6964223B7D7D7D733A373A22696E6465786573223B613A313A7B733A333A226E6964223B613A313A7B693A303B733A333A226E6964223B7D7D733A323A226964223B733A323A223138223B7D';
  db_query("UPDATE {field_config} SET `data`={$field_config_group} WHERE  `field_name`='" . GojiraSettings::CONTENT_TYPE_GROUP_FIELD . "'");
  
  $field_config_instance_group_user = '0x613A373A7B733A353A226C6162656C223B733A31343A224C6F636174696F6E2067726F7570223B733A363A22776964676574223B613A353A7B733A363A22776569676874223B733A323A223131223B733A343A2274797065223B733A31343A226F7074696F6E735F73656C656374223B733A363A226D6F64756C65223B733A373A226F7074696F6E73223B733A363A22616374697665223B693A313B733A383A2273657474696E6773223B613A303A7B7D7D733A383A2273657474696E6773223B613A313A7B733A31383A22757365725F72656769737465725F666F726D223B693A313B7D733A373A22646973706C6179223B613A313A7B733A373A2264656661756C74223B613A353A7B733A353A226C6162656C223B733A353A2261626F7665223B733A343A2274797065223B733A32323A226E6F64655F7265666572656E63655F64656661756C74223B733A383A2273657474696E6773223B613A303A7B7D733A363A226D6F64756C65223B733A31343A226E6F64655F7265666572656E6365223B733A363A22776569676874223B693A333B7D7D733A383A227265717569726564223B693A313B733A31313A226465736372697074696F6E223B733A303A22223B733A31333A2264656661756C745F76616C7565223B4E3B7D';
  db_query("UPDATE {field_config_instance} SET `data`={$field_config_instance_group_user} WHERE  `field_name`='" . GojiraSettings::CONTENT_TYPE_GROUP_FIELD . "' AND bundle = 'user'");
  
  $field_config_instance_group_node = '0x613A373A7B733A353A226C6162656C223B733A31343A224C6F636174696F6E2067726F7570223B733A363A22776964676574223B613A353A7B733A363A22776569676874223B733A313A2234223B733A343A2274797065223B733A31343A226F7074696F6E735F73656C656374223B733A363A226D6F64756C65223B733A373A226F7074696F6E73223B733A363A22616374697665223B693A313B733A383A2273657474696E6773223B613A303A7B7D7D733A383A2273657474696E6773223B613A313A7B733A31383A22757365725F72656769737465725F666F726D223B623A303B7D733A373A22646973706C6179223B613A313A7B733A373A2264656661756C74223B613A353A7B733A353A226C6162656C223B733A353A2261626F7665223B733A343A2274797065223B733A32323A226E6F64655F7265666572656E63655F64656661756C74223B733A383A2273657474696E6773223B613A303A7B7D733A363A226D6F64756C65223B733A31343A226E6F64655F7265666572656E6365223B733A363A22776569676874223B693A373B7D7D733A383A227265717569726564223B693A303B733A31313A226465736372697074696F6E223B733A303A22223B733A31333A2264656661756C745F76616C7565223B4E3B7D';
  db_query("UPDATE {field_config_instance} SET `data`={$field_config_instance_group_node} WHERE  `field_name`='" . GojiraSettings::CONTENT_TYPE_GROUP_FIELD . "' AND bundle = '" . GojiraSettings::CONTENT_TYPE_LOCATION . "'");
  
  $field_config_instance_tags = '0x613A373A7B733A353A226C6162656C223B733A31303A2250726F70657274696573223B733A383A227265717569726564223B693A303B733A363A22776964676574223B613A353A7B733A363A22776569676874223B693A303B733A343A2274797065223B733A32313A227461786F6E6F6D795F6175746F636F6D706C657465223B733A363A226D6F64756C65223B733A383A227461786F6E6F6D79223B733A363A22616374697665223B693A303B733A383A2273657474696E6773223B613A323A7B733A343A2273697A65223B693A36303B733A31373A226175746F636F6D706C6574655F70617468223B733A32313A227461786F6E6F6D792F6175746F636F6D706C657465223B7D7D733A373A22646973706C6179223B613A323A7B733A373A2264656661756C74223B613A343A7B733A343A2274797065223B733A363A2268696464656E223B733A353A226C6162656C223B733A353A2261626F7665223B733A383A2273657474696E6773223B613A303A7B7D733A363A22776569676874223B693A343B7D733A363A22746561736572223B613A343A7B733A343A2274797065223B733A363A2268696464656E223B733A353A226C6162656C223B733A353A2261626F7665223B733A383A2273657474696E6773223B613A303A7B7D733A363A22776569676874223B693A313B7D7D733A383A2273657474696E6773223B613A323A7B733A31343A22616C6C6F7765645F76616C756573223B613A313A7B693A303B613A323A7B733A333A22766964223B693A313B733A363A22706172656E74223B693A303B7D7D733A31383A22757365725F72656769737465725F666F726D223B623A303B7D733A31313A226465736372697074696F6E223B733A303A22223B733A31333A2264656661756C745F76616C7565223B4E3B7D';
  $field_config_tags = '0x613A373A7B733A31323A22656E746974795F7479706573223B613A303A7B7D733A31323A227472616E736C617461626C65223B733A313A2230223B733A383A2273657474696E6773223B613A313A7B733A31343A22616C6C6F7765645F76616C756573223B613A313A7B693A303B613A323A7B733A31303A22766F636162756C617279223B733A31393A226C6F636174696F6E5F766F636162756C617279223B733A363A22706172656E74223B733A313A2230223B7D7D7D733A373A2273746F72616765223B613A353A7B733A343A2274797065223B733A31373A226669656C645F73716C5F73746F72616765223B733A383A2273657474696E6773223B613A303A7B7D733A363A226D6F64756C65223B733A31373A226669656C645F73716C5F73746F72616765223B733A363A22616374697665223B733A313A2231223B733A373A2264657461696C73223B613A313A7B733A333A2273716C223B613A323A7B733A31383A224649454C445F4C4F41445F43555252454E54223B613A313A7B733A33363A226669656C645F646174615F6669656C645F6C6F636174696F6E5F766F636162756C617279223B613A313A7B733A333A22746964223B733A32393A226669656C645F6C6F636174696F6E5F766F636162756C6172795F746964223B7D7D733A31393A224649454C445F4C4F41445F5245564953494F4E223B613A313A7B733A34303A226669656C645F7265766973696F6E5F6669656C645F6C6F636174696F6E5F766F636162756C617279223B613A313A7B733A333A22746964223B733A32393A226669656C645F6C6F636174696F6E5F766F636162756C6172795F746964223B7D7D7D7D7D733A31323A22666F726569676E206B657973223B613A313A7B733A333A22746964223B613A323A7B733A353A227461626C65223B733A31383A227461786F6E6F6D795F7465726D5F64617461223B733A373A22636F6C756D6E73223B613A313A7B733A333A22746964223B733A333A22746964223B7D7D7D733A373A22696E6465786573223B613A313A7B733A333A22746964223B613A313A7B693A303B733A333A22746964223B7D7D733A323A226964223B733A323A223137223B7D';
  db_query("UPDATE {field_config} SET `data`={$field_config_tags} WHERE  `field_name`='" . GojiraSettings::CONTENT_TYPE_LOCATION_VOCABULARY_FIELD . "'");
  db_query("UPDATE {field_config_instance} SET `data`={$field_config_instance_tags} WHERE  `field_name`='" . GojiraSettings::CONTENT_TYPE_LOCATION_VOCABULARY_FIELD . "'");
}

/*
 * Creates the needed nodes
 */

function create_nodes() {
  // create the node with the thanks text after user registration
  $node = new stdClass();
  $node->type = GojiraSettings::CONTENT_TYPE_TEXT;
  node_object_prepare($node);
  $node->title = "user registered text";
  $node->language = LANGUAGE_NONE;
  $node->uid = 1;
  $node->field_code[LANGUAGE_NONE][0]['value'] = 'THANKS_REGISTERED';
  $node->field_text[LANGUAGE_NONE][0]['value'] = 'Bedank tekst na het registreren.';
  if ($node = node_submit($node)) { // Prepare node for saving
    node_save($node);
  }

  // create the node with the thanks text after user registration
  $node = new stdClass();
  $node->type = GojiraSettings::CONTENT_TYPE_TEXT;
  node_object_prepare($node);
  $node->title = "edit employee help text";
  $node->language = LANGUAGE_NONE;
  $node->uid = 1;
  $node->field_code[LANGUAGE_NONE][0]['value'] = 'EDIT_EMPLOYEE_HELP_TEXT';
  $node->field_text[LANGUAGE_NONE][0]['value'] = 'Hier kunt U de gebruiker voor het systeem bewerken die vanuit uw locatie kan inloggen. Hij/Zij kan alleen de gegevens uit het systeem lezen die U kunt lezen en bewerken. Als U aangeeft dat de gebruiker ook gegevens mag bewerken krijgt de nieuwe gebruiker evenveel rechten als U.';
  if ($node = node_submit($node)) { // Prepare node for saving
    node_save($node);
  }

  // create the node with the thanks text after user registration
  $node = new stdClass();
  $node->type = GojiraSettings::CONTENT_TYPE_TEXT;
  node_object_prepare($node);
  $node->title = "edit account help text";
  $node->language = LANGUAGE_NONE;
  $node->uid = 1;
  $node->field_code[LANGUAGE_NONE][0]['value'] = 'EDIT_SELF_HELP_TEXT';
  $node->field_text[LANGUAGE_NONE][0]['value'] = 'Bewerk hier uw eigen gegevens.';
  if ($node = node_submit($node)) { // Prepare node for saving
    node_save($node);
  }

//  // create the node with the thanks text after user registration
//  $node = new stdClass();
//  $node->type = GojiraSettings::CONTENT_TYPE_TEXT;
//  node_object_prepare($node);
//  $node->title = "tekst op welkoms pagina";
//  $node->language = LANGUAGE_NONE;
//  $node->uid = 1;
//  $node->field_code[LANGUAGE_NONE][0]['value'] = 'WELCOME_TEXT';
//  $node->field_text[LANGUAGE_NONE][0]['value'] = 'Welkom op onze website! hier kunt u bla bla bla';
//  if ($node = node_submit($node)) { // Prepare node for saving
//    node_save($node);
//  }
//  
//  // create the node with the thanks text accepting terms & conditions
//  $node = new stdClass();
//  $node->type = GojiraSettings::CONTENT_TYPE_TEXT;
//  node_object_prepare($node);
//  $node->title = "Algemene voorwaarden";
//  $node->language = LANGUAGE_NONE;
//  $node->uid = 1;
//  $node->field_code[LANGUAGE_NONE][0]['value'] = 'TERMS_CONDITIONS';
//  $node->field_text[LANGUAGE_NONE][0]['value'] = 'Algemene voorwaarden';
//  if ($node = node_submit($node)) { // Prepare node for saving
//    node_save($node);
//  }
  
}

/**
 * Creates the roles gojira needs
 */
function create_roles() {
  $roles[] = helper::ROLE_EMPLOYEE;
  $roles[] = helper::ROLE_EMPLOYER;
  $roles[] = helper::ROLE_SUBSCRIBED;

  foreach ($roles as $role) {
    $std = new stdClass();
    $std->name = $role;
    user_role_save($std);
  }
}

/**
 * Set settings
 */
function implement_settings() {
  // put our own frontpage up
  variable_set('site_frontpage', 'welcome');
  // let only use the default register page
  variable_set('user_register', 0);
  // do not let the users set pictures of themself
  variable_set('user_pictures', 0);
  // let's not bother users with there timezone shizzle
  variable_set('configurable_timezones', 0);
}

/**
 * Add vocabulairy settings
 */
function add_vocabulary() {
  taxonomy_vocabulary_save((object) array(
              'name' => 'Location vocabulary',
              'machine_name' => GojiraSettings::VOCABULARY_LOCATION,
  ));

  $field = array('field_name' => GojiraSettings::CONTENT_TYPE_LOCATION_VOCABULARY_FIELD, 'type' => 'taxonomy_term_reference');
  field_create_field($field);
  $instance = array(
      'field_name' => GojiraSettings::CONTENT_TYPE_LOCATION_VOCABULARY_FIELD,
      'entity_type' => 'node',
      'label' => GojiraSettings::CONTENT_TYPE_LOCATION,
      'bundle' => GojiraSettings::CONTENT_TYPE_LOCATION,
      'required' => false,
      'widget' => array(
          'type' => 'taxonomy_autocomplete'
      ),
      'display' => array(
          'default' => array('type' => 'hidden'),
          'teaser' => array('type' => 'hidden')
      ),
      'settings' => array(
          'allowed_values' => array(
              array('vid' => 1, 'parent' => 0)
          )
      )
  );
  field_create_instance($instance);
  // can't seem to get the setting array correct, so let's do it this way #biteme
  $field_config_instance_vocabulary = '0x613A373A7B733A353A226C6162656C223B733A31303A2250726F70657274696573223B733A383A227265717569726564223B693A303B733A363A22776964676574223B613A353A7B733A363A22776569676874223B693A303B733A343A2274797065223B733A32313A227461786F6E6F6D795F6175746F636F6D706C657465223B733A363A226D6F64756C65223B733A383A227461786F6E6F6D79223B733A363A22616374697665223B693A313B733A383A2273657474696E6773223B613A323A7B733A343A2273697A65223B693A36303B733A31373A226175746F636F6D706C6574655F70617468223B733A32313A227461786F6E6F6D792F6175746F636F6D706C657465223B7D7D733A373A22646973706C6179223B613A323A7B733A373A2264656661756C74223B613A343A7B733A343A2274797065223B733A363A2268696464656E223B733A353A226C6162656C223B733A353A2261626F7665223B733A383A2273657474696E6773223B613A303A7B7D733A363A22776569676874223B693A343B7D733A363A22746561736572223B613A343A7B733A343A2274797065223B733A363A2268696464656E223B733A353A226C6162656C223B733A353A2261626F7665223B733A383A2273657474696E6773223B613A303A7B7D733A363A22776569676874223B693A313B7D7D733A383A2273657474696E6773223B613A323A7B733A31343A22616C6C6F7765645F76616C756573223B613A313A7B693A303B613A323A7B733A333A22766964223B693A313B733A363A22706172656E74223B693A303B7D7D733A31383A22757365725F72656769737465725F666F726D223B623A303B7D733A31313A226465736372697074696F6E223B733A303A22223B733A31333A2264656661756C745F76616C7565223B4E3B7D';
  $field_config_vocabulary = '0x613A373A7B733A31323A22656E746974795F7479706573223B613A303A7B7D733A31323A227472616E736C617461626C65223B733A313A2230223B733A383A2273657474696E6773223B613A313A7B733A31343A22616C6C6F7765645F76616C756573223B613A313A7B693A303B613A323A7B733A31303A22766F636162756C617279223B733A31393A226C6F636174696F6E5F766F636162756C617279223B733A363A22706172656E74223B733A313A2230223B7D7D7D733A373A2273746F72616765223B613A353A7B733A343A2274797065223B733A31373A226669656C645F73716C5F73746F72616765223B733A383A2273657474696E6773223B613A303A7B7D733A363A226D6F64756C65223B733A31373A226669656C645F73716C5F73746F72616765223B733A363A22616374697665223B733A313A2231223B733A373A2264657461696C73223B613A313A7B733A333A2273716C223B613A323A7B733A31383A224649454C445F4C4F41445F43555252454E54223B613A313A7B733A33363A226669656C645F646174615F6669656C645F6C6F636174696F6E5F766F636162756C617279223B613A313A7B733A333A22746964223B733A32393A226669656C645F6C6F636174696F6E5F766F636162756C6172795F746964223B7D7D733A31393A224649454C445F4C4F41445F5245564953494F4E223B613A313A7B733A34303A226669656C645F7265766973696F6E5F6669656C645F6C6F636174696F6E5F766F636162756C617279223B613A313A7B733A333A22746964223B733A32393A226669656C645F6C6F636174696F6E5F766F636162756C6172795F746964223B7D7D7D7D7D733A31323A22666F726569676E206B657973223B613A313A7B733A333A22746964223B613A323A7B733A353A227461626C65223B733A31383A227461786F6E6F6D795F7465726D5F64617461223B733A373A22636F6C756D6E73223B613A313A7B733A333A22746964223B733A333A22746964223B7D7D7D733A373A22696E6465786573223B613A313A7B733A333A22746964223B613A313A7B693A303B733A333A22746964223B7D7D733A323A226964223B733A323A223137223B7D';
  db_query("UPDATE {field_config} SET `data`={$field_config_vocabulary} WHERE  `field_name`='" . GojiraSettings::CONTENT_TYPE_LOCATION_VOCABULARY_FIELD . "'");
  db_query("UPDATE {field_config_instance} SET `data`={$field_config_instance_vocabulary} WHERE  `field_name`='" . GojiraSettings::CONTENT_TYPE_LOCATION_VOCABULARY_FIELD . "'");
}