<?php
class MailerHtml{

    public static function sendUserInformMapmoderatorUseraction($recieverMail, $userModel, $locationModel, $locationsetModel, $action){

        $username = $userModel->name;
        $locationsetTitle = $locationsetModel->title;
        $locationTitle = $locationModel->title;
        $locationUrl = 'http://' . $_SERVER['SERVER_NAME'] . $locationModel->url;
        $userMail = $userModel->mail;

        if($action == 'add'){
            $action = 'toegevoegd';
            $subject = sprintf('Gebruiker %s voegde %s toe aan %s op %s ', $username, $locationTitle, $locationsetTitle, date('d-m-Y'));
        } else {
            $action = 'verwijderd';
            $subject = sprintf('Gebruiker %s verwijderde %s van %s op %s ', $username, $locationTitle, $locationsetTitle, date('d-m-Y'));
        }

        $html = <<<EOT
<p>Beste kaartbeheerder,</p>
<p>De gebruiker <i>{$username}</i> heeft op de kaart <i>{$locationsetTitle}</i> de zorgverlener <i>{$locationTitle}</i> {$action}.</p>
<table class="btn-primary" cellpadding="0" cellspacing="0" border="0">
<tr>
<td>
  <a href="{$locationUrl}"><i>{$locationTitle}</i> op SocialeKaart.care.</a>
</td>
</tr>
</table>
<p>U kunt de gebruiker ook direct een e-mail sturen op het volgende adres: <a href="mailto:{$userMail}">{$userMail}</a>.</p>
<p>Als u hier verder nog vragen over hebt horen wij dit graag. U kunt ons bereiken op het e-mailadres <a href="mailto:info@socialekaart.care">info@socialekaart.care</a>.</p>
EOT;

        $html = self::wrapContent($html);

        self::sendMail(
            $recieverMail, // to
            'no-reply@socialekaart.care', // from
            $subject, // subject
            variable_get('site_mail', 'info@socialekaart.care'), // reply to
            $html, // content text/html
            false // attachment
        );
    }

    /**
     * This email get's send when a invoice needs to be send after a payment
     *
     * @param string $send_to_address
     * @param string $invoice_file
     * @param integer $ideal_id
     * @return boolean
     */
    public static function sendUserInvoiceOfNewSubscription($send_to_address, $invoice_file = null, $ideal_id) {

        $payment = Subscriptions::getPaymentInfo($ideal_id);

        $group = node_load($payment->gid);
        $groups_main_doctor_uid = helper::value($group, GojiraSettings::CONTENT_TYPE_ORIGINAL_DOCTOR, 'uid');
        $main_doctor = user_load($groups_main_doctor_uid);

        $sBody = <<<EOT
        <p>Beste %s,</p>
        <p>In de bijlage vindt u uw factuur (%s) van het door u betaalde abonnement.</p>
        <p>We wensen u veel gebruikersgemak met de nieuwe functionaliteiten.</p>
        <p>Als u nog vragen heeft, zien we uw bericht graag tegemoet. U kunt reageren op deze mail.</p>
EOT;

        $sBody = sprintf($sBody, helper::value($main_doctor, GojiraSettings::CONTENT_TYPE_USER_TITLE), $payment->increment);
        $sBody = self::wrapContent($sBody);

        $attachment['path'] = $invoice_file;
        $attachment['name'] = 'Factuur_Socialekaart_' . $payment->increment . '.pdf';

        Mailer::sendMail(
                $send_to_address, // to
                variable_get('site_mail', 'info@socialekaart.care'), // from
                t('Invoice SocialeKaart.care').' '.$payment->increment, // subject
                variable_get('site_mail', 'info@socialekaart.care'), // reply to
                $sBody, // content
                $attachment
            );

    }

    /**
     * Thie e-mail will be send when a group's subscription is going to end in 30 days from now
     *
     * @param stdClass $user
     */
    public static function sendUserSubscriptionEndWarning($main_doctor) {
        return;
        $title = helper::value($main_doctor, GojiraSettings::CONTENT_TYPE_USER_TITLE);

        $sBody = <<<EOT
        <p>Beste %s,</p>
        <p>We willen u informeren dat binnen 30 dagen uw abonnement op SocialeKaart.care verloopt. Wilt u gebruik blijven maken van alle functionaliteiten om snel en betrouwbaar te kunnen verwijzen dan kunt u uw abonnement verlengen. Ga daarvoor in het menu van SocialeKaart.care naar Abonneren. Volg daar de benodigde stappen.</p>
EOT;

        $sBody = sprintf($sBody, $title);
        $sBody = self::wrapContent($sBody);

        Mailer::sendMail(
            $main_doctor->mail, // to
            variable_get('site_mail', 'info@socialekaart.care'), // from
            t('Your subscription on SocialeKaart.care is going to expire within 30 days'), // subject
            variable_get('site_mail', 'info@socialekaart.care'), // reply to
            $sBody, // content
            false // attachment
        );
    }

