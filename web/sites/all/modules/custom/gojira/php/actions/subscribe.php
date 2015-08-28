<?php
/**
 * This page generates a form to crud a employee
 *
 * @return string
 */
function subscribe() {

  global $user;
  $user = user_load($user->uid);

  $lastPayed = Subscriptions::getEndCurrentPeriod();
  if(!$lastPayed){
      $lastPayed = helper::getTime();
  }
  
  $extend_new_end_date = date('d-m-Y',strtotime('+'.variable_get('SUBSCRIPTION_PERIOD').' days',$lastPayed));
  $original_end_date = date('d-m-Y',$lastPayed);
    
  return theme('subscribe', array('original_end_date'=>$original_end_date, 'extend_new_end_date'=>$extend_new_end_date,'subscribed' => in_array(helper::ROLE_SUBSCRIBED_MASTER, array_values($user->roles))));
}

function gojira_to_ideal_page_form($form, &$form_state) {

  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array('#type' => 'submit', '#value' => t('Subscribe now'));
  
  $form['actions']['submit']['#prefix'] = '<span class="gbutton rounded noshadow right gbutton_wide">';
  $form['actions']['submit']['#suffix'] = '</span>';
  
  return $form;
}
function gojira_to_ideal_page_form_submit($form, &$form_state) {
    drupal_goto('idealpay');
}

function gojira_to_ideal_page_extend_form($form, &$form_state) {

  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array('#type' => 'submit', '#value' => t('Extend your subscription'));
  
  $form['actions']['submit']['#prefix'] = '<span class="gbutton rounded noshadow right gbutton_wide">';
  $form['actions']['submit']['#suffix'] = '</span>';
  
  return $form;
}
function gojira_to_ideal_page_extend_form_submit($form, &$form_state) {
    drupal_goto('idealpay');
}
