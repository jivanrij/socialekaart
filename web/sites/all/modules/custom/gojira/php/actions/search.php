<?php

// ajax action that is called when searching
function search() {
    global $user;
    $user = user_load($user->uid);

    $user_is_admin = false;
    if (in_array('administrator', array_values($user->roles))) {
        $user_is_admin = true;
    }

    $single_location = (isset($_GET['s']) && is_numeric($_GET['s']));

    $output['s'] = '';
    $searchResults = array();
    $foundNodes = array();
    $output['has_tags'] = true;
    $output['by_id'] = false;

    if (isset($_GET['s']) && ($_GET['s'] == 'locationsset')) {
        // SHOW OWN LIST ON THE LOCATIONSSET TEMPLATE
        if (isset($_GET['cat_id']) && is_numeric($_GET['cat_id'])) {
            if ($_GET['id'] == 'favorites') { // display own list
                $foundNodes = Favorite::getInstance()->getAllFavoritesInCategory($_GET['cat_id']);
            } else { // display a map
                $foundNodes = Locationsets::getInstance()->getLocations($_GET['id'], $_GET['cat_id']);
            }
        }
    } else if ($single_location) {
        // we have been given a nid as a tag, let's show a single location
        $output['by_id'] = $_GET['s'];
        $foundNodes[$_GET['s']] = node_load($_GET['s']);
    } else if (isset($_GET['s']) && $_GET['s'] != '') {

        // NORMAL SEARCH

        $tags = explode(' ', urldecode($_GET['s']));

        $filteredTags = array();
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if ($tag != "") {
                $filteredTags[] = $tag;
            }
        }
        
        // get all the nodes based on the normal tags
        $foundNodes = Search::getInstance()->doSearch($filteredTags);

        $output['s'] = implode(' ', $filteredTags);
    } else {
        $output['has_tags'] = false;
    }

    $popupHtml = '';
    $lonLow = null;
    $lonHigh = null;
    $latLow = null;
    $latHigh = null;
    // format the results to smaller data
    foreach ($foundNodes as $key => $foundNode) {

        if (is_null($foundNode->longitude) && is_null($foundNode->latitude)) {
            $location = Location::getLocationObjectOfNode($foundNode->nid);
            if($location){
                $foundNode->latitude = $location->getLatitude();
                $foundNode->longitude = $location->getLongitude();
            }else{
                $foundNode->latitude = null;
                $foundNode->longitude = null;
            }
        }

        if ((is_null($latLow) || $foundNode->latitude <= $latLow) && !is_null($foundNode->latitude)) {
            $latLow = $foundNode->latitude;
        }
        if ((is_null($lonLow) || $foundNode->longitude <= $lonLow)  && !is_null($foundNode->longitude)) {
            $lonLow = $foundNode->longitude;
        }
        if ((is_null($latHigh) || $foundNode->latitude >= $latHigh)  && !is_null($foundNode->latitude)) {
            $latHigh = $foundNode->latitude;
        }
        if ((is_null($lonHigh) || $foundNode->longitude >= $lonHigh)  && !is_null($foundNode->longitude)) {
            $lonHigh = $foundNode->longitude;
        }

        $searchResults[] = array(
            'd' => $foundNode->distance,
            's' => $foundNode->score,
            'n' => $foundNode->nid,
            't' => $foundNode->title,
            'lo' => $foundNode->longitude,
            'la' => $foundNode->latitude
        );
    }

    $searchResultsJavascript = _merge_and_strip_searchresults_for_js($searchResults, $output['has_tags']);

    $output['searchResults'] = $searchResults;
    $output['resultcounttotal'] = count($searchResults);

    $mobileDetect = new Mobile_Detect();
    if ($mobileDetect->isTablet()) {
        $output['page_length'] = 5;
    } else {
        $output['page_length'] = 10; // TODO make 10
    }

    $output['to_much_results_found'] = Search::getInstance()->toMuchResults;
    $output['user_is_admin'] = $user_is_admin;

    if ($output['by_id']) {
        $location = Location::getLocationObjectOfNode($output['by_id']);
    }

    if (!$location) {
        $location = Location::getCurrentLocationObjectOfUser(true);
    }

    if ($location) {
        $output['longitude'] = $location->longitude;
        $output['latitude'] = $location->latitude;
    }

    $output['mapSearchResults'] = array_values($searchResultsJavascript);
    $output['mapSearchResultsCount'] = count($searchResultsJavascript);
    $output['page'] = 'gojirasearch';
    $output['url'] = '/';
    if (isset($output['by_id']) && $output['by_id'] && isset($_GET['loc'])) {
        $output['loc'] = $_GET['loc'];
    } else {
        $output['loc'] = 0;
    }
    $output['has_tags'] = $output['has_tags'];
    $output['tags_changed_message'] = t('Tags successfully changed');
    $output['tags_not_changed_message'] = t('Failed to modify tags');

    // determ the zoom level that is shown after a search result
    if (helper::value($user, GojiraSettings::CONTENT_TYPE_SEARCH_GLOBAL_FIELD)) { // user searches on a country level
        if (isset($_GET['s']) && ($_GET['s'] == 'ownlist')) { // country level == on, but also filters on favorites
            $output['zoom'] = GojiraSettings::MAP_ZOOMLEVEL_REGION; // show region level
        } else if (Search::getInstance()->getCityNameFromTags()) { // country level == on, but also searches with a city name
            $output['zoom'] = GojiraSettings::MAP_ZOOMLEVEL_STREET; // show street level
        } else { // country == on
            $output['zoom'] = GojiraSettings::MAP_ZOOMLEVEL_COUNTRY; // show country level
        }
    } else { // normal search result
        $output['zoom'] = GojiraSettings::MAP_ZOOMLEVEL_STREET; // show street level
    }

    if (isset($_GET['s']) && ($_GET['s'] == 'locationsset')) {
        $output['results_html'] = '';
    } else {
        $output['results_html'] = Search::getInstance()->getResultListHtml($output);
    }

    $output['single_location'] = $single_location;

    if (!is_null($latLow) && !is_null($lonLow) && !is_null($latHigh) && !is_null($lonHigh)) {
        $output['boxInfo'] = array(
            'latLow' => $latLow,
            'lonLow' => $lonLow,
            'latHigh' => $latHigh,
            'lonHigh' => $lonHigh
        );
    } else {
        $output['boxInfo'] = array(
            'latLow' => $location->latitude,
            'lonLow' => $location->longitude,
            'latHigh' => $location->latitude,
            'lonHigh' => $location->longitude
        );
    }
    echo json_encode($output, true);
    exit;
}

