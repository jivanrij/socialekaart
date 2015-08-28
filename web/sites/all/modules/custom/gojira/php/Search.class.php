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
 */

class Search {

    const SEARCH_RADIUS = 10;

    public static $instance = null;
    public $toMuchResults = false;

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
            $h .= '<div id="search_results" class="rounded">';
            if ($output['city_in_tag'] && $output['check_city'] == true && !helper::value($oUser, GojiraSettings::CONTENT_TYPE_SEARCH_GLOBAL_FIELD)) {
                $h .= '<p class="info_text">';
                $h .= t('You are searching in the area of %city%.', array('%city%' => $output['city_in_tag'])) . '<br />';
                $h .= '<a id="search_own_area" href="/?tags=' . str_replace(' ', '', $_GET['tags']) . '&check_city=0" title="' . t('Search in your own area') . '">' . t('Search in your own area') . '</a>';
                $h .= '</p>';
            }
            if ($output['resultcounttotal'] >= 1) {
                $h .= '<p>';
                if ($_GET['tags'] == 'favorites') {
                    $h .= t('Your favorites') . ':';
                } else {
                    $h .= t('Found locations') . ':';
                }
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
                    
                    if ($result['x']) {
                        continue;
                    }
                    $lastEntry = ($counter == $output['resultcounttotal']);
                    $admin_info = '';
                    if ($output['user_is_admin']) {
                        $admin_info = 'nid' . $result['n'] . 'score' . $result['s'] . 'd' . $result['d'];
                    }

                    $h .= '<li class="' . $admin_info . '">';
                    $h .= '<a id="loc_' . $result['n'] . '" href="' . $result['n'] . '" rel="' . $result['lo'] . ',' . $result['la'] . '">';
                    $h .= $result['t'];
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
                $h .= '<p>' . $output['nothing_found_message'] . '</p>';
            }


            $h .= '</div>';
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

        $improve_txt = t('Is this information incorrect of incomplete?');
        $improve_link = t('Report it here.');

        $popupHtml = <<<EAT
<div id="location_%nid%" class="search_result_wrapper">
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
    %street% %housenr%<br />
    %postcode%, %city%<br />
    %phone%<br />
    %email%
  </div>
  <br />
  %url%
  %labels%
  <div class="add_label">
    {$add_label}
  </div>
  <div class="inform">
    {$improve_txt} <a href="?q=inform&nid=%nid%" title="{$improve_txt} {$improve_link}">{$improve_link}</a>
  </div>
  %admin%
</div>
EAT;

        $terms = array();
        $term = null;

        $vocabulairyLoaded = null;

        $favoriteClass = "no";
        if (Favorite::getInstance()->isFavorite($foundNode->nid)) {
            $favoriteClass = "yes";
        }

        $adminLink = '';
        if (user_access(helper::PERMISSION_SHOW_DEBUG)) {

            $search_index_info = '';
            $index_info = db_query('select word, score from searchword join searchword_nid on (searchword.id = searchword_nid.searchword_id) where searchword_nid.node_nid = ' . $foundNode->nid);
            foreach ($index_info as $info) {
                $search_index_info .= 'score:' . $info->score . ' word:' . $info->word . '<br />';
            }
            $search_index_info = '<p>' . $search_index_info . '</p>';

            $adminLink = $search_index_info . '<div class="admin_info"><a href="/admin/config/system/gojiratools&redirect_to_view=1&index_some=' . $foundNode->nid . '">Reindex location</a> <a href="/showlocation&loc=' . $foundNode->nid . '" title="weergeven in frontend">weergeven</a> <a href="/location/edit&id=' . $foundNode->nid . '" title="beheren in frontend">frontend</a> <a title="beheren in de backend" href="/node/' . $foundNode->nid . '/edit&destination=admin/content">backend</a> <!-- &nbsp; <a target="_blank" href="https://maps.google.com/maps?q=' . helper::value($foundNode, GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD) . '+' . helper::value($foundNode, GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD) . ',' . helper::value($foundNode, GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD) . ',' . helper::value($foundNode, GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD) . '" title="weergeven in google maps">google maps</a>--></div>';
        }

        $url = helper::value($foundNode, GojiraSettings::CONTENT_TYPE_URL_FIELD);
        $email = helper::value($foundNode, GojiraSettings::CONTENT_TYPE_EMAIL_FIELD);
        $note = helper::value($foundNode, GojiraSettings::CONTENT_TYPE_NOTE_FIELD);

