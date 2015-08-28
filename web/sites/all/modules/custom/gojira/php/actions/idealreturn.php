<?php

function idealreturn() {

    //&id=725563&status=1&salt=34385&checksum=4e61737c9cd27f387e9a35cbb996a6b5342f6461
//    http://www.mijnwebsite.nl/bedankt-voor-uw-betaling?id=[transaction_id]&status=[status]&salt=[salt]&checksum=[checksum]
//
//Hiermee worden de volgende gegevens toegevoegd aan de URL:
//De transactie ID
//De status van de betaling (1 = betaald, 0 = mislukt)
//Een Salt. Dit is een willekeurig nummer tussen de 10000 en 999999
//Een checksum. Deze bestaat uit Transactie ID + Transactie Code + Status + Salt


    if (isset($_GET['id']) && isset($_GET['status']) && isset($_GET['salt']) && isset($_GET['checksum'])) {
        $status = false;
        $details = false;
        $info = false;
        $qantani = Qantani::CreateInstance(variable_get('IDEAL_MERCHANT_ID'), variable_get('IDEAL_MERCHANT_KEY'), variable_get('IDEAL_MERCHANT_SECRET'));
        
        $info = db_query("SELECT ideal_id, ideal_code, gid, uid FROM {gojira_payments} WHERE ideal_id = :id", array(':id' => $_GET['id']))->fetchObject();
        if ($info) {
            
            $status = $qantani->getPaymentStatus($info->ideal_code);

            $details = $qantani->getTransactionStatus(
                    array(
                        'TransactionID' => $info->ideal_id,
                        'TransactionCode' => $info->ideal_code
                    )
            );
        }

        if ($status && $details && $info) {
//            201400005
            
            $lowest_increment = date('Y').'00001'; // get the lowest possible increment for this year
            
            $increment = db_query("SELECT increment FROM gojira_payments ORDER BY increment DESC")->fetchField();
            
            if($lowest_increment > $increment){ // is the last increment is lower then the lowest possible this must be the first of the year, use the lowest possible
                $increment = $lowest_increment;
            }else{
                $increment++; // increase the increment: 201500009 to 201500010
            }
            
            db_query("UPDATE {gojira_payments} SET `status`=1, `increment`=:increment WHERE `ideal_id`=:id AND `ideal_code`=:code ", array(':id' => $info->ideal_id, ':code' => $info->ideal_code,'increment'=>$increment));
            Subscriptions::subscribe($info->ideal_id);
            header('Location: /?q=idealsuccess&id='.$_GET['id']);
            exit;
        } else {
            watchdog(WATCHDOG_CRITICAL, 'payment fail ideal_id: ' . $_GET['id'] . '<br /> lastError: ' . $qantani->getLastError());
            db_query("UPDATE {gojira_payments} SET `status`=2 WHERE `ideal_id`=:id ", array(':id' => $_GET['id']));
            drupal_goto('idealfail');
        }
    }



    return theme('idealreturn', array());
}
