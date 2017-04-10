<?php

/**
 * This form is the crud for the location nodes
 */
function gojira_idealpay_form($form, &$form_state) {

    $paymentConditions = t('Payment conditions');
    $form['agree_terms_conditions'] = array(
        '#type' => 'checkbox',
        '#required' => true,
        '#title' => 'Ik ga akkoord met de <a href="/AlgemeneVoorwaarden" title="algemene voorwaarden" target="_new">algemene voorwaarden</a>.',
    );

    $form['submit'] = array(
        '#type' => 'submit',
        '#prefix' => '<div class="gbutton_wrapper"><a class="gbutton rounded noshadow left" onclick="window.history.back();" title="' . t('Back') . '"><span>' . t('Back') . '</span></a><span class="gbutton rounded noshadow right">',
        '#value' => t('Pay'),
        '#suffix' => '</span></div>'
    );
    return $form;
}

function gojira_idealpay_form_validate($form, &$form_state) {
    if($form['agree_terms_conditions']['#value'] !== 1){
        form_set_error('agree_terms_conditions', t('You need to confirm you agree with the payment conditions.'));
    }
}

function gojira_idealpay_form_submit($form, &$form_state) {

    $order_id = time();
    global $user;

    $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
    $hostname = $_SERVER['HTTP_HOST'];
    //$path     = dirname(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']);

    $info = Subscriptions::getNewPaymentInfo();

    try{
        $mollie = new Mollie_API_Client;
        $mollie->setApiKey(variable_get('MOLLIE_API_KEY'));

        $paymentInfo = array(
            "amount"       => $info['total'],
            "description"  => $info['description'],
            "redirectUrl"  => "{$protocol}://{$hostname}/idealreturn?order_id={$order_id}",
            "webhookUrl"   => "{$protocol}://{$hostname}/idealcallback",
            "metadata"     => array(
                "order_id" => $order_id,
            ),
        );


        // creats a payment @ mollie and returns the $payment to generate the
        // url for the user & to store it in the database
        $payment = $mollie->payments->create($paymentInfo);

        Subscriptions::addPaymentLog($user->uid, $info['amount'], $info['description'], $order_id, $info['new_start'], $info['new_end'], 0, $info['tax'], $info['total'], $payment->status);
die('e');
        header('location: ' . $payment->getPaymentUrl());
        exit;
    } catch (Mollie_API_Exception $e) {
        watchdog(GojiraSettings::WATCHDOG_IDEAL, $e->getMessage());
        drupal_goto('idealfail');
    }
}
