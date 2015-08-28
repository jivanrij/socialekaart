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
            'from_email' => 'jonathan@vanrij.org',
            'from_name' => 'Example Name',
            'to' => array(
                array(
                    'email' => 'jonathan@vanrij.org',
                    'name' => 'Recipient Name',
                    'type' => 'to'
                )
            ),
            'headers' => array('Reply-To' => 'jonathan@vanrij.org'),
            'important' => false,
            'track_opens' => null,
            'track_clicks' => null,
            'auto_text' => null,
            'auto_html' => null,
            'inline_css' => null,
            'url_strip_qs' => null,
            'preserve_recipients' => null,
            'view_content_link' => null,
            'bcc_address' => 'jonathan@vanrij.org',
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
            'merge_vars' => array(
                array(
                    'rcpt' => 'jonathan@vanrij.org',
                    'vars' => array(
                        array(
                            'name' => 'merge2',
                            'content' => 'merge2 content'
                        )
                    )
                )
            ),
            'tags' => array('password-resets'),
//            'subaccount' => 'customer-123',
            'google_analytics_domains' => array('example.com'),
            'google_analytics_campaign' => 'message.from_email@example.com',
            'metadata' => array('website' => 'www.example.com'),
            'recipient_metadata' => array(
                array(
                    'rcpt' => 'recipient.email@example.com',
                    'values' => array('user_id' => 123456)
                )
            ),
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
        $aInfo['subject'] = t('Invoice SocialeKaart.care');
        $aInfo['text'] = $sBody;
        $aInfo['to'][] = array(
            'email' => $send_to_address,
            'name' => $send_to_address,
            'type' => 'to'
        );
        if(trim(variable_get('mailadres_information_bcc', 'blijnder@gmail.com')) != ''){
            $aInfo['to'][] = array(
                'email' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                'name' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                'type' => 'bcc'
            );
        }
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
        if(trim(variable_get('mailadres_information_bcc', 'blijnder@gmail.com')) != ''){
            $aInfo['to'][] = array(
                'email' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                'name' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                'type' => 'bcc'
            );
        }

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
        if(trim(variable_get('mailadres_information_bcc', 'blijnder@gmail.com')) != ''){
            $aInfo['to'][] = array(
                'email' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                'name' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                'type' => 'bcc'
            );
        }
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
        if(trim(variable_get('mailadres_information_bcc', 'blijnder@gmail.com')) != ''){
            $aInfo['to'][] = array(
                'email' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                'name' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                'type' => 'bcc'
            );
        }
        $oMailer = new Mailer();
        $oMailer->send($aInfo);
    }

    /**
     * Thie e-mail will be send when a user get's disabled due to a unsubscription
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
        if(trim(variable_get('mailadres_information_bcc', 'blijnder@gmail.com')) != ''){
            $aInfo['to'][] = array(
                'email' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                'name' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                'type' => 'bcc'
            );
        }
        $oMailer = new Mailer();
        $oMailer->send($aInfo);
    }

    /**
     * Thie e-mail will be send when a group's subscription is going to end in 30 days from now
     * 
     * @param stdClass $user
     */
    public static function sendSubscriptionEndWarning($main_doctor) {

        $body = variable_get('gojira_subscription_expire_warning', '');
        $body = str_replace(array('%doctor%', '%url%'), array(helper::value($main_doctor, GojiraSettings::CONTENT_TYPE_USER_TITLE), '<a href="https://socialekaart.care/idealpay" title="Verleng uw abonnement.">Verleng uw abonnement.</a>'), $body);

        $aInfo['from_email'] = variable_get('site_mail', 'info@socialekaart.care');
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = t('Your subscription on SocialeKaart.care is going to expire within 30 days');
        $aInfo['html'] = $body;
        $aInfo['to'][] = array(
            'email' => $main_doctor->mail,
            'name' => $main_doctor->mail,
            'type' => 'to'
        );
        if(trim(variable_get('mailadres_information_bcc', 'blijnder@gmail.com')) != ''){
            $aInfo['to'][] = array(
                'email' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                'name' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                'type' => 'bcc'
            );
        }
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
        $body = str_replace(array('%doctor%', '%url%'), array(helper::value($main_doctor, GojiraSettings::CONTENT_TYPE_USER_TITLE), '<a href="https://socialekaart.care/idealpay" title="Verleng uw abonnement.">Verleng uw abonnement.</a>'), $body);

        $aInfo['from_email'] = variable_get('site_mail', 'info@socialekaart.care');
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = t('Your subscription on SocialeKaart.care is expired');
        $aInfo['text'] = $body;
        $aInfo['to'][] = array(
            'email' => $main_doctor->mail,
            'name' => $main_doctor->mail,
            'type' => 'to'
        );
        if(trim(variable_get('mailadres_information_bcc', 'blijnder@gmail.com')) != ''){
            $aInfo['to'][] = array(
                'email' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                'name' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                'type' => 'bcc'
            );
        }
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
        $aInfo['from_email'] = variable_get('site_mail', 'info@socialekaart.care');
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = t('A question from SocialeKaart.care from user ' . $oUser->name . ' about ' . $sTopic);
        $aInfo['text'] = 'User: ' . helper::value($oUser, GojiraSettings::CONTENT_TYPE_USER_TITLE) . ' with uid: ' . $oUser->uid . '<br /><br />' . $sQuestion;
        $aInfo['html'] = 'User: ' . helper::value($oUser, GojiraSettings::CONTENT_TYPE_USER_TITLE) . ' with uid: ' . $oUser->uid . '<br /><br />' . $sQuestion;
        $aInfo['to'][] = array(
            'email' => variable_get('mailadres_helpdesk', 'helpdesk@socialekaart.care'),
            'name' => variable_get('mailadres_helpdesk', 'helpdesk@socialekaart.care'),
            'type' => 'to'
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

        $aInfo['from_email'] = variable_get('site_mail', 'info@socialekaart.care');
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
    public static function sendLocationAddedByUserToAdmin($sBody, $sLocationTitle) {

        $message['subject'] = 'Er is een nieuwe locatie suggestie aangemaakt op socialekaart.care met de titel ' . $params['title'];

        $aInfo['from_email'] = variable_get('site_mail', 'info@socialekaart.care');
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = 'Locatie '.$sLocationTitle.' is door een gebruiker aangemaakt';
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
     * Informs the user that someone with the same username is tryed to login on SocialeKaart.care from HAWeb
     * In the case the accounts are not linked. 
     * 
     * In this e-mail the user get's a linkt to login with and merge the accounts.
     */
    public static function sendDoubleAccountWarning($sEmail) {

//        $sBody = <<<EOT
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
        
//        $sBody = variable_get('gojira_double_account_login_warning', '');
        

        $aInfo['from_email'] = variable_get('site_mail', 'info@socialekaart.care');
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = 'Inloggen op SocialeKaart.care vanuit HAweb?';
        $aInfo['text'] = variable_get('gojira_double_account_login_warning', '');
        $aInfo['to'][] = array(
            'email' => $sEmail,
            'name' => $sEmail,
            'type' => 'to'
        );
        if(trim(variable_get('mailadres_information_bcc', 'blijnder@gmail.com')) != ''){
            $aInfo['to'][] = array(
                'email' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                'name' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                'type' => 'bcc'
            );
        }
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
        $sAccount = 'http://socialekaart.care/user/' . $oUser->uid . '/edit';
        
        $sBody = <<<EOT
Er is een account aangemaakt door {$sTitle}.<br />
Van deze account moet het BIG nummer worden gecontrolleerd en dan kan hij geactiveerd worden.<br />
De gebruiker heeft het BIG nummer: {$sBig}<br />
Gebruikers backend: {$sAccount}<br />
Accounts activeren pagina: <a href="https://socialekaart.care/admin/config/system/gojiraactivateuser">https://socialekaart.care/admin/config/system/gojiraactivateuser</a></br>
<a href="https://www.bigregister.nl/zoeken/zoekenopbignummer/default.aspx">BIG controle</a><br />
EOT;

        $aInfo['from_email'] = variable_get('site_mail', 'info@socialekaart.care');
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
     * Informs the user he has logged on to SocialeKaart for the first time through SSO from haweb 
     * 
     */
    public static function newAccountThroughSSO($oUser) {
        
            $sEmail = $oUser->mail;
//            $sBody = <<<EOT
//Welkom bij SocialeKaart.care!
//                
//U bent zojuist voor het eerst ingelogd in SocialeKaart.care via uw Haweb.nl account.
//
//Vanaf nu heeft u onbeperkt toegang tot een uniek bestand met contactgegevens van zorgaanbieders in uw regio.
//
//Dit betekent dat u voortaan vanuit uw praktijk:
//- snel kunt zoeken op verwijsgegevens;
//- medische contactgegevens altijd en overal online beschikbaar hebt;
//- eenvoudig een 'eigen' bestand met verwijsgegevens kunt opbouwen;
//- samen met andere huisartsen, zorgaanbieders kunt voorzien van eigenschappen waarop ze vindbaar zijn;
//- voor het systeem onbekende zorgaanbieders kunt toevoegen.
//
//Voor onze gebruikers is het mogelijk een abonnement te nemen. Omdat u vanuit Haweb lid bent gewoorden kunt u voor de eerste 3 maanden gebruik maken van de extra functionaliteiten die behoren tot een abonnement. Ook krijgt u via uw lidmaatschap bij Haweb korting als u later besluit over te gaan op een abonnement.
//    
//Met een abonnement kunt u volledig reclame vrij gebruikmaken van de volgende functionaliteiten:
//- zoeken naar verwijsgegevens in het gehele land;
//- uw collega's en medewerkers laten werken met dezelfde informatie d.m.v. extra accounts;
//- u kunt meerdere praktijken toevoegen zodat u vanuit een andere plaats/praktijk kunt zoeken naar zorginstellingen.
//
//Als laatste willen wij u op de hoogste stellen dat we standaard beschikken over 117.000 zorginstellingen verspreid over het gehele land. Verschillende hiervan zult u kennen en sommige niet, ook zult zorginstellingen kennen wie wij niet in ons systeem hebben staan. Deze kunt u makkelijk en snel toevoegen via ons 'Zorgverlener toevoegen' formulier. Op deze manier kunt u uw eigen zorgkaart compleet krijgen en kunnen uw collega's hier ook direct profijt van hebben.
//                
//Wij wensen u veel plezier in het werken met SocialeKaart.care.
//Uw vragen en ideëen zijn meer dan welkom, stuur ze naar: info@socialekaart.care.
//
//Met vriendelijke groet,
//Het team van SocialeKaart.care
//EOT;
//            
//            $sBody = variable_get('new_account_through_sso', $sBody);

            $aInfo['from_email'] = variable_get('site_mail', 'info@socialekaart.care');
            $aInfo['from_name'] = 'SocialeKaart.care';
            $aInfo['subject'] = 'Welkom bij SocialeKaart.care!';
            $aInfo['text'] = variable_get('new_account_through_sso', '');
            $aInfo['to'][] = array(
                'email' => $sEmail,
                'name' => $sEmail,
                'type' => 'to'
            );
            if(trim(variable_get('mailadres_information_bcc', 'blijnder@gmail.com')) != ''){
                $aInfo['to'][] = array(
                    'email' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                    'name' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                    'type' => 'bcc'
                );
            }
            $oMailer = new Mailer();
            $oMailer->send($aInfo);

    }

    /**
     * Informs the user he has logged on to SocialeKaart for the first time through SSO from haweb 
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
        if(trim(variable_get('mailadres_information_bcc', 'blijnder@gmail.com')) != ''){
            $aInfo['to'][] = array(
                'email' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                'name' => variable_get('mailadres_information_bcc', 'blijnder@gmail.com'),
                'type' => 'bcc'
            );
        }

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
