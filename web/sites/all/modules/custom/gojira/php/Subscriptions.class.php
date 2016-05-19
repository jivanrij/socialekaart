<?php

class Subscriptions {

    /**
     * Gives a groups users all the roles for a payed group and set's the information on the group about when there is payed.
     */
    public static function subscribe($ideal_id) {

        $info = db_query("SELECT ideal_id, gid, uid FROM {gojira_payments} WHERE ideal_id = :id", array(':id' => $ideal_id))->fetchObject();
        if ($info) {
            $group = node_load($info->gid);

            if ($group->field_payed_status[LANGUAGE_NONE][0]['value'] != 1) {
                // subscribe the group
                $group->field_payed_status[LANGUAGE_NONE][0]['value'] = 1;
                node_save($group);
                self::sendSubscribeEmail($info->uid, $info->ideal_id);
                self::setRolesForPayed($group);
                return true;
            }else{
                // group is allready subscribed, no need to do enything except for the invoice mail to the admin
                self::sendSubscribeEmail($info->uid, $info->ideal_id);
                return true;
            }
        }
        return false;
    }

    /**
     * Gives a groups users all the roles for a payed group and set's the information on the group about when there is payed.
     * Based on the group id
     */
    public static function subscribeByGroupId($gid) {

        $info = db_query("SELECT ideal_id, gid, uid, ideal_id FROM {gojira_payments} WHERE gid = :gid", array(':gid' => $gid))->fetchObject();
        if ($info) {
            return self::subscribe($info->ideal_id);
        }
        return false;
    }

    /**
     * Sends a e-mail with the related information of a subscription
     */
    public static function sendSubscribeEmail($uid, $ideal_id) {
        $user = user_load($uid);
        $file = self::generateSubscribePDF($ideal_id);
        Mailer::sendInvoiceOfNewSubscription($user->mail, $file, $ideal_id);
    }

    /**
     * Get's you some information about the ideal payment
     *
     * @param integer $ideal_id
     * @return stdClass
     */
    public static function getPaymentInfo($ideal_id) {
        return db_query("SELECT ideal_id, gid, uid, increment, period_start, period_end, amount, discount, tax, payed FROM {gojira_payments} WHERE ideal_id = :id", array(':id' => $ideal_id))->fetchObject();
    }

