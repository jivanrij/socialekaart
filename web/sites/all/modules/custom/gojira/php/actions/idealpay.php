<?php

/**
 * This page generates a form to crud a employee
 *
 * @return string
 */
function idealpay() {
    
    return theme('idealpay', array('gojira_idealpay_form'=>drupal_get_form('gojira_idealpay_form'), 'paymentinfo'=>Subscriptions::getNewPaymentInfo()));
}
