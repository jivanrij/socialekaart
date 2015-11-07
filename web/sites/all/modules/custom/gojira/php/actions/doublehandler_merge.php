<?php

function doublehandler_merge() {

    return false; // still needs testing
    
    //578675-578682
    $ids = explode('-', filter_input(INPUT_GET, 'ids', FILTER_SANITIZE_ENCODED));

    // get all data from all locations
    $nodesToMerge = array();
    foreach ($ids as $nid) {
        if ($nid !== '') {
            $node = node_load($nid);
            $nodesToMerge[] = array(
              'nid' => $nid,
              'title' => $node->title,
              //'category' => Category::getCategoryName($node),
              'favorites' => Favorite::getAllFaviritedGroupsByPractices($nid), // key is gid+pid
              'likes' => Labels::getAllLabelLikesByGroup($nid), // key is gid+tid
              'labels' => Labels::getLabels($node) // key is tid, value is name
            );
        }
    }
    
    // get/set a master location
    $masterLocation = array_pop($nodesToMerge);
    
    // merge all the data into the master location
    foreach($nodesToMerge as $nodeToMerge) {
        foreach($nodeToMerge['favorites'] as $gidpid=>$favorite){
            $masterLocation['favorites'][$gidpid] = $favorite;
        }
        foreach($nodeToMerge['likes'] as $gidtid=>$like){
            $masterLocation['likes'][$gidtid] = $like;
        }
        foreach($nodeToMerge['labels'] as $tid=>$name){
            $masterLocation['labels'][$tid] = $name;
        }
        node_delete($nodeToMerge['nid']);
    }
    
    // clean up the favorites & likes of the master locations
    db_query('DELETE FROM `group_location_favorite` WHERE `nid`=:nid', array('nid'=>$masterLocation['nid']));
    db_query('DELETE FROM `group_location_term` WHERE `nid`=:nid', array('nid'=>$masterLocation['nid']));
    
    // set all the labels, favorites & likes back on the master node
    $masterNode = node_load($masterLocation['nid']);
    foreach($masterLocation['labels'] as $tid=>$name){
        // add all the labels, does not add it if the location has it
        Labels::addLabel($name, $masterNode);
    }
    foreach($masterLocation['favorites'] as $gidpid=>$favorite){
        // set's the location back on the favorites of the group/practice that we have stored
        Favorite::getInstance()->setFavorite($masterLocation['nid'], $favorite['gid'], $favorite['pid']);
    }
    foreach($masterLocation['likes'] as $gidtid=>$like){
        // set's the likes back on the location
        Labels::like($like['tid'], $masterLocation['nid'], $like['gid']);
    }

    exit;
}
