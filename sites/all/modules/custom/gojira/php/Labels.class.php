<?php

class Labels {

    /**
    *   Get's all the labels
    **/
    public static function getAllLabels()
    {
        $return = array();
        $sql = "select taxonomy_term_data.name as term, taxonomy_term_data.tid as tid from taxonomy_term_data where vid in (select vid from taxonomy_vocabulary where machine_name in ('" . GojiraSettings::VOCABULARY_LOCATION . "')) order by taxonomy_term_data.name";
        $results = db_query($sql);
        foreach ($results as $result) {
            $return[$result->tid] = $result->term;
        }

        return $return;
    }

    /**
     * Prepairs a label to be saved
     *
     * @param string $labelToBe
     * @return label
     */
    public static function prepairLabel($labelToBe) {
        return strtolower(trim(str_replace(',', '', $labelToBe)));
    }

    /**
     * Get's you all the labels of the given node
     *
     * @param $node
     * @return array
     */
    public static function getLabels($node) {
        $terms = array();
        $term = field_view_field('node', $node, GojiraSettings::CONTENT_TYPE_LOCATION_VOCABULARY_FIELD);

        // get the original terms of the object
        if (isset($term['#object'])) {
            $vocabulairyLoaded = $term['#object'];
            $vocabulairyLoaded = $vocabulairyLoaded->field_location_labels;
            foreach ($vocabulairyLoaded['und'] as $term) {
                $terms[$term['taxonomy_term']->tid] = $term['taxonomy_term']->name;
            }
        }
        return $terms;
    }

    /**
     * Draws all the labels
     *
     * @param $node
     * @return string
     */
    public static function drawMobileLabels($node) {

        $sHtml = '';

        $terms = self::getLabels($node);

        $shuffle = array();
        foreach ($terms as $tid => $label) {
            $shuffle[] = array('tid' => $tid, 'label' => $label);
        }

        shuffle($shuffle);

        foreach ($shuffle as $termInfo) {

            $tid = $termInfo['tid'];
            $term = $termInfo['label'];

            $amount_of_likes = self::getLikes($tid, $node->nid);

            if ($amount_of_likes > 20) {
                $amount_of_likes = 20; // css is limited to 20 right now
            }

            $sHtml .= '<div class="score_' . $amount_of_likes . '">' . $term . '</div>';
        }

        $sHtml = '<div class="mobile_labels">' . $sHtml . '</div>';

        return $sHtml;
    }

    /**
     * Draws all the labels
     *
     * @param type $node
     */
    public static function draw($node) {

        $returnHtml = '';

        $terms = self::getLabels($node);

        $label_button = '';
        $remove_label = '';
        if (user_access(helper::PERM_HUISARTS_LABELS)) {
            $label_button = '<button class="labelbutton"><span class="tooltip_not" title="%sign_title%">%button%</span></button>';
            $remove_label_button = '<button class="labelremovebutton"><span class="tooltip_not" title="' . t('Remove') . '">' . t('Remove') . '</span></button>';
        }

        if (count($terms) > 0) {
            $returnHtml = '<p>' . t('Designated labels:') . '</p>';
        }

        $html = <<<EAT
        <div class="label_wrapper">
          <div id="label_%tid%" class="%sign_class% label rounded noshadow">
            <div class="labelnumber">%count%</div>
            <div class="labeltxt">%label%</div>
            {$label_button}
            %remove_label_button%
          </div>
        </div>
EAT;

        $shuffle = array();
        foreach ($terms as $tid => $label) {
            $shuffle[] = array('tid' => $tid, 'label' => $label);
        }

        shuffle($shuffle);

        foreach ($shuffle as $termInfo) {

            $tid = $termInfo['tid'];
            $term = $termInfo['label'];

            $hasLiked = self::groupHasLiked($tid, $node->nid, Group::getGroupId());

            if ($hasLiked) {
                $button = '-';
                $signTxt = t('Unlike');
                $signClass = 'plus';
            } else {
                $button = '+';
                $signTxt = t('Like');
                $signClass = 'minus';
            }

            $amount_of_likes = self::getLikes($tid, $node->nid);

            $remove_label_button_usable = '';
            if ($amount_of_likes == 0) {
                $remove_label_button_usable = $remove_label_button;
            }

            $returnHtml .= str_replace(
                    array('%remove_label_button%', '%count%', '%label%', '%button%', '%sign_class%', '%sign_title%', '%tid%'), array(
                $remove_label_button_usable,
                $amount_of_likes,
                $term,
                $button,
                $signClass,
                $signTxt,
                $tid), $html);
        }

        $returnHtml = '<div class="labels">' . $returnHtml . '</div>';

        return $returnHtml;
    }

    /**
     * Get's you the amount of likes the label has on this locations
     *
     * @param integer $tid
     * @param integer $nid
     * @return integer
     */
    public static function getLikes($tid, $nid) {
        return db_query("SELECT count(id) AS count FROM group_location_term where tid = :tid AND nid = :nid", array(':tid'=>$tid, ':nid'=>$nid))->fetchField();
    }

