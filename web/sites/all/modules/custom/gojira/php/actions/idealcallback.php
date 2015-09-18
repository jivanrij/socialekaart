<?php

/**
 * This page generates a form to crud a employee
 *
 * @return string
 */
function idealcallback() {
    $oQantani = null;
    $oInfo = null;
    $oQantani = null;
    $bStatus = false;
    $bDetails = null;

    //http://www.mijnwebsite.nl/bedankt.php?id=[transaction_id]&status=[status]&salt=[salt]&checksum1=[checksum]&checksum2=[checksum2]&desc=[description]
    //&id=725563&status=1&salt=34385&checksum=4e61737c9cd27f387e9a35cbb996a6b5342f6461
//    http://www.mijnwebsite.nl/bedankt-voor-uw-betaling?id=[transaction_id]&status=[status]&salt=[salt]&checksum=[checksum]
//
//Hiermee worden de volgende gegevens toegevoegd aan de URL:
//De transactie ID
//De status van de betaling (1 = betaald, 0 = mislukt)
//Een Salt. Dit is een willekeurig nummer tussen de 10000 en 999999
//Een checksum. Deze bestaat uit Transactie ID + Transactie Code + Status + Salt

    if (isset($_GET['id']) && isset($_GET['status']) && isset($_GET['salt']) && isset($_GET['checksum'])) {
        $oQantani = Qantani::CreateInstance(variable_get('IDEAL_MERCHANT_ID'), variable_get('IDEAL_MERCHANT_KEY'), variable_get('IDEAL_MERCHANT_SECRET'));

        //ideal_id, ideal_code, gid, uid,
        $oInfo = db_query("SELECT status, callback_times, ideal_code, ideal_id FROM {gojira_payments} WHERE ideal_id = :id", array(':id' => $_GET['id']))->fetchObject();
        if ($oInfo) {
            if ($oInfo->status == 1) {
                return 'x'; // this will tell ideal that the payment is succesfull done
                exit;
            }
            $oQantani = Qantani::CreateInstance(variable_get('IDEAL_MERCHANT_ID'), variable_get('IDEAL_MERCHANT_KEY'), variable_get('IDEAL_MERCHANT_SECRET'));
            $bStatus = $oQantani->getPaymentStatus($oInfo->ideal_code);
            $bDetails = $oQantani->getTransactionStatus(
                    array(
                        'TransactionID' => $oInfo->ideal_id,
                        'TransactionCode' => $oInfo->ideal_code
                    )
            );
            
            if(!$bStatus){
                watchdog(GojiraSettings::WATCHDOG_IDEAL, 'ideal callback: payment status of gojira_payments.id ' . $_GET['id'] . 'is false');
                exit;
            }

            if ($bStatus && $bDetails) {
                $iLowestIncrement = date('Y') . '00001'; // get the lowest possible increment for this year
                $iIncrement = db_query("SELECT increment FROM gojira_payments ORDER BY increment DESC")->fetchField();
                if ($iLowestIncrement > $iIncrement) { // is the last increment is lower then the lowest possible this must be the first of the year, use the lowest possible
                    $iIncrement = $iLowestIncrement;
                } else {
                    $iIncrement++; // increase the increment: 201500009 to 201500010
                }
                $iCallbackTimes = $oInfo->callback_times++;
                db_query("UPDATE {gojira_payments} SET `callback_times` = :callback_times `status`=1, `increment`=:increment WHERE `ideal_id`=:id AND `ideal_code`=:code ", array(':id' => $oInfo->ideal_id, ':code' => $info->ideal_code, ':increment' => $iIncrement, ':callback_times' => $iCallbackTimes));
                Subscriptions::subscribe($oInfo->ideal_id);
                return 'x'; // this will tell ideal that the payment is succesfull done
                exit;
            } else {
                watchdog(GojiraSettings::WATCHDOG_IDEAL, 'ideal callback: payment fail ideal_id: ' . $_GET['id'] . '<br /> lastError: ' . $oQantani->getLastError());
                exit;
            }
        }else{
            watchdog(GojiraSettings::WATCHDOG_IDEAL, 'ideal callback: unable to find payment info, no valid ideal_id given: '.$_GET['id']);
            exit;
        }
    }

    watchdog(GojiraSettings::WATCHDOG_IDEAL, 'ideal callback: missing ideal information');
    exit;
}
