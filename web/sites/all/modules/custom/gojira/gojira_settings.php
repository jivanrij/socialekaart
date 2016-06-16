<?php
class GojiraSettings
{
    // http://www.easy-ideal.com/api-implementeren/

    // watchdog options types
    const WATCHDOG_IDEAL = 'iDeal issue';
    const WATCHDOG_SUBSCRIPTIONS = 'Sunbscription issue';
    const WATCHDOG_LOCATION = 'Location related';
    const WATCHDOG_LOGGING = 'loggin information';
    const WATCHDOG_PAGELOAD = 'Pageloads';

    // CONTENT TYPES
    const CONTENT_TYPE_LOCATION = 'location'; // a medical location of the location of the users
    const CONTENT_TYPE_TEXT = 'text'; // node holding text usable by the system
    const CONTENT_TYPE_GROUP = 'gojira_group'; // group node, relating users & locations with eachother
    const CONTENT_TYPE_ADD = 'add'; // represents an add in the system
    const CONTENT_TYPE_CATEGORY = 'category'; // category of a location
    const CONTENT_TYPE_PAGE_PUBLIC = 'page_public'; // a content page publically visible
    const CONTENT_TYPE_PAGE = 'page'; // a content page
    const CONTENT_TYPE_PAGE_BIG = 'page_big'; // a content page with a wide text area
    const CONTENT_TYPE_FAQ = 'faq'; // a content page with a wide text area
    const CONTENT_TYPE_POSTCODEAREA = 'postcodearea'; // postcode area to link locationsets with
    const CONTENT_TYPE_SET_OF_LOCATIONS = 'locationsset'; // set of locations

    // LOCATION FIELDS
    const CONTENT_TYPE_TEXT_FIELD = 'field_text';
    const CONTENT_TYPE_ADDRESS_CITY_FIELD = 'field_address_city';
    const CONTENT_TYPE_EMAIL_FIELD = 'field_email';
    const CONTENT_TYPE_ADDRESS_STREET_FIELD = 'field_address_street';
    const CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD = 'field_address_streetnumber';
    const CONTENT_TYPE_ADDRESS_POSTCODE_FIELD = 'field_address_postcode';
    const CONTENT_TYPE_TELEPHONE_FIELD = 'field_telephone';
    const CONTENT_TYPE_FAX_FIELD = 'field_fax';
    const CONTENT_TYPE_NOTE_FIELD = 'field_note';
    const CONTENT_TYPE_SHOW_LOCATION_FIELD = 'field_visible_to_other_user'; // other users can't see this locations
    const CONTENT_TYPE_LOCATION_VOCABULARY_FIELD = 'field_location_labels';
    const CONTENT_TYPE_MODERATED_STATUS_FIELD = 'field_moderated_status';
    const CONTENT_TYPE_URL_FIELD = 'field_url';
    const CONTENT_TYPE_CATEGORY_FIELD = 'field_category';

    // CONTENT TYPE TEXT FIELDS
    const CONTENT_TYPE_CODE_FIELD = 'field_code';

    // CONTENT TYPE PUBLIC PAGE FIELDS
    const CONTENT_TYPE_META_TAGS_FIELD = 'field_meta_tags';
    const CONTENT_TYPE_META_DESCRIPTION_FIELD = 'field_meta_description';

    // ADD FIELDS
    const CONTENT_TYPE_ADD_IMAGE_FIELD = 'field_add_image'; // holds the add image
    const CONTENT_TYPE_ADD_SHOWFROM_FIELD = 'field_showfrom'; // timestamp
    const CONTENT_TYPE_ADD_SHOWUNTILL_FIELD = 'field_showuntill'; // timestamp
    const CONTENT_TYPE_ADD_URL_FIELD = 'field_url'; // url to link to

