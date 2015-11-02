<?php

function gojiratheme_preprocess_page(&$vars) {
    $messages = drupal_get_messages(NULL, TRUE);
    $vars['messages_list'] = $messages;
}

function gojiratheme_form_element($vars) {

    $aNotAllowed = array('agree_terms_conditions', 'pass');

    $required = '';
    if($vars['element']['#required']){
        $required = 'required';
    }    
    
    if (!in_array($vars['element']['#name'], $aNotAllowed)) {
        // put the description of a field in the title of the label
        if (isset($vars['element']) && isset($vars['element']['#description'])) {
            $sDescription = $vars['element']['#description'];
            $vars['element']['#title'] = $vars['element']['#title'];
            unset($vars['element']['#description']);
            $sOriginal = theme_form_element($vars);
            return str_replace('<label', '<label title="' . $sDescription . '" class="has_help '.$required.'"', $sOriginal);
        }
    }

    return theme_form_element($vars);
}

// remove a tag from the head for Drupal 7
function gojiratheme_html_head_alter(&$head_elements) {
  unset($head_elements['system_meta_generator']);
}