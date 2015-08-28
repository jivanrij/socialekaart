<?php

/**
 * This form is the crud for the location nodes
 */
function gojira_locationedit_form($form, &$form_state) {
    $node = false;
    $id = 'new';
    if (isset($_GET['id'])) {
        $node = node_load($_GET['id']);
        global $user;
        if (!helper::canChangeLocation($user->uid, $node->nid)) {
            form_set_error('not_allowed', t('You are not allowed to do this.'));
            drupal_goto('settings');
        }
    }

    if ($node) {
        $id = $_GET['id'];
    }
    $form['id'] = array(
        '#title' => t('id'),
        '#type' => 'hidden',
        '#required' => TRUE,
        '#default_value' => ($node ? $id : 'new'),
    );

    $form = gojira_get_core_location_form($form, $form_state, $node, 'locationedit');

    $form['submit'] = array(
        '#type' => 'submit',
        '#prefix' => '<div class="gbutton_wrapper"><a class="gbutton rounded noshadow left" onclick="window.history.back();" title="' . t('Back') . '"><span>' . t('Back') . '</span></a><span class="gbutton rounded noshadow right">',
        '#value' => t('Submit'),
        '#suffix' => '</span></div>'
    );

    return $form;
}

/**
 * This part of the form is put in a function to be used in gojira_locationedit_form & gojira_settings_form
 * 
 * @param array $form
 * @param array $form_state
 * @param stdClass $node
 * @param boolean $skipEmail
 * @return array
 */
function gojira_get_core_location_form($form, &$form_state, $node, $caller) {

    if ($caller != 'locationedit' && $caller != 'settings') {
        return $form;
    }
    $title_helper = '';
    $form['title'] = array(
        '#title' => t('Title') . $title_helper,
        '#type' => 'textfield',
        '#default_value' => ($node ? $node->title : ''),
        '#required' => true,
    );

    if ($caller == 'locationedit') {
        if ($node) {
            $emailfield = GojiraSettings::CONTENT_TYPE_EMAIL_FIELD;
            $emailfield = $node->$emailfield;
            $emailaddress = $emailfield['und'][0]['value'];
        }
        $form['email'] = array(
            '#title' => t('E-mailaddress'),
            '#type' => 'textfield',
            '#default_value' => ($node ? $emailaddress : ''),
            '#required' => false,
        );
    }

    helper::addAddressFormPart($form, $node);

    $form[GojiraSettings::CONTENT_TYPE_TELEPHONE_FIELD] = array(
        '#title' => t('Telephone'),
        '#type' => 'textfield',
        '#default_value' => ($node ? helper::value($node, GojiraSettings::CONTENT_TYPE_TELEPHONE_FIELD) : ''),
        '#required' => true,
    );

    $form[GojiraSettings::CONTENT_TYPE_FAX_FIELD] = array(
        '#title' => t('Faxnumber'),
        '#type' => 'textfield',
        '#default_value' => ($node ? helper::value($node, GojiraSettings::CONTENT_TYPE_FAX_FIELD) : ''),
        '#required' => false,
    );

    return $form;
}

function gojira_locationedit_form_validate($form, &$form_state) {
    $id = 'new';
    if (isset($_POST['id']) && $_POST['id'] != 'new') {
        $id = $_POST['id'];
    }

    gojira_get_core_location_form_validate($form, $form_state, $id);
}

/**
 * This part of the form is put in a function to be used in gojira_locationedit_form & gojira_settings_form
 * 
 * @param array $form
 * @param array $form_state
 * @param integer $id
 */
