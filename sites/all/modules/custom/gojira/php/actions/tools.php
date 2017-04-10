<?php

function tools() {
    $made_empty = false;
    $filled_all = false;
    $need_indexing = array();
    $changed_category_locations = array();
    $groups = array();

    global $user;
    $user = user_load($user->uid);
    if (in_array('administrator', array_values($user->roles))) {
        $groups = Group::getAllGroups();

        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        set_time_limit(99999999);

        //$results = db_query('select nid, title from node where type = \'location\' and indexed != changed order by title')->fetchAll();
        $results = db_query('select nid, title from node join field_data_field_visible_to_other_user on (node.nid = field_data_field_visible_to_other_user.entity_id) where type = \'location\' and indexed != changed AND field_data_field_visible_to_other_user.field_visible_to_other_user_value = 1 AND field_data_field_visible_to_other_user.bundle = \'location\' AND field_data_field_visible_to_other_user.delta = 0 order by title')->fetchAll();
        foreach ($results as $needs_index) {
            $need_indexing[$needs_index->nid] = $needs_index->title;
        }

        if (isset($_GET['index_some']) && $_GET['index_some'] != '') {
            $result = Search::getInstance()->indexSomeNodes($_GET['index_some']);
            drupal_set_message(t('Amount of nodes reindexed: ' . $result), 'status');
            if (isset($_GET['redirect_to_view']) && is_numeric($_GET['index_some'])) {
                header('Location: /?q=showlocation&loc=' . $_GET['index_some']);
                exit;
            }
        }

        if (isset($_GET['empty_all']) && $_GET['empty_all'] == 'shit!') {
//      Importer::emptyLocations();
//      drupal_set_message(t('Removed all the locations.'), 'status');
//      $made_empty = true;
            drupal_set_message(t('This option is disabled.'), 'status');
            header('Location: /?q=admin/config/system/gojiratools');
            exit;
        }

        if (isset($_GET['change_category']) && $_GET['change_category'] == '1') {
            if (strlen($_GET['new_category_name']) > 0 && is_numeric($_GET['change_category_nid'])) {
                $changed_category_locations = Category::moveCategory($_GET['change_category_nid'], $_GET['new_category_name']);
            }
            drupal_set_message(t('Category is moved'), 'status');
            header('Location: /?q=admin/config/system/gojiratools');
            exit;
        }

        if (isset($_GET['remove_category']) && $_GET['remove_category'] == '1') {
            if (is_numeric($_GET['remove_category_locations_nid'])) {
                Category::cleanupCategory($_GET['remove_category_locations_nid']);
            }
            drupal_set_message(t('Removed category.'), 'status');
            header('Location: /?q=admin/config/system/gojiratools');
            exit;
        }

//        if (isset($_GET['set_payed_group'])) {
//            Subscriptions::subscribeByGroupId($_GET['set_payed_group']);
//            drupal_set_message(t('Subscribed group ' . $_GET['set_payed_group']), 'status');
//            header('Location: /?q=admin/config/system/gojiratools');
//            exit;
//        }

//        if (isset($_GET['set_not_payed_group'])) {
//
//            $payment = db_query("SELECT * FROM gojira_payments WHERE gid = ".$_GET['set_not_payed_group']." ORDER BY period_end DESC limit 1")->fetchObject();
//
//            if($payment){
//                Subscriptions::unsubscribe($_GET['set_not_payed_group'], $payment);
//                drupal_set_message(t('Unsubscribed group ' . $_GET['set_not_payed_group']), 'status');
//            }else{
//                drupal_set_message('No payment info found for this group', 'status');
//            }
//
//            header('Location: /?q=admin/config/system/gojiratools');
//            exit;
//        }

        if (isset($_GET['set_reindex_all'])) {
            db_query("UPDATE `node` SET `indexed`=0 WHERE  `type`='location'");
            drupal_set_message(t('Just set all the locations to be reindexed.'), 'status');
            header('Location: /?q=admin/config/system/gojiratools');
            exit;
        }

        if (isset($_GET['set_backup_flags'])) {
            db_query("UPDATE `node` SET `exported`=0");
            drupal_set_message(t('All the locations can be backupped now.'), 'status');
            header('Location: /?q=admin/config/system/gojiratools');
            exit;
        }

        if (isset($_GET['restore_backup'])) {
            helper::restoreBackup(20, false);
        }

        if (isset($_GET['backup_truncate'])) {
            db_query("TRUNCATE `practices_backup`");
            drupal_set_message(t('You now have a empty backup table (practices_backup).'), 'status');
            header('Location: /?q=admin/config/system/gojiratools');
            exit;
        }

        if (isset($_GET['backup_practices'])) {
            set_time_limit(999999999999999);
            ini_set('memory_limit', '50000M');

            $rResult = db_query("select node.nid, X(node.point) as longitude, Y(node.point) as latitude, node.source, node.changed from node where type = 'location' and (exported is null or exported = 0) limit 20000");

            foreach ($rResult as $oLocation) {
                $oNode = node_load($oLocation->nid);
                $sCity = helper::value($oNode, GojiraSettings::CONTENT_TYPE_ADDRESS_CITY_FIELD);
                $sStreet = helper::value($oNode, GojiraSettings::CONTENT_TYPE_ADDRESS_STREET_FIELD);
                $sNumber = helper::value($oNode, GojiraSettings::CONTENT_TYPE_ADDRESS_STREETNUMBER_FIELD);
                $sPostcode = helper::value($oNode, GojiraSettings::CONTENT_TYPE_ADDRESS_POSTCODE_FIELD);
                $sTelephone = helper::value($oNode, GojiraSettings::CONTENT_TYPE_TELEPHONE_FIELD);
                $sFax = helper::value($oNode, GojiraSettings::CONTENT_TYPE_FAX_FIELD);
                $sUrl = helper::value($oNode, GojiraSettings::CONTENT_TYPE_URL_FIELD);
                $sNote = helper::value($oNode, GojiraSettings::CONTENT_TYPE_NOTE_FIELD);
                $sMail = helper::value($oNode, GojiraSettings::CONTENT_TYPE_EMAIL_FIELD);
                $iGroup = helper::value($oNode, 'field_gojira_group', 'nid');
                $sCategory = Category::getCategoryName($oNode);
                $sLabels = implode('|', Labels::getLabels($oNode));

                $sql = <<<EOT
INSERT INTO `practices_backup`
    (`import_it`, `title`, `email`, `city`, `street`, `number`, `postcode`, `telephone`, `fax`, `url`, `labels`, `category`, `note`, `latitude`, `longitude`, `group_id`, `visible`, `nid`, `source`)
        VALUES
    (0, :title, '{$sMail}', :city, :street, :number, '{$sPostcode}', '{$sTelephone}', '{$sFax}', '{$sUrl}', '{$sLabels}', :category, :note, '{$oLocation->latitude}', '{$oLocation->longitude}', '{$iGroup}', '{$oNode->status}', '{$oNode->nid}','{$oLocation->source}')
EOT;

                db_query("DELETE FROM `practices_backup` WHERE  `nid`={$oLocation->nid}");
                db_query($sql, array(':title' => $oNode->title, ':city' => $sCity, ':street' => $sStreet, ':category' => $sCategory, ':note' => $sNote, ':number' => $sNumber));
                db_query('UPDATE `node` SET `exported`=1 WHERE  `nid`=' . $oLocation->nid);
            }

            drupal_set_message(t('Made a backup to practices_backup.'), 'status');
            header('Location: /?q=admin/config/system/gojiratools');
            exit;
        }


        if (isset($_POST['add_coordinates'])) {
            db_query("UPDATE `node` SET point = GeomFromText('POINT(" . $_POST['longitude'] . " " . $_POST['latitude'] . ")') WHERE  `nid`=:nid", array(':nid' => $_POST['location_id']));
            drupal_set_message(t('Added coordinates on location, do NOT FORGET to publish this location if needed.'), 'status');
            header('Location: /?q=admin/config/system/gojiratools&activate_id='.$_POST['location_id']);
            exit;
        }

        if (isset($_POST['activate_a_node_id'])) {
            $oNode = node_load($_POST['activate_a_node_id']);
            $oNode->status = 1;
            node_save($oNode);
            if($oNode->type == 'location'){
                Search::getInstance()->updateSearchIndex($oNode);
            }
            drupal_set_message(t('Node activated:').' '.$_POST['activate_a_node_id'], 'status');
            drupal_set_message(t('Node is indexed for search'), 'status');
            header('Location: /?q=admin/config/system/gojiratools');
            exit;
        }

        if (isset($_POST['gojira_send_mail'])) {
            global $user;
            $user = user_load($user->uid);

            if (isset($_POST['email'])) {
                $user->mail = $_POST['email'];
            }

            switch ($_POST['gojira_send_mail']) {
                case 'sendUserInvoiceOfNewSubscription':
                    $ideal_id = db_query("select ideal_id from gojira_payments order by id DESC limit 1 ")->fetchField();
                    if ($ideal_id && isset($_POST['email'])) {
                        $file = Subscriptions::generateSubscribePDF($ideal_id);
                        MailerHtml::sendUserInvoiceOfNewSubscription($_POST['email'], $file, $ideal_id);
                        drupal_set_message(t('Just send a test Invoice Of New Subscription e-mail to ' . $_POST['email'] . '.'), 'status');
                    } else {
                        drupal_set_message(t('Just failed to send a test Invoice Of New Subscription e-mail. No Ideal Id found or false e-mail.'), 'error');
                    }
                    break;
                case 'sendUserSubscriptionEndWarning':
                    MailerHtml::sendUserSubscriptionEndWarning($user);
                    drupal_set_message(t('Just send the e-mail a doctor get\'s when the subscription is going to end in 30 days from now, to: ' . $user->mail . '.'), 'status');
                    break;
                case 'sendUserSubscriptionEnded':
                    MailerHtml::sendUserSubscriptionEnded($user);
                    drupal_set_message(t('Just send the e-mail a doctor get\'s when the subscription is ended, to: ' . $user->mail . '.'), 'status');
                    break;
                case 'sendAccountMergeRequest':
                    Mailer::sendAccountMergeRequest($user, auto_login_url_create($user->uid, '/?q=loginlink/but/fake', true));
                    drupal_set_message(t('Just send the e-mail a doctor get\'s when the subscription is ended, to: ' . $user->mail . '.'), 'status');
                    break;
                case 'sendUserAccountActivatedByAdmin':
                    MailerHtml::sendUserAccountActivatedByAdmin($user);
                    drupal_set_message(t('Just send the mail to the user that gets send when an admin activates the account.'), 'status');
                    break;
                case 'sendAccountNeedsValidation':
                    Mailer::sendAccountNeedsValidation($user);
                    drupal_set_message(t('Just send the e-mail to the admin to tell hem an account needs activation.'), 'status');
                    break;
            }
        }

//        if (isset($_GET['replace_labels_cat_id'])) {
//            $category_id = $_GET['replace_labels_cat_id'];
//            $locations = Category::getAllLocationsFromCategory($category_id);
//            $labels = explode(' ', trim($_GET['labels']));
//
//            $labels_to_add = array();
//            foreach ($labels as $label) {
//                $clean_label = Labels::prepairLabel($label);
//                $tid = Labels::saveLabel($clean_label);
//                $labels_to_add[$tid] = $clean_label;
//            }
//
//            if (count($labels) > 0) {
//                foreach ($locations as $location) {
//                    $location->field_location_labels[LANGUAGE_NONE] = array();
//                    foreach ($labels_to_add as $tid => $clean_label) {
//                        $location->field_location_labels[LANGUAGE_NONE][count($location->field_location_labels[LANGUAGE_NONE])]['tid'] = $tid;
//                    }
//                    node_save($location);
//                    Search::getInstance()->updateSearchIndex($location->nid);
//                }
//            }
//            drupal_set_message(t('Replaced the labels (<i>' . implode(',', $labels) . '</i>) of all the locations of the category ' . $_GET['replace_labels_cat_id'] . '.'), 'status');
//            header('Location: /?q=admin/config/system/gojiratools');
//            exit;
//        }
    }

    $categories = db_query("select node.nid, node.title from {node} where type = '" . GojiraSettings::CONTENT_TYPE_CATEGORY . "'")->fetchAll();

    $amount_exported = db_query("select count(nid) from node where exported = 1 and type = 'location'")->fetchField();
    $amount_not_exported = db_query("select count(nid) from node where exported = 0 and type = 'location'")->fetchField();
    $amount_backupped = db_query("select count(id) from practices_backup")->fetchField();
    $backupped_no_coordinates = db_query("select count(id) from practices_backup where latitude = ''")->fetchField();
    $backupped_to_import = db_query("select count(id) from practices_backup where import_it = 1")->fetchField();
    $backupped_not_to_import = db_query("select count(id) from practices_backup where import_it = 0")->fetchField();



    return theme('tools', array(
        'groups' => $groups,
        'changed_category_locations' => $changed_category_locations,
        'categories' => $categories,
        'need_indexing' => $need_indexing,
        'made_empty' => $made_empty,
        'filled_all' => $filled_all,
        'amount_exported' => $amount_exported,
        'amount_not_exported' => $amount_not_exported,
        'amount_backupped' => $amount_backupped,
        'backupped_no_coordinates' => $backupped_no_coordinates,
        'backupped_not_to_import' => $backupped_not_to_import,
        'backupped_to_import' => $backupped_to_import,
    ));
}
