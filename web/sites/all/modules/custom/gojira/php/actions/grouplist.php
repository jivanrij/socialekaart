<?php
function grouplist(){

    $groups = db_query("select title, nid as gid from node where type = 'gojira_group'");

    return theme('grouplist', array('groups'=>$groups));
}
