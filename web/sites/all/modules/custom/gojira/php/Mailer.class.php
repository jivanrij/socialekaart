<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Mailer {

    private $oMandrill = null;
    private $aMessage = Array();

    public function __construct() {
        $this->oMandrill = new Mandrill(variable_get('mandrill_api_key'));
        // set all the defaults
        $this->aMessage = array(
//            'html' => '<p>Example HTML content</p>',
//            'text' => 'Example text content',
            'subject' => 'example subject',
//            'from_email' => 'jonathan@vanrij.org',
            'from_name' => 'Example Name',
//            'to' => array(
//                array(
//                    'email' => 'jonathan@vanrij.org',
//                    'name' => 'Recipient Name',
//                    'type' => 'to'
//                )
//            ),
            'headers' => array('Reply-To' => ''),
            'important' => false,
            'track_opens' => null,
            'track_clicks' => null,
            'auto_text' => null,
            'auto_html' => null,
            'inline_css' => null,
            'url_strip_qs' => null,
            'preserve_recipients' => null,
            'view_content_link' => null,
//            'bcc_address' => 'jonathan@vanrij.org',
            'tracking_domain' => null,
            'signing_domain' => null,
            'return_path_domain' => null,
            'merge' => true,
            'merge_language' => 'mailchimp',
            'global_merge_vars' => array(
                array(
                    'name' => 'merge1',
                    'content' => 'merge1 content'
                )
            ),
//            'merge_vars' => array(
//                array(
//                    'rcpt' => 'jonathan@vanrij.org',
//                    'vars' => array(
//                        array(
//                            'name' => 'merge2',
//                            'content' => 'merge2 content'
//                        )
//                    )
//                )
//            ),
//            'tags' => array('password-resets'),
//            'subaccount' => 'customer-123',
//            'google_analytics_domains' => array('example.com'),
//            'google_analytics_campaign' => 'message.from_email@example.com',
//            'metadata' => array('website' => 'www.example.com'),
//            'recipient_metadata' => array(
//                array(
//                    'rcpt' => 'recipient.email@example.com',
//                    'values' => array('user_id' => 123456)
//                )
//            ),
//            'attachments' => array(
//                array(
//                    'type' => 'text/plain',
//                    'name' => 'myfile.txt',
//                    'content' => 'ZXhhbXBsZSBmaWxl'
//                )
//            ),
//            'images' => array(
//                array(
//                    'type' => 'image/png',
//                    'name' => 'IMAGECID',
//                    'content' => 'ZXhhbXBsZSBmaWxl'
//                )
//            )
        );
    }

    /**
     * Overwrites default settings for the mail if needed
     * 
     * @param string $sKey
     * @param array $aInfo
     */
    private function overwriteDefault($sKey, &$aInfo) {
        if (array_key_exists($sKey, $aInfo)) {
            $this->aMessage[$sKey] = $aInfo[$sKey];
        }
    }

    /**
     * Sends the e-mail
     * 
     * @param array $aInfo
     * @throws Mandrill_Error
     */
    public function send($aInfo = array()) {
        try {
            $this->overwriteDefault('from_email', $aInfo);
            $this->overwriteDefault('from_name', $aInfo);
            $this->overwriteDefault('subject', $aInfo);
            $this->overwriteDefault('text', $aInfo);
            $this->overwriteDefault('html', $aInfo);
            $this->overwriteDefault('to', $aInfo);
            $this->overwriteDefault('attachments', $aInfo);

            $result = $this->oMandrill->messages->send($this->aMessage, false, 'Main Pool');
        } catch (Mandrill_Error $e) {
            watchdog('Mandrill', 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * This email get's send when a invoice needs to be send after a payment
     * 
     * @param string $send_to_address
     * @param string $invoice_file
     * @param integer $ideal_id
     * @return boolean
     */
    public static function sendInvoiceOfNewSubscription($send_to_address, $invoice_file = null, $ideal_id) {

        $payment = Subscriptions::getPaymentInfo($ideal_id);

        $group = node_load($payment->gid);
        $groups_main_doctor_uid = helper::value($group, GojiraSettings::CONTENT_TYPE_ORIGINAL_DOCTOR, 'uid');
        $main_doctor = user_load($groups_main_doctor_uid);

        $sBody = variable_get('gojira_invoice_email', '');
        $sBody = str_replace(array('%invoice_id%', '%doctor%'), array($payment->increment, helper::value($main_doctor, GojiraSettings::CONTENT_TYPE_USER_TITLE)), $sBody);

        if ($invoice_file) {
            //$email->AddAttachment($invoice_file, 'Factuur_Socialekaart_' . $payment->increment . '.pdf');
            $attachment = file_get_contents($invoice_file);
            $attachment_encoded = base64_encode($attachment);
            $aInfo['attachments'] = array(
                array(
                    'content' => $attachment_encoded,
                    'type' => "application/pdf",
                    'name' => 'Factuur_Socialekaart_' . $payment->increment . '.pdf',
            ));
        }
        $aInfo['from_email'] = variable_get('site_mail', 'info@socialekaart.care');
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = t('Invoice SocialeKaart.care').' '.$payment->increment;
        $aInfo['text'] = $sBody;
        $aInfo['to'][] = array(
            'email' => $send_to_address,
            'name' => $send_to_address,
            'type' => 'to'
        );
        $aInfo['to'][] = array(
            'email' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'name' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'type' => 'bcc'
        );
        $oMailer = new Mailer();
        $oMailer->send($aInfo);
    }

    /**
     * This email will be send after a new employee user is made by the main doctor
     * 
     * @param stdClass $account
     */
    public static function sendWelcomeMailToEmployee($account) {
        $url = user_pass_reset_url($account);

        $group = Group::getGroupId($account->uid);
        $group = node_load($group);
        $groups_main_doctor_uid = helper::value($group, GojiraSettings::CONTENT_TYPE_ORIGINAL_DOCTOR, 'uid');

        $main_doctor = user_load($groups_main_doctor_uid);

        $body = variable_get('gojira_new_employee_email', '');
        $body = str_replace(array('%url%', '%doctor%', '%name%'), array($url, helper::value($main_doctor, GojiraSettings::CONTENT_TYPE_USER_TITLE), helper::value($account, GojiraSettings::CONTENT_TYPE_USER_TITLE)), $body);

        $aInfo['from_email'] = variable_get('site_mail', 'info@socialekaart.care');
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = t('SocialeKaart.care account created by @main_doctor', array('@main_doctor' => helper::value($main_doctor, GojiraSettings::CONTENT_TYPE_USER_TITLE)));
        $aInfo['text'] = $body;
        $aInfo['to'][] = array(
            'email' => $account->mail,
            'name' => $account->mail,
            'type' => 'to'
        );
        $aInfo['to'][] = array(
            'email' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'name' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'type' => 'bcc'
        );


        $oMailer = new Mailer();
        $oMailer->send($aInfo);
    }

    /**
     * This email will be send after a new employer user is made by the main doctor
     * 
     * @param stdClass $account
     */
    public static function sendWelcomeMailToEmployer($account) {
        $url = user_pass_reset_url($account);

        $group = Group::getGroupId($account->uid);
        $group = node_load($group);
        $groups_main_doctor_uid = helper::value($group, GojiraSettings::CONTENT_TYPE_ORIGINAL_DOCTOR, 'uid');

        $main_doctor = user_load($groups_main_doctor_uid);

        $body = variable_get('gojira_new_employer_email', '');
        $body = str_replace(array('%url%', '%doctor%', '%name%'), array($url, helper::value($main_doctor, GojiraSettings::CONTENT_TYPE_USER_TITLE), helper::value($account, GojiraSettings::CONTENT_TYPE_USER_TITLE)), $body);

        $aInfo['from_email'] = variable_get('site_mail', 'info@socialekaart.care');
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = t('SocialeKaart.care account created by @main_doctor', array('@main_doctor' => helper::value($main_doctor, GojiraSettings::CONTENT_TYPE_USER_TITLE)));
        $aInfo['text'] = $body;
        $aInfo['to'][] = array(
            'email' => $account->mail,
            'name' => $account->mail,
            'type' => 'to'
        );

        $aInfo['to'][] = array(
            'email' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'name' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'type' => 'bcc'
        );
        $oMailer = new Mailer();
        $oMailer->send($aInfo);
    }

    /**
     * Thie e-mail will be send when a user get's disabled due to a unsubscription
     * 
     * @param stdClass $user
     */
    public static function sendUnsubscribeMail($user) {

        $group = Group::getGroupId($user->uid);
        $group = node_load($group);
        $groups_main_doctor_uid = helper::value($group, GojiraSettings::CONTENT_TYPE_ORIGINAL_DOCTOR, 'uid');

        $main_doctor = user_load($groups_main_doctor_uid);

        $body = variable_get('gojira_unsubscribe_user', '');
        $body = str_replace(array('%doctor%', '%name%'), array(helper::value($main_doctor, GojiraSettings::CONTENT_TYPE_USER_TITLE), helper::value($user, GojiraSettings::CONTENT_TYPE_USER_TITLE)), $body);

        $aInfo['from_email'] = variable_get('site_mail', 'info@socialekaart.care');
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = t('Your account on SocialeKaart.care is deactivated');
        $aInfo['text'] = $body;
        $aInfo['to'][] = array(
            'email' => $user->mail,
            'name' => $user->mail,
            'type' => 'to'
        );
        $aInfo['to'][] = array(
            'email' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'name' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'type' => 'bcc'
        );
        $oMailer = new Mailer();
        $oMailer->send($aInfo);
    }

    /**
     * Thie e-mail will be send when a user get's enabled due to a subscription
     * 
     * @param stdClass $user
     */
    public static function sendSubscribeActivationMail($user) {

        $group = Group::getGroupId($user->uid);
        $group = node_load($group);
        $groups_main_doctor_uid = helper::value($group, GojiraSettings::CONTENT_TYPE_ORIGINAL_DOCTOR, 'uid');

        $main_doctor = user_load($groups_main_doctor_uid);

        $body = variable_get('gojira_subscribe_activate_user', '');
        $body = str_replace(array('%doctor%', '%name%'), array(helper::value($main_doctor, GojiraSettings::CONTENT_TYPE_USER_TITLE), helper::value($user, GojiraSettings::CONTENT_TYPE_USER_TITLE)), $body);

        $aInfo['from_email'] = variable_get('site_mail', 'info@socialekaart.care');
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = t('Your account on SocialeKaart.care is activated');
        $aInfo['text'] = $body;
        $aInfo['to'][] = array(
            'email' => $user->mail,
            'name' => $user->mail,
            'type' => 'to'
        );
        $aInfo['to'][] = array(
            'email' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'name' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'type' => 'bcc'
        );
        $oMailer = new Mailer();
        $oMailer->send($aInfo);
    }

    /**
     * Thie e-mail will be send when a group's subscription is going to end in 30 days from now
     * 
     * @param stdClass $user
     */
    public static function sendSubscriptionEndWarning($main_doctor) {

        $title = helper::value($main_doctor, GojiraSettings::CONTENT_TYPE_USER_TITLE);
        
        $body = variable_get('gojira_subscription_expire_warning', '');
        $body = str_replace(array('%title%'), array($title), $body);

        $aInfo['from_email'] = variable_get('site_mail', 'info@socialekaart.care');
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = t('Your subscription on SocialeKaart.care is going to expire within 30 days');
        $aInfo['html'] = $body;
        $aInfo['to'][] = array(
            'email' => $main_doctor->mail,
            'name' => $title,
            'type' => 'to'
        );
        $aInfo['to'][] = array(
            'email' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'name' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'type' => 'bcc'
        );
        $oMailer = new Mailer();
        $oMailer->send($aInfo);
    }

    /**
     * Sends the e-mail for a doctor to tell him the subscription is ended
     * 
     * @param stdClass $main_doctor
     * @return boolean
     */
    public static function sendSubscriptionEnded($main_doctor) {
        $body = variable_get('gojira_subscription_ended', '');
        $body = str_replace(array('%doctor%', '%url%'), array(helper::value($main_doctor, GojiraSettings::CONTENT_TYPE_USER_TITLE), '<a href="https://www.socialekaart.care/idealpay" title="Verleng uw abonnement.">Verleng uw abonnement.</a>'), $body);

        $aInfo['from_email'] = variable_get('site_mail', 'info@socialekaart.care');
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = t('Your subscription on SocialeKaart.care is expired');
        $aInfo['html'] = $body;
        $aInfo['to'][] = array(
            'email' => $main_doctor->mail,
            'name' => $main_doctor->mail,
            'type' => 'to'
        );
        $aInfo['to'][] = array(
            'email' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'name' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'type' => 'bcc'
        );
        $oMailer = new Mailer();
        $oMailer->send($aInfo);
    }

    /**
     * Sends a e-mail to the admins of the site with a question of the user
     * 
     * @param stdClass $main_doctor
     * @return boolean
     */
    public static function sendQuestion($oUser, $sQuestion, $sTopic) {
        $aInfo['from_email'] = 'no-reply@socialekaart.care';
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = 'Vraag van gebruiker ' . $oUser->name . ' over ' . $sTopic;
        $aInfo['text'] = 'User: ' . helper::value($oUser, GojiraSettings::CONTENT_TYPE_USER_TITLE) . ' with uid: ' . $oUser->uid . '<br /><br />' . $sQuestion;
        $aInfo['html'] = 'User: ' . helper::value($oUser, GojiraSettings::CONTENT_TYPE_USER_TITLE) . ' with uid: ' . $oUser->uid . '<br /><br />' . $sQuestion;
        $aInfo['to'][] = array(
            'email' => variable_get('site_mail', 'info@socialekaart.care'),
            'name' => variable_get('site_mail', 'info@socialekaart.care'),
            'type' => 'to'
        );
        $aInfo['to'][] = array(
            'email' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'name' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'type' => 'bcc'
        );
        $oMailer = new Mailer();
        $oMailer->send($aInfo);
    }

    /**
     * Informs the admin about a improvement suggestion from a user
     * 
     * @return boolean
     */
    public static function sendImproveSuggestion($title, $url, $user, $user_url, $type_of_problem, $info, $better_title = '') {

        $body = <<<EOT
Titel: {$title}
Zie locatie: {$url}

Informatie komt van gebruiker: {$user}
Zie gebruiker: {$user_url}

Type probleem: {$type_of_problem}
Extra informatie: {$info}
Better title: {$better_title}
EOT;

        $aInfo['from_email'] = 'no-reply@socialekaart.care';
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = '[' . $title . '] - Verbetersuggestie voor locatie - ' . $type_of_problem;
        $aInfo['text'] = $body;
        $aInfo['to'][] = array(
            'email' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'name' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'type' => 'to'
        );
        $oMailer = new Mailer();
        $oMailer->send($aInfo);
    }

    /**
     * Sends the e-mail to the admin with the information that a user has added a location
     * 
     * @return boolean
     */
    public static function sendLocationAddedByUserToAdmin($oLocation, $oUser) {

  $sBody = <<<EOT
{$oLocation->title}
https://www.socialekaart.care/node/{$oLocation->nid}/edit

Aangemaakt door gebruiker {$oUser->name}
https://www.socialekaart.care/user/{$oUser->uid}/edit
EOT;
        
        $aInfo['from_email'] = variable_get('site_mail', 'info@socialekaart.care');
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = 'Locatie '.$oLocation->title.' is door een gebruiker aangemaakt';
        $aInfo['text'] = $sBody;
        $aInfo['to'][] = array(
            'email' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'name' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'type' => 'to'
        );
        $oMailer = new Mailer();
        $oMailer->send($aInfo);
    }
    
    /**
     * This e-mail informs the admin that there is a new account added and it need validation
     */
    public static function sendAccountNeedsValidation($oUser) {

        $sTitle = helper::value($oUser, GojiraSettings::CONTENT_TYPE_USER_TITLE);
        $sEmail = $oUser->mail;
        $sBig = helper::value($oUser, GojiraSettings::CONTENT_TYPE_BIG_FIELD);
        $sAccount = 'https://www.socialekaart.care/user/' . $oUser->uid . '/edit';
        
        $sUrl = "https://www.socialekaart.care/admin/config/system/gojiraactivateuser";
        
        $sBody = <<<EOT
Er is een account aangemaakt door {$sTitle}.<br />
Van deze account moet het BIG nummer worden gecontrolleerd en dan kan hij <a href="{$sUrl}">geactiveerd</a> worden.<br />
De gebruiker heeft het BIG nummer: {$sBig}<br />
<br />
<a href="{$sAccount}">Gebruikers backend</a></br>
<a href="{$sUrl}">Accounts activeren pagina</a></br>
<a href="https://www.bigregister.nl/zoeken/zoekenopbignummer/default.aspx">BIG controle</a><br />
EOT;

        $aInfo['from_email'] = 'no-reply@socialekaart.care';
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = 'Account moet gevalideerd worden van '.$sTitle;
        $aInfo['html'] = $sBody;
        $aInfo['to'][] = array(
            'email' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'name' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'type' => 'to'
        );
        $oMailer = new Mailer();
        $oMailer->send($aInfo);
    }
    

    /**
     * Informs the user he has logged on to SocialeKaart for the first time and had has given a free period
     * 
     */
    public static function newAccountWithFreePeriod($oUser) {
        
            $sEmail = $oUser->mail;
  
            $aInfo['from_email'] = variable_get('site_mail', 'info@socialekaart.care');
            $aInfo['from_name'] = 'SocialeKaart.care';
            $aInfo['subject'] = 'Gefeliciteerd!';
            $aInfo['text'] = <<<EOT
U krijgt u van ons een gratis proefabonnement voor de duur van 3 maanden!

Met de standaard versie van Sociale Kaart kunt u eenvoudig en snel verwijzen naar zorgverleners in uw regio. Door kenmerken toe te voegen aan zorgverleners worden de zoekresultaten steeds relevanter.

Een (proef-)abonnement op de volledige versie geeft u daarnaast nog de volgende extra functionaliteiten:
- eenvoudig uw eigen sociale kaart samenstellen waarbinnen u kunt zoeken;
- zoeken naar verwijsgegevens specifiek in bepaalde steden of dorpen;
- zoeken in heel Nederland;
- uw collega's en medewerkers laten werken met dezelfde informatie;
- meerdere praktijken toevoegen zodat u ook vanuit andere praktijken kunt zoeken.

Mocht u na de proefperiode besluiten een abonnement af te sluiten kunt u dit simpel via de website doen. Een abonnement is 2 euro per maand en kan per jaar afgerekend worden.

Als laatste willen we u op de hoogte stellen dat we standaard beschikken over meer dan 115.000 zorgverleners verspreid over het gehele land. Mocht u toch zorgaanbieders kennen die ontbreken in SocialeKaart.care dan kunt u deze gemakkelijk en snel toevoegen via de link 'Zorgaanbieder toevoegen' in het menu. Op deze manier kunt u uw eigen sociale kaart compleet krijgen en hebben uw collega's hier direct profijt van.

We wensen u veel plezier in het werken met Sociale Kaart!

Met vriendelijke groet,
Het team van Sociale Kaart
EOT;
            $aInfo['to'][] = array(
                'email' => $sEmail,
                'name' => $sEmail,
                'type' => 'to'
            );
            $aInfo['to'][] = array(
                'email' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
                'name' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
                'type' => 'bcc'
            );
            $oMailer = new Mailer();
            $oMailer->send($aInfo);

    }

    /**
     * get's send to an account that has been activated by the admin through the backend tools page. Mosly used for users who register through the frontend, not HAweb.
     * 
     */
    public static function accountActivatedByAdmin($oUser) {
        
        $sUrl = user_pass_reset_url($oUser);
        $sBody = variable_get('account_activated_by_admin', '');

        $aInfo['from_email'] = variable_get('site_mail', 'info@socialekaart.care');
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = t('SocialeKaart.care account activated');
        $aInfo['html'] = str_replace('%url%', '<a href="'.$sUrl.'">'.$sUrl.'</a>', $sBody);
        $aInfo['to'][] = array(
            'email' => $oUser->mail,
            'name' => $oUser->mail,
            'type' => 'to'
        );

        $oMailer = new Mailer();
        $oMailer->send($aInfo);
    }
    
    /**
     * Informs the admin that there is a location added without coordinates
     * 
     */
    public static function locationWithoutCoordinatesAdded($oLocation) {
        
//        $sUrl = user_pass_reset_url($oUser);
        $sAddress = Location::getAddressString($oLocation);
        
        $sCategory = Category::getCategoryName($oLocation);
        $iLocation = $oLocation->nid;
        
        $sBody = <<<EOT
Voor de volgende locatie heeft google geen coordinaten gevonden.<br />
Deze moeten opgezocht worden en toegevoegd worden.<br />
Deze locatie is van het type <b>{$sCategory}</b>.<br />
Let op! Praktijken van huisartsen moeten snel voorzien worden van coordinaten.<br />
<br />
<a href="https://www.socialekaart.care/?q=node/572388/edit&destination=admin/content">socialekaart.care/?q=node/572388/edit&destination=admin/content</a><br />
<br />
<a href="https://www.socialekaart.care/?q=admin/config/system/gojiratools&location_id={$iLocation}">socialekaart.care/?q=admin/config/system/gojiratools</a><br />
Address:<br />
{$sAddress}<br />
<br />
<br />
<b>TODO: A) Zoek de coordinaten op via google maps & voeg ze toe via de tools page, B) publiceer daarna de praktijk/locatie.</b>
EOT;

        $aInfo['from_email'] = 'no-reply@socialekaart.care';
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = 'SocialeKaart.care No Coordinates found! - '.$iLocation;
        $aInfo['html'] = $sBody;
        $aInfo['to'][] = array(
            'email' => variable_get('site_mail', 'info@socialekaart.care'),
            'name' => variable_get('site_mail', 'info@socialekaart.care'),
            'type' => 'to'
        );
        $aInfo['to'][] = array(
            'email' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'name' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'type' => 'bcc'
        );

        $oMailer = new Mailer();
        $oMailer->send($aInfo);

    }
    
    /**
     * Informs the admin that there is a group flagged as payed without payment info
     * 
     */
    public static function checkSubscriptionFail($iGroup) {
        $aInfo['from_email'] = 'no-reply@socialekaart.care';
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = 'SocialeKaart.care ISSUE - group ' . $iGroup . ' has no payment information';
        $aInfo['text'] = 'SocialeKaart.care ISSUE - group ' . $iGroup . ' has no payment information, but is flagged as a group with a payed account.';
        $aInfo['to'][] = array(
            'email' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'name' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'type' => 'to'
        );


        $oMailer = new Mailer();
        $oMailer->send($aInfo);
    }
    
    /**
     * Informs the admin that a user thinks there are double locations
     */
    public static function informAdminAboutDoubleLocations($sLocationIds) {
        
        $aLocations = explode(',',$sLocationIds);
        
        $sHtmlLinks = '';
        $ids = array();
        foreach($aLocations as $iLocation){
            if(is_numeric($iLocation)){
                $oLocation = node_load($iLocation);
                $sHtmlLinks .= '<a href="https://www.socialekaart.care/?loc='.$iLocation.'">'.$oLocation->title.' '.$oLocation->nid.'</a><br />';
                $ids[] = $iLocation;
            }
        }
        
        $ids = implode(',', $ids);
        //auto_login_url_create($user->uid, '/?q=loginlink/but/fake', true)
        
        if($sHtmlLinks == ''){
            $sHtmlLinks = 'mmm... no numeric ids or nodes found...';
        }
        
        global $user;
        $oUser = user_load($user->uid);
        $aInfo['from_email'] = 'no-reply@socialekaart.care';
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = 'SocialeKaart.care ISSUE - double locations reported by user '.$oUser->name;
        $aInfo['html'] = <<<EOT
User {$oUser->name} ({$oUser->uid}) thinks the following locations are double.<br />
{$sHtmlLinks}<br />
TODO: check them out and if they are double, optionally merge them.<br />
<br />
<a href="https://www.socialekaart.care/admin/config/system/doublelocations/?ids_from_mail={$ids}">Double location merge page</a>
EOT;
        $aInfo['to'][] = array(
            'email' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'name' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'type' => 'to'
        );


        $oMailer = new Mailer();
        $oMailer->send($aInfo);
    }

    /**
     * Add given e-mail to the mailchimp list
     * 
     * @param String $sEmail
     */
    public static function subscribeToMailchimp($sEmail) {
        mailchimp_subscribe(variable_get('gojira_mailchimp_list_key'), $sEmail, null, false, false, 'html', true, true);
    }

}
