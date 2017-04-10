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
     * Sends a e-mail to the admins of the site with a question of the user
     *
     * @param stdClass $main_doctor
     * @return boolean
     */
    public static function sendQuestion($oUser, $sQuestion, $sTopic) {
        $aInfo['from_email'] = 'no-reply@socialekaart.care';
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = 'Vraag van gebruiker ' . $oUser->name . ' over ' . $sTopic;

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

        if (true) { // let's skip mandril for now
            Mailer::sendMail(
                    $aInfo['to'][0]['email'], // to
                    $aInfo['from_email'], // from
                    $aInfo['subject'], // subject
                    variable_get('site_mail', 'info@socialekaart.care'), // reply to
                    $aInfo['html'], // content text/html
                    false, // attachment
                    $aInfo['to'][1]['email'], // bcc
                    true // html
                    );
        } else {
            $oMailer = new Mailer();
            $oMailer->send($aInfo);
        }
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
        $aInfo['subject'] = '[' . $title . '] - Verbetersuggestie voor locatie - ' . $type_of_problem . ' on ' . date('d-m-Y H:i');
        $aInfo['text'] = $body;
        $aInfo['to'][] = array(
            'email' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'name' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'type' => 'to'
        );

        if (true) { // let's skip mandril for now
            Mailer::sendMail(
                    $aInfo['to'][0]['email'], // to
                    $aInfo['from_email'], // from
                    $aInfo['subject'], // subject
                    variable_get('site_mail', 'info@socialekaart.care'), // reply to
                    $aInfo['text'], // content text/html
                    false, // attachment
                    false, // bcc
                    false // html
                    );
        } else {
            $oMailer = new Mailer();
            $oMailer->send($aInfo);
        }
    }

    /**
     * Sends the e-mail to the admin with the information that a user has added a location
     *
     * @return boolean
     */
    public static function sendLocationAddedByUserToAdmin($oLocation, $oUser) {

  $sBody = <<<EOT
{$oLocation->title}
https://socialekaart.care/node/{$oLocation->nid}/edit

Aangemaakt door gebruiker {$oUser->name}
https://socialekaart.care/user/{$oUser->uid}/edit
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
        if (true) { // let's skip mandril for now
            Mailer::sendMail(
                    $aInfo['to'][0]['email'], // to
                    $aInfo['from_email'], // from
                    $aInfo['subject'], // subject
                    variable_get('site_mail', 'info@socialekaart.care'), // reply to
                    $aInfo['text'], // content text/html
                    false, // attachment
                    false, // bcc
                    false // html
                    );
        } else {
            $oMailer = new Mailer();
            $oMailer->send($aInfo);
        }
    }

    /**
     * This e-mail informs the admin that there is a new account added and it need validation
     */
    public static function sendAccountNeedsValidation($oUser) {

        $sTitle = helper::value($oUser, GojiraSettings::CONTENT_TYPE_USER_TITLE);
        $sEmail = $oUser->mail;
        $sBig = helper::value($oUser, GojiraSettings::CONTENT_TYPE_BIG_FIELD);
        $sAccount = 'https://socialekaart.care/user/' . $oUser->uid . '/edit';

        $sUrl = "https://socialekaart.care/admin/config/system/gojiraactivateuser";

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

        if (true) { // let's skip mandril for now
            Mailer::sendMail(
                    $aInfo['to'][0]['email'], // to
                    $aInfo['from_email'], // from
                    $aInfo['subject'], // subject
                    variable_get('site_mail', 'info@socialekaart.care'), // reply to
                    $aInfo['html'], // content text/html
                    false, // attachment
                    false, // bcc
                    true // html
                    );
        } else {
            $oMailer = new Mailer();
            $oMailer->send($aInfo);
        }
    }

    /**
     * Informs the admin that there is a location added without coordinates
     *
     */
    public static function locationWithoutCoordinatesAdded($oLocation) {

//        $sUrl = user_pass_reset_url($oUser);
        $sAddress = Location::getAddressString($oLocation).'<br />'.$oLocation->title;

        $sCategory = Category::getCategoryName($oLocation);
        $iLocation = $oLocation->nid;

        $sBody = <<<EOT
Voor de volgende locatie heeft google geen coordinaten gevonden.<br />
Deze moeten opgezocht worden en toegevoegd worden.<br />
Deze locatie is van het type <b>{$sCategory}</b>.<br />
Let op! Praktijken van huisartsen moeten snel voorzien worden van coordinaten.<br />
<br />
<a href="https://socialekaart.care/?q=node/572388/edit&destination=admin/content">socialekaart.care/?q=node/572388/edit&destination=admin/content</a><br />
<br />
<a href="https://socialekaart.care/?q=admin/config/system/gojiratools&location_id={$iLocation}">socialekaart.care/?q=admin/config/system/gojiratools</a><br />
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

        if (true) { // let's skip mandril for now
            Mailer::sendMail(
                    $aInfo['to'][0]['email'], // to
                    $aInfo['from_email'], // from
                    $aInfo['subject'], // subject
                    variable_get('site_mail', 'info@socialekaart.care'), // reply to
                    $aInfo['html'], // content text/html
                    false, // attachment
                    $aInfo['to'][1]['email'], // bcc
                    true // html
                    );
        } else {
            $oMailer = new Mailer();
            $oMailer->send($aInfo);
        }

    }

    /**
     * Informs the admin that there is a group flagged as payed without payment info
     *
     */
    public static function checkSubscriptionFail($iGroup) {
        $aInfo['from_email'] = 'no-reply@socialekaart.care';
        $aInfo['from_name'] = 'SocialeKaart.care';
        $aInfo['subject'] = 'SocialeKaart.care ISSUE - group ' . $iGroup . ' has no payment information';
        $aInfo['text'] = 'SocialeKaart.care ISSUE - group ' . $iGroup . ' has no payment information, but is flagged as a group with a payed account. Perhaps this group is in it\'s free intro period.';
        $aInfo['to'][] = array(
            'email' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'name' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'type' => 'to'
        );


        if (true) { // let's skip mandril for now
            Mailer::sendMail(
                    $aInfo['to'][0]['email'], // to
                    $aInfo['from_email'], // from
                    $aInfo['subject'], // subject
                    variable_get('site_mail', 'info@socialekaart.care'), // reply to
                    $aInfo['text'], // content text/html
                    false, // attachment
                    false, // bcc
                    false // html
                    );
        } else {
            $oMailer = new Mailer();
            $oMailer->send($aInfo);
        }
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
                $sHtmlLinks .= '<a href="https://socialekaart.care/?loc='.$iLocation.'">'.$oLocation->title.' '.$oLocation->nid.'</a><br />';
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
        $aInfo['subject'] = 'Double locations reported by user '.$oUser->name.' on '.date('d-m-Y H:i');
        $aInfo['html'] = <<<EOT
User {$oUser->name} ({$oUser->uid}) thinks the following locations are double.<br />
{$sHtmlLinks}<br />
TODO: check them out and if they are double, optionally merge them.<br />
<br />
<a href="https://socialekaart.care/admin/config/system/doublelocations/?ids_from_mail={$ids}">Double location merge page</a>
EOT;
        $aInfo['to'][] = array(
            'email' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'name' => variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'),
            'type' => 'to'
        );


        if (true) { // let's skip mandril for now
            Mailer::sendMail(
                    $aInfo['to'][0]['email'], // to
                    $aInfo['from_email'], // from
                    $aInfo['subject'], // subject
                    variable_get('site_mail', 'info@socialekaart.care'), // reply to
                    $aInfo['html'], // content text/html
                    false, // attachment
                    false, // bcc
                    true // html
                    );
        } else {
            $oMailer = new Mailer();
            $oMailer->send($aInfo);
        }
    }

    /**
     * Add given e-mail to the mailchimp list
     *
     * @param String $sEmail
     */
    public static function subscribeToMailchimp($sEmail) {
        mailchimp_subscribe(variable_get('gojira_mailchimp_list_key'), $sEmail, null, false, false, 'html', true, true);
    }



    /**
     * Sends a HTML mail
     * Note: NOT trough Mandril
     *
     * @param string $to
     * @param string $from
     * @param string $subject
     * @param string $replyto
     * @param string $message
     * @param string $bcc Default false
     * @param boolean $bcc Default true
     * @return boolean
     */
    public static function sendMail($to, $from, $subject, $replyto, $message, $attachment = false, $bcc = false, $html = true) {

        require_once getcwd().'/'.drupal_get_path('module', 'gojira').'/inc/PHPMailer/class.phpmailer.php';

        $mail = new PHPMailer;

        $mail->From = $from;
        $mail->FromName = $from;

        $mail->addAddress($to);
        $mail->addReplyTo($replyto, "SocialeKaart.care");

        $mail->addBCC($bcc);

        $mail->isHTML($html);

        $mail->Subject = $subject;
        $mail->Body = $message;

        if(is_array($attachment)){
            $mail->AddAttachment($attachment['path'], $attachment['name']);
        }

        if (!$mail->send()) {
            return false;
        } else {
            return true;
        }
    }

}
