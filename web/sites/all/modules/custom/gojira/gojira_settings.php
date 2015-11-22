<?php
class GojiraSettings
{
    // http://www.easy-ideal.com/api-implementeren/
    
    // watchdog options types
    const WATCHDOG_IDEAL = 'iDeal issue';
    const WATCHDOG_HAWEB_SSO = 'HAweb SSO issue';
    const WATCHDOG_SUBSCRIPTIONS = 'Sunbscription issue';
    const WATCHDOG_LOCATION = 'Location related';
    
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
    const CONTENT_TYPE_SEARCH_FAVORITES_FIELD = 'field_search_favorites'; // user want's to search in the favorites
    const CONTENT_TYPE_SEARCH_GLOBAL_FIELD = 'field_search_global'; // user want's to search on a global level
    const CONTENT_TYPE_USER_VALIDATED_FIELD = 'field_user_validated'; // the user is validated
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