    // zorgverlenersset FIELDS
    const CONTENT_TYPE_LOCATIONSET_POSTCODES = 'field_postcodeareas';
    const CONTENT_TYPE_LOCATIONSET_LOCATIONS = 'field_setlocations';
    const CONTENT_TYPE_LOCATIONSET_SUBTITLE = 'field_subtitel';

    // postcodearea FIELDS
    const CONTENT_TYPE_POSTCODE_NUMBER = 'field_postcodenumber';

    // vocabulairy related fields
    const VOCABULARY_LOCATION = 'gojira_labels';

    // USER FIELDS
    const CONTENT_TYPE_USER_TITLE = 'field_user_title'; // the user is a doctor
    const CONTENT_TYPE_IS_DOCTOR_FIELD = 'field_is_doctor'; // the user is a doctor
    const CONTENT_TYPE_CONDITIONS_AGREE_FIELD = 'field_agree_conditions'; // the user has agreed to the conditions
    const CONTENT_TYPE_SEARCH_GLOBAL_FIELD = 'field_search_global'; // user want's to search on a global level
    const CONTENT_TYPE_HAS_MULTIPLE_LOCATIONS_FIELD = 'field_has_multiple_locations'; // user want's to handle multiple locations on his account
    const CONTENT_TYPE_BIG_FIELD = 'field_big'; // users big number
    const CONTENT_TYPE_TUTORIAL_FIELD = 'field_seen_tutorial'; // the user has seen the tutorial
    const CONTENT_TYPE_USER_NOT_IMPORTED = 'field_user_not_imported';
    const CONTENT_TYPE_USER_LAST_SELECTED_LOCATION = 'field_selected_location'; // the last selected location of the user

    // GROUP FIELDS
    const CONTENT_TYPE_ORIGINAL_DOCTOR = 'field_original_doctor'; // the original doctor who used this group, when the payed period is over, this is the only person who can have access
    const CONTENT_TYPE_PAYED_STATUS = 'field_payed_status'; // boolean - does the group has a payed status on the moment?

    // USER AND LOCATION FIELDS
    const CONTENT_TYPE_GROUP_FIELD = 'field_gojira_group';

    // IMAGE STYLES
    const IMAGE_STYLE_ADD_SMALL = 'add_small';
    const IMAGE_STYLE_ADD_WIDE = 'add_wide';

    const MAP_ZOOMLEVEL_STREET = 14;
    const MAP_ZOOMLEVEL_REGION = 12;
    const MAP_ZOOMLEVEL_COUNTRY = 8;

    const SYSTEM_WWW_ROOT_FOLDER = 'wwwroot';

    const IDEAL_FREE_PERIOD_DESCRIPTION = 'Free intro Period';
}