        if (trim($url) != '') {
            $url = '<div class="url"><a target="_new" href="http://' . $url . '" title="' . $foundNode->title . '">' . $url . '</a></div>';
        }

        if (trim($email) != '') {
            $email = '<a mailto="' . $email . '" id="mailto">' . $email . '</a><br />';
        }

        
        $oCurrentLocation = Location::getCurrentLocationObjectOfUser();
        $favorites = '';
        if (Favorite::getInstance()->isFavorite($foundNode->nid, $oCurrentLocation->nid)) {
            $fav_class = 'yes';
        } else {
            $fav_class = 'no';
        }
        $favorites = '';
        if (user_access(helper::PERMISSION_MODERATE_LOCATION_CONTENT)) {
            $favorites = '<div class="favorites"><span class="fav_row ' . $fav_class . '"><span id="fav_switch_label">' . t('Put on my list:') . ' </span><button class="fav_yes rounded noshadow">' . t('Yes') . '</button> / <button class="fav_no rounded noshadow">' . t('No') . '</button></span></div>';
        } else {
            if ($fav_class == 'yes') {
                $favorites = '<div class="favorites"><span>' . t('Is part of your favorites.') . '</span></div>';
            }
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

        $labels = Labels::draw($foundNode);

        $html = str_replace(
                array('%title%',
            '%street%',
            '%housenr%',
            '%postcode%',
            '%city%',
            '%phone%',
            '%email%',
            '%url%',
            '%labels%',
            '%admin%',
            '%nid%',
            '%favorites%',
            '%category%'), array($foundNode->title,
            helper::value($foundNode, GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD),
            helper::value($foundNode, GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD),
            helper::value($foundNode, GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD),
            helper::value($foundNode, GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD),
            helper::value($foundNode, GojiraSettings::CONTENT_TYPE_TELEPHONE_FIELD),
            $email,
            $url,
            $labels,
            $adminLink,
            $foundNode->nid,
            $favorites,
            $category_txt), $popupHtml);

        return $html;
    }