    /**
     * Generates a PDF as a invoice
     */
    public static function generateSubscribePDF($ideal_id) {
        require_once(getcwd() . "/sites/all/libraries/dompdf/dompdf_config.inc.php");

        if (!file_exists(getcwd() . '/../invoices/')) {
            watchdog('gojira', 'Missing invoice folder for generateSubscribePDF');
            throw new Exception('unable to generate invoice, please contact site administrator');
        }

        $file_name = getcwd() . '/../invoices/' . date('Ymdis') . '_' . $ideal_id . '.pdf';

        $info = self::getPaymentInfo($ideal_id);

        $user = user_load($info->uid);

        $amount = $info->amount;
        $discount = $info->discount;
        $amount_with_discount = $info->amount - $info->discount;
        $tax = $info->tax;
        $total_payed = $info->payed;

        $period_start = date('d-m-Y', $info->period_start);
        $period_end = date('d-m-Y', $info->period_end);

        $amount = helper::formatMoney($amount);
        $discount = helper::formatMoney($discount);
        $amount_with_discount = helper::formatMoney($amount_with_discount);
        $tax = helper::formatMoney($tax);
        $total_payed = helper::formatMoney($total_payed);

        $name = helper::value($user, GojiraSettings::CONTENT_TYPE_USER_TITLE);
        $date = date('d-m-Y', helper::getTime());
        $invoice_number = $info->increment;
        //$html = variable_get('gojira_invoice_template');
        $html = <<<EOT
    <!DOCTYPE html>
    <html>
        <head>
            <style>
                body{
                    font-family: Arial, Verdana, Sans-Serif;
                    font-size:14px;
                    color:#4d4d4d;
                }
                body div{
                    width:100%;
                    overflow:hidden;
                    margin-top:30px;
                    margin-bottom:40px;
                }
                div#logo{
                    background-image: url(sites/all/modules/custom/gojira/img/logo_socialekaartcare.png);
                    background-position: right;
                    height: 50px;
                    width: 100%;
                    display:inline;
                    background-repeat: no-repeat;
                    background-position: right;
                    float: right;
                    margin-right: 0;
                    margin-left:40px;
                }
                div#adres{
                    margin-top:150px;
                }
                div#day{
                    text-align: right;
                }
                div#about{
                }
                div#period{
                }
                div#invoice_number{
                    text-align: left;
                }
                div.line{
                    width:70%;
                    float:left;
                    display:block;
                    margin:0;
                    padding:0;
                }
                div.line hr{
                    margin:0;
                    padding:0;
                    border-width: 1px;
                    border-style: inset;
                }
                div.table{
                    width:70%;
                    margin:0;
                }
                div.invoice_line_plus{
                    background-image: url(sites/all/modules/custom/gojira/img/invoice_line_plus.png);
                    background-position: right;
                    height:30px;
                    width: 80%;
                    margin:0;
                    background-repeat: no-repeat;
                    background-position: right;
                    background-size: auto 10px;
                }
                div.invoice_line_min{
                    background-image: url(sites/all/modules/custom/gojira/img/invoice_line_min.png);
                    background-repeat: no-repeat;
                    background-position: right;
                    height:30px;
                    width: 80%;
                    margin:0;
                    background-size: auto 10px;
                }
                div#footer{
                    text-align: center;
                    font-size:12px;
                    position: fixed;
                    bottom:0px;
                }
                table, tr{
                    width:100%
                }
                td.left{
                    width:50%;
                    text-align: left;
                }
                td.right{
                    width:50%;
                    text-align: right;
                }
            </style>
        </head>
        <body>
            <div id="logo">

            </div>
            <div id="adres">
                Aan: %name_customer%
            </div>
            <div id="day">
                %date%
            </div>
            <div id="about">
                <b>Betreft:</b> Factuur voor SocialeKaart.care abonnement
            </div>
            <div id="period">
                <b>Periode:</b> %period_start% t/m %period_end%
            </div>
            <div id="invoice_number">
                <b>Factuurnummer:</b> %factuur_nr%
            </div>
            <div class="table">
                <table>
                    <tr>
                        <td class="left">Jaarabonnement Sociale Kaart</td>
                        <td class="right">€ %amount%</td>
                    </tr>
                </table>
            </div>
            <div class="line">
                <hr />
            </div>
            <div class="table">
                <table>
                    <tr>
                        <td class="left">Btw 21%</td>
                        <td class="right">+ € %tax%</td>
                    </tr>
                </table>
            </div>
            <div class="line">
                <hr />
            </div>
            <div class="table">
                <table>
                    <tr>
                        <td class="left"><b>Totaal</b></td>
                        <td class="right"><b>€ %total_payed%</b></td>
                    </tr>
                </table>
<p>Dit bedrag is reeds voldaan via een iDeal transactie.</p>
            </div>
            <div id="footer">
                Blijnder VOF - Admiraal de Ruyterstraat 18 - 3262 XE Oud-Beijerland - info@socialekaart.care - www.socialekaart.care<br />
                IBAN: NL60 INGB 0006 7997 07 - Btw-nummer: NL855087833B01 - KVK: 63090732
            </div>
        </body>
    </html>
