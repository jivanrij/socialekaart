<?php

/**
 * @file
 * Administration forms for the One Signin client module.
 */

/**
 * Form callback for the redirects admin form.
 */
function onesignin_client_redirects() {
  $form = array();

  $form['onesignin_client_registration_redirect'] = array(
    '#type' => 'textfield',
    '#title' => t('Registration redirect'),
    '#default_value' => variable_get('onesignin_client_registration_redirect', ''),
    '#description' => t('The url (internal) to redirect users to after they have completed the registration.'),
  );

  return system_settings_form($form);
}

/**
 * Form callback for the mapping settings form.
 */
function onesignin_client_mapping() {
  $form = array('mapping' => array());
  $mapping = variable_get('onesignin_client_mapping', array());

  $client_fields = field_info_instances('user');

  $xmlrpc = new onesignin_client_xmlrpc();
  $server_fields = $xmlrpc->get_server_fields(TRUE);

  if (!empty($client_fields['user']) && !empty($server_fields)) {
    $unmapped_fields = array();
    $row = 0;
    foreach ($server_fields as $field) {
      if (isset($mapping[$field['name']])) {
        $client_fieldname = $mapping[$field['name']];
        $form['mapping'][] = array(
          'server_field' => array(
            '#title' => 'Server field',
            '#type' => 'item',
            '#markup' => $field['label'],
          ),
          'client_field' => array(
            '#title' => 'Client field',
            '#type' => 'item',
            '#markup' => $client_fields['user'][$client_fieldname]['label'],
          ),
          'actions' => array(
            '#title' => t('Actions'),
            '#type' => 'item',
            '#markup' => l(t('remove mapping'), 'admin/config/people/onesignin-client/mapping/' . $field['name'] . '/delete'),
          )
        );
        unset($client_fields['user'][$client_fieldname]);
      }
      else {
        $unmapped_fields[$field['name']] = $field['label'];
      }
    }
    if (!empty($unmapped_fields) && !empty($client_fields['user'])) {
      $available_fields = array();
      foreach ($client_fields['user'] as $fieldname => $field) {
        $available_fields[$fieldname] = $field['label'];
      }

      $form['mapping']['new_mapping'] = array(
        'server_field' => array(
          '#title' => t('Server field'),
          '#type' => 'select',
          '#options' => $unmapped_fields,
        ),
        'client_field' => array(
          '#title' => t('Client field'),
          '#type' => 'select',
          '#options' => $available_fields,
        ),
      );
    }
  }
  elseif (!empty($server_fields)) {
    $form['no-items'] = array(
      '#type' => 'item',
      '#title' => t('No fields found to map to.'),
    );
  }
  elseif (!empty($client_fields['user'])) {
    $form['no-items'] = array(
      '#type' => 'item',
      '#title' => t('No profile fields found on the server.'),
    );
  }

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );

  return $form;
}

/**
 * Theme callback for the mapping form.
 *
 * Makes the form look like a nice table.
 */
function theme_onesignin_client_mapping($variables) {
  $output = '';
  $form = $variables['form'];
  $header = array(t('Server field'), t('Client field'), t('Actions'));


  foreach (element_children($form['mapping']) as $key) {
    $row = array();
    $form['mapping'][$key]['server_field']['#title_display'] = 'invisible';
    $row[] = drupal_render($form['mapping'][$key]['server_field']);

    $form['mapping'][$key]['client_field']['#title_display'] = 'invisible';
    $row[] = drupal_render($form['mapping'][$key]['client_field']);

    if (isset($form['mapping'][$key]['actions'])) {
      $form['mapping'][$key]['actions']['#title_display'] = 'invisible';
      $row[] = drupal_render($form['mapping'][$key]['actions']);
    }
    else {
      $row[] = '';
    }

    $rows[] = $row;
  }

  if (!empty($rows)) {
    $output .= theme('table', array('header' => $header, 'rows' => $rows));
  }
  else {
    $output .= drupal_render($form['no-items']);
  }
  $output .= drupal_render_children($form);

  return $output;
}

/**
 * Submit handler for the mapping settings form.
 */
function onesignin_client_mapping_submit($form, &$form_state) {
  $new_mapping = $form_state['values'];
  $mapping = variable_get('onesignin_client_mapping', array());
  $mapping[$new_mapping['server_field']] = $new_mapping['client_field'];
  variable_set('onesignin_client_mapping', $mapping);
}

/**
 * Form callback for the delete confirmation form.
 */
function onesignin_client_delete_mapping($form, &$form_state, $field_name) {
  $xmlrpc = new onesignin_client_xmlrpc();
  $server_fields = $xmlrpc->get_server_fields(FALSE);

  $field_label = FALSE;
  foreach ($server_fields as $server_field) {
    if ($server_field['name'] == $field_name) {
      $field_label = $server_field['label'];
      break;
    }
  }

  if ($field_label) {
    $form = array();
    $form['field_name'] = array(
      '#type' => 'value',
      '#value' => $field_name,
    );

    return confirm_form(
      $form,
      t('Are you sure you want to delete the mapping to the field %field', array('%field' => $field_label)),
      'admin/config/people/onesignin-client/mapping'
    );
  }
  else {
    drupal_set_message(t('No such field found.'), 'error');
    drupal_goto('admin/config/people/onesignin-client/mapping');
  }
}

/**
 * Submit handler to delete a mapping.
 */
