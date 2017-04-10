<?php
// $Id: $

/**
 * @file
 * Domain configuration and form alters for hook_domainconf() and
 * hook_domainbatch().
 */

/**
 * Declaration used by the hook implementation of hook_domainbatch().
 */
function _onesignin_client_domainbatch() {
  $batch = array();

  module_load_include('admin.inc', 'onesignin_client');
  $admin_form = onesignin_client_admin_form();

  $default = array(
    '#group' => t('One Sign-in'),
    '#module' => t('One Sign-in (client)'),
    '#permission' => 'administer onesignin client',
    '#domain_action' => 'domain_conf',
    '#update_all' => TRUE,
    '#weight' => 0,
  );

  // Enable or disable onesignin_client functionality.
  $item = array(
    '#variable' => 'onesignin_client',
    '#meta_description' => t('Enable or disable One Sign-in functionality.'),
    '#data_type' => 'integer',
    '#system_default' => 'domain-conf-ignore',
    '#form' => array(
      '#type' => 'select',
      '#title' => t('One Sign-in client'),
      '#options' => array(
        '' => t('Disabled'),
        '1' => t('Enabled'),
      ),
    ),
  ) + $default;

  $item['#form']['#description'] = $admin_form['onesignin_client']['#description'];
  $item['#form']['#disabled'] = $admin_form['onesignin_client']['#disabled'];
  $batch['onesignin_client'] = $item;

  // Provide a field for the site ID.
  $item = array(
    '#variable' => 'onesignin_client_site_id',
    '#meta_description' => t('Set a One Sign-in identifier for each site individually, or use the same identifier for all subsites.'),
    '#data_type' => 'string',
  ) + $default;
  if (!isset($item['#system_default'])) {
    $item['#system_default'] = '';
  }
  $item['#form'] = $admin_form['info']['onesignin_client_site_id'];
  unset($item['#form']['#default_value']);
  $batch['onesignin_client_site_id'] = $item;

  // Set the server address.
  $item = array(
    '#variable' => 'onesignin_client_server',
    '#meta_description' => t('Set the Single Sign-in server address.'),
  ) + $default;

  $item['#form'] = $admin_form['advanced']['onesignin_client_server'];
  $item['#system_default'] = '';
  unset($item['#form']['#default_value']);
  $item['#form']['#required'] = FALSE;
  $batch['onesignin_client_server'] = $item;

  // Set the shared secret.
  $item = array(
    '#variable' => 'onesignin_client_secret',
    '#meta_description' => t('Set the Single Sign-in shared secret. You are advised to use a unique shared secret per site.'),
  ) + $default;

  $item['#form'] = $admin_form['advanced']['onesignin_client_secret'];
  unset($item['#form']['#default_value']);
  $item['#form']['#required'] = FALSE;
  $item['#system_default'] = NULL;
  $batch['onesignin_client_secret'] = $item;

  // Set the ignored paths.
  $item = array(
    '#variable' => 'onesignin_client_ignored_paths',
    '#meta_description' => t('Exclude these pages from Single Sign-in. One path per line.'),
  ) + $default;

  $item['#form'] = $admin_form['info']['onesignin_client_ignore_paths'];
  unset($item['#form']['#default_value']);
  $item['#form']['#required'] = FALSE;
  $item['#system_default'] = NULL;
  $batch['onesignin_client_ignore_paths'] = $item;

  return $batch;
}

/**
 * Alter the default domain_conf_form().
 * @see onesignin_client_form_domain_conf_form_alter()
 */