    /**
     * Does the tags search
     * 
     * @param array $labels
     */
    public function doSearch($labels, $check_city = true, $force_global = false, $force_favorites = false) {
        $found = array();
        $foundNodes = array();

        global $user;
        $user = user_load($user->uid);

        if (count($labels) == 0) {
            return array();
        }

        // make from jantje, piet en klaas -=> jantje, piet, en, klaas
        $labels = explode(' ', implode(' ', $labels));

        // make lowercase of all tags
        // remove all the city names
        // remove all the blacklisted words
        $lowertags = array();
        foreach ($labels as $label) {
            $label = strtolower($label);
            // it's no city and checkcity is true add the label to the labels to search on
            // or checkcity is false, then always add the labels to search with
            if (($check_city && !Location::isKnownCity($label)) || !$check_city) {
                $lowerlabels[] = helper::cleanSearchTag($label);
            }
        }

        $lowerlabels = helper::cleanArrayWithBlacklist($lowerlabels);

        $labels = $lowerlabels;

        if (count($labels) == 0) {
            return array();
        }

        // for each tag:
        // A) get all nodes that have it linked
        // B) store them all in array with related score.
        // C) if node is allready saved with a score from previous tag, add score
        // also make a comma seporated string with the id's for in the other search query
        $foundNodes = array();
        $nidsSql = '0';
        foreach ($labels as $label) {
            $sql = "SELECT searchword_nid.node_nid AS nid, searchword.word AS word, searchword_nid.score AS score FROM {searchword} JOIN {searchword_nid} on (searchword.id = searchword_nid.searchword_id) WHERE word LIKE :label1 OR word LIKE :label2";
            $result = db_query($sql, array(':label1' => $label . '%', ':label2' => '%' . $label))->fetchAll();
            foreach ($result as $found) {
                if (array_key_exists($found->nid, $foundNodes)) {
                    $foundNodes[$found->nid] = (int) ($found->score + $foundNodes[$found->nid]);
                } else {
                    $foundNodes[$found->nid] = (int) $found->score;
                    $nidsSql .= ',' . $found->nid;
                }
                if ($found->word == $label) {
                    $foundNodes[$found->nid] = $foundNodes[$found->nid] + 0.5;
                }
            }
        }

        // get the highest score
        $highest_score = 0;
        foreach ($foundNodes as $sid => $score) {
            if ($highest_score < $score) {
                $highest_score = $score;
            }
        }

        // build the case to add the score field to the query
        if (count($foundNodes)) {
            $score_sql = ' (CASE ';
            foreach ($foundNodes as $nid => $score) {
                $score_sql .= " WHEN node.nid = {$nid} THEN {$score} ";
            }
            $score_sql .= " ELSE 0 END) AS score, ";
        } else {
            $score_sql = ' 0 as score, ';
        }

        $location = $this->getCenterMap($check_city);

        $favoriteFilter = '';
        if ($force_favorites || helper::value($user, GojiraSettings::CONTENT_TYPE_SEARCH_FAVORITES_FIELD)) {
            $favoriteFilter = " AND group_location_favorite.gid = " . Group::getGroupId();
        }

        if (count($labels)) {
            $relatedNids = ' node.nid in (' . $nidsSql . ') ';
        } else {
            $relatedNids = ' 1=1 ';
        }

        $order_by_sql = 'ORDER BY score desc, distance asc';
        $distance = 0.09;
        if ($force_global || helper::value($user, GojiraSettings::CONTENT_TYPE_SEARCH_GLOBAL_FIELD)) {
            $distance = 20.0;
            if (!$this->getCityNameFromTags()) {
                // only remove the distance order (this one retrieves all the search results in a radius) when we search global && don't search a city
                $order_by_sql = 'ORDER BY score desc';
            }
        }


        // add admin link to the result if you are admin
        $visible_join = ' join field_data_field_visible_to_other_user on (node.nid = field_data_field_visible_to_other_user.entity_id) ';
        $visible_where = " AND field_data_field_visible_to_other_user.field_visible_to_other_user_value = 1 AND field_data_field_visible_to_other_user.bundle = 'location' AND field_data_field_visible_to_other_user.delta = 0 ";

        $filter = '';
        if (variable_get('gojira_search_in', 'all') == 'adhocdata') {
            $filter = " AND node.source = 'adhocdata' ";
        } elseif (variable_get('gojira_search_in', 'all') == 'spider') {
            $filter = " AND node.source = 'spider' ";
        } elseif (variable_get('gojira_search_in', 'all') == 'gojira') {
            $filter = " AND node.source = 'gojira' ";
        } elseif (variable_get('gojira_search_in', 'all') == 'adhocdata_gojira') {
            $filter = " AND (node.source = 'adhocdata' OR node.source = 'gojira') ";
        } elseif (variable_get('gojira_search_in', 'all') == 'spider_gojira') {
            $filter = " AND (node.source = 'spider' OR node.source = 'gojira') ";
        }
        
        $limit = variable_get('SEARCH_MAX_RESULT_AMOUNT') + 1; // let's add one to the result, so we can check if we have more results the the max, afterwards remove it
        // query get's all the nodes in radius, maybe only from favorites, but surly visible, and filters them on the nodes with the related tags

$iMinLongitude = ($location->longitude-$distance);
$iMaxLongitude = ($location->longitude+$distance);
$iMinLatitude = ($location->latitude-$distance);
$iMaxLatitude = ($location->latitude+$distance);

//if(true){
//    $sql_max_distance = " AND (X(point) BETWEEN {$iMinLongitude} AND {$iMaxLongitude} AND Y(point) BETWEEN {$iMinLatitude} AND {$iMaxLatitude}) ";
//    $sDistanceField = "((ACOS(SIN({$location->latitude} * PI() / 180) * SIN(Y(point) * PI() / 180) + COS({$location->latitude} * PI() / 180) * COS(Y(point) * PI() / 180) * COS(({$location->longitude} - (point)) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) as distance ";
//    
//    
//    //$sDistanceField = " (POWER(ABS({$location->longitude} - X(point)),2) + POWER(ABS({$location->latitude} - Y(point)),2)) as distance ";
//    
//
//}else{
//    $sql_max_distance = " AND (`node`.`cordinates_x` BETWEEN {$iMinLongitude} AND {$iMaxLongitude} AND `node`.`cordinates_y` BETWEEN {$iMinLatitude} AND {$iMaxLatitude}) ";
//    $sDistanceField = "((ACOS(SIN({$location->latitude} * PI() / 180) * SIN(`node`.`cordinates_y` * PI() / 180) + COS({$location->latitude} * PI() / 180) * COS(`node`.`cordinates_y` * PI() / 180) * COS(({$location->longitude} - `node`.`cordinates_x`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) as distance ";    
//}

    $sql_max_distance = " AND (X(point) BETWEEN {$iMinLongitude} AND {$iMaxLongitude} AND Y(point) BETWEEN {$iMinLatitude} AND {$iMaxLatitude}) ";
    $sDistanceField = "((ACOS(SIN({$location->latitude} * PI() / 180) * SIN(Y(point) * PI() / 180) + COS({$location->latitude} * PI() / 180) * COS(Y(point) * PI() / 180) * COS(({$location->longitude} - (point)) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) as distance ";

        
        $sql = <<<EOT
SELECT 
node.nid, node.title,
{$score_sql}
{$sDistanceField}
FROM node 
{$visible_join} 
left join group_location_favorite on (group_location_favorite.nid = node.nid)
WHERE {$relatedNids}
{$sql_max_distance}
{$favoriteFilter} 
{$visible_where} 
{$filter} 
GROUP BY node.nid {$order_by_sql} LIMIT {$limit} 
EOT;


        $results = db_query($sql);

        $counter = 0;
        $resultNodes = array();
        foreach ($results as $result) {
            
            $distance = $result->distance;
            $score = $result->score;

            // we got one extra to check if we are above the max amount of results, let's check that here, and remove the one the is to much
            $counter++;
            if ($counter > variable_get('SEARCH_MAX_RESULT_AMOUNT')) {
                Search::getInstance()->toMuchResults = true;
                break;
            }

            $node = node_load($result->nid);
            $node->score = $score;
            $node->self = false;
            $node->distance = $distance;

            $resultNodes[$result->nid] = $node;
        }
        return $resultNodes;
    }

