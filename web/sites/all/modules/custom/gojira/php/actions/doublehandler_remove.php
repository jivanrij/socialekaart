<?php

function doublehandler_remove() {
    $ids = explode('-', filter_input(INPUT_GET, 'ids', FILTER_SANITIZE_ENCODED));

    foreach ($ids as $nid) {
        if ($nid !== '') {
            node_delete($nid);
        }
    }

    exit;
}
