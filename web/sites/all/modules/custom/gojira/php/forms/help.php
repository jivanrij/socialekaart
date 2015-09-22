<?php

function gojira_help_form($form, &$form_state) {

    $form['info'] = array(
        '#markup' => '<hr /><p>' . t('Can\'t you find a answer to a question you have? Please ask us trough this form.') . '</p>',
    );
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $form['#attributes']['class'][] = 'post';
    }
    
    $form['question'] = array(
        '#title' => t('Your question'),
        '#type' => 'textarea',
        '#required' => true,
    );

    $form['submit'] = array(
        '#type' => 'submit',
        '#prefix' => '<div class="gbutton_wrapper"><span class="gbutton rounded noshadow left">',
        '#value' => t('Submit'),
        '#suffix' => '</span></div>'
    );

    return $form;
}

function gojira_help_form_submit($form, &$form_state) {
    global $user;
    $oUser = user_load($user->uid);
    Mailer::sendQuestion($oUser, strip_tags($form['question']['#value']), strip_tags($form['topic']['#value']));
    drupal_set_message(t('Thanks you for your question, we will contact you as soon as possible.'), 'status');
}
