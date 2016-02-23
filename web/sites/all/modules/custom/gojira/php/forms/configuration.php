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

    $form['cron']['cron_remove_unlinked_tax_terms'] = array(
        '#title' => t('Remove all the unused taxonomy terms (labels).'),
        '#type' => 'select',
        '#options' => array(0 => 'no', 1 => 'yes'),
        '#default_value' => variable_get('cron_remove_unlinked_tax_terms', 1),
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
        '#description' => 'Here you can save several words to be put on the blacklist for the searchindex.<br />The input should be: word1,word1,shit,bla,bloody,blacklist<br />When building the index of a location, these words will be left out. So new words in the blacklist will be removed from the searchindex after a reindex of the location(s).'
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
    $form['api']['IDEAL_MERCHANT_ID'] = array(
        '#title' => t('iDeal merchant ID'),
        '#type' => 'textfield',
        '#default_value' => variable_get('IDEAL_MERCHANT_ID', '2836'),
    );
    $form['api']['IDEAL_MERCHANT_KEY'] = array(
        '#title' => t('iDeal merchant key'),
        '#type' => 'textfield',
        '#default_value' => variable_get('IDEAL_MERCHANT_KEY', 'aOEUoPH'),
    );
    $form['api']['IDEAL_MERCHANT_SECRET'] = array(
        '#title' => t('iDeal merchant secret'),
        '#type' => 'textfield',
        '#default_value' => variable_get('IDEAL_MERCHANT_SECRET', 'wt0OZLRYHkZiln4dmftgker3k'),
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
    $form['email']['gojira_invoice_email'] = array(
        '#title' => t('Invoice e-mail template'),
        '#type' => 'textarea',
        '#default_value' => variable_get('gojira_invoice_email'),
        '#description' => 'TEXT e-mail<br />Invoice e-mail template.<br />
  You can use the following replacement tags:<br />
  %doctor% <- The name of the doctor.<br />
  %invoice_id% <- Invoice id.'
    );
    $form['email']['gojira_new_employee_email'] = array(
        '#title' => t('New Employee e-mail template'),
        '#type' => 'textarea',
        '#default_value' => variable_get('gojira_new_employee_email'),
        '#description' => 'TEXT e-mail<br />You can use the following replacement tags:<br />
  %url% <- login link for the passwordreset<br />
  %doctor% <- name of the doctor<br />
  %name% <- name of new user<br />'
    );
    $form['email']['gojira_subscription_expire_warning'] = array(
        '#title' => t('Subscription is about to expire warning'),
        '#type' => 'textarea',
        '#default_value' => variable_get('gojira_subscription_expire_warning'),
        '#description' => 'HTML e-mail<br />This is the template of the e-mail that get\'s send when the subscription is going to expire in 30 days from now. This template it HTML.<br />
        %title% <- name of the doctor'
    );
    $form['email']['gojira_subscription_ended'] = array(
        '#title' => t('Subscription is ended'),
        '#type' => 'textarea',
        '#default_value' => variable_get('gojira_subscription_ended'),
        '#description' => 'TEXT e-mail<br />This is the template of the e-mail that get\'s send to a doctor when the subscription ended. This template it HTML.<br />
  %doctor% <- name of the doctor<br />'
    );
    $form['email']['gojira_new_employer_email'] = array(
        '#title' => t('New Employer e-mail template'),
        '#type' => 'textarea',
        '#default_value' => variable_get('gojira_new_employer_email'),
        '#description' => 'TEXT e-mail<br />You can use the following replacement tags:<br />
  %url% <- login link for the passwordreset<br />
  %doctor% <- name of the doctor<br />
  %name% <- name of new user<br />'
    );
    $form['email']['gojira_unsubscribe_user'] = array(
        '#title' => t('Unsbscribe user e-mail template'),
        '#type' => 'textarea',
        '#default_value' => variable_get('gojira_unsubscribe_user'),
        '#description' => 'TEXT e-mail<br />E-mail template of the e-mail that get\'s send when a user get\'s unsubscribed.<br />
  You can use the following replacement tags:<br />
  %doctor% <- name of the doctor<br />
  %name% <- name of new user<br />'
    );
    $form['email']['gojira_subscribe_activate_user'] = array(
        '#title' => t('Activate subscribed user'),
        '#type' => 'textarea',
        '#default_value' => variable_get('gojira_subscribe_activate_user'),
        '#description' => 'TEXT e-mail<br />E-mail template of the e-mail that get\'s send when a user get\'s activated after a subscription after the account got downgraded.<br />
  You can use the following replacement tags:<br />
  %doctor% <- name of the doctor<br />
  %name% <- name of new user<br />'
    );

//    $sBody = <<<EOT
//Beste,
//
//Deze e-mail ontvangt u omdat wij denken dat u zojuist heeft geprobeerd in te loggen op SocialeKaart.care vanuit uw Haweb.nl omgeving.
//
//We hebben alleen geconstateerd dat u voor beide omgevingen 2 losse accounts heeft met hetzelfde e-mailadres. Wilt u toch graag met 1 en hetzelfde account inloggen op beide omgevingen? Dan kunt u het volgende doen:
//1. Pas uw e-mailadres aan van uw SocialeKaart.care account. 
//2. Log daarna uit uit SocialeKaart.care en log in in Haweb.nl.
//3. Klik nu in Haweb.nl op de link naar SocialeKaart.care, er zal nu een nieuwe gekoppelde account in SocialeKaart.care aangemaakt worden.
//4. Wij kunnen dan als u wilt alle gegevens van uw originele SocialeKaart.care account overzetten naar uw nieuwe account. Als u ons een e-mail stuurd met dit verzoek zetten wij voor u graag deze gegevens over. U kunt ons met dit verzoek e-mailen op info@socialekaart.care.
//
//We hopen u hiermee voldoende te hebben ingelicht. Als u hier nog vragen over hebt horen wij dit graag.
//
//Met vriendelijke groet,
//Het team van SocialeKaart.care
//info@socialekaart.care
//EOT;

    $form['email']['gojira_double_account_login_warning'] = array(
        '#title' => t('Double account warning'),
        '#type' => 'textarea',
        '#default_value' => variable_get('gojira_double_account_login_warning', ''),
        '#description' => 'TEXT e-mail<br />This e-mail get\'s send to a user when he try\'s to login from Haweb and there is allready an account with that e-mailadres in SocialeKaart.care<br />'
    );

    $form['email']['account_activated_by_admin'] = array(
        '#title' => t('Account activated by admin'),
        '#type' => 'textarea',
        '#default_value' => variable_get('account_activated_by_admin', 'Your account has been activated'),
        '#description' => 'HTML e-mail<br />This mail is send to a user when the admin activates his/her account.<br />
          You can use the following replacement tags:<br />
          %url% <- the url of the password reset link.<br />'
    );

    $form['submit_down_under'] = array(
        '#type' => 'submit',
        '#value' => t('Submit'),
    );

    return $form;
}

function gojira_configuration_form_submit($form, &$form_state) {
    variable_set('gojira_unsubscribe_user', $_POST['gojira_unsubscribe_user']);
    variable_set('gojira_subscribe_activate_user', $_POST['gojira_subscribe_activate_user']);
    variable_set('gojira_new_employer_email', $_POST['gojira_new_employer_email']);
    variable_set('gojira_new_employee_email', $_POST['gojira_new_employee_email']);
    variable_set('gojira_invoice_email', $_POST['gojira_invoice_email']);
    variable_set('gojira_blacklist_search_words', $_POST['gojira_blacklist_search_words']);
    variable_set('IDEAL_MERCHANT_ID', $_POST['IDEAL_MERCHANT_ID']);
    variable_set('IDEAL_MERCHANT_KEY', $_POST['IDEAL_MERCHANT_KEY']);
    variable_set('IDEAL_MERCHANT_SECRET', $_POST['IDEAL_MERCHANT_SECRET']);
    variable_set('SUBSCRIPTION_PERIOD', $_POST['SUBSCRIPTION_PERIOD']);
    variable_set('CENTER_COUNTRY_LATITUDE', $_POST['CENTER_COUNTRY_LATITUDE']);
    variable_set('CENTER_COUNTRY_LONGITUDE', $_POST['CENTER_COUNTRY_LONGITUDE']);
    variable_set('SEARCH_MAX_RESULT_AMOUNT', $_POST['SEARCH_MAX_RESULT_AMOUNT']);
    variable_set('gojira_amount_calls_to_google', $_POST['gojira_amount_calls_to_google']);
    variable_set('gojira_subscription_expire_warning', $_POST['gojira_subscription_expire_warning']);
    variable_set('gojira_subscription_ended', $_POST['gojira_subscription_ended']);
    variable_set('gojira_check_coordinates_on_update_node', $_POST['gojira_check_coordinates_on_update_node']);
    variable_set('gojira_mailchimp_list_key', $_POST['gojira_mailchimp_list_key']);
    variable_set('mandrill_api_key', $_POST['mandrill_api_key']);
    variable_set('postocde_nl_secret', $_POST['postocde_nl_secret']);
    variable_set('postocde_nl_key', $_POST['postocde_nl_key']);
    variable_set('meta_global_description', $_POST['meta_global_description']);
    variable_set('meta_global_tags', $_POST['meta_global_tags']);
    variable_set('mapbox_accesstoken', $_POST['mapbox_accesstoken']);
    variable_set('mapbox_projectid', $_POST['mapbox_projectid']);
    variable_set('account_activated_by_admin', $_POST['account_activated_by_admin']);
    variable_set('gojira_double_account_login_warning', $_POST['gojira_double_account_login_warning']);
    variable_set('mailadres_information_inform_admin', $_POST['mailadres_information_inform_admin']);
    variable_set('mailadres_helpdesk', $_POST['mailadres_helpdesk']);
    variable_set('cron_remove_unlinked_tax_terms', $_POST['cron_remove_unlinked_tax_terms']);
    variable_set('cron_update_search_index_where_needed', $_POST['cron_update_search_index_where_needed']);
    variable_set('cron_check_subscriptions', $_POST['cron_check_subscriptions']);
    variable_set('cron_restore_backup_locations', $_POST['cron_restore_backup_locations']);
    variable_set('gojira_ideal_return_url', $_POST['gojira_ideal_return_url']);
    variable_set('gojira_subscription_year_total', $_POST['gojira_subscription_year_total']);
    variable_set('gojira_subscription_year_tax', $_POST['gojira_subscription_year_tax']);
    variable_set('gojira_subscription_year_price', $_POST['gojira_subscription_year_price']);
    variable_set('gojira_subscription_month_price', $_POST['gojira_subscription_month_price']);    
    drupal_set_message(t('Saved all the settings.'), 'status');
}
