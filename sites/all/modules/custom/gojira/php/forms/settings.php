<?php

function gojira_settings_form($form, &$form_state) {

    global $user;
    $user = user_load($user->uid);

    $form[GojiraSettings::CONTENT_TYPE_USER_TITLE] = array(
        '#title' => t('Name/Title'),
        '#type' => 'textfield',
        '#required' => true,
        '#default_value' => helper::value($user, GojiraSettings::CONTENT_TYPE_USER_TITLE),
    );

    $description = t('Your e-mailadres is also your username.');

    $form['email'] = array(
        '#title' => t('E-mailadres'),
        '#type' => 'textfield',
        '#required' => true,
        '#description' => $description,
        '#default_value' => ($user ? $user->mail : '')
    );

    if (user_access(helper::PERM_BASIC_ACCESS)) {

        $form['line'] = array(
            '#markup' => '<hr /><h2>'.t('Your practice').'</h2>',
        );

        // the user has only one location to manage or no master/subscribed privileges
        if (!has_multiple_locations($user) || !user_access(helper::PERM_HUISARTS_MORE_PRACTICES)) {
            $locations = Location::getUsersLocations(false);
            $locationNode = array_shift($locations);
            $form = gojira_get_core_location_form($form, $form_state, $locationNode, 'settings');
            $form[GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD]['#required'] = true;
            $form[GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD]['#required'] = true;
            $form[GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD]['#required'] = true;
            $form[GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD]['#required'] = true;
        }

        // user is allowed to manage multiple locations yes/no
        if (user_access(helper::PERM_HUISARTS_MORE_PRACTICES)) {

            $disabled = ((count(Location::getUsersLocations(false)) > 1) ? true : false);

            $multiple = GojiraSettings::CONTENT_TYPE_HAS_MULTIPLE_LOCATIONS_FIELD;
            $multiple = $user->$multiple;
            if (!isset($multiple[LANGUAGE_NONE])) {
                $multiple[LANGUAGE_NONE][0]['value'] = 0;
            }
            $multiplevalue = $multiple[LANGUAGE_NONE][0]['value'];
            if ($disabled) {
                $sDescriptionMultiField = t('Now you have subscription, you can add multiple locations. You can only turn this option off when you have one practice.');
            } else {
                $sDescriptionMultiField = t('Now you have subscription, you can add multiple locations.');
            }
            $form[GojiraSettings::CONTENT_TYPE_HAS_MULTIPLE_LOCATIONS_FIELD] = array(
                '#title' => t('Multiple locations'),
                '#type' => 'checkbox',
                '#disabled' => $disabled,
                '#description' => $sDescriptionMultiField,
                '#default_value' => $multiplevalue
            );
        }
    }

    $form['submit'] = array(
        '#type' => 'submit',
        '#prefix' => '<div class="gbutton_wrapper"><a class="gbutton rounded noshadow left" onclick="window.history.back();" title="' . t('Back') . '"><span>' . t('Back') . '</span></a><span class="gbutton rounded noshadow right">',
        '#value' => t('Submit'),
        '#suffix' => '</span></div>'
    );

    return $form;
}

function gojira_settings_form_validate($form, &$form_state) {
    global $user;
    $user = user_load($user->uid);

    $email = $form['email']['#value'];
    $uid = $user->uid;

    if ($error = user_validate_mail($email)) {
        form_set_error('email', $error);
    } else {
        if ($error = user_validate_name($email)) {
            form_set_error('email', $error);
        } else {
            if (db_query("SELECT * FROM {users} WHERE uid != :uid", array(':uid' => $uid))->fetchField()) {
                form_set_error('email', t('The given e-mail address is already in use by a user or not correctly formed.'));
            }
        }
    }
    // to save location info, you need to be a master doctor, have the permission & not have multiple locations
    if (user_access(helper::PERM_BASIC_ACCESS) && in_array(helper::ROLE_HUISARTS, $user->roles)) {
        if (!has_multiple_locations($user)) { // if we do not have multiple locations, we need to validate the form values for one location
            $locations = Location::getUsersLocations(false);

            if (count($locations) == 1) {
                $locationNode = array_pop($locations);
                gojira_get_core_location_form_validate($form, $form_state, $locationNode->nid);
            } else if (count($locations) == 0) {
                gojira_get_core_location_form_validate($form, $form_state, 'new');
            }
        }
    }
}