EOT;



        $html = str_replace(
                array(
            '%amount%',
            '%discount%',
            '%amount_with_discount%',
            '%tax%',
            '%total_payed%',
            '%date%',
            '%period_start%',
            '%period_end%',
            '%factuur_nr%',
            '%name_customer%'), array(
            $amount,
            $discount,
            $amount_with_discount,
            $tax,
            $total_payed,
            $date,
            $period_start,
            $period_end,
            $invoice_number,
            $name), $html);

        echo $html;


        $dompdf = new DOMPDF();
        $dompdf->load_html($html);
        $dompdf->render();
        $output = $dompdf->output();
        file_put_contents($file_name, $output);

        return $file_name;
    }

    /**
     * Set the payed status to false & set's the roles for the groups users to not payed functions
     *
     * @param integer $group_nid
     * @param stdClass $payment
     */
    public static function unsubscribe($group_nid, $payment) {
        // unsubscribe the group
        $group = node_load($group_nid);
        if ($group && $group->type == GojiraSettings::CONTENT_TYPE_GROUP) {
            $group->field_payed_status[LANGUAGE_NONE][0]['value'] = 0;
            node_save($group);
            $users = Group::getAllUsers($group->nid);
            foreach ($users as $groupuser) { // through the users
                if ($groupuser->uid != 1) {
                    if (helper::value($group, GojiraSettings::CONTENT_TYPE_ORIGINAL_DOCTOR, 'uid') == $groupuser->uid) {
                        // master user
                        self::removeRoleFromUser($groupuser->uid, helper::ROLE_SUBSCRIBED_MASTER);
                        // only send this mail if it has never been send for this payment
                        if ($payment->warning_ended !== 0) {
                            Mailer::sendSubscriptionEnded($groupuser);
                            db_query("UPDATE `gojira_payments` SET `warning_ended`=1 WHERE  `id`={$payment->id}");
                        }
                    } else {
                        // not the master user
                        Mailer::sendUnsubscribeMail($groupuser);
                        $groupuser->status = 0;
                    }
                    user_save($groupuser);
                }
            }
        }
    }

    /**
     * Checks all the subscriptions. Unsubscribe them if they are due.
     * Checks ths by the is payed flag on the group and the latest end date on the payments table.
     */
    public static function checkSubscriptions() {
        // get all the group nodes with the payed status
        $group_nodes = db_query("select node.nid, node.title from node join field_data_field_payed_status on (node.nid = field_data_field_payed_status.entity_id) where node.type = 'gojira_group' and field_data_field_payed_status.field_payed_status_value = 1 group by node.nid")->fetchAll();
        foreach ($group_nodes as $group) {

            if (strtolower($group->title) == 'admin') {
                continue;
            }

            // timestamp of subscription end date
            $payment = self::getLatestPaymentPeriod($group->nid);

            // check if we have a payment info object, if not, warn the admin, and do nothing
            if ($payment == false) {
                watchdog(GojiraSettings::WATCHDOG_SUBSCRIPTIONS, 'checkSubscriptions: group ' . $group->nid . ' is flaged as payed group but has no payment information');
                Mailer::checkSubscriptionFail($group->nid);
                continue;
            }

            $end = $payment->period_end;

            if (date('Ymd', $end) < date('Ymd', helper::getTime()) || !$end) {
                self::unsubscribe($group->nid, $payment);
            } else {
                // if we are 30 days before the day the subscription ends, let's send them a reminder.
                $estimated_end_date = strtotime("+30 day", helper::getTime());

                if (date('Ymd', $estimated_end_date) >= date('Ymd', $end) && date('Ymd', $end) > date('Ymd', helper::getTime())) {
                    // todo send a reminder that the subscription is going to end in 30 days
                    if ($payment->warning_send == 0) { // only send when it's never been send for this payment
                        $group = node_load($group->nid);
                        $groups_main_doctor_uid = helper::value($group, GojiraSettings::CONTENT_TYPE_ORIGINAL_DOCTOR, 'uid');
                        $main_doctor = user_load($groups_main_doctor_uid);

                        Mailer::sendSubscriptionEndWarning($main_doctor);
                        db_query("UPDATE `gojira_payments` SET `warning_send`=1 WHERE  `id`={$payment->id}");
                    }
                }
            }
        }
    }

    /**
     * Gives all the payed subscription roles to the users of a group, sends activation mails and makes users active. Can be used after payment is done.
     */
    public static function setRolesForPayed($group, $bSendMails = true) {
        if (is_numeric($group)) {
            $group = node_load($group);
        }
        if ($group->type == GojiraSettings::CONTENT_TYPE_GROUP) {
            $users = Group::getAllUsers($group->nid);
            foreach ($users as $groupuser) {
                $groupuser->status = 1;
                user_save($groupuser);

                if (helper::value($group, GojiraSettings::CONTENT_TYPE_ORIGINAL_DOCTOR, 'uid') == $groupuser->uid) {
                    // is master user
                    self::addRoleToUser($groupuser->uid, helper::ROLE_SUBSCRIBED_MASTER);
                } else {
                    // is not the master user
                    // don't need to assign a role, we only need to activate them
                    if ($bSendMails) {
                        Mailer::sendSubscribeActivationMail($groupuser);
                    }
                }
            }
        }
    }

    /**
     * Adds the payment to the payment log of gojira
     */
    public static function addPaymentLog($uid, $amount, $description, $ideal_id, $start_date, $end_date, $discount, $tax, $payed, $status = 0) {

        foreach (func_get_args() as $sValue) {
            if (is_null($sValue)) {
                watchdog(WATCHDOG_CRITICAL, 'addPaymentLog parameter missing. ' . json_encode(func_get_args()));
                throw new Exception('addPaymentLog parameter missing. ' . json_encode(func_get_args()));
            }
        }

        $user = user_load($uid);
        $sql = "INSERT INTO `gojira_payments` (`uid`, `name`, `description`, `amount`, `gid`, `ideal_id`, `period_start`, `status`, `period_end`,`discount`,`tax`,`payed`) VALUES ({$uid}, '{$user->name}', '{$description}', " . str_replace(',', '.', $amount) . ", " . Group::getGroupId($uid) . ", '{$ideal_id}', {$start_date}, '$status', {$end_date},{$discount},{$tax},{$payed})";
        db_query($sql);
    }

    /**
     * Adds a role to a user
     */
    private static function addRoleToUser($user, $role_name) {
        if (is_numeric($user)) {
            $user = user_load($user);
        }

        $key = array_search($role_name, $user->roles);
        if ($key == false) {
            // Get the rid from the roles table.
            $roles = user_roles(true);
            $rid = array_search($role_name, $roles);
            if ($rid != false) {
                $new_role[$rid] = $role_name;
                $all_roles = $user->roles + $new_role; // Add new role to existing roles.
                user_save($user, array('roles' => $all_roles));
            }
        }
    }

    /**
     * Remove a role from a user
     */
    private static function removeRoleFromUser($user, $role_name) {
        if (is_numeric($user)) {
            $user = user_load($user);
        }
        // Only remove the role if the user already has it.
        $key = array_search($role_name, $user->roles);
        if ($key == true) {
            // Get the rid from the roles table.
            $roles = user_roles(true);
            $rid = array_search($role_name, $roles);
            if ($rid != false) {
                // Make a copy of the roles array, without the deleted one.
                $new_roles = array();
                foreach ($user->roles as $id => $name) {
                    if ($id != $rid) {
                        $new_roles[$id] = $name;
                    }
                }
                user_save($user, array('roles' => $new_roles));
            }
        }
    }

    /**
     * Gives a true back when the current logged in group had a payed status on this moment
     *
     * @return boolean
     */
    public static function currentGroupHasPayed() {
        $iGid = Group::getGroupId();
        $oGroup = node_load($iGid);
        return (bool) helper::value($oGroup, GojiraSettings::CONTENT_TYPE_PAYED_STATUS);
    }

    /**
     * Get's the end of the current/last period the user has payed for.
     */
    public static function getEndCurrentPeriod($format = null, $gid = null) {

        if (is_null($gid)) {
            $gid = Group::getGroupId();
        }

        // get the lates payment row thas is payed for of a specified groep
        $period_end = db_query("SELECT period_end FROM gojira_payments WHERE gid = {$gid} AND status = 'paid' ORDER BY period_end DESC")->fetchField();

        if (!$period_end) {
            return false;
        }
        if (is_null($format)) {
            return $period_end;
        } else {
            return date($format, $period_end);
        }
    }

    /**
     * Get's you the latest valid payment information of a group
     *
     * @param integer $gid
     * @return boolean|stdClass
     */
    public static function getLatestPaymentPeriod($gid = null) {
        if (is_null($gid)) {
            $gid = Group::getGroupId();
        }
        $payment = db_query("SELECT * FROM gojira_payments WHERE gid = {$gid} AND status = 'paid' ORDER BY period_end DESC limit 1")->fetchObject();
        if (!$payment) {
            return false;
        }
        return $payment;
    }

    /**
     * Tell's you if the current user can extend a subscription
     *
     * @return boolean
     */
    public static function canExtend() {

        $group_id = Group::getGroupId();

        $payment = db_query("SELECT period_end FROM {gojira_payments} WHERE gid = :gid AND status = 'paid' ORDER BY increment DESC", array(':gid' => $group_id))->fetchObject();

        if ($payment) {
            $endYear = date('Y', $payment->period_end);
            $nowYear = date('Y');

            if ($endYear <= ++$nowYear) {
                return true; // you can extend a subscription as long as the current end moment is not later then this year + 1 year
            }
            return false;
        }
        return true;
    }

    /**
     * Get's you the required info for a new payment
     *
     * @global stdClass $user
     * @return array
     */
    public static function getNewPaymentInfo() {

        $aInfo = array();

        global $user;
        $oUser = user_load($user->uid);

        $aInfo['user'] = $oUser;

        $aInfo['amount'] = variable_get('gojira_subscription_year_price');
        $aInfo['tax'] = variable_get('gojira_subscription_year_tax');
        $aInfo['total'] = variable_get('gojira_subscription_year_total');

        $tCurrentEnd = Subscriptions::getEndCurrentPeriod();
        if (!$tCurrentEnd) {
            // no current or past or future end date of running abonnee
            $tNewStart = helper::getTime();
        } else {
            if (date('Ymd', $tCurrentEnd) < date('Ymd', helper::getTime())) {
                $tNewStart = helper::getTime();
            } else {
                $tNewStart = $tCurrentEnd;
            }
        }

        $aInfo['current_end'] = $tCurrentEnd;

        $iPeriodDays = variable_get('SUBSCRIPTION_PERIOD');

        $aInfo['period_days'] = $iPeriodDays;

        $tNewEnd = strtotime("+{$iPeriodDays} days", $tNewStart);

        $aInfo['new_end'] = $tNewEnd;
        $aInfo['new_start'] = $tNewStart;

        $sDescription = 'SocialeKaartAbonnement';
        $aInfo['description'] = $sDescription;

        return $aInfo;
    }

    /**
     * Adds crucial information/roles/stuff to a new created user NOT from the HAWeb SSO
     *
     * Do NOT run this function on allready existing accounts
     *
     * @param stdClass $account
     */
    public static function giveNewUserDiscount(&$account) {

        $iFreePeriodeGiven = db_query("select count(id) from `gojira_payments` where uid = {$account->uid} and description = '" . GojiraSettings::IDEAL_FREE_PERIOD_DESCRIPTION . "'")->fetchField();
        if ($iFreePeriodeGiven > 0) {
            watchdog(GojiraSettings::WATCHDOG_IDEAL, 'user ' . $account->uid . ' allready has gotten a free period. Kill the setNewFreePeriodUser rights/payments proces.');
            return;
        }

        $group = node_load(Group::getGroupId($account->uid));

        $group->field_payed_status[LANGUAGE_NONE][0]['value'] = 1;
        node_save($group);

        Subscriptions::setRolesForPayed($group, false);
        // add a payment log so the group will have a payed period of 3 months
        $sql = "INSERT INTO `gojira_payments` (`uid`, `name`, `description`, `amount`, `gid`, `ideal_id`, `period_start`, `status`, `period_end`,`discount`,`tax`,`payed`) VALUES ({$account->uid}, '{$account->name}', '" . GojiraSettings::IDEAL_FREE_PERIOD_DESCRIPTION . "', 0, " . $group->nid . ", '0', '0', " . helper::getTime() . ", 1, " . strtotime("+3 months", helper::getTime()) . ",0,0,0)";

        db_query($sql);
    }

}
