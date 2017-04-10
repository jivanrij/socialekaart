<?php
/**
 * This is a wrapper for the messages of Drupal in Gojira
 *
 * @author jonathan
 */

class Messages {
  
  public static function setLocationMessage(){
    form_set_error('location', t('Cannot find a location based on the given information. Please check if you have filled in the whole form with correct information and there are no missing fields.'));
  }
  
  public static function setCategoryMessage(){
    form_set_error('missing_category', t('You must select a category to add the location.'));
  }
  
  public static function setCategoryAndLocationMessage(){
    form_set_error('missing_category_and_location', t('We cannot find a location based on the given information and you have forgotten to select a category.'));
  }  
  
  public static function setTitleMessage(){
    form_set_error('unique_title', t('There is already a location with this title known in the system. Please pick another.'));
  }
  
  public static function setTitleMissingMessage(){
    form_set_error('title_missing', t('Please add the title, without it we can\'t save.'));
  }
  
  public static function setEmailMessage(){
    form_set_error('unique_email', t('The given e-mail address is already in use by a user or not correctly formed.'));
  }
  
  public static function setCridentialsMessage(){
    //form_set_error('cridentials', t('No correct cridentials given.'));
  }

  public static function setNeedToAgreeMessage(){
    form_set_error('agree', t('You need to agree with the terms & conditions.'));
  }
  
  public static function setNotAllowedToChangeData(){
    drupal_set_message(t('Don\'t delete yourself.'), 'warning');
    form_set_error('cridentials', t('You are not allowed to change this data. This can damage the system.'));
  }
  
  public static function getFormMessage(){
    $message = false;
    $formErrors = form_get_errors();

    if($formErrors == null){
      return $message;
    }
    
//    $fields = array('missing_information','missing_title_suggest','agree_terms_conditions','title_missing','missing_category_and_location','missing_category','agree','not_allowed','incomplete','cridentials','big_failed','location', 'unique_title', 'unique_email', 'mail_not_found');
    
    $returnHtml = '';
    foreach(form_get_errors() as $field){
//      if(array_key_exists($field,$formErrors)){
        $returnHtml .= $field.'<br />';
//      }
    }
    
    
   
    return $returnHtml;
  }
  
}