    /**
     * This e-mail get's send when a user is registered
     *
     * @param stdClass $user
     */
    public static function sendUserAccountWaitingForActivation($oUser) {

        $sBody = <<<EOT
            <p>Beste %s,
            <p>Bedankt dat u zich heeft geregistreerd op SocialeKaart.care. De aanvraag voor een account wacht momenteel op goedkeuring. Zodra de aanvraag is goedgekeurd ontvangt u een e-mail met daarin inloginformatie, hoe het wachtwoord kan worden gewijzigd en andere details.</p>
EOT;

        $sBody = sprintf($sBody, helper::value($oUser, GojiraSettings::CONTENT_TYPE_USER_TITLE));
        $sBody = self::wrapContent($sBody);

        Mailer::sendMail(
            $oUser->mail, // to
            variable_get('site_mail', 'info@socialekaart.care'), // from
            'SocialeKaart.care - Wachtend op de goedkeuring van de beheerder', // subject
            variable_get('site_mail', 'info@socialekaart.care'), // reply to
            $sBody, // content
            false // attachment
        );
    }

    /**
     * Sends the e-mail for a doctor to tell him the subscription is ended
     *
     * @param stdClass $main_doctor
     * @return boolean
     */
    public static function sendUserSubscriptionEnded($main_doctor) {
        return;
        $sBody = <<<EOT
        <p>Beste %s,</p>
        <p>Helaas is uw abonnement op SocialeKaart.care verlopen.</p>
        <p>Graag zetten we de voordelen van een abonnement op SocialeKaart.care voor u op een rijtje. U kunt:</p>
        <ul>
            <li>een sociale kaart voor uw eigen praktijk samenstellen en hierbinnen zoeken;</li>
            <li>meerdere praktijken aanmaken om deze als uitgangspunt voor het zoeken te gebruiken.</li>
        </ul>
        <p>Wilt u gebruik blijven maken van al deze functionaliteiten om snel en betrouwbaar te kunnen verwijzen? Dan kunt u vanuit het menu makkelijk uw abonnement verlengen.</p>
EOT;

        $sBody = sprintf($sBody, helper::value($main_doctor, GojiraSettings::CONTENT_TYPE_USER_TITLE));
        $sBody = self::wrapContent($sBody);

        Mailer::sendMail(
                $main_doctor->mail, // to
                variable_get('site_mail', 'info@socialekaart.care'), // from
                t('Your subscription on SocialeKaart.care is expired'), // subject
                variable_get('site_mail', 'info@socialekaart.care'), // reply to
                $sBody, // content
                false // attachment
            );

    }

    /**
     * Informs the user he has logged on to SocialeKaart for the first time and had has given a free period
     *
     */
    public static function sendUserNewAccountWithFreePeriod($oUser) {

        $sBody = <<<EOT
        <p>U krijgt u van ons een gratis proefabonnement op de plus versie voor de duur van 3 maanden!</p>
        <p>Met de standaard versie van SocialeKaart.care kunt u eenvoudig en snel verwijzen naar zorgverleners in uw regio. Door kenmerken toe te voegen aan zorgverleners worden de zoekresultaten steeds relevanter.</p>
        <p>Een (proef-)abonnement op de plus versie geeft u daarnaast nog de volgende extra functionaliteiten:</p>
        <ul>
            <li>eenvoudig uw eigen sociale kaart samenstellen waarbinnen u kunt zoeken;</li>
            <li>meerdere praktijken toevoegen zodat u ook vanuit andere praktijken kunt zoeken.</li>
        </ul>
        <p>Mocht u na de proefperiode besluiten een abonnement af te sluiten kunt u dit simpel via de website doen. Een abonnement is 5 euro per maand en kan per jaar afgerekend worden.</p>
        <p>Als laatste willen we u op de hoogte stellen dat we standaard beschikken over meer dan 115.000 zorgverleners verspreid over het gehele land. Mocht u toch zorgaanbieders kennen die ontbreken in SocialeKaart.care dan kunt u deze gemakkelijk en snel toevoegen via de link 'Zorgaanbieder toevoegen' in het menu. Op deze manier kunt u uw eigen sociale kaart compleet krijgen en hebben uw collega's hier direct profijt van.</p>
        <p>We wensen u veel plezier in het werken met SocialeKaart.care!</p>
EOT;

        $sBody = self::wrapContent($sBody);

        Mailer::sendMail(
                $oUser->mail, // to
                variable_get('site_mail', 'info@socialekaart.care'), // from
                'Gratis proefperiode van 3 maanden op de plus versie van SocialeKaart.care', // subject
                variable_get('site_mail', 'info@socialekaart.care'), // reply to
                $sBody, // content
                false // attachment
            );
    }

