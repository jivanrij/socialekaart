<?php
function idealreport() {
    $rPayments = db_query("SELECT id, method, uid, callback_times, name, description, amount, gid, ideal_id, status, period_start, period_end, warning_send, warning_ended, increment, discount, tax, payed FROM gojira_payments ORDER BY id DESC");
    return theme('idealreport', array('rPayments'=>$rPayments));
}
