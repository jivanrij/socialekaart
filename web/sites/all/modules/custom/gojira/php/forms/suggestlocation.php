<?php

/**
 * This form is the crud for the location nodes
 */
function gojira_suggestlocation_form($form, &$form_state) {

  $form['info'] = array(
    '#markup' => '<p>'.t('If you know a location that is not know in this system, you can use this form to add it.').'</p>',
  );
  
  $form['title'] = array(
      '#title' => t('Title'),
      '#type' => 'textfield',
      '#required' => true,
  );

  $form['email'] = array(
      '#title' => t('E-mail'),
      '#type' => 'textfield',
      '#required' => false,
  );

  helper::addAddressFormPart($form, false);

  $form[GojiraSettings::CONTENT_TYPE_TELEPHONE_FIELD] = array(
      '#title' => t('Telephone'),
      '#type' => 'textfield',
      '#required' => false,
  );

  $form[GojiraSettings::CONTENT_TYPE_FAX_FIELD] = array(
      '#title' => t('Faxnumber'),
      '#type' => 'textfield',
      '#required' => false,
  );
  
  $categorys = db_query("select title, nid from node where type = 'category' and status = 1 and title != 'Huisarts'");
  $cat_options = array(0=>t('Select category'));
  foreach($categorys as $category){
    $cat_options[$category->nid] = $category->title;
  }
  $form[GojiraSettings::CONTENT_TYPE_CATEGORY_FIELD] = array(
      '#title' => t('Category'),
      '#type' => 'select',
      '#required' => true,
      '#options' => $cat_options,
      '#default_value' => 0,
  );

  
  $form[GojiraSettings::CONTENT_TYPE_URL_FIELD] = array(
      '#title' => t('Website'),
      '#type' => 'textfield',
      '#required' => false,
  );


  $form['submit'] = array(
      '#type' => 'submit',
      '#prefix' => '<div class="gbutton_wrapper"><a class="gbutton rounded noshadow left" onclick="window.history.back();" title="'.t('Back').'"><span>'.t('Back').'</span></a><span class="gbutton rounded noshadow right">',
      '#value' => t('Submit'),
      '#suffix' => '</span></div>'
  );
  
  return $form;
}

function gojira_suggestlocation_form_validate($form, &$form_state) {
  
  if($form[GojiraSettings::CONTENT_TYPE_CATEGORY_FIELD]['#value'] == '0'){
    form_set_error(GojiraSettings::CONTENT_TYPE_CATEGORY_FIELD, t('You must select a category to add the location.'));
  }
  
  if(trim($form['title']['#value']) == ''){
    form_set_error('title', t('Please add the title, without it we can\'t save.'));
  }else{
    $knownTitle = db_query("select title from {node} where type = 'location' and title = :t1", array(':t1' => $form['title']['#value']))->fetchField();
    if ($knownTitle) {
      form_set_error('title', t('There is already a location with this title known in the system. Please pick another.'));
    }
  }

  $location = Location::getLocationForAddress(
                  Location::formatAddress(
                          $form[GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD]['#value'], $form[GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD]['#value'], $form[GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD]['#value'], $form[GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD]['#value']
                  )
  );
  
  if (!$location) {
    form_set_error('location', t('Cannot find a location based on the given information. Please check if you have filled in the whole form with correct information and there are no missing fields.'));
  }
}

function gojira_suggestlocation_form_submit($form, &$form_state) {
  
  global $user;
  
  // no existing location found, let's creat one 
  $node = new stdClass();
  $node->type = GojiraSettings::CONTENT_TYPE_LOCATION;
  node_object_prepare($node);
  $node->language = LANGUAGE_NONE;
  $node->uid = $user->uid;
  $node->status = 1;
  $node->promote = 0;
  $node->comment = 0;

  $user = user_load($user->uid);

  // get the group the user is linked to and link the new location to it
//  $groupField = GojiraSettings::CONTENT_TYPE_GROUP_FIELD;
//  $groupFieldUser = $user->$groupField;
//  $node->$groupField = array(LANGUAGE_NONE => array(0 => array('nid' => $groupFieldUser[LANGUAGE_NONE][0]['nid'])));

  $node = node_submit($node); // Prepare node for saving

  $hasModerator = GojiraSettings::CONTENT_TYPE_MODERATED_STATUS_FIELD;
  $node->$hasModerator = array(LANGUAGE_NONE => array(0 => array('value' => 2)));

  $node->type = GojiraSettings::CONTENT_TYPE_LOCATION;
  $node->uid = $user->uid;
  $node->title = $form['title']['#value'];

  $emailfield = GojiraSettings::CONTENT_TYPE_EMAIL_FIELD;
  $node->$emailfield = array('und' => array(0 => array('value' => trim($form['email']['#value']))));

  $telephonefield = GojiraSettings::CONTENT_TYPE_TELEPHONE_FIELD;
  $node->$telephonefield = array('und' => array(0 => array('value' => $form[GojiraSettings::CONTENT_TYPE_TELEPHONE_FIELD]['#value'])));

  $faxfield = GojiraSettings::CONTENT_TYPE_FAX_FIELD;
  $node->$faxfield = array('und' => array(0 => array('value' => $form[GojiraSettings::CONTENT_TYPE_FAX_FIELD]['#value'])));

  $faxfield = GojiraSettings::CONTENT_TYPE_URL_FIELD;
  $node->$faxfield = array('und' => array(0 => array('value' => $form[GojiraSettings::CONTENT_TYPE_URL_FIELD]['#value'])));

  $catfield = GojiraSettings::CONTENT_TYPE_CATEGORY_FIELD;
  $node->$catfield = array('und' => array(0 => array('nid' => $form[GojiraSettings::CONTENT_TYPE_CATEGORY_FIELD]['#value'])));
  
  $visiblefield = GojiraSettings::CONTENT_TYPE_SHOW_LOCATION_FIELD;
  $node->$visiblefield = array(LANGUAGE_NONE => array(0 => array('value' => 1)));
  
  foreach (Location::getAddressFields() as $field) {
    $node->$field = array('und' => array(0 => array('value' => $form[$field]['#value'])));
  }

  node_save($node);
  
  Mailer::sendLocationAddedByUserToAdmin($base_url.'/node/'.$node->nid.'/edit' . '<br /><br />Aangemaakt door: ' . $base_url.'/user/'.$user->uid.'/edit', $node->title);
  
  drupal_set_message(t('Location successfully suggested.'), 'status');
  
  drupal_goto('suggestlocationthanks',array('query'=>array('nid'=>$node->nid)));
}
