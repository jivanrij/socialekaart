<?php

function gojira_configuration_form($form, &$form_state) {

    $form['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Submit'),
    );

    $form['algemene_instellingen'] = array(
        '#title' => t('Algemene instellingen'),
        '#type' => 'fieldset',
        '#description' => t('Verschillende algemene instellingen van SocialeKaart.care.'),
    );
    $form['zoek_instellingen'] = array(
        '#title' => t('Instellingen zoekfunctionaliteit'),
        '#type' => 'fieldset',
        '#description' => t('Instellingen met betrekking op de zoekfunctionaliteit.'),
    );
    $form['teksten'] = array(
        '#title' => t('Teksten en vertalingen'),
        '#type' => 'fieldset',
        '#description' => t('Verschillende teksten en vertalingen die niet via de standaard van Drupal opgepakt zijn (of kunnen zijn).'),
    );
    $form['email'] = array(
        '#title' => t('E-mail templates'),
        '#type' => 'fieldset',
        '#description' => t('Verschillende e-mail templates die door SocialeKaart.care gabruikt worden. Als je de templatie die je zoekt hier niet kan vinden moet je even kijken in de tabs onderaan de pagina <a href="/?q=admin/config/people/accounts">hier</a>.'),
    );
    $form['api'] = array(
        '#title' => t('API gegevens'),
        '#type' => 'fieldset',
        '#description' => t('Keys, id\'s en tokens van verschillende APIs.'),
    );
    $form['cron'] = array(
        '#title' => t('CRON'),
        '#type' => 'fieldset',
        '#description' => t('Schakel bepaalde CRON mogelijkheden aan of uit.'),
    );

    $form['algemene_instellingen']['gojira_show_error_page'] = array(
        '#title' => t('Show error page'),
        '#type' => 'select',
        '#options' => array(0 => 'no', 1 => 'yes'),
        '#default_value' => variable_get('gojira_show_error_page', 1),
        '#description' => t('Force a custom error page. /error.php'),
    );

    $form['cron']['cron_update_search_index_where_needed'] = array(
        '#title' => t('Updates the search index of changed nodes.'),
        '#type' => 'select',
        '#options' => array(0 => 'no', 1 => 'yes'),
        '#default_value' => variable_get('cron_update_search_index_where_needed', 1),
    );
    $form['cron']['cron_check_subscriptions'] = array(
        '#title' => t('Check subscriptions and disable account who are passed there payed period.'),
        '#type' => 'select',
        '#options' => array(0 => 'no', 1 => 'yes'),
        '#default_value' => variable_get('cron_check_subscriptions', 1),
    );
    $form['cron']['cron_restore_backup_locations'] = array(
        '#title' => t('Restores up to 200 locations from the backup table.'),
        '#type' => 'select',
        '#options' => array(0 => 'no', 1 => 'yes'),
        '#default_value' => variable_get('cron_restore_backup_locations', 0),
    );
    $form['algemene_instellingen']['gojira_subscription_month_price'] = array(
        '#title' => t('Subscription price per month'),
        '#type' => 'textfield',
        '#default_value' => variable_get('gojira_subscription_month_price', 5),
        '#description' => 'This value is just for show.<br />!!!If you change the price, don\'t forget to change the price in the following TEXT nodes: PAYED_TEXT_SUBSCRIBED_PAGE/NOT_PAYED_TEXT_SUBSCRIBED_PAGE/CANNOT_PAY_2_YEARS_AHEAD!!!',
    );
    $form['algemene_instellingen']['gojira_subscription_year_price'] = array(
        '#title' => t('Subscription price per year'),
        '#type' => 'textfield',
        '#default_value' => variable_get('gojira_subscription_year_price', 60),
        '#description' => '!!!If you change the price, don\'t forget to change the price in the following TEXT nodes: PAYED_TEXT_SUBSCRIBED_PAGE/NOT_PAYED_TEXT_SUBSCRIBED_PAGE/CANNOT_PAY_2_YEARS_AHEAD!!!',
    );
    $form['algemene_instellingen']['gojira_subscription_year_tax'] = array(
        '#title' => t('Subscription tax per year'),
        '#type' => 'textfield',
        '#default_value' => variable_get('gojira_subscription_year_tax', 12.60),
        '#description' => '!!!If you change the price, don\'t forget to change the price in the following TEXT nodes: PAYED_TEXT_SUBSCRIBED_PAGE/NOT_PAYED_TEXT_SUBSCRIBED_PAGE/CANNOT_PAY_2_YEARS_AHEAD!!!',
    );
    $form['algemene_instellingen']['gojira_subscription_year_total'] = array(
        '#title' => t('Subscription price total (inc. tax) per year'),
        '#type' => 'textfield',
        '#default_value' => variable_get('gojira_subscription_year_total', 72.60),
        '#description' => '!!!If you change the price, don\'t forget to change the price in the following TEXT nodes: PAYED_TEXT_SUBSCRIBED_PAGE/NOT_PAYED_TEXT_SUBSCRIBED_PAGE/CANNOT_PAY_2_YEARS_AHEAD!!!',
    );
    $form['algemene_instellingen']['gojira_check_coordinates_on_update_node'] = array(
        '#title' => t('Check coordinates on saving of nodes.'),
        '#type' => 'select',
        '#options' => array(0 => 'no', 1 => 'yes'),
        '#default_value' => variable_get('gojira_check_coordinates_on_update_node', 1),
        '#description' => 'If this is on the system will check the coordinates of each location on each save action. These coordinates are cached, so google will not be requested. This option is put in the system so you can disable this while importing locations. The import does this after a location is saved itself. For the system to work correct this needs to be put on ON, for the import to work correct, this needs to be put on OFF.'
    );
    $form['algemene_instellingen']['gojira_amount_calls_to_google'] = array(
        '#title' => t('Amount of calls to google each day.'),
        '#type' => 'textfield',
        '#default_value' => variable_get('gojira_amount_calls_to_google', 1),
        '#description' => 'This is the amount of calls the system can do to google to get coordinates for locations.'
    );
    $form['algemene_instellingen']['gojira_ideal_return_url'] = array(
        '#title' => t('Return url of the iDeal payment'),
        '#type' => 'textfield',
        '#default_value' => variable_get('gojira_ideal_return_url', ''),
        '#description' => 'Return url after a user has made a payment, must be absolute.'
    );
    $form['algemene_instellingen']['mailadres_information_inform_admin'] = array(
        '#title' => t('Informative e-mails to Admin'),
        '#type' => 'textfield',
        '#default_value' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
        '#description' => 'E-mail address to send informative e-mails about users doing stuff like adding locations.'
    );
    $form['algemene_instellingen']['mailadres_helpdesk'] = array(
        '#title' => t('Helpdesk e-mail address'),
        '#type' => 'textfield',
        '#default_value' => variable_get('mailadres_helpdesk', 'helpdesk@socialekaart.care')
    );
    $form['algemene_instellingen']['SUBSCRIPTION_PERIOD'] = array(
        '#title' => t('Aantal dagen voor een abonnement'),
        '#type' => 'textfield',
        '#default_value' => variable_get('SUBSCRIPTION_PERIOD', 365),
        '#description' => 'Het aantal dagen van de lengte van een abonnement.'
    );
    $form['algemene_instellingen']['mail_prefix'] = array(
        '#title' => 'Mail subject prefix',
        '#type' => 'textfield',
        '#default_value' => variable_get('mail_prefix', ''),
        '#description' => 'Prefix voor de HTML e-mail templates. Gebruik in demo & test omgevingen.'
    );
    $form['teksten']['meta_global_description'] = array(
        '#title' => t('Meta description on the homepage'),
        '#type' => 'textfield',
        '#default_value' => variable_get('meta_global_description')
    );
    $form['teksten']['meta_global_tags'] = array(
        '#title' => t('Meta tags on the homepage'),
        '#type' => 'textfield',
        '#default_value' => variable_get('meta_global_tags')
    );
    $form['teksten']['gojira_blacklist_search_words'] = array(
        '#title' => t('Blacklist of words'),
        '#type' => 'textarea',
        '#default_value' => variable_get('gojira_blacklist_search_words', 'de,het,een'),
        '#description' => 'Here you can save several words to be put on the blacklist for the searchindex.<br />The input should be: word1,word1,shit,bla,bloody,blacklist<br />When building the index of a location, these words will be left out. So new words in the blacklist will be removed from the searchindex after a reindex of the location(s). These words will also be stript from the search.'
    );
    $form['api']['postocde_nl_key'] = array(
        '#title' => t('Postcode.nl key'),
        '#type' => 'textfield',
        '#default_value' => variable_get('postocde_nl_key')
    );
    $form['api']['postocde_nl_secret'] = array(
        '#title' => t('Postcode.nl secret'),
        '#type' => 'textfield',
        '#default_value' => variable_get('postocde_nl_secret')
    );
    $form['api']['gojira_mailchimp_list_key'] = array(
        '#title' => t('Mailchimp list key.'),
        '#type' => 'textfield',
        '#default_value' => variable_get('gojira_mailchimp_list_key'),
        '#description' => 'This is the key of the mailchimp list new users subscribe to.'
    );
    $form['api']['mandrill_api_key'] = array(
        '#title' => t('Mandrill API key.'),
        '#type' => 'textfield',
        '#default_value' => variable_get('mandrill_api_key'),
        '#description' => 'This is the API key used for sending mails with Mandrill.'
    );
    $form['api']['MOLLIE_API_KEY'] = array(
        '#title' => t('Mollie API key'),
        '#type' => 'textfield',
        '#default_value' => variable_get('MOLLIE_API_KEY', ''),
    );
    $form['api']['mapbox_accesstoken'] = array(
        '#title' => t('Mapbox access token'),
        '#type' => 'textfield',
        '#default_value' => variable_get('mapbox_accesstoken', ''),
    );
    $form['api']['mapbox_projectid'] = array(
        '#title' => t('Mapbox project id'),
        '#type' => 'textfield',
        '#default_value' => variable_get('mapbox_projectid', ''),
    );
    $form['zoek_instellingen']['SEARCH_MAX_RESULT_AMOUNT'] = array(
        '#title' => t('Maximum of searchresults to display'),
        '#type' => 'textfield',
        '#default_value' => variable_get('SEARCH_MAX_RESULT_AMOUNT', 500),
    );
    $form['zoek_instellingen']['CENTER_COUNTRY_LATITUDE'] = array(
        '#title' => t('Default Latitude'),
        '#type' => 'textfield',
        '#default_value' => variable_get('CENTER_COUNTRY_LATITUDE', 52.3040498),
        '#description' => 'Deze latitude wordt gebruikt als er geen bekend is. Voorbeeld: <i>52.3040498</i>.'
    );
    $form['zoek_instellingen']['CENTER_COUNTRY_LONGITUDE'] = array(
        '#title' => t('Default Longitude'),
        '#type' => 'textfield',
        '#default_value' => variable_get('CENTER_COUNTRY_LONGITUDE', 5.6300203),
        '#description' => 'Deze longitude wordt gebruikt als er geen bekend is. Voorbeeld: <i>5.6300203</i>.'
    );
    $form['submit_down_under'] = array(
        '#type' => 'submit',
        '#value' => t('Submit'),
    );

    return $form;
}