/**
 * merge results if they are to close, based on there coordinates
 * imagine: result 1,2,3 & 4
 * and 1 & 2 are close to eachother this code will merge this array into the following: a(2=>(1,2),3,4). 2 will have the coordinates of 1
 * and 1, 2 & 3 are close to eachother this code will merge this array into the following: a(2=>(1,2,3),4). 2 will have the coordinates of 1
 *
 * @param type $searchResults
 */
function _merge_and_strip_searchresults_for_js($searchResults, $hasTags) {

    // used array keys, made them shorter for load speed
    // d distance
    // s score
    // n nid
    // x self
    // t title
    // lo longitude
    // la latutude
    // c count_merged
    // m merged
    // h merged_html
    // always have your own location on the map

    $returnArray = array();

    $originalResults = $searchResults; // make a backup of the original data, don't change this info
    $adopted = array(); // this array holds nid's of locations that are merged with others

    foreach ($searchResults as $result) {

        unset($result['d']);
        unset($result['s']);

        // for each result
        // if i'm never merged before
        if (!array_key_exists($result['n'], $adopted)) {

            // check each all other results
            foreach ($originalResults as $originalResult) {

                if ($originalResult['n'] != $result['n'] && !array_key_exists($originalResult['n'], $adopted) && Location::locationsAreClose($result['lo'], $result['la'], $originalResult['lo'], $originalResult['la'])) {

                    // now we know that $result[nid] and $originalResult[nid] belong together

                    $mergeMasterNid = $result['n'];

                    // merge them
                    $returnArray[$mergeMasterNid]['m'][$originalResult['n']] = $originalResult;
                    $returnArray[$mergeMasterNid]['m'][$result['n']] = $result;

                    $returnArray[$mergeMasterNid]['c'] = count($returnArray[$mergeMasterNid]['m']);
                    $returnArray[$mergeMasterNid]['lo'] = $result['lo'];
                    $returnArray[$mergeMasterNid]['la'] = $result['la'];

                    // let's remind them
                    $adopted[$originalResult['n']] = $originalResult['n'];
                    $adopted[$result['n']] = $result['n'];
                } else {
                    // not close to anyone, let's store it if it's not stored allready
                    if (!array_key_exists($result['n'], $adopted)) {
                        $adopted[$result['n']] = $result['n'];
                        $returnArray[$result['n']] = $result;
                        $returnArray[$result['n']]['c'] = 0;
                    }
                }
            }
        }
    }



    foreach ($returnArray as $key => $return) {
        $iCounter = 0;
        $aNodes = array();
        $selfHtml = null;
        if ($return['c'] > 1) {
            $mergedHtml = '';
            foreach ($return['m'] as $mergedOne) {
                if ($hasTags) {
                    $iCounter++;
                    $mergedHtml .= '<li id="map_link_to_' . $mergedOne['n'] . '" class="map_link_to "><a onClick="focusLocation(' . $mergedOne['n'] . ');return false;" href="#' . $mergedOne['n'] . '" title="' . $mergedOne['t'] . '">' . $mergedOne['t'] . '</a></li>';
                    $aNodes[] = $mergedOne['n'];
                } else {
                    $iCounter++;
                    $mergedHtml .= '<li id="map_link_to_' . $mergedOne['n'] . '" class="map_link_to "><a onClick="gotoLocation(' . $mergedOne['n'] . ');return false;" href="#' . $mergedOne['n'] . '" title="' . $mergedOne['t'] . '">' . $mergedOne['t'] . '</a></li>';
                    $aNodes[] = $mergedOne['n'];
                }
            }

            $sNodes = implode(',', $aNodes);

            if ($selfHtml !== null) {
                $mergedHtml = $selfHtml . $mergedHtml;
            }

            $mergedHtml = '<ul>' . $mergedHtml . '</ul>';

            $sUid = uniqid();

            if ($iCounter > 1) {
                $mergedHtml .= '<span id="report_double_' . $sUid . '"><a href="#" onClick="reportDoublePractices(\'' . $sNodes . '\',\'' . $sUid . '\');return false;">' . t('Click here if you think these practices are double') . '</a></span>';
            }

            $returnArray[$key]['h'] = $mergedHtml;
        }
    }
    return $returnArray;
}