    /**
     * get's send to an account that has been activated by the admin through the backend tools page. Mosly used for users who register through the frontend, not HAweb.
     *
     */
    public static function sendUserAccountActivatedByAdmin($oUser) {

        $sBody = <<<EOT
        <p>Uw Sociale Kaart account is geactiveerd!</p>
        <p>Vanaf nu heeft u onbeperkt toegang tot een uniek bestand met contactgegevens van zorgaanbieders in uw regio.</p>
        <p>Dit betekent dat u voortaan vanuit uw praktijk:</p>
        <ul>
            <li>snel kunt zoeken op verwijsgegevens;</li>
            <li>medische contactgegevens altijd en overal online beschikbaar hebt;</li>
            <li>samen met andere huisartsen, zorgaanbieders kunt voorzien van kenmerken waarop deze vindbaar zijn;</li>
            <li>voor het systeem onbekende zorgaanbieders kunt toevoegen.</li>
        </ul>
        <p>Ook is het voor u mogelijk een abonnement te nemen. Als u besluit dit te doen kunt u volledig gebruikmaken van de volgende functionaliteiten:</p>
        <ul>
            <li>eenvoudig een eigen sociale kaart met verwijsgegevens opbouwen;</li>
            <li>u kunt meerdere praktijken toevoegen zodat u vanuit een andere plaats/praktijk kunt zoeken naar zorginstellingen.</li>
        </ul>
        <p>Het enige wat u nog hoeft te doen is het opgeven van een wachtwoord om in te loggen. De volgende link kan &eacute;&eacute;nmalig worden gebruikt en zal u leiden naar een pagina waar u uw wachtwoord kunt instellen.</p>
        <table class="btn-primary" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td>
                    <a href="%s">Wachtwoord opgeven</a>
                </td>
            </tr>
        </table>
        <p>Wij wensen u veel plezier in het werken met SocialeKaart.care.</p>
        <p>Uw vragen en idee&euml;n zijn meer dan welkom, stuur ze naar: info@socialekaart.care.</p>
EOT;

        $sUrl = user_pass_reset_url($oUser);
        $sBody = sprintf($sBody, $sUrl);

        $sBody = self::wrapContent($sBody);

        Mailer::sendMail(
                $oUser->mail, // to
                variable_get('site_mail', 'info@socialekaart.care'), // from
                'Uw SocialeKaart.care account is geactiveerd', // subject
                variable_get('site_mail', 'info@socialekaart.care'), // reply to
                $sBody, // content
                false // attachment
            );
    }