function gojira_configuration_form_submit($form, &$form_state) {
    variable_set('gojira_blacklist_search_words', $_POST['gojira_blacklist_search_words']);
    variable_set('MOLLIE_API_KEY', $_POST['MOLLIE_API_KEY']);
    variable_set('SUBSCRIPTION_PERIOD', $_POST['SUBSCRIPTION_PERIOD']);
    variable_set('CENTER_COUNTRY_LATITUDE', $_POST['CENTER_COUNTRY_LATITUDE']);
    variable_set('CENTER_COUNTRY_LONGITUDE', $_POST['CENTER_COUNTRY_LONGITUDE']);
    variable_set('SEARCH_MAX_RESULT_AMOUNT', $_POST['SEARCH_MAX_RESULT_AMOUNT']);
    variable_set('gojira_amount_calls_to_google', $_POST['gojira_amount_calls_to_google']);
    variable_set('gojira_check_coordinates_on_update_node', $_POST['gojira_check_coordinates_on_update_node']);
    variable_set('gojira_mailchimp_list_key', $_POST['gojira_mailchimp_list_key']);
    variable_set('mandrill_api_key', $_POST['mandrill_api_key']);
    variable_set('postocde_nl_secret', $_POST['postocde_nl_secret']);
    variable_set('postocde_nl_key', $_POST['postocde_nl_key']);
    variable_set('meta_global_description', $_POST['meta_global_description']);
    variable_set('meta_global_tags', $_POST['meta_global_tags']);
    variable_set('mapbox_accesstoken', $_POST['mapbox_accesstoken']);
    variable_set('mapbox_projectid', $_POST['mapbox_projectid']);
    variable_set('mail_prefix', $_POST['mail_prefix']);
    variable_set('mailadres_information_inform_admin', $_POST['mailadres_information_inform_admin']);
    variable_set('mailadres_helpdesk', $_POST['mailadres_helpdesk']);
    variable_set('cron_update_search_index_where_needed', $_POST['cron_update_search_index_where_needed']);
    variable_set('cron_check_subscriptions', $_POST['cron_check_subscriptions']);
    variable_set('cron_restore_backup_locations', $_POST['cron_restore_backup_locations']);
    variable_set('gojira_ideal_return_url', $_POST['gojira_ideal_return_url']);
    variable_set('gojira_subscription_year_total', $_POST['gojira_subscription_year_total']);
    variable_set('gojira_subscription_year_tax', $_POST['gojira_subscription_year_tax']);
    variable_set('gojira_subscription_year_price', $_POST['gojira_subscription_year_price']);
    variable_set('gojira_subscription_month_price', $_POST['gojira_subscription_month_price']);
    variable_set('gojira_show_error_page', $_POST['gojira_show_error_page']);
    drupal_set_message(t('Saved all the settings.'), 'status');
}
