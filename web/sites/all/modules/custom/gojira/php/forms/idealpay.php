<?php

/**
 * This form is the crud for the location nodes
 */
function gojira_idealpay_form($form, &$form_state) {

    $form['info'] = array(
        '#markup' => '<p>' . t('Select the bank to do your payment with and push the pay button. You will then be redirected to an iDeal page to complete the transaction.') . '</p>',
    );

    $qantani = Qantani::CreateInstance(variable_get('IDEAL_MERCHANT_ID'), variable_get('IDEAL_MERCHANT_KEY'), variable_get('IDEAL_MERCHANT_SECRET'));
    $banks = array();
    foreach ($qantani->Ideal_getBanks() as $bank) {
        $banks[$bank['Id']] = $bank['Name'];
    }
    $form['ideal_bank'] = array(
        '#title' => t('Select bank'),
        '#type' => 'select',
        '#required' => true,
        '#options' => $banks,
        '#default_value' => 0,
    );

    $paymentConditions = t('Payment conditions');
    $form['agree_terms_conditions'] = array(
        '#title' => t('Agree with payment conditions'),
        '#type' => 'checkbox',
        '#required' => true,
        '#description' => 'Ik ga akkoord met de <a href="/algemene-voorwaarden" title="algemene voorwaarden" target="_new">algemene voorwaarden</a>.',
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

    $qantani = Qantani::CreateInstance(variable_get('IDEAL_MERCHANT_ID'), variable_get('IDEAL_MERCHANT_KEY'), variable_get('IDEAL_MERCHANT_SECRET'));

    $info = Subscriptions::getNewPaymentInfo();
    $user = $info['user'];
    
    $url = $qantani->Ideal_execute(array(
        'Amount' => $info['total'],
        'Currency' => 'EUR',
        'Description' => $info['description'],
        'Bank' => $_POST['ideal_bank'],
        'Return' => variable_get('gojira_ideal_return_url','no return url set')
    ));
    if ($url) {
        
        $transactionId = $qantani->GetLastTransactionId();
        $transactionCode = $qantani->GetLastTransactionCode();

        Subscriptions::addPaymentLog($user->uid, $info['amount'], $info['description'], $transactionId, $transactionCode, $info['new_start'], $info['new_end'], $info['discount'], $info['tax'], $info['total'], 0, $_POST['ideal_bank']);
        
        header('location: ' . $url);
        exit;
    } else {
        watchdog(GojiraSettings::WATCHDOG_IDEAL, 'failed to generate ideal url for '.$ideal_id.'<br /> lastError: '.$qantani->getLastError());
        drupal_goto('idealfail');
    }
}
