<?php
// implements the return page described in https://www.qantani.com/documentatie/api/statuswijzigingen/?lang=cURL
function idealreturn() {

// open
// cancelled
// pending
// expired
// paid
// paidout
// refunded

    $paidStatusses = array('paid','paidout');
    $status = db_query("SELECT status FROM {gojira_payments} WHERE ideal_id = :id", array(':id' => $_GET['order_id']))->fetchField();

    if (in_array($status, $paidStatusses)) {
        // success!
        header('Location: /idealsuccess?id=' . $_GET['id']);
        exit;
    } else {
        drupal_goto('idealfail');
        exit;
    }
}