//CREATE TABLE `address_cache` (
//`id` int(10) NOT NULL AUTO_INCREMENT,
//  `address` varchar(100) NOT NULL,
//  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
//  `street` varchar(200) DEFAULT NULL,
//  `houseNumber` varchar(10) DEFAULT NULL,
//  `houseNumberAddition` varchar(10) DEFAULT NULL,
//  `postcode` varchar(7) DEFAULT NULL,
//  `city` varchar(200) DEFAULT NULL,
//  `municipality` varchar(100) DEFAULT NULL,
//  `addressType` varchar(100) DEFAULT NULL,
//  `coordinates_x` varchar(32) NOT NULL,
//  `coordinates_y` varchar(32) NOT NULL,
//  PRIMARY KEY (`id`)
//) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
//
//CREATE TABLE `gojira_payments` (
//`id` int(11) NOT NULL AUTO_INCREMENT,
//  `uid` int(11) NOT NULL DEFAULT '0',
//  `name` varchar(50) NOT NULL DEFAULT '0',
//  `description` varchar(50) NOT NULL,
//  `amount` float NOT NULL DEFAULT '0',
//  `gid` int(11) NOT NULL,
//  `ideal_id` varchar(50) NOT NULL,
//  `ideal_code` varchar(50) NOT NULL,
//  `status` int(11) DEFAULT NULL COMMENT '0=open, 1=completed, 2=failed',
//  `period_start` int(11) NOT NULL,
//  `period_end` int(11) NOT NULL,
//  `warning_send` int(11) NOT NULL DEFAULT '0' COMMENT 'The system has send a warning the the subscription is going to end',
//  `callback_times` int(11) NOT NULL DEFAULT '0' COMMENT 'Amount of times the callback was used for this payment. Max is 6 times after 5, 10, 30, 60, 120 and 300 minutes.',
//  `warning_ended` int(11) NOT NULL DEFAULT '0' COMMENT 'The system has send a warning the subscription is ended',
//  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
//  `increment` int(11) DEFAULT NULL,
//  `discount` float NOT NULL DEFAULT '0',
//  `tax` float NOT NULL DEFAULT '0',
//  `payed` float NOT NULL DEFAULT '0',
//  `bank` varchar(50) NOT NULL DEFAULT '' COMMENT 'Bank of the user',
//  PRIMARY KEY (`id`)
//) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 COMMENT='stores all the payments done';
//
//CREATE TABLE `group_location_favorite` (
//`id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id of the row',
//  `gid` int(11) unsigned NOT NULL COMMENT 'id of the group this note belongs to',
//  `nid` int(11) unsigned NOT NULL COMMENT 'id of the location node this note belongs to',
//  `pid` int(11) unsigned NOT NULL COMMENT 'id of the location/practice the user makes the favorite from',
//  PRIMARY KEY (`id`),
//  KEY `id` (`id`),
//  KEY `FK_group_location_note_node` (`gid`),
//  KEY `FK_group_location_note_node_2` (`nid`),
//  CONSTRAINT `FK_group_location_note_node` FOREIGN KEY (`gid`) REFERENCES `node` (`nid`) ON DELETE CASCADE ON UPDATE CASCADE,
//  CONSTRAINT `FK_group_location_note_node_2` FOREIGN KEY (`nid`) REFERENCES `node` (`nid`) ON DELETE CASCADE ON UPDATE CASCADE
//) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8;
//
//CREATE TABLE `group_location_note` (
//`nid` int(10) unsigned NOT NULL COMMENT 'the location',
//  `gid` int(10) unsigned NOT NULL COMMENT 'the group',
//  `note` text COMMENT 'the note',
//  UNIQUE KEY `nid_gid` (`nid`,`gid`)
//) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='A note on a location by a group member';
//
//CREATE TABLE `group_location_term` (
//`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id of the row',
//  `gid` int(10) unsigned DEFAULT NULL COMMENT 'link to the group',
//  `nid` int(10) unsigned DEFAULT NULL COMMENT 'link to the location',
//  `tid` int(10) unsigned DEFAULT NULL COMMENT 'link to term',
//  PRIMARY KEY (`id`),
//  UNIQUE KEY `gid_nid_tid` (`gid`,`nid`,`tid`),
//  KEY `id` (`id`),
//  KEY `FK_group_location_term_node_2` (`nid`),
//  KEY `FK_group_location_term_taxonomy_term_data` (`tid`),
//  CONSTRAINT `FK_group_location_term_node` FOREIGN KEY (`gid`) REFERENCES `node` (`nid`) ON DELETE CASCADE ON UPDATE CASCADE,
//  CONSTRAINT `FK_group_location_term_node_2` FOREIGN KEY (`nid`) REFERENCES `node` (`nid`) ON DELETE CASCADE ON UPDATE CASCADE,
//  CONSTRAINT `FK_group_location_term_taxonomy_term_data` FOREIGN KEY (`tid`) REFERENCES `taxonomy_term_data` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE
//) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8 COMMENT='Link between a group, location & tag';
//
//
//CREATE TABLE `practices_backup` (
//`id` int(11) NOT NULL AUTO_INCREMENT,
//  `title` varchar(256) DEFAULT NULL,
//  `email` varchar(256) DEFAULT NULL,
//  `city` varchar(256) DEFAULT NULL,
//  `street` varchar(256) DEFAULT NULL,
//  `number` varchar(256) DEFAULT NULL,
//  `postcode` varchar(256) DEFAULT NULL,
//  `telephone` varchar(256) DEFAULT NULL,
//  `fax` varchar(256) DEFAULT NULL,
//  `url` varchar(256) DEFAULT NULL,
//  `labels` varchar(512) DEFAULT NULL,
//  `category` varchar(256) DEFAULT NULL,
//  `note` varchar(256) DEFAULT NULL,
//  `latitude` varchar(256) DEFAULT NULL,
//  `longitude` varchar(256) DEFAULT NULL,
//  `group_id` varchar(256) DEFAULT NULL,
//  `visible` varchar(256) DEFAULT NULL,
//  `nid` varchar(256) DEFAULT NULL,
//  `source` varchar(256) DEFAULT NULL,
//  `import_it` int(1) DEFAULT NULL COMMENT 'Flag for importer. Imports all locations with import_it = 1',
//  UNIQUE KEY `id` (`id`),
//  KEY `id_b` (`id`)
//) ENGINE=MyISAM AUTO_INCREMENT=2447 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='This table backs up all the practices into one table.';
//
//CREATE TABLE `searchword` (
//`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
//  `word` varchar(128) NOT NULL,
//  UNIQUE KEY `word` (`word`),
//  KEY `id` (`id`)
//) ENGINE=InnoDB AUTO_INCREMENT=739 DEFAULT CHARSET=latin1 COMMENT='The search index holding all the words.';
//
//CREATE TABLE `searchword_nid` (
//`node_nid` int(10) unsigned NOT NULL,
//  `searchword_id` int(10) unsigned NOT NULL,
//  `score` double unsigned NOT NULL,
//  PRIMARY KEY (`node_nid`,`searchword_id`),
//  KEY `searchword link` (`searchword_id`),
//  CONSTRAINT `location link` FOREIGN KEY (`node_nid`) REFERENCES `node` (`nid`) ON DELETE CASCADE ON UPDATE CASCADE,
//  CONSTRAINT `searchword link` FOREIGN KEY (`searchword_id`) REFERENCES `searchword` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
//) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Connection betweet a word in the index & the location';
// CREATE TABLE `remove_locations` (
//   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
//   `nid` int(11) unsigned NOT NULL COMMENT 'This id is from a node that needs to be removed.',
//   PRIMARY KEY (`id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

