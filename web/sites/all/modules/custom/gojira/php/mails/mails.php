<?php

function gojira_mail($key, &$message, $params) {
    switch ($key) {
        case 'informregister':
            $message['subject'] = t($params['name'] . ' heeft zich geregistreerd op socialekaart.care.');
            $message['body'][] = t('Er heeft zich iemand met het e-mailadres ' . $params['name'] . ' geregistreerd op socialekaart.care.<br /><br />' . $params['url'] . '<br /><br />https://www.bigregister.nl/zoeken/zoekenopnaamenspecialisme/default.aspx');
            break;
    }
}

