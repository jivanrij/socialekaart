<?php

/**
 * This page generates the ideal pay page
 *
 * @return string
 */
function idealpay() {

    return theme('idealpay', array('gojira_idealpay_form'=>drupal_get_form('gojira_idealpay_form'), 'paymentinfo'=>Subscriptions::getNewPaymentInfo()));
}
