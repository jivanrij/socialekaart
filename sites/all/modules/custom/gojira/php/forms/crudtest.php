<?php

function gojira_crudtest_form($form, &$form_state) {
    $options[0] = t('Make your choice.');
    $options['is_double'] = t('This location is double stored.');
    $options['does_not_exist'] = t('This location does not exist (anymore).');
    $options['wrong_title'] = t('The location has a incorrect title.');
    $options['wrong_address'] = t('The address of this location is incorrect.');
    $options['wrong_coordinates'] = t('This location is shown on the wrong place.');
    $options['wrong_category'] = t('This location has the wrong category.');
    $options['incorrect_labels'] = t('I see some verry incorrect labels that need to be removed.');
    $options['other_contact_adres'] = t('There is another contact adres.');
    $options['other'] = t('A reason not listed here.');
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

function gojira_crudtest_form_validate($form, &$form_state) {
    if ($form['title']['#value'] === '0') {
        form_set_error('title', t('This is an error message.'));
    }
}

function gojira_crudtest_form_submit($form, &$form_state) {
    global $base_url;
    global $user;
    $user = user_load($user->uid);

    $nid = false;
    $title = false;
    $node = false;

    drupal_goto('crudtest');
}
