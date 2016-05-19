<?php

/**
 * This form is the crud for the location nodes
 */
function gojira_idealpay_form($form, &$form_state) {

    // $form['info'] = array(
    //     '#markup' => '<p>' . t('Select the bank to do your payment with and push the pay button. You will then be redirected to an iDeal page to complete the transaction.') . '</p>',
    // );

    $methods = array();

    try{
        $mollie = new Mollie_API_Client;
        $mollie->setApiKey(variable_get('MOLLIE_API_KEY'));

        $methods = $mollie->methods->all();

        foreach ($methods->data as $method)
        {
            $methods[$method->id] = $method->description;
    	}
    } catch (Mollie_API_Exception $e) {
        watchdog(GojiraSettings::WATCHDOG_IDEAL, $e->getMessage());
        drupal_goto('idealfail');
    }

    $form['method'] = array(
        '#title' => t('Select method'),
        '#type' => 'select',
        '#required' => true,
        '#options' => $methods,
        '#default_value' => 0,
    );

    $paymentConditions = t('Payment conditions');
    $form['agree_terms_conditions'] = array(
        '#type' => 'checkbox',
        '#required' => true,
        '#title' => 'Ik ga akkoord met de <a href="https://socialekaart.care/sites/default/skfiles/Algemene_Voorwaarden.pdf" title="algemene voorwaarden" target="_new">algemene voorwaarden</a>.',
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

    $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
    $hostname = $_SERVER['HTTP_HOST'];
    $path     = dirname(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']);

    $info = Subscriptions::getNewPaymentInfo();

    try{
        $mollie = new Mollie_API_Client;
        $mollie->setApiKey(variable_get('MOLLIE_API_KEY'));

        // creats a payment @ mollie and returns the $payment to generate the
        // url for the user & to store it in the database
        $payment = $mollie->payments->create(array(
            "amount"       => $info['amount'],
            "description"  => $info['description'],
            "redirectUrl"  => "{$protocol}://{$hostname}{$path}/03-return-page.php?order_id={$order_id}",
            "webhookUrl"   => "{$protocol}://{$hostname}{$path}/idealcallback",
            "metadata"     => array(
                "order_id" => $order_id,
            ),
        ));

        Subscriptions::addPaymentLog($user->uid, $info['amount'], $info['description'], $order_id, $info['new_start'], $info['new_end'], 0, $info['tax'], $info['total'], $payment->status, $_POST['method']);

        header('location: ' . $payment->getPaymentUrl());
        exit;
    } catch (Mollie_API_Exception $e) {
        watchdog(GojiraSettings::WATCHDOG_IDEAL, $e->getMessage());
        drupal_goto('idealfail');
    }
}