function _onesignin_client_domain_conf_form_alter(&$form, &$form_state) {
  $fieldset_key = t('One Sign-in');

  $domain_info = domain_load($form['domain_id']['#value']);

  if (isset($form[$fieldset_key])) {
    $default_site_id = (isset($domain_info['subdomain']) ? rtrim($domain_info['subdomain'] . $GLOBALS['base_path'], '/') : onesignin_client_site_id());

    $domain_site_id = domain_conf_variable_get($domain_info['domain_id'], 'onesignin_client_site_id');

    if (!$domain_site_id) {
      $domain_site_id = variable_get('onesignin_client_site_id', $default_site_id);
    }

    $form[$fieldset_key]['onesignin_client_domainconf'] = array(
      '#type' => 'select',
      '#title' => t('Configuration source'),
      '#description' => t('Please select which source should be used for <em>Site ID</em>, <em>Server address</em> and <em>Shared secret</em>.'),
      '#options' => array(
        'domain-conf-ignore' => t('Use primary domain settings'),
        1 => t('Use per-domain settings'),
      ),
      '#default_value' => (domain_conf_variable_get($domain_info['domain_id'], 'onesignin_client_domainconf') ? domain_conf_variable_get($domain_info['domain_id'], 'onesignin_client_domainconf') : 'primary'),
    );

    $has_config = TRUE;
    if (isset($form[$fieldset_key]['onesignin_client_domainconf']['#default_value']) && $form[$fieldset_key]['onesignin_client_domainconf']['#default_value'] == 'domain-conf-ignore') {
      unset($form[$fieldset_key]['onesignin_client_site_id']['#default_value']);
      unset($form[$fieldset_key]['onesignin_client_server']['#default_value']);
      unset($form[$fieldset_key]['onesignin_client_secret']['#default_value']);
      unset($form[$fieldset_key]['onesignin_client_ignore_paths']['#default_value']);
      $has_config = FALSE;
    }

    $form[$fieldset_key]['info'] = array(
      '#type' => 'fieldset',
      '#title' => t('Client site information'),
      '#collapsible' => TRUE,
      '#collapsed' => !$has_config,
      '#description' => t("When enabling the One Sign-in client, you should first create an entry for this site in the authentication server configuration. There, you must enter the Site ID <strong><code>@site_id</code></strong> and Site endpoint <strong><code>@site_endpoint</code></strong>, then copy the <em>Shared secret</em> and enter it in this form, together with the <em>Server address</em> for the authentication server installation.", array(
        '@site_id' => $domain_site_id,
        '@site_endpoint' => (isset($domain_info['path']) ? $domain_info['path'] : '') . 'onesignin/response',
      )),
    );

    $form[$fieldset_key]['info']['onesignin_client_site_id'] = $form[$fieldset_key]['onesignin_client_site_id'];
    unset($form[$fieldset_key]['onesignin_client_site_id']);

    $form[$fieldset_key]['info']['site_url'] = array(
      '#type' => 'item',
      '#title' => t('Site endpoint'),
      '#markup' => check_plain(url((isset($domain_info['path']) ? $domain_info['path'] : '') . 'onesignin/response', array('absolute' => TRUE))),
    );

    $form[$fieldset_key]['advanced'] = array(
      '#type' => 'fieldset',
      '#title' => t('Authentication server information'),
      '#collapsed' => TRUE,
      '#collapsible' => TRUE,
    );

    $form[$fieldset_key]['advanced']['onesignin_client_server'] = $form[$fieldset_key]['onesignin_client_server'];
    unset($form[$fieldset_key]['onesignin_client_server']);

    $form[$fieldset_key]['advanced']['onesignin_client_secret'] = $form[$fieldset_key]['onesignin_client_secret'];
    unset($form[$fieldset_key]['onesignin_client_secret']);

    $form[$fieldset_key]['advanced']['onesignin_client_ignore_paths'] = $form[$fieldset_key]['onesignin_client_ignore_paths'];
    unset($form[$fieldset_key]['onesignin_client_ignore_paths']);
  }

  array_unshift($form['#submit'], '_onesignin_client_domain_conf_form_submit');
  $form['#validate'][] = '_onesignin_client_domain_conf_form_validate';
}

/**
 * Custom validation function for warning a user about empty server and/or
 * secret when functionality is switched on.
 */
function _onesignin_client_domain_conf_form_validate(&$form, $form_state) {
  if (!empty($form_state['values']['onesignin_client_domainconf']) && $form_state['values']['onesignin_client_domainconf'] != 'domain-conf-ignore') {
    if (empty($form_state['values']['onesignin_client_site_id'])) {
      form_set_error('onesignin_client_site_id', t('One Sign-on Site ID can not be empty.'));
    }
    if (empty($form_state['values']['onesignin_client_server'])) {
      form_set_error('onesignin_client_server', t('One Sign-on server address must be provided.'));
    }
    if (empty($form_state['values']['onesignin_client_secret'])) {
      form_set_error('onesignin_client_secret', t('One Sign-on shared secret must be provided.'));
    }
  }
}

/**
 * Custom submit handling function for unsetting server and secret when primary
 * domain settings should be used.
 */
function _onesignin_client_domain_conf_form_submit($form, &$form_state) {
  if (isset($form_state['values']['onesignin_client_domainconf']) && $form_state['values']['onesignin_client_domainconf'] == 'domain-conf-ignore') {
    unset($form_state['values']['onesignin_client_site_id']);
    unset($form_state['values']['onesignin_client_server']);
    unset($form_state['values']['onesignin_client_secret']);
    unset($form_state['values']['onesignin_client_ignore_paths']);
    domain_conf_variable_delete($form_state['values']['domain_id'], 'onesignin_client_site_id');
    domain_conf_variable_delete($form_state['values']['domain_id'], 'onesignin_client_server');
    domain_conf_variable_delete($form_state['values']['domain_id'], 'onesignin_client_secret');
    domain_conf_variable_delete($form_state['values']['domain_id'], 'onesignin_client_ignore_paths');
  }
}
