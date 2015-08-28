<?php

function gojira_inform_form($form, &$form_state) {
    $options[0] = t('Make your choice.');
    $options['does_not_exist'] = t('This location does not exist (anymore).');
    $options['wrong_title'] = t('The location has a incorrect title.');
    $options['wrong_address'] = t('The address of this location is incorrect.');
    $options['wrong_coordinates'] = t('This location is shown on the wrong place.');
    $options['wrong_category'] = t('This location has the wrong category.');
    $options['incorrect_labels'] = t('I see some verry incorrect labels that need to be removed.');
    foreach ($options as $key => $option) {
        $select_options[$key] = $option;
    }
    $form['type_of_problem'] = array(
        '#title' => t('Type of suggestion'),
        '#type' => 'select',
        '#required' => true,
        '#options' => $select_options,
        '#default_value' => 0,
    );

    $form['nid'] = array(
        '#type' => 'hidden',
        '#value' => $_GET['nid'],
    );

    $form['title'] = array(
        '#title' => t('Better title') . '<span class="form-required" title="Dit veld is verplicht.">*</span>',
        '#type' => 'textfield',
        '#required' => false,
    );

    $form['info'] = array(
        '#title' => t('More specific information'),
        '#type' => 'textarea',
        '#required' => true,
    );

    $form['submit'] = array(
        '#type' => 'submit',
        '#prefix' => '<div class="gbutton_wrapper"><a class="gbutton rounded noshadow left" onclick="window.history.back();" title="' . t('Back') . '"><span>' . t('Back') . '</span></a><span class="gbutton rounded noshadow right">',
        '#value' => t('Submit'),
        '#suffix' => '</span></div>'
    );

    return $form;
}

function gojira_inform_form_validate($form, &$form_state) {

    if ($form['type_of_problem']['#value'] === '0') {
        form_set_error('type_of_problem', t('Please select a type of improvement.'));
    }

    if (trim($form['type_of_problem']['#value']) == 'wrong_title' && trim($form['title']['#value']) == '') {
        form_set_error('title', t('Please suggest a better title.'));
    }
}

function gojira_inform_form_submit($form, &$form_state) {
    global $base_url;
    global $user;
    $user = user_load($user->uid);

    $nid = false;
    $title = false;
    $node = false;
    
    $sTitleChanged = '';
    
    if (isset($_GET['nid'])) {
        $node = node_load($_GET['nid']);
        $old_title = $node->title;
        $nid = $node->nid;
        if(helper::canChangeLocation($user->uid, $node->nid) ){
            if (trim($form['type_of_problem']['#value']) == 'wrong_title' && trim($form['title']['#value']) != '') {
                $node->title = trim($form['title']['#value']);
                node_save($node);
            }
            $sTitleChanged = 'Title changed to: ';
        }else{
            $sTitleChanged = 'Title is not changed: ';
        }
    }

    //drupal_set_message(t('Thank you for giving us the improvement suggestion for location <i>%location_name%</i>', array('%location_name%' => $title)), 'status');

    Mailer::sendImproveSuggestion($old_title, $base_url . '/node/' . $nid . '/edit', $user->name, $base_url . '/user/' . $user->uid . '/edit', $form['type_of_problem']['#value'], $form['info']['#value'], $sTitleChanged.$form['title']['#value']);

    drupal_goto('informthanks');
}