    /**
     * Sends the passwordreset link to the user.
     *
     * @param $oUser
     */
    public static function sendUserAccountPasswordReset($oUser) {

        $sBody = <<<EOT
<p>Beste,<p/>
<p>Op SocialeKaart.care is een verzoek gedaan voor een nieuw wachtwoord van uw account.<p/>
<p>U kunt nu inloggen door op de volgende link te klikken:<p/>
        <table class="btn-primary" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td>
                    <a href="%s">Nieuw wachtwoord opgeven</a>
                </td>
            </tr>
        </table>
<p>Deze link kan slechts &eacute;&eacute;nn keer gebruikt worden en zal u doorverwijzen naar een pagina waar u uw wachtwoord opnieuw kunt instellen. De geldigheid van de link zal na een periode verlopen. Er zal niets gebeuren wanneer de link niet wordt gebruikt.<p/>
EOT;

        $sUrl = user_pass_reset_url($oUser);
        $sBody = sprintf($sBody, $sUrl);

        $sBody = self::wrapContent($sBody);

        Mailer::sendMail(
            $oUser->mail, // to
            variable_get('site_mail', 'info@socialekaart.care'), // from
            sprintf('Vervangende inloggegevens voor %s op SocialeKaart.care', $sUrl->mail), // subject
            variable_get('site_mail', 'info@socialekaart.care'), // reply to
            $sBody, // content
            false // attachment
        );
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
     * @param array attachment
     * @return boolean
     */
    public static function sendMail($to, $from, $subject, $replyto, $message, $attachment = false)
    {
        require_once getcwd().'/'.drupal_get_path('module', 'gojira').'/inc/PHPMailer/class.phpmailer.php';

        $mail = new PHPMailer;

        $mail->From = $from;
        $mail->FromName = $from;

        $mail->addAddress($to);
        $mail->addReplyTo($replyto, "SocialeKaart.care");

        $mail->addBCC(variable_get('mailadres_information_inform_admin', 'blijnder@gmail.com'));

        $mail->isHTML(true);

        $mail->Subject = variable_get('mail_prefix', '') .  $subject;
        $mail->Body = $message;

        if (is_array($attachment)) {
            $mail->AddAttachment($attachment['path'], $attachment['name']);
        }

        if (!$mail->send()) {
            return false;
        } else {
            return true;
        }
    }

    public static function wrapContent($content)
    {
        // https://github.com/leemunroe/responsive-html-email-template
        $html = <<<EOT
        <!doctype html>
        <html>
        <head>
        <meta name="viewport" content="width=device-width">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Really Simple HTML Email Template</title>
        <style>
        /* -------------------------------------
            GLOBAL
        ------------------------------------- */
        * {
          font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
          font-size: 100%;
          line-height: 1.6em;
          margin: 0;
          padding: 0;
        }

        img {
          max-width: 600px;
          width: auto;
        }

        body {
          -webkit-font-smoothing: antialiased;
          height: 100%;
          -webkit-text-size-adjust: none;
          width: 100% !important;
        }


        /* -------------------------------------
            ELEMENTS
        ------------------------------------- */
        a {
          color: #b7072a;
        }

        .btn-primary {
          Margin-bottom: 10px;
          width: auto !important;
        }

        .btn-primary td {
          background-color: #b7072a;
          border-radius: 25px;
          font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
          font-size: 14px;
          text-align: center;
          vertical-align: top;
        }

        .btn-primary td a {
          background-color: #b7072a;
          border: solid 1px #b7072a;
          border-radius: 3px;
          border-width: 10px 20px;
          display: inline-block;
          color: #ffffff;
          cursor: pointer;
          font-weight: bold;
          line-height: 2;
          text-decoration: none;
        }

        .last {
          margin-bottom: 0;
        }

        .first {
          margin-top: 0;
        }

        .padding {
          padding: 10px 0;
        }


        /* -------------------------------------
            BODY
        ------------------------------------- */
        table.body-wrap {
          padding: 20px;
          width: 100%;
        }

        table.body-wrap .container {
          border: 1px solid #f0f0f0;
        }


        /* -------------------------------------
            FOOTER
        ------------------------------------- */
        table.footer-wrap {
          clear: both !important;
          width: 100%;
        }

        .footer-wrap .container p {
          color: #666666;
          font-size: 12px;

        }

        table.footer-wrap a {
          color: #999999;
        }


        /* -------------------------------------
            TYPOGRAPHY
        ------------------------------------- */
        h1,
        h2,
        h3 {
          color: #111111;
          font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
          font-weight: 200;
          line-height: 1.2em;
          margin: 40px 0 10px;
        }

        h1 {
          font-size: 36px;
        }
        h2 {
          font-size: 28px;
        }
        h3 {
          font-size: 22px;
        }

        p,
        ul,
        ol {
          font-size: 14px;
          font-weight: normal;
          margin-bottom: 10px;
        }

        ul li,
        ol li {
          margin-left: 5px;
          list-style-position: inside;
        }

        /* ---------------------------------------------------
            RESPONSIVENESS
        ------------------------------------------------------ */

        /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
        .container {
          clear: both !important;
          display: block !important;
          Margin: 0 auto !important;
          max-width: 600px !important;
        }

        /* Set the padding on the td rather than the div for Outlook compatibility */
        .body-wrap .container {
          padding: 20px;
        }

        /* This should also be a block element, so that it will fill 100% of the .container */
        .content {
          display: block;
          margin: 0 auto;
          max-width: 600px;
        }

        /* Let's make sure tables in the content area are 100% wide */
        .content table {
          width: 100%;
        }

        </style>
        </head>

        <body bgcolor="#f6f6f6">

        <!-- body -->
        <table class="body-wrap" bgcolor="#f6f6f6">
          <tr>
            <td></td>
            <td class="container" bgcolor="#FFFFFF">

              <!-- content -->
              <div class="content">
              <table>
                <tr>
                  <td>
                    $content
                    <p>Met vriendelijke groet,</p>
                    <p>Het team van SocialeKaart.care</p>
                  </td>
                </tr>
              </table>
              </div>
              <!-- /content -->

            </td>
            <td></td>
          </tr>
        </table>
        <!-- /body -->

        <!-- footer -->
        <table class="footer-wrap">
          <tr>
            <td></td>
            <td class="container">

              <!-- content -->
              <div class="content">
                <table>
                  <tr>
                    <td align="center">
                      <p>
                      Deze e-mail is ter kennisgeving, de e-mail is geen onderdeel van een mailinglijst.
                      </p>
                    </td>
                  </tr>
                </table>
              </div>
              <!-- /content -->

            </td>
            <td></td>
          </tr>
        </table>
        <!-- /footer -->

        </body>
        </html>
EOT;
        return $html;
    }
}
