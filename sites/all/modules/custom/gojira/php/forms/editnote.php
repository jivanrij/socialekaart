<?php

function gojira_editnote_form($form, &$form_state) {
    
    if($_GET['nid'] && is_numeric($_GET['nid'])){
        $nid = $_GET['nid'];
    }
    
    $form['nid'] = array(
        '#type' => 'hidden',
        '#value' => $nid,
    );

    $form['note'] = array(
        '#title' => t('Your note'),
        '#type' => 'textarea',
        '#required' => false,
        '#default_value' => Location::getNote($nid),
    );

    $form['submit'] = array(
        '#type' => 'submit',
        '#prefix' => '<div class="gbutton_wrapper"><a class="gbutton rounded noshadow left" onclick="window.history.back();" title="' . t('Back') . '"><span>' . t('Back') . '</span></a><span class="gbutton rounded noshadow right">',
        '#value' => t('Submit'),
        '#suffix' => '</span></div>'
    );

    return $form;
}

function gojira_editnote_form_validate($form, &$form_state) {
}

function gojira_editnote_form_submit($form, &$form_state) {
    global $base_url;
    $nid = false;
    $node = false;
    if (isset($_GET['nid'])) {
        $node = node_load($_GET['nid']);
        $nid = $node->nid;
    }

    Location::setNote($nid, trim($form['note']['#value']));
    
//    drupal_set_message(t('Note added succesfully.'), 'status');

    header('Location: /?loc='.$nid);
    exit;
}