//April 2016 -- add search to node
// ALTER TABLE `node` ADD `search` TEXT NULL COMMENT 'Holds the labels related to this location for full text search' ;
// ZET HIER EEN FULL TEXT INDEX OP

//APRIL 2016
// CREATE TABLE `remove_locations` (
//   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
//   `nid` int(11) unsigned NOT NULL COMMENT 'This id is from a node that needs to be removed.',
//   PRIMARY KEY (`id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

// MAART 2016 - implementatie van mollie
// ALTER TABLE `gojira_payments` DROP `ideal_code`;
// ALTER TABLE `gojira_payments` CHANGE `status` `status` VARCHAR(11)  NULL  DEFAULT NULL;
// ALTER TABLE `gojira_payments` CHANGE `bank` `method` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Method of payment';

// JUNI 2016 - ADDING REPORTING
// CREATE TABLE `gojira_reporter` (
//   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
//   `user` int(11) DEFAULT NULL,
//   `datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
//   `url` varchar(255) DEFAULT NULL,
//   `ip` varchar(18) DEFAULT NULL,
//   `agent` varchar(512) DEFAULT NULL,
//   `note` varchar(255) DEFAULT NULL,
//   `mobile` int(1) DEFAULT NULL,
//   `params` text,
//   PRIMARY KEY (`id`)
// ) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4;
