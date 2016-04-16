<?php

/**
  ALTER TABLE `node` ADD COLUMN `point` POINT NULL DEFAULT NULL COMMENT 'the coordinates of the location' AFTER `source`;

  UPDATE `node`
  SET `point`= GeomFromText(
  CONCAT(
  CONCAT(
  CONCAT(
  CONCAT(
  'POINT(',
  node.coordinates_x),' '), node.coordinates_y), ')')) WHERE  `nid`>0 and node.type = 'location';

  X - low value - Horizontal - Longitude
  Y - high value - vertical - latitude


 */
class Search {

    public static $instance = null;
    public $toMuchResults = false;

    /**
     * Lists of characters thac will be replaced in the searchindex & search terms.
     * We do this so a user can search with ë & e, and both will work
     */
    public static $aSpecialChars = array('ë', 'ï', '\'', 'è', 'é', '-', 'û', '"', '&');
    public static $aSpecialCharsReplacements = array('e', 'i', '', 'e', 'e', '', 'u', '', 'en');

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new Search();
        }
        return self::$instance;
    }

    /**
     * Gives you the searchresults html output
     *
     * @param type $output
     */
    public function getResultListHtml($output) {
        
        $pageLength = $output['page_length'];
        $counter = 1;
        $previousPage = 0;
        $page_number = 0;

        global $user;
        $oUser = user_load($user->uid);

        $h = '';
        $h .= '<div id="search_result_info">';
        
        if ($output['has_tags'] || isset($output['loc'])) {
            $h .= '<div id="search_results" class="rounded"><div>';
//            $h .= '<button class="close_box" title="Sluiten"></button>';
            if ($output['resultcounttotal'] >= 1) {
                $h .= '<p>';
                $h .= t("Found the following locations with").' <span class="display_query">'.$output['s'].'</span> :';
                $h .= '</p>';
                $h .= '<ul class="page_0 rl">';
                foreach ($output['searchResults'] as $result) {

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

                    $lastEntry = ($counter == $output['resultcounttotal']);
                    $admin_info = '';
                    $title = $result['t'];
                    if ($output['user_is_admin']) {
                        $admin_info = ($result['s'] - $result['d']) . ' s:' . $result['s'] . ' d:' . $result['d'];
                        $title = $result['t'];
                    }

                    $h .= '<li class="' . $admin_info . '">';
                    $h .= '<a title="' . $title . '" id="loc_' . $result['n'] . '" href="' . $result['n'] . '" rel="' . $result['lo'] . ',' . $result['la'] . '">';
                    $h .= $title;
                    $h .= '</a>';
                    $h .= '</li>';
                    if ($counter % $pageLength == 0) {
                        if (!$lastEntry) {
                            $h .= '</ul>';
                            $page_number = $counter - $pageLength;
                            $style = '';
                            if ($counter != $pageLength) {
                                $style = ' style="display: none;" ';
                            }
                            $h .= '<div class="results_paging page_' . $page_number . '" ' . $style . '>';
                            if ($counter != $pageLength) {
                                $start_number_previous = ($counter - ($pageLength * 2));
                                $h .= '<a href="#" ref="page_' . $start_number_previous . '" class="gbutton gbutton_small rounded noshadow left previous"><span>' . t('Previous') . '</span></a>';
                            }
                            $first_page_class = '';
                            if ($counter == $pageLength) {
                                $first_page_class = ' class="first_page" ';
                            }
                            $h .= '<span ' . $first_page_class . '>';
                            $h .= '<span class="result_txt">' . t('Result') . '&nbsp;</span>';
                            $h .= ($counter - $pageLength) . ' - ' . $counter . ' (' . $output['resultcounttotal'] . ')';
                            $h .= '</span>';
                            $h .= '<a href="#" ref="page_' . $counter . '" class="gbutton gbutton_small rounded noshadow right next"><span>' . t('Next') . '</span></a>';
                            $h .= '</div>';
                            $previousPage = $counter;

                            $h .= '<ul style="display: none;" class="page_' . $counter . '">';
                        }
                    }
                    if ($lastEntry) {
                        $h .= '</ul>';
                        $h .= '<div class="results_paging page_' . $previousPage . '" style="display: none;">';
                        $previous_page_number = $previousPage - $pageLength;
                        $h .= '<a href="#" ref="page_' . $previous_page_number . '" class="gbutton gbutton_small rounded noshadow left previous"><span>' . t('Previous') . '</span></a>';
                        $h .= '<span class="last_page"><span class="result_txt">' . t('Result') . '</span> ' . $previousPage . ' - ' . $counter . ' (' . $output['resultcounttotal'] . ')</span>';

                        if ($output['to_much_results_found'] && $output['to_much_results_found'] == true) {
                            $h .= '<p class="searchresult_note_max">' . t('The system has limited the searchresults to %limit% for performance reasons. If you cannot find what you are looking for, be more specific in your search.', array('%limit%' => variable_get('SEARCH_MAX_RESULT_AMOUNT'))) . '<br /></p>';
                        } else if ($page_number > 1) {
                            $h .= '<p class="searchresult_note_max">' . t('Are you not able to find what you are looking for? This can have several reasons, read about this on the following page.') . '<br /></p>';
                        }

                        $h .= '</div>';
                    }
                    $counter++;
                }
            } else {
                $h .= '<p>'.t("No locations found with searchterm").': <span class="display_query">'.$output['s'].'</span>.</p>';
            }

            if (helper::value($oUser, GojiraSettings::CONTENT_TYPE_SEARCH_GLOBAL_FIELD)) { // user searches on a country level
                $h .= '<div id="search_radius_switcher">U zoekt landelijk, klik <a id="switch_to_region_search">hier</a> om in uw regio te zoeken.</div>'; 
            }else{
                $h .= '<div id="search_radius_switcher">U zoekt regionaal, klik <a id="switch_to_country_search">hier</a> om landelijk te zoeken.</div>'; 
            }

            $h .= '</div></div>';
        }
        $hidden_class = '';
        if ($output['by_id']) {
            $hidden_class = 'hidden';
        }
        $h .= '<div class="rounded ' . $hidden_class . '" id="selected_location_info">';
        $h .= '</div>';
        $h .= '</div>';

        return $h;
    }

    public function getResultItemTableHtml($foundNode) {

        $add_label = '';
        if (user_access(helper::PERMISSION_MODERATE_LOCATION_CONTENT)) {
            $add_label = '<form id="new_label_form_%nid%" class="new_label_form"><div class="new_label_wrapper"><input class="new_label" value="label toevoegen" name="new_label_%nid%" id="new_label_%nid%" /><button class="add_new_label"><span>' . t('+') . '</span></button></div></form>';
        }

        if (user_access(helper::PERMISSION_CORRECT_EXISTING_LOCATIONS)) {
            $inform = '<i class="fa fa-wrench"></i> Gegevens onjuist/onvolledig? <a href="/inform&nid=%nid%" title="Informeer ons">Informeer ons</a> of <a href="/location/correct&nid=%nid%" title="wijzig hier">wijzig hier</a>.';
        } else {
            $inform = '<i class="fa fa-envelope-o"></i> Gegevens onjuist/onvolledig? <a href="/inform&nid=%nid%" title="Informeer ons">Informeer ons</a>.';
        }

        $yournote = t('Your note:') . '<br />';
        $popupHtml = <<<EAT
<div id="location_%nid%" class="search_result_wrapper">
  <button class="close_box" title="Sluiten"></button>
  <div class="grouped">
    <div class="title">
      <h2>%title%</h2>
    </div>
    <div class="category">
      %category%
    </div>
  </div>
  %favorites%
  <div class="adres">
    %adres%
  </div>
  <div class="note">
    <i>{$yournote}</i>
    %note%
  </div>
  <br />
  %url%
  %labels%
  <div class="add_label">
    {$add_label}
  </div>
  <div class="inform">
    {$inform}
  </div>
</div>
EAT;

        $terms = array();
        $term = null;

        $vocabulairyLoaded = null;

        $favoriteClass = "no";
        if (Favorite::getInstance()->isFavorite($foundNode->nid)) {
            $favoriteClass = "yes";
        }

        $url = helper::value($foundNode, GojiraSettings::CONTENT_TYPE_URL_FIELD);
        $email = helper::value($foundNode, GojiraSettings::CONTENT_TYPE_EMAIL_FIELD);
        $note = helper::value($foundNode, GojiraSettings::CONTENT_TYPE_NOTE_FIELD);


        if (trim($url) != '') {
            $url = '<div class="url"><a target="_new" href="http://' . $url . '" title="' . $foundNode->title . '">' . $url . '</a></div>';
        }

        if (trim($email) != '') {
            $email = '<a mailto="' . $email . '" id="mailto">' . $email . '</a>';
        }

        $oCurrentLocation = Location::getCurrentLocationObjectOfUser();

        $sFavorites = '';
        if (user_access(helper::PERMISSION_PERSONAL_LIST)) {
            $sOnList = 'false';
            if (Favorite::getInstance()->isFavorite($foundNode->nid, $oCurrentLocation->nid)) {
                $sOnList = 'true';
            }
            $sFavorites = '<div class="favorites_switch"><a class="in_favorites ' . $sOnList . '" title="Bepaal of deze zorgverlener in uw kaart zit.">In <i>Mijn kaart</i></a></div>';
        }

        $category_txt = '';
        $category_nid = helper::value($foundNode, GojiraSettings::CONTENT_TYPE_CATEGORY_FIELD, 'nid');

        if (is_numeric($category_nid)) {
            $category_node = node_load($category_nid);
            $category_txt = '<p>' . $category_node->title . '</p>';
        }
        if (trim($category_txt) == '') {
            $category_txt = '<p>' . t('This locations has no category.') . '</p>';
        }

        $labels = Labels::draw($foundNode) . Labels::drawMobileLabels($foundNode);

        // format the adres
        $adres = '';
        $street = trim(helper::value($foundNode, GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD));
        $number = trim(helper::value($foundNode, GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD));
        $postcode = trim(helper::value($foundNode, GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD));
        $city = trim(helper::value($foundNode, GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD));
        $telephone = trim(helper::value($foundNode, GojiraSettings::CONTENT_TYPE_TELEPHONE_FIELD));
        $fax = trim(helper::value($foundNode, GojiraSettings::CONTENT_TYPE_FAX_FIELD));
        if ($street . $number !== '') {
            $adres .= $street . ' ' . $number . '<br />';
        }
        if ($postcode . $city !== '') {
            $adres .= $postcode . ' ' . $city . '<br />';
        }
        if ($telephone !== '') {
            $adres .= 'Tel.' . $telephone . '<br />';
        }
        if ($fax !== '') {
            $adres .= 'Fax.' . $fax . '<br />';
        }
        if ($email !== '') {
            $adres .= $email;
        }

        $html = str_replace(
                array('%title%',
            '%adres%',
            '%url%',
            '%labels%',
            '%nid%',
            '%favorites%',
            '%category%',
            '%note%'), array($foundNode->title,
            $adres,
            $url,
            $labels,
            $foundNode->nid,
            $sFavorites,
            $category_txt,
            nl2br(Location::getNote($foundNode->nid, ' <a title="' . t('Edit note') . '" class="fa fa-pencil" href="/editnote?nid=' . $foundNode->nid . '"></a>', t('Empty'))),
                ), $popupHtml);

        return $html;
    }

    /**
     * Does the tags search
     *
     * @param array $labels
     */
    public function doSearch($labels, $search_type = null) {

        // Clean up the labels
        // make from jantje, piet en klaas -=> jantje, piet, en, klaas
        $labels = explode(' ', implode(' ', $labels));
        // clean all tags
        $cleanTags = array();
        foreach ($labels as $label) {
            $c = self::cleanSearchTag($label);
            if($c !== ''){
                $cleanTags[] = $c;
            }
        }
        $labels = helper::cleanArrayWithBlacklist($cleanTags);

        if (count($labels) == 0) {
            return array();
        }

        // get the map center
        $location = Location::getCurrentLocationObjectOfUser(true);

        // set defaults
        global $user;
        $oUser = user_load($user->uid);

        $limitToRegion = false;

        // get search type
        if (is_null($search_type)) {
            if (helper::value($oUser, GojiraSettings::CONTENT_TYPE_SEARCH_GLOBAL_FIELD)) {
                $search_type = helper::SEARCH_TYPE_COUNTRY;
            }else{
                $search_type = helper::SEARCH_TYPE_REGION;
                $limitToRegion = true;
            }
        }


        $locations = self::findLocations($labels, variable_get('SEARCH_MAX_RESULT_AMOUNT'), $location, $limitToRegion, false);

        $locationNids = '0';
        foreach($locations as $location)
        {
            $locationNids .= ','.$location['nid'];
        }


        $foundNodes = array();
        foreach ($labels as $label) {
            $sql = <<<EOT
SELECT searchword_nid.node_nid AS nid, searchword_nid.score AS score, searchword.word FROM searchword JOIN searchword_nid on (searchword.id = searchword_nid.searchword_id) 
WHERE (word LIKE :label1 OR word LIKE :label2) AND searchword_nid.node_nid in ({$locationNids})
EOT;
            $result = db_query($sql, array(':label1' => $label . '%', ':label2' => '%' . $label))->fetchAll();
            foreach ($result as $found) {
                if (array_key_exists($found->nid, $foundNodes)) {
                    $foundNodes[$found->nid]['score'] = (int) ($found->score + $foundNodes[$found->nid]['score']);
                } else {
                    $foundNodes[$found->nid] = $locations[$found->nid];
                    $node = node_load($found->nid);
                    $node->longitude = $locations[$found->nid]['longitude'];
                    $node->latitude = $locations[$found->nid]['latitude'];
                    $foundNodes[$found->nid]['node'] = $node;
                    $foundNodes[$found->nid]['score'] = (int) $found->score;
                }
            }
        }

        usort($foundNodes,"sort_locations");

        return $foundNodes;
    }

    /**
     * Finds the locations eligable for the searchresults based on the labels & distance
     *
     * @param array of labels
     * @param integer Search limit
     * @param bool Locations object of the users practice
     * @param bool Limit by radius if we search from practice
     * @return array
     */
    public function findLocations($labels, $limit, $centerLocation = false, $limitByRadius = false, $filterFavorites = false)
    {
        if(!is_array($labels))
        {
            return array();
        }

        // define the sql part for the distance result SELECT field to order on
        $distanceField = ' 0 as distance ';
        if ($limitByRadius) {
            $lat1 = 'Y(node.point)';
            $lon1 = 'X(node.point)';
            $lat2 = $centerLocation->latitude;
            $lon2 = $centerLocation->longitude;
            $distanceField = <<<EOT
        (1200 * acos( cos( radians($lat1) )
      * cos( radians($lat2) )
      * cos( radians($lon2) - radians($lon1)) + sin(radians($lat1))
      * sin( radians($lat2) )))  as distance
EOT;
        }

        // define the sql part for the distance limit in the WHERE
        $sql_max_distance = '1=1';
        if($limitByRadius & $centerLocation)
        {
            // query get's all the nodes in radius, maybe only from favorites, but surly visible, and filters them on the nodes with the related tags
            $distance = 0.09;
            $iMinLongitude = ($centerLocation->longitude - ($distance * 2));
            $iMaxLongitude = ($centerLocation->longitude + ($distance * 2));
            $iMinLatitude = ($centerLocation->latitude - $distance);
            $iMaxLatitude = ($centerLocation->latitude + $distance);

            $sql_max_distance = " (X(point) BETWEEN {$iMinLongitude} AND {$iMaxLongitude} AND Y(point) BETWEEN {$iMinLatitude} AND {$iMaxLatitude}) ";
        }

        // define the sql part for the labels
        $matchAgainsts = array();
        foreach($labels as $label)
        {
            $matchAgainsts[] =  "MATCH(node.search) AGAINST('{$label}*' IN BOOLEAN MODE)"; // is nog een optie als het veel in tijd uitmaakt
            //$matchAgainsts[] =  "MATCH(node.search) AGAINST('{$label}*' IN BOOLEAN MODE) AND MATCH(node.search) AGAINST('*{$label}' IN BOOLEAN MODE)";
        }
        $matchAgainsts = '('.implode(' AND ', $matchAgainsts).')';

        // favorites filter
        $favJoin = "";
        $favWhere = "1=1";
        if($filterFavorites)
        {
            $gid = Group::getGroupId();
            $favJoin = "join group_location_favorite on (group_location_favorite.nid = node.nid)";
            $favWhere = "group_location_favorite.gid = {$gid}";
        }

        $sql = <<<EOT
SELECT node.title, node.nid, {$distanceField}, X(point) as longitude, Y(point) as latitude
FROM node 
join field_data_field_visible_to_other_user on (node.nid = field_data_field_visible_to_other_user.entity_id) 
{$favJoin} 
WHERE {$matchAgainsts} 
AND {$sql_max_distance} 
AND field_data_field_visible_to_other_user.field_visible_to_other_user_value = 1 
AND field_data_field_visible_to_other_user.bundle = 'location' 
AND field_data_field_visible_to_other_user.delta = 0 
AND {$favWhere} 
LIMIT {$limit}
EOT;

        $result = db_query($sql);

        $results = array();
        foreach($result as $loc)
        {
            $results[$loc->nid] = array(
                'nid'=>$loc->nid,
                'title'=>$loc->title,
                'distance'=>$loc->distance,
                'longitude'=>$loc->longitude,
                'latitude'=>$loc->latitude
            );
        }

        return $results;

    }

    /**
     * Index all the locations for the search
     */
    public function indexAll() {
        set_time_limit(10000000);
        ini_set('memory_limit', '128M');
        $results = db_query("select node.nid from {node} where node.type = 'location' and status = 1")->fetchAll();
        $amount = 0;
        foreach ($results as $result) {
            $this->updateSearchIndex($result->nid);
            $amount++;
        }
        return $amount;
    }

    /**
     * Makes it possible for the admin to reindex some id's
     *
     * @param string $ids
     */
    public function indexSomeNodes($ids) {
        $amount = 0;
        if (user_access('administer')) {

            set_time_limit(10000000);
            ini_set('memory_limit', '128M');
            $results = db_query("select node.nid from {node} where status = 1 and node.type = 'location' and node.nid in ({$ids})")->fetchAll();
            $amount = 0;
            foreach ($results as $result) {
                $this->updateSearchIndex($result->nid);
                $amount++;
            }
        }
        return $amount;
    }

    /**
     * This function updates the nodes that need updating of there indexes
     * This is the case when the last updated timestamp is != with the indexed timestamp
     *
     * @param integer $max
     */
    public function indexNeeded($max = null) {

        if (is_null($max)) {
            $max = variable_get('search_cron_limit', 1000);
        }

        set_time_limit(10000000);
        ini_set('memory_limit', '128M');

        $results = db_query('SELECT nid FROM node WHERE node.type = \'location\' AND indexed != changed limit ' . $max)->fetchAll();

        foreach ($results as $result) {
            $this->updateSearchIndex($result->nid);
        }
    }

    /**
     * Updates the search index for the given node
     *
     * @param stClass|integer $node
     */
    public function updateSearchIndex($node) {

        if (is_numeric($node)) {
            $node = node_load($node);
        }

        $nid = $node->nid;

        $text_array = $this->getSearchNodeText($node);

        // remove all the linked words
        db_query('DELETE FROM `searchword_nid` WHERE  `node_nid`=' . $nid);

        // add the new ones
        $text_string = '';
        foreach ($text_array as $word => $score) {
            $text_string .= ' '.$word;
            $word_id = $this->addUpdateWordToindex($word);
            if ($word_id) {
                $this->linkWordToLocation($word_id, $nid, $score);
            }
        }


        $text_string = self::cleanSearchTag($text_string);
        // tell the system the node is indexed


        db_query('UPDATE `node` SET `search`= \'' . $text_string . '\', `changed`=' . time() . ', `indexed`=' . time() . ' WHERE  `nid`=' . $nid . ';');
    }

    /**
     * Adds a word to the searchindex
     * In the process makes it lowecase
     *
     * @param type $word
     * @return id of the word row
     */
    private function addUpdateWordToindex($word) {
        $word = self::cleanSearchTag($word);
        if ($word != '') {
            $id = db_query("SELECT id FROM searchword WHERE word = '{$word}'")->fetchField();
            if (!$id) {
                db_query("INSERT INTO `searchword` (`word`) VALUES ('{$word}')");
                $id = db_query("SELECT id FROM searchword WHERE word = '{$word}'")->fetchField();
            }
            return $id;
        }
        return false;
    }

    /**
     * Removes current link between location and word, and adds a new one besed on the given information
     *
     * @param int $word_id
     * @param int $location_nid
     * @param int $score
     */
    private function linkWordToLocation($word_id, $location_nid, $score = 1) {
        db_query("DELETE FROM `searchword_nid` WHERE  `node_nid`={$location_nid} AND `searchword_id`={$word_id}");
        db_query("INSERT INTO `searchword_nid` (`node_nid`, `searchword_id`, `score`) VALUES ({$location_nid}, {$word_id}, {$score})");
    }

    /**
     * Get's you the text that needs to be put in the search index for the given node
     *
     * @param stClass $node
     * @return array
     */
    public function getSearchNodeText($oNode) {

        if ($oNode->type != GojiraSettings::CONTENT_TYPE_LOCATION) {
            return array();
        }

        $aText = array();
        $aBlacklist = explode(',', variable_get('gojira_blacklist_search_words'));

        $aText[helper::value($oNode, GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD)] = 1;

        // add the labels to the search, let the labels with more likes weight more
        if (isset($oNode->field_location_labels)) {
            $aField = $oNode->field_location_labels;
            if (array_key_exists(LANGUAGE_NONE, $aField)) {
                foreach ($aField[LANGUAGE_NONE] as $aLabel) {
                    if (!in_array($aLabel, $aBlacklist)) {
                        $iLikes = Labels::getLikes($aLabel['tid'], $oNode->nid);
                        $oTerm = taxonomy_term_load($aLabel['tid']);
                        $sTerm = self::cleanSearchTag($oTerm->name);
                        for ($i = 0; $i <= $iLikes; $i++) {
                            $aText[$sTerm] = $iLikes + 1;
                        }
                    }
                }
            }
        }

        // cut up the title based on spaces and add the words if they are not allready there from the labels
        // so if the city name is allready there and it's in the title it won't be added again
        $aTitles = explode(' ', $oNode->title);
        foreach ($aTitles as $sTitlePart) {
            $sTitle = self::cleanSearchTag($oNode->title);
            if (!array_key_exists($sTitle, $aText)) {
                if (!in_array($sTitle, $aBlacklist)) {
                    $aText[$sTitlePart] = 1;
                }
            }
        }

        // cut up the category based on spaces and add the words if they are not allready there from the labels
        $aCategories = explode(' ', Category::getCategoryName($oNode));
        foreach ($aCategories as $sCategoryPart) {
            $sCategoryPart = self::cleanSearchTag($sCategoryPart);
            if (!array_key_exists($sCategoryPart, $aText)) {
                if (!in_array($sCategoryPart, $aBlacklist)) {
                    $aText[$sCategoryPart] = 1;
                }
            }
        }


        // check if a label had a space, then it needs to be stored as 2 separate words with the score devided
        foreach ($aText as $sLabel => $iScore) {
            $iSpaces = substr_count($sLabel, ' ');
            if ($iSpaces == 1) {
                $iScore = $iScore / 2;
                $aLabels = explode(' ', $sLabel);
                $aKeys = array(0, 1);
                foreach ($aKeys as $iKey) { // do the following for key 0 & 1
                    if (isset($aLabels[$iKey])) {
                        if (array_key_exists($aLabels[$iKey], $aText)) {
                            $aText[$aLabels[$iKey]] = $aText[$aLabels[$iKey]] + $iScore;
                        } else {
                            $aText[$aLabels[$iKey]] = $iScore;
                        }
                    }
                }
                unset($aText[$sLabel]);
            }
        }

        return $aText;
    }

    /**
     * Gives you the correct Location object to base the center of the map on
     *
     * Uses the by the user chosen location unless:
     * A) a specific location is given in the uri
     * B) a city name is given as a tag
     *
     * @return Location
     */
    public function getCenterMap() {
        // return default user Location
        return Location::getCurrentLocationObjectOfUser(true);
    }

    /**
     * Checks all the given tag's in the $_GET for city names, if one if found, returns it. Else returns false.
     *
     * @return boolean|string
     */
    public function getCityNameFromTags() {
        // check if there is a city name given as a tag
        // if so, return that Location
        if (array_key_exists('tags', $_GET)) {

            $citys = Location::getKnownCitys();

            $tags = explode(' ', urldecode($_GET['s']));
            foreach ($tags as $tag) {
                $tag = trim($tag);
                if ($tag != "") {
                    if (array_key_exists(strtolower($tag), $citys)) {
                        return $tag;
                    }
                }
            }
        }
        return false;
    }

    // cleans strings to be used as index words or search terms
    private static function cleanSearchTag($tag) {
        return trim(
                strtolower(
                        preg_replace("/[^A-Za-z0-9 '.]/", '', str_replace(Search::$aSpecialChars, Search::$aSpecialCharsReplacements, $tag)
                        )
                )
        );
    }

    public static function getSearchTypeBasedOnQuery() {
        if (isset($_GET['type'])) {
            if ($_GET['type'] == helper::SEARCH_TYPE_COUNTRY) {
                return helper::SEARCH_TYPE_COUNTRY;
            }
            if ($_GET['type'] == helper::SEARCH_TYPE_OWNLIST) {
                return helper::SEARCH_TYPE_OWNLIST;
            }
        }
        if ($_GET['q'] == helper::SEARCH_TYPE_OWNLIST) {
            return helper::SEARCH_TYPE_OWNLIST;
        }
        if (Locationsets::onLocationset()) {
            return helper::SEARCH_TYPE_OWNLIST;
        }
        return helper::SEARCH_TYPE_REGION;
    }

    /**
     * Tells you if you can show the given search type
     * 
     * @param string $type
     * @return boolean
     */
    public static function searchTypeIsSelected($type) {
        switch ($type) {
            case helper::SEARCH_TYPE_COUNTRY:
                if (user_access(helper::PERMISSION_SEARCH_GLOBAL)) {
                    if (self::getSearchTypeBasedOnQuery() == helper::SEARCH_TYPE_COUNTRY) {
                        return true;
                    }
                }
                break;
            case helper::SEARCH_TYPE_LOCATIONSET:
                if (user_access(helper::PERMISSION_LOCATIONSETS)) {
                    if (self::getSearchTypeBasedOnQuery() == helper::SEARCH_TYPE_LOCATIONSET || Locationsets::onLocationset()) {
                        return true;
                    }
                }
                break;
            case helper::SEARCH_TYPE_OWNLIST:
                if (user_access(helper::PERMISSION_PERSONAL_LIST)) {
                    if (self::getSearchTypeBasedOnQuery() == helper::SEARCH_TYPE_OWNLIST && Locationsets::onOwnMap() && !Locationsets::onLocationset()) {
                        return true;
                    }
                }
                break;
        }

        if (self::getSearchTypeBasedOnQuery() == helper::SEARCH_TYPE_REGION && helper::SEARCH_TYPE_REGION == $type) {
            return true;
        }

        return false;
    }

    public static function searchInOwnMap($tags) {
//        $currentPractice = Location::getCurrentLocationNodeObjectOfUser();
//        $ownlistLocations = Favorite::getInstance()->getAllFavoriteLocations($currentPractice->nid);
//
//        $foundNodes = array();
//        $nidsSql = '0';
//
        if (!is_array($tags)) {
            $tags = explode(' ', $tags);
        }

        $cleanTags = array();
        foreach ($tags as $tag) {
            $clean = self::cleanSearchTag($tag);
            if ($clean != '') {
                $cleanTags[] = self::cleanSearchTag($tag);
            }
        }

        $foundLocations = self::findLocations($cleanTags, 1000, false, false, true);

        foreach ($foundLocations as $foundLocation) {
            $foundLocation['node'] = node_load($foundLocation['nid']);
        }

//        foreach ($ownlistLocations as $ownlistLocation) {
//            if (array_key_exists($ownlistLocation->nid, $foundLocations)) {
//                $return[$ownlistLocation->nid] = $foundLocations;
//            }
//        }

//        // get an array with all the nids related to the searchwords based on the labels
//        $nodeCounter = array();
//        $foundNodes = array();
//        foreach ($cleanTags as $tag) {
//            $sql = "SELECT searchword_nid.node_nid AS nid, searchword.word AS word, searchword_nid.score AS score FROM {searchword} JOIN {searchword_nid} on (searchword.id = searchword_nid.searchword_id) WHERE word LIKE :label1 OR word LIKE :label2";
//            $result = db_query($sql, array(':label1' => $tag . '%', ':label2' => '%' . $tag))->fetchAll();
//            foreach ($result as $found) {
//                $nodeCounter[$tag . $found->nid] = true;
//                $foundNodes[$found->nid] = $found->nid;
//            }
//        }
//
//        // clean the resultset of all nodes that do not have hits on all the labels
//        // this part makes a AND function of the search
//        foreach ($foundNodes as $nid => $foundNode) {
//            foreach ($cleanTags as $tag) {
//                if (!isset($nodeCounter[$tag . $nid])) {
//                    // node has no hits on one of the labels, remove it
//                    if (isset($foundNodes[$nid])) {
//                        unset($foundNodes[$nid]);
//                    }
//                }
//            }
//        }
//
//        $return = array();
//        foreach ($ownlistLocations as $ownlistLocation) {
//            if (array_key_exists($ownlistLocation->nid, $foundNodes)) {
//                $loc = Location::getLocationObjectOfNode($ownlistLocation->nid);
//                if ($loc) {
//                    $ownlistLocation->latitude = $loc->latitude;
//                    $ownlistLocation->longitude = $loc->longitude;
//                }
//                $return[$ownlistLocation->nid] = $ownlistLocation;
//            }
//        }

        return $foundLocations;
    }
}

/**
 * Sort the search results
 *
 * @param array $loc1
 * @param array $loc2
 * @return int
 */
function sort_locations($loc1,$loc2)
{

    if($loc1['score'] == $loc2['score'])
    {
        // if the score is equal, base the sort on the distance
        if($loc1['distance'] == $loc2['distance'])
        {
            return 0;
        }
        if($loc1['distance'] < $loc2['distance'])
        {
            return -1;
        }
        if($loc1['distance'] > $loc2['distance'])
        {
            return 1;
        }
    }
    if($loc1['score'] < $loc2['score'])
    {
        return -1;
    }
    if($loc1['score'] < $loc2['score'])
    {
        return 1;
    }
}