    /**
     * Index all the locations for the search
     */
    public function indexAll() {
        set_time_limit(10000000);
        ini_set('memory_limit', '128M');
        $results = db_query("select node.nid from {node} where node.type = 'location'")->fetchAll();
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
            $results = db_query("select node.nid from {node} where node.type = 'location' and node.nid in ({$ids})")->fetchAll();
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
     * 
     * @param integer $max
     */
    public function indexNeeded($max = null) {

        if (is_null($max)) {
            $max = variable_get('search_cron_limit', 1000);
        }

        set_time_limit(10000000);
        ini_set('memory_limit', '128M');

        $results = db_query('SELECT nid FROM node JOIN field_data_field_visible_to_other_user ON (node.nid = field_data_field_visible_to_other_user.entity_id) WHERE field_data_field_visible_to_other_user.field_visible_to_other_user_value = 1 AND field_data_field_visible_to_other_user.bundle = \'location\' AND field_data_field_visible_to_other_user.delta = 0 AND indexed != changed limit ' . $max)->fetchAll();

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
        foreach ($text_array as $word => $score) {
            $word_id = $this->addUpdateWordToindex($word);
            if ($word_id) {
                $this->linkWordToLocation($word_id, $nid, $score);
            }
        }

        // tell the system the node is indexed
        db_query('UPDATE `node` SET `changed`=' . time() . ', `indexed`=' . time() . ' WHERE  `nid`=' . $nid . ';');
    }

    /**
     * Adds a word to the searchindex
     * In the process makes it lowecase
     * 
     * @param type $word
     * @return id of the word row
     */
    private function addUpdateWordToindex($word) {
        $word = strtolower(helper::cleanSearchTag($word));
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
    public function getSearchNodeText($node) {

        if ($node->type != GojiraSettings::CONTENT_TYPE_LOCATION) {
            return array();
        }

        $text_array = array();
        $blacklist = explode(',', variable_get('gojira_blacklist_search_words'));

        // add the labels to the search, let the labels with more likes weight more
        $field = GojiraSettings::CONTENT_TYPE_LOCATION_VOCABULARY_FIELD;
        $field = $node->$field;
        if (array_key_exists('und', $field)) {
            foreach ($field[LANGUAGE_NONE] as $label) {
                if (!in_array($label, $blacklist)) {
                    $likes = Labels::getLikes($label['tid'], $node->nid);
                    $termStd = taxonomy_term_load($label['tid']);
                    $term = helper::cleanSearchTag($termStd->name);
                    for ($i = 0; $i <= $likes; $i++) {
                        $text_array[$term] = $likes + 1;
                    }
                }
            }
        }

        // cut up the title based on spaces and add the words if they are not allready there from the labels
        $titles = explode(' ', $node->title);
        foreach ($titles as $title_part) {
            $title = helper::cleanSearchTag($node->title);
            if (!array_key_exists($title, $text_array)) {
                if (!in_array($title, $blacklist)) {
                    $text_array[$title_part] = 1;
                }
            }
        }

        // cut up the category based on spaces and add the words if they are not allready there from the labels
        $categories = explode(' ', Category::getCategoryName($node));
        foreach ($categories as $category_part) {
            $category_part = helper::cleanSearchTag($category_part);
            if (!array_key_exists($category_part, $text_array)) {
                if (!in_array($category_part, $blacklist)) {
                    $text_array[$category_part] = 1;
                }
            }
        }

        return $text_array;
    }

    /**
     * Get's you all the location nodes
     * 
     * @return array of nodes
     */
//  public function getAllLocationNodes(){
//
//    set_time_limit(10000000);
//    
//    $location = $this->getCenterMap();
//    $params[':x'] = $location->longitude;
//    $params[':y'] = $location->latitude;
//
//    //$sql = "select nid from {node} where node.status = 1 and type = 'location'";
//    
//    $sql = "SELECT nid from {node} where type = 'location';";
//    
//    $nodes = array();
//    $results = db_query($sql, $params);
//    foreach($results as $result){
//      $this->addLocationToResult($nodes, $result->nid);
//    }
//
//    return $nodes;
//  }

    /**
     * Get's all locations with a tag
     * 
     * @param string $tag
     * @return locations
     */
//  public function getAllLocationsWithTag($tag) {
//    set_time_limit(10000000);
//
//    $results = db_query("select node.nid from {node} join field_data_field_location_vocabulary on (field_data_field_location_vocabulary.entity_id = node.nid) join taxonomy_term_data on (taxonomy_term_data.tid = field_data_field_location_vocabulary.field_location_vocabulary_tid) where node.type = 'location' and taxonomy_term_data.name = '" . $tag . "'")->fetchAll();
//    $nodes = array();
//    foreach ($results as $result) {
//      $result->score = 0;
//      $result->distance = 0;
//      $result->self = 0;
//      $nodes[] = $result;
//    }
//    
//    return $nodes;
//  }

    /**
     * Gives you the correct Location object to base the center of the map on
     * 
     * Uses the by the user chosen location unless:
     * A) a specific location is given in the uri
     * B) a city name is given as a tag
     * 
     * @return Location
     */
    public function getCenterMap($checkForCity = true) {

        if ($checkForCity) {
            $city = $this->getCityNameFromTags();
            if ($city) {
                $location = Location::getLocationForAddress($city . ',the netherlands');
                if ($location) {
                    $this->mapCenter = $location;
                    return $this->mapCenter;
                }
            }
        }

        // return default user Location
        return Location::getCurrentLocationObjectOfUser(true);
    }

    /**
     * Checks all the given tag's in the $_GET for city names, if one if found, returns if. Else returns false.
     * 
     * @return boolean|string
     */
    public function getCityNameFromTags() {
        // check if there is a city name given as a tag
        // if so, return that Location
        if (array_key_exists('tags', $_GET)) {

            $citys = Location::getKnownCitys();

            $tags = explode(' ', urldecode($_GET['tags']));
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

    /**
     * Add a location to a result array with the related checks
     * 
     * @param array $nodes
     * @param integer $nid
     * @param boolean|null $personal
     * @param integer|null $distance
     */
//  private function addLocationToResult(&$nodes, $nid, $distance = null) {
//    $n = node_load($nid);
//
//    if(!is_null($distance)){
//      $n->distance = $distance;
//    }
//    
//    //if it's an imported location the visible field can be null, let's fill it with default
//    $field = GojiraSettings::CONTENT_TYPE_SHOW_LOCATION_FIELD;
//    $fieldData = $n->$field;
//    if (count($fieldData) == 0) {
//      $n->$field = array(LANGUAGE_NONE => array(0 => array('value' => 1)));
//      node_save($n);
//    }
//    // check if the location is visible, TODO put in query
//    $fieldData = $n->$field;
//    if ($fieldData[LANGUAGE_NONE][0]['value'] == 1) {
//      $n->distance = $result->distance; // TODO bug?
//      $nodes[$nid] = $n;
//    }
//  }
}
