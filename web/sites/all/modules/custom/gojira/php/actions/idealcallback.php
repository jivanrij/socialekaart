<?php

/**
 *  This function is called after the user has payed, right before the user
 * get's back to the site
 *
 * implements the callback page described in https://www.qantani.com/documentatie/api/statuswijzigingen/?lang=cURL
 *
 * @return string
 */
function idealcallback() {

    try{
        if (!empty($_GET['testByMollie']))
    	{
    		die('OK');
    	}

        $mollie = new Mollie_API_Client;
        $mollie->setApiKey(variable_get('MOLLIE_API_KEY'));

        $payment  = $mollie->payments->get($_POST["id"]);
    	$order_id = $payment->metadata->order_id;

        db_query("UPDATE {gojira_payments} SET `status`=:status  WHERE `ideal_id`=:id", array(':id' => $order_id,':status' => $payment->status));

        if ($payment->isPaid() == true) {
            // success, let's register this user as payed one
            $iLowestIncrement = date('Y') . '00001'; // get the lowest possible increment for this year
            $iIncrement = db_query("SELECT increment FROM gojira_payments ORDER BY increment DESC")->fetchField();
            if ($iLowestIncrement > $iIncrement) { // is the last increment is lower then the lowest possible this must be the first of the year, use the lowest possible
                $iIncrement = $iLowestIncrement;
            } else {
                $iIncrement++; // increase the increment: 201500009 to 201500010
            }
            db_query("UPDATE {gojira_payments} SET `increment`=:increment WHERE `ideal_id`=:id", array(':id' => $oInfo->ideal_id, ':increment' => $iIncrement));
            Subscriptions::subscribe($order_id);
        } elseif ($payment->isOpen() == false) {
            watchdog(GojiraSettings::WATCHDOG_IDEAL, "Payment {$order_id} has gone wrong.");
            exit;
         }

    } catch (Mollie_API_Exception $e) {
        watchdog(GojiraSettings::WATCHDOG_IDEAL, $e->getMessage());
        drupal_goto('idealfail');
    }
}
