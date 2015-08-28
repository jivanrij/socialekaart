<?php

function gojira_employeeedit_form($form, &$form_state) {
    global $user;
    $id = 'new';
    $employee = false;
    if (isset($_GET['id']) && !is_null($_GET['id']) && is_numeric($_GET['id'])) {

        $id = $_GET['id'];
        $employee = user_load($id);
        // check some security
        if (!helper::canChangeOtherUser($user->uid, $id)) {
            form_set_error('not_allowed', t('You are not allowed to do this.'));
            drupal_goto('employee/list');
        }
    }

    $self = false;
    if ($user->uid == $id) {
        $self = true;
    }

    if ($id == 'new') {
        $form['info'] = array(
            '#markup' => '<p>' . t('Use this form to add a new user to the system. This user can operate in the system based on all your information.') . '</p>',
        );
    } else {
        $form['info'] = array(
            '#markup' => '<p>' . t('Use this form to change or remove a user.') . '</p>',
        );
    }

    $form[GojiraSettings::CONTENT_TYPE_USER_TITLE] = array(
        '#title' => t('Name/Title'),
        '#type' => 'textfield',
        '#required' => true,
        '#default_value' => ($employee ? helper::value($employee, GojiraSettings::CONTENT_TYPE_USER_TITLE) : ''),
    );

    $form['email'] = array(
        '#title' => t('E-mailadres'),
        '#type' => 'textfield',
        '#default_value' => ($employee ? $employee->mail : ''),
        '#required' => true,
    );

    $form['id'] = array(
        '#title' => t('id'),
        '#type' => 'hidden',
        '#required' => true,
        '#default_value' => $id,
    );


    if (!$self) {
        $rights = 0;
        if ($employee && in_array(helper::ROLE_EMPLOYER, $employee->roles)) {
            $rights = 1;
        }
        $form['rights'] = array(
            '#type' => 'radios',
            '#title' => t('Employer rights'),
            '#default_value' => $rights,
            '#options' => array(0 => t('This user can only see and use the data of the system.'), 1 => t('This user is able to contribute and change data in the system.'))
        );
    }

    $form['submit'] = array(
        '#type' => 'submit',
        '#prefix' => '<div class="gbutton_wrapper"><a href="/employee/list" class="gbutton rounded noshadow left" title="' . t('Back') . '"><span>' . t('Back') . '</span></a><span class="gbutton rounded noshadow right">',
        '#value' => t('Submit'),
        '#suffix' => '</span></div>'
    );

    return $form;
}

function gojira_employeeedit_form_validate($form, &$form_state) {

    $name_from_dtb = db_query('SELECT name FROM {users} WHERE (mail = :name OR name = :name) AND uid != :uid', array(':name' => $form['email']['#value'], ':uid' => $form['id']['#value']))->fetchField();
    if ($name_from_dtb) {
        form_set_error('email', t('E-mailadres allready in use.'));
    }

    if ($error = user_validate_mail($form['email']['#value'])) {
        form_set_error('email', t('E-mail not correctly formed.'));
    }

    if ($error = user_validate_name($form['email']['#value'])) {
        form_set_error('email', t('Cannot use this e-mail as a username.'));
    }
}

function gojira_employeeedit_form_submit($form, &$form_state) {
    global $user;
    $employer = user_load($user->uid); // employer is the current user

    $submit = 'new';
    if (is_numeric($form['id']['#value'])) {
        if (!helper::canChangeOtherUser($employer->uid, $form['id']['#value'])) {
            drupal_set_message(t('Employee not changed because you do not have the right.'), 'error');
            drupal_goto('employee/list');
        }
        $submit = 'existing';
    }

    $roles = array();
    $activeRoles = user_roles(true);
    foreach ($activeRoles as $key => $role) {
        if (isset($form['rights']['#value']) && $form['rights']['#value'] == 1) {
            if ($role == helper::ROLE_AUTHENTICATED || $role == helper::ROLE_EMPLOYER) {
                $roles[$key] = $role;
            }
        } else {
            if ($role == helper::ROLE_AUTHENTICATED || $role == helper::ROLE_EMPLOYEE) {
                $roles[$key] = $role;
            }
        }
    }

    if ($submit == 'existing') {
        $employee = user_load($form['id']['#value']);
        // only change roles if i'm not editing myself
        if ($employer->uid != $employee->uid) {
            $employee->roles = $roles;
        }
        $employee->name = $employee->mail = $form['email']['#value'];
    } else {
        //set up the user fields
        $fields = array(
            'name' => $form['email']['#value'],
            'mail' => $form['email']['#value'],
            'pass' => user_password(7),
            'status' => 1,
            'roles' => $roles,
        );
        $employee = user_save('', $fields);

        $searchFavoritesField = GojiraSettings::CONTENT_TYPE_SEARCH_FAVORITES_FIELD;
        $employee->$searchFavoritesField = array(LANGUAGE_NONE => array(0 => array('value' => 0)));
        $searchGlobalField = GojiraSettings::CONTENT_TYPE_SEARCH_GLOBAL_FIELD;
        $employee->$searchGlobalField = array(LANGUAGE_NONE => array(0 => array('value' => 0)));
        $conditionsField = GojiraSettings::CONTENT_TYPE_CONDITIONS_AGREE_FIELD;
        $employee->$conditionsField = array(LANGUAGE_NONE => array(0 => array('value' => 0)));
        $tutorialField = GojiraSettings::CONTENT_TYPE_TUTORIAL_FIELD;
        $employee->$tutorialField = array(LANGUAGE_NONE => array(0 => array('value' => 0)));
        $importedField = GojiraSettings::CONTENT_TYPE_USER_NOT_IMPORTED;
        $employee->$importedField = array(LANGUAGE_NONE => array(0 => array('value' => 1)));
    }

    $fieldsToSave = array(
        GojiraSettings::CONTENT_TYPE_USER_TITLE
    );
    foreach ($fieldsToSave as $fieldName) {
        $employee->$fieldName = array(LANGUAGE_NONE => array(0 => array('value' => $form[$fieldName]['#value'])));
    }

    // get the group the user is linked to and link the new location to it
    $groupField = GojiraSettings::CONTENT_TYPE_GROUP_FIELD;
    $groupFieldUser = $employer->$groupField;
    $employee->$groupField = array(LANGUAGE_NONE => array(0 => array('nid' => $groupFieldUser[LANGUAGE_NONE][0]['nid'])));

    user_save($employee);

    if ($submit == 'existing') {
        drupal_set_message(t('Succesfully changed.'), 'status');
    } else {
        //drupal_mail('user', 'register_no_approval_required', $employee->mail, NULL, array('account' => $employee), variable_get('site_mail', 'no@reply.com'));
        if (isset($form['rights']['#value']) && $form['rights']['#value'] == 1) {
            drupal_set_message(t('Succesfully created a employer account.'), 'status');
            Mailer::sendWelcomeMailToEmployer($employee);
        } else {
            drupal_set_message(t('Succesfully created a employee account.'), 'status');
            Mailer::sendWelcomeMailToEmployee($employee);
        }
    }

    drupal_goto('employee/list');
}