function onesignin_client_delete_mapping_submit($form, &$form_state) {
  $mapping = variable_get('onesignin_client_mapping', array());
  unset($mapping[$form_state['values']['field_name']]);
  variable_set('onesignin_client_mapping', $mapping);

  drupal_goto('admin/config/people/onesignin-client/mapping');
}

/**
 * Declares admin settings form for the One Signin client module.
 */
function onesignin_client_admin_form() {

  $form['onesignin_client'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable One Sign-in client'),
    '#default_value' => variable_get('onesignin_client', FALSE),
    '#disabled' => module_exists('onesignin_server'),  // Do not allow enabling client if the server module is enabled.
    '#description' => t('Turn One Sign-in functionality on or off.') . (module_exists('onesignin_server') ? ' ' . t('It is not possible to enable this if the One Sign-in server module is enabled on this site.') : ''),
  );

  $form['info'] = array(
    '#type' => 'fieldset',
    '#title' => t('Client site information'),
    '#collapsible' => TRUE,
    '#description' => t("When enabling the One Sign-in client, you should first create an entry for this site in the authentication server configuration. There, you must enter the Site ID <strong><code>@site_id</code></strong> and Site endpoint <strong><code>@site_endpoint</code></strong>, then copy the <em>Shared secret</em> and enter it in this form, together with the <em>Server address</em> for the authentication server installation.", array(
      '@site_id' => variable_get('onesignin_client_site_id', onesignin_client_site_id()),
      '@site_endpoint' => url('onesignin/response', array('absolute' => TRUE)),
    )),
  );

  if (module_exists('domain_conf')) {
    $form['info']['domain_conf'] = array(
      '#value' => '<p>' . t('You have the <a href="@link_url">@link_title</a> module enabled, you can also configure these settings per domain.', array(
        '@link_url' => url('admin/build/domain'),
        '@link_title' => t('Domain Configuration'),
      )) . '</p>',
    );
  }
  elseif (module_exists('domain')) {
    $form['info']['domain_conf'] = array(
      '#value' => '<p>' . t('When you enable the <a href="@link_url">@link_title</a> module, you can also configure these settings per domain.', array(
        '@link_url' => url('admin/build/modules', array('fragment' => 'edit-status-domain')),
        '@link_title' => t('Domain Configuration'),
      )) . '</p>',
    );
  }

  $form['info']['onesignin_client_site_id'] = array(
    '#type' => 'textfield',
    '#title' => t('Site ID'),
    '#default_value' => variable_get('onesignin_client_site_id', onesignin_client_site_id()),
    '#description' => t('This must be a unique identifier per site.') . (module_exists('domain') ? ' ' . t('With the Domain Access module installed, this means that you can either use the same <em>Site ID</em> for all sub-domains, but beware that login-requests will be redirected from the One Sign-in server <em>to only one endpoint</em>. So, if you want login-requests to redirect back from the One Sign-in server to the same (sub-) domain after the login is succeeded, you will need to set entries for each of the sites.') : ''),
    '#maxlength' => 128,
  );

  $form['info']['site_url'] = array(
    '#type' => 'item',
    '#title' => t('Site endpoint url'),
    '#markup' => check_plain(url('onesignin/response', array('absolute' => TRUE))),
  );

  $form['info']['xmlrpc'] = array(
    '#type' => 'item',
    '#title' => t('XML-RPC notification endpoint url'),
    '#markup' => check_plain(url('xmlrpc.php', array('absolute' => TRUE))),
  );

  $form['info']['onesignin_client_ignore_paths'] = array(
    '#type' => 'textarea',
    '#title' => t('Excluded paths'),
    '#description' => t('Exclude these pages from Signle Sign-in. One path per line.'),
    '#default_value' => variable_get('onesignin_client_ignore_paths', ''),
  );

  $form['advanced'] = array(
    '#type' => 'fieldset',
    '#title' => t('Authentication server information'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $form['advanced']['onesignin_client_server'] = array(
    '#type' => 'textfield',
    '#title' => t('Server address'),
    '#description' => t('Enter the full HTTP address to the single-sign-on server path, starting with either http:// or https://.'),
    '#default_value' => variable_get('onesignin_client_server', ''),
    '#required' => TRUE,
    '#element_validate' => array('onesignin_client_server_element_validate'),
    '#maxlength' => 255,
  );

  $form['advanced']['onesignin_client_secret'] = array(
    '#type' => 'textfield',
    '#title' => t('Shared secret'),
    '#description' => t('Enter the shared secret from the One Sign-in server in this field. Keep this information secret as it is used to "sign" all conversations between the client and server components.'),
    '#default_value' => variable_get('onesignin_client_secret', ''),
    '#required' => TRUE,
  );

  $form['advanced']['onesignin_client_allow_bypass'] = array(
    '#type' => 'checkbox',
    '#title' => t('Allow bypass <em>(discouraged)</em>'),
    '#description' => t("Allow bypassing of One Sign-in functionality by using <em>?no-sso</em> in the query string. <strong>Warning! You are strongly advised not to enable this feature unless you know exactly what you are dealing with.</strong> Enabling this will allow users who append <em>?no-sso</em> in their request to register for a new account locally and possibly setting a password, thereby creating future conflicts in synchronization."),
    '#default_value' => variable_get('onesignin_client_allow_bypass', FALSE),
  );

  return system_settings_form($form);
}

function onesignin_client_server_element_validate($element, &$form_state) {
  if (!empty($element['#value']) && !valid_url($element['#value'], TRUE)) {
    form_error($element, t('This url is not valid.'));
  }
}
