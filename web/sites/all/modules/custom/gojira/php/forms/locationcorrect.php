<?php

/**
 * This form makes it possible to correct an existing location
 */
function gojira_locationcorrect_form($form, &$form_state) {
    global $user;
    $node = false;
    if (isset($_GET['nid'])) {
        $node = node_load($_GET['nid']);
//        global $user;
//        if (!helper::canChangeLocation($user->uid, $node->nid)) {
//            form_set_error('not_allowed', t('You are not allowed to do this.'));
//            drupal_goto('settings');
//        }
    }

    if ($node) {
        $id = $_GET['nid'];
    }
    
    $form['nid'] = array(
        '#title' => t('nid'),
        '#type' => 'hidden',
        '#required' => TRUE,
        '#default_value' => $id,
    );

    $form = gojira_get_core_location_form($form, $form_state, $node, 'locationedit');

    $form[GojiraSettings::CONTENT_TYPE_URL_FIELD] = array(
        '#title' => t('Website'),
        '#type' => 'textfield',
        '#required' => false,
        '#default_value' => ($node ? helper::value($node, GojiraSettings::CONTENT_TYPE_URL_FIELD) : ''),
    );
    
    $form['submit'] = array(
        '#type' => 'submit',
        '#prefix' => '<div class="gbutton_wrapper"><span class="gbutton rounded noshadow right">',
        '#value' => t('Submit'),
        '#suffix' => '</span></div>'
    );

    return $form;
}

function gojira_locationcorrect_form_validate($form, &$form_state) {
    gojira_get_core_location_form_validate($form, $form_state, $_POST['nid']);
}

function gojira_locationcorrect_form_submit($form, &$form_state) {
    $id = $_POST['nid'];
    global $user;
    $oUser = user_load($user->uid);
    $node = node_load($id);

    $node->uid = $oUser->uid;
    $node->title = $form['title']['#value'];

    $emailfield = GojiraSettings::CONTENT_TYPE_EMAIL_FIELD;
    $node->$emailfield = array(LANGUAGE_NONE => array(0 => array('value' => $form['email']['#value'])));

    $telephonefield = GojiraSettings::CONTENT_TYPE_TELEPHONE_FIELD;
    $node->$telephonefield = array(LANGUAGE_NONE => array(0 => array('value' => $form[GojiraSettings::CONTENT_TYPE_TELEPHONE_FIELD]['#value'])));

    $faxfield = GojiraSettings::CONTENT_TYPE_FAX_FIELD;
    $node->$faxfield = array(LANGUAGE_NONE => array(0 => array('value' => $form[GojiraSettings::CONTENT_TYPE_FAX_FIELD]['#value'])));

    $faxfield = GojiraSettings::CONTENT_TYPE_URL_FIELD;
    $node->$faxfield = array('und' => array(0 => array('value' => $form[GojiraSettings::CONTENT_TYPE_URL_FIELD]['#value'])));
    
//  $employeesfield = GojiraSettings::CONTENT_TYPE_NOTE_FIELD;
//  $node->$employeesfield = array(LANGUAGE_NONE => array(0 => array('value' => $form[GojiraSettings::CONTENT_TYPE_NOTE_FIELD]['#value'])));

    foreach (Location::getAddressFields() as $field) {
        $node->$field = array(LANGUAGE_NONE => array(0 => array('value' => $form[$field]['#value'])));
    }
    
    node_save($node);
    
    $location = Location::getLocationForAddress(
                    Location::formatAddress(
                            $form[GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD]['#value'], $form[GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD]['#value'], $form[GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD]['#value'], $form[GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD]['#value']
                    )
    );
    if (!$location) {
        drupal_set_message(t('Location information successfully stored. But we could not find the coordinates. We will also give it a try, for now the location is inactive.'), 'status');
        $node->status = 0;
        node_save($node);
        Mailer::locationWithoutCoordinatesAdded($node);
    }else{
        Location::storeLocatioInNode($location, $node->nid);
        drupal_set_message(t('Location information successfully stored.'), 'status');
    }

    header('Location: /location/correct&nid='.$id);
    exit;
}
