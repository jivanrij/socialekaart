<?php
function groupdetail(){

    $group = node_load($_GET['gid']);

    $members = db_query("select users.uid as uid, name, mail from field_data_field_gojira_group join users on (users.uid = field_data_field_gojira_group.entity_id) where field_data_field_gojira_group.field_gojira_group_nid = ".$group->nid);
    $memberUsers = array();
    foreach($members as $member){
        $memberUsers[] = user_load($member->uid);
    }

    $practices = db_query("select node.nid as nid from field_data_field_gojira_group join node on (node.nid = field_data_field_gojira_group.entity_id) where node.type = 'location' and field_data_field_gojira_group.field_gojira_group_nid = ".$group->nid);
    $practiceNodes = array();
    foreach($practices as $practice){
        $node = node_load($practice->nid);
        $location = Location::getLocationObjectOfNode($practice->nid);
        $node->mapslink = "https://www.google.nl/maps/@{$location->latitude},{$location->longitude},18z";
        $practiceNodes[] = $node;
    }

    $payments = db_query("SELECT ideal_id, gid, uid, description, increment, created_at, period_start, period_end, amount, discount, tax, payed, name FROM {gojira_payments} WHERE gid = :gid", array(':gid' => $group->nid));

    return theme('groupdetail', array('group'=>$group, 'members'=>$memberUsers,'practices'=>$practiceNodes,'payments'=>$payments));
}