    /**
     * Tells you if a group has liked a term on a location
     *
     * @param integer $tid
     * @param integer $nid
     * @param integer $gid
     * @return integer
     */
    public static function groupHasLiked($tid, $nid, $gid) {
        return db_query("SELECT count(id) AS count FROM group_location_term where tid = {$tid} AND nid = {$nid} AND gid = {$gid}")->fetchField();
    }

    /**
     * Unlikes a label for a group/location combination
     *
     * @param integer $tid
     * @param integer $nid
     * @param integer $gid
     */
    public static function unlike($tid, $nid, $gid = null) {
        if (is_null($gid)) {
            $gid = Group::getGroupId();
        }
        db_query("DELETE FROM `group_location_term` WHERE  `tid`={$tid} AND `gid`={$gid} AND `nid`={$nid}");
    }

    /**
     * Unlikes a label for a group/location combination
     *
     * @param integer $tid
     * @param integer $nid
     * @param integer $gid
     */
    public static function like($tid, $nid, $gid = null) {
        if (is_null($gid)) {
            $gid = Group::getGroupId();
        }

        if(!self::groupHasLiked($tid, $nid, $gid)){
            db_query("INSERT INTO `group_location_term` (`gid`, `nid`, `tid`) VALUES ({$gid}, {$nid}, {$tid})");
        }
    }

    /**
     * Saves a Label
     *
     * @param string $label
     * @return integer tid or false if failed
     */
    public static function saveLabel($label) {
        $label = strtolower(trim($label));
        if ($term = taxonomy_get_term_by_name($label, GojiraSettings::VOCABULARY_LOCATION)) {
            $terms_array = array_keys($term);
            return $terms_array['0'];
        } else {
            $term = new StdClass();
            $term->name = $label;
            $v = taxonomy_vocabulary_machine_name_load(GojiraSettings::VOCABULARY_LOCATION);
            $term->vid = $v->vid;
            if (!empty($term->name)) {
                taxonomy_term_save($term);
                $term = taxonomy_get_term_by_name($label, GojiraSettings::VOCABULARY_LOCATION);
                $tid = key($term);
            }
            return $tid;
        }
        return false;
    }

    /**
     * Saves a array of labels to a location node
     *
     * @param array $labels
     * @param stdClass $node
     * @return boolean
     */
    public static function saveArrayOfLabelsOnNode($labels, $node) {
        if (is_numeric($node)) {
            $node = node_load($node);
        }

        $labels = helper::cleanArrayWithBlacklist($labels);

        foreach ($labels as $label) {
            if (strlen($label) > 2) {
                $label = self::prepairLabel($label);
                $tid = self::saveLabel($label);

                if (!isset($node->field_location_labels) || !isset($node->field_location_labels[LANGUAGE_NONE])) {
                    $node->field_location_labels[LANGUAGE_NONE] = array();
                }

                foreach ($node->field_location_labels[LANGUAGE_NONE] as $node_label) {
                    if ($node_label['tid'] == $tid) {
                        continue 2;
                    }
                }
                $node->field_location_labels[LANGUAGE_NONE][count($node->field_location_labels[LANGUAGE_NONE])]['tid'] = $tid;
                node_save($node);
            }
        }

        Search::getInstance()->updateSearchIndex($node->nid);

        return true;
    }

    /**
     * Saves a label to a location node
     *
     * @param array $labels
     * @param stdClass $node
     * @return boolean
     */
    public static function addLabel($sLabel, $oNode){
        return self::addAndScoreLabel($sLabel, $oNode, false);
    }

    /**
     * Saves a label to a location node
     * and gives it one + by the current user
     *
     * @param array $labels
     * @param stdClass $node
     * @return boolean
     */
    public static function addAndScoreLabel($sLabel, $oNode, $like = true) {
        if (is_numeric($oNode)) {
            $oNode = node_load($oNode);
        }

        // not on the blacklist & long enough
        if (!helper::inBlacklist($sLabel)) {

            $sLabel = self::prepairLabel($sLabel);
            $iTid = self::saveLabel($sLabel);

            if (!isset($oNode->field_location_labels) || !isset($oNode->field_location_labels[LANGUAGE_NONE])) {
                $oNode->field_location_labels[LANGUAGE_NONE] = array();
            }

            foreach ($oNode->field_location_labels[LANGUAGE_NONE] as $aNodeLabel) {
                if ($aNodeLabel['tid'] == $iTid) {
                    return true; // stop the function if the location allready has the label
                }
            }
            $oNode->field_location_labels[LANGUAGE_NONE][count($oNode->field_location_labels[LANGUAGE_NONE])]['tid'] = $iTid;
            node_save($oNode);
        }

        if($like){
            self::like($iTid, $oNode->nid, Group::getGroupId());
        }

        Search::getInstance()->updateSearchIndex($oNode->nid);

        return true;
    }

    /**
     * Gets all the groups that have favorited this locations
     *
     * returns array with combined gid & pid
     */
    public function getAllLabelLikesByGroup($nid) {
        $return = array();
        $results = db_query('select gid, tid from group_location_term where nid = :nid', array(':nid' => $nid));
        foreach ($results as $result) {
            $return[$result->gid+$result->tid] = array(
                'gid' => $result->gid,
                'tid' => $result->tid
            );
        }
        return $return;
    }

}