function gojira_settings_form_submit($form, &$form_state) {

    global $user;
    $user = user_load($user->uid);

    $redirect = 'settings';

    $hasMultipleLocations = has_multiple_locations($user);

    $user->mail = $form['email']['#value'];
    $user->name = $form['email']['#value'];

    if ($user->uid == 1) {
        $user->name = 'admin';
    }

    $fieldsToSave = array(
        GojiraSettings::CONTENT_TYPE_HAS_MULTIPLE_LOCATIONS_FIELD,
        GojiraSettings::CONTENT_TYPE_USER_TITLE
    );

    foreach ($fieldsToSave as $fieldName) {
        $user->$fieldName = array(LANGUAGE_NONE => array(0 => array('value' => $form[$fieldName]['#value'])));
    }

    user_save($user);

// to save location info, you need to be a master doctor, have the permission & not have multiple locations
    if (user_access(helper::PERM_BASIC_ACCESS)) {
        if (!$hasMultipleLocations) {
            // if we do not have multiple locations, we need to save the form values for one location
            // if we have multiple locations, we don't have a location form here

            $locations = Location::getUsersLocations(false);
            if (count($locations) == 1) {
                $locationNode = array_pop($locations);
                $id = $locationNode->nid;
            } else if (count($locations) == 0) {
                $id = 'new';
            }

            if ($id == 'new') {
                // no existing location found, let's creat one
                $node = new stdClass();
                $node->type = GojiraSettings::CONTENT_TYPE_LOCATION;
                node_object_prepare($node);
                $node->language = LANGUAGE_NONE;
                $node->status = 1;
                $node->promote = 0;
                $node->comment = 0;

                global $user;
                $user = user_load($user->uid);

                // get the group the user is linked to and link the new location to it
                $groupField = GojiraSettings::CONTENT_TYPE_GROUP_FIELD;
                $groupFieldUser = $user->$groupField;

                if(count($groupFieldUser) == 0) {
                    $group = Group::createNewGroup($user);
                    $groupField = GojiraSettings::CONTENT_TYPE_GROUP_FIELD;

                    $user->$groupField = array(LANGUAGE_NONE => array(0 => array('nid' => $group->nid)));
                    user_save($user);

                    $node->$groupField = array(LANGUAGE_NONE => array(0 => array('nid' => $group->nid)));
                } else {
                    $node->$groupField = array(LANGUAGE_NONE => array(0 => array('nid' => $groupFieldUser[LANGUAGE_NONE][0]['nid'])));
                }

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
            $node->uid = $user->uid;
            $node->title = $form['title']['#value'];

            $emailfield = GojiraSettings::CONTENT_TYPE_EMAIL_FIELD;
            $node->$emailfield = array(LANGUAGE_NONE => array(0 => array('value' => $form['email']['#value'])));

            $telephonefield = GojiraSettings::CONTENT_TYPE_TELEPHONE_FIELD;
            $node->$telephonefield = array(LANGUAGE_NONE => array(0 => array('value' => $form[GojiraSettings::CONTENT_TYPE_TELEPHONE_FIELD]['#value'])));

            $faxfield = GojiraSettings::CONTENT_TYPE_FAX_FIELD;
            $node->$faxfield = array(LANGUAGE_NONE => array(0 => array('value' => $form[GojiraSettings::CONTENT_TYPE_FAX_FIELD]['#value'])));

            foreach (Location::getAddressFields() as $field) {
                $node->$field = array(LANGUAGE_NONE => array(0 => array('value' => $form[$field]['#value'])));
            }

            node_save($node);
        }
    }

    // saving the coordinates is done in gojira_entity_update
    drupal_set_message(t('Your settings and location information is succesfully changed.'), 'status');

    if(variable_get('gojira_check_coordinates_on_update_node', 1) == 0 && user_access('administer')){
        drupal_set_message('gojira_check_coordinates_on_update_node is turned off', 'status');
    }

    drupal_goto($redirect);
}

/**
 * Tells you if the user uses the multiple locations option
 *
 * @param stdClass $user
 * @return boolean
 */
function has_multiple_locations($user) {
    if (user_access(helper::PERM_HUISARTS_MORE_PRACTICES)) {
        if (helper::value($user, GojiraSettings::CONTENT_TYPE_HAS_MULTIPLE_LOCATIONS_FIELD) || (isset($_POST[GojiraSettings::CONTENT_TYPE_HAS_MULTIPLE_LOCATIONS_FIELD]) && $_POST[GojiraSettings::CONTENT_TYPE_HAS_MULTIPLE_LOCATIONS_FIELD])) {
            return true;
        }
    }
    return false;
}