function gojira_get_core_location_form_validate($form, &$form_state, $id) {
    if ($id == 'new') {
        $knownTitle = db_query("select title from {node} where title = :t1", array(':t1' => $form['title']['#value']))->fetchField();
    } else {
        $knownTitle = db_query("select title from {node} where title = :t1 and nid != :nid", array(':t1' => $form['title']['#value'], ':nid' => $id))->fetchField();
    }
    if ($knownTitle) {
        form_set_error('title', t('There is already a location with this title known in the system. Please pick another.'));
    }

    $location = Location::getLocationForAddress(
                    Location::formatAddress(
                            $form[GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD]['#value'], $form[GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD]['#value'], $form[GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD]['#value'], $form[GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD]['#value']
                    )
    );

    if (!$location) {
        form_set_error('no_location_found', t('Cannot find a location based on the given information. Please check if you have filled in the whole form with correct information and there are no missing fields.'));
    }
}

function gojira_locationedit_form_submit($form, &$form_state) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    } else {
        $id = 'new';
    }

    global $user;
    $oUser = user_load($user->uid);

    if ($id == 'new') {
        $node = new stdClass();
        $node->type = GojiraSettings::CONTENT_TYPE_LOCATION;
        node_object_prepare($node);
        $node->language = LANGUAGE_NONE;
        $node->status = 1;
        $node->promote = 0;
        $node->comment = 0;

        // get the group the user is linked to and link the new location to it
        $groupField = GojiraSettings::CONTENT_TYPE_GROUP_FIELD;
        $groupFieldUser = $oUser->$groupField;
        $node->$groupField = array(LANGUAGE_NONE => array(0 => array('nid' => $groupFieldUser[LANGUAGE_NONE][0]['nid'])));

        $node = node_submit($node); // Prepare node for saving

        node_save($node);

        $visiblefield = GojiraSettings::CONTENT_TYPE_SHOW_LOCATION_FIELD;
        $node->$visiblefield = array(LANGUAGE_NONE => array(0 => array('value' => 0)));

        $hasModerator = GojiraSettings::CONTENT_TYPE_MODERATED_STATUS_FIELD;
        $node->$hasModerator = array(LANGUAGE_NONE => array(0 => array('value' => 1)));

        $category_nid = Category::getCategoryNID('Huisarts');
        $catfield = GojiraSettings::CONTENT_TYPE_CATEGORY_FIELD;
        $node->$catfield = array('und' => array(0 => array('nid' => $category_nid)));
    } else {
        $node = node_load($id);
        // remove all tags for the node, new will be saved
    }

    $node->type = GojiraSettings::CONTENT_TYPE_LOCATION;
    $node->uid = $oUser->uid;
    $node->title = $form['title']['#value'];

    $emailfield = GojiraSettings::CONTENT_TYPE_EMAIL_FIELD;
    $node->$emailfield = array(LANGUAGE_NONE => array(0 => array('value' => $form['email']['#value'])));

    $telephonefield = GojiraSettings::CONTENT_TYPE_TELEPHONE_FIELD;
    $node->$telephonefield = array(LANGUAGE_NONE => array(0 => array('value' => $form[GojiraSettings::CONTENT_TYPE_TELEPHONE_FIELD]['#value'])));

    $faxfield = GojiraSettings::CONTENT_TYPE_FAX_FIELD;
    $node->$faxfield = array(LANGUAGE_NONE => array(0 => array('value' => $form[GojiraSettings::CONTENT_TYPE_FAX_FIELD]['#value'])));

//  $employeesfield = GojiraSettings::CONTENT_TYPE_NOTE_FIELD;
//  $node->$employeesfield = array(LANGUAGE_NONE => array(0 => array('value' => $form[GojiraSettings::CONTENT_TYPE_NOTE_FIELD]['#value'])));

    foreach (Location::getAddressFields() as $field) {
        $node->$field = array(LANGUAGE_NONE => array(0 => array('value' => $form[$field]['#value'])));
    }
    
    node_save($node);
    
    if ($id == 'new') {
        // save the new location to the default one to load
        $oUser->field_selected_location = array(LANGUAGE_NONE => array(0 => array('nid' => $node->nid)));
        user_save($oUser);
    }
    
    drupal_set_message(t('Location information successfully stored.'), 'status');

    drupal_goto('settings');
}
