<?php

function doublehandler_remove() {
    $ids = explode('-', filter_input(INPUT_GET, 'ids', FILTER_SANITIZE_ENCODED));

    foreach ($ids as $nid) {
        if ($nid !== '') {
            $node = node_load($nid);
            $node->status = 0;
            node_save($node);
            db_query("UPDATE `node` SET `double_checked`=1, `source`='double' WHERE  `nid`=" . $nid);
        }
    }

    exit;
}
