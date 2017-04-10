<?php

function doublehandler_checked() {
    $ids = explode('-', filter_input(INPUT_GET, 'ids', FILTER_SANITIZE_ENCODED));
    foreach ($ids as $nid) {
        if ($nid !== '') {
            db_query("UPDATE `node` SET `double_checked`=1 WHERE  `nid`=" . $nid);
        }
    }
    exit;
}
