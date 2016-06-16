<?php
class Reporter{
    public static function reportThis($note = ''){
        global $user;
        $mobileDetect = new Mobile_Detect();

        $agent = $mobileDetect->getUserAgent();
        $userId = $user->uid;
        $url = base_path().current_path();
        $ip = ip_address();

        $params = '';
        if (count($_POST) > 0) {
            $params .= 'POST: '.json_encode($_POST);
        }
        if (count($_GET) > 0) {
            $params .= 'GET:'.json_encode($_GET);
        }

        $isMobile = 0;
        if ($mobileDetect->isMobile()) {
            $isMobile = 1;
        }

        $thirtyDaysBack = date('Y-m-d H:m:s', strtotime('-30 days'));
        db_query("DELETE FROM `gojira_reporter` WHERE datetime < '{$thirtyDaysBack}'");
        db_query("INSERT INTO `gojira_reporter` (`params`, `mobile`, `user`, `url`, `ip`, `agent`, `note`) VALUES ('{$params}',{$isMobile}, {$userId}, '{$url}', '{$ip}', '{$agent}', '{$note}')");
    }
}
