<?php
function tools(){
  $made_empty = false;
  $filled_all = false;
  $need_indexing = array();
  $changed_category_locations = array();
  $groups = array();
  
  global $user;
  $user = user_load($user->uid);
  if (in_array('administrator', array_values($user->roles))) {
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    set_time_limit(99999999);
    
    
//    if(isset($_GET['importcsv']) && $_GET['importcsv'] == '1'){
//        Importer::AdhocdataImportCSV();
//        drupal_set_message(t('Done importing'), 'status');
//        //drupal_set_message(t('No need to import enything... '), 'status');
//    }
    
    if(isset($_GET['importadhocdata']) && $_GET['importadhocdata'] == '1'){
//        Importer::AdhocdataImportLocations($_GET['importadhocdata_amount']);
//        drupal_set_message(t('Done importing from adhocdata table'), 'status');
        drupal_set_message(t('This option is disabled.'), 'status');
    }
    
    if(isset($_GET['importtolocations']) && $_GET['importtolocations'] == '1'){
//        Importer::importLocations($_GET['importtolocations_amount']);
//        drupal_set_message(t('Done importing from locations table'), 'status');
        drupal_set_message(t('This option is disabled.'), 'status');
    }

    
//    if(isset($_GET['findimportedcoordinates']) && $_GET['findimportedcoordinates'] == '1'){
//        Importer::AdhocdataGetCoordinates($_GET['findimportedcoordinates_amount'], true, true, true);
//        drupal_set_message(t('Done finding some coordinates.'), 'status');
//    }
    
    $groups = Group::getAllGroups();
  
    if(isset($_GET['index_some']) && $_GET['index_some'] != ''){
      $result = Search::getInstance()->indexSomeNodes($_GET['index_some']);

      drupal_set_message(t('Amount of nodes reindexed: '.$result), 'status');
      
      if(isset($_GET['redirect_to_view']) && is_numeric($_GET['index_some'])){
        //drupal_goto('/?loc='.$_GET['index_some']);
        header('Location: /?q=showlocation&loc='.$_GET['index_some']);
        exit;
      }
      
    }

    //$results = db_query('select nid, title from node where type = \'location\' and indexed != changed order by title')->fetchAll();
    $results = db_query('select nid, title from node join field_data_field_visible_to_other_user on (node.nid = field_data_field_visible_to_other_user.entity_id) where type = \'location\' and indexed != changed AND field_data_field_visible_to_other_user.field_visible_to_other_user_value = 1 AND field_data_field_visible_to_other_user.bundle = \'location\' AND field_data_field_visible_to_other_user.delta = 0 order by title')->fetchAll();
    foreach ($results as $needs_index) {
      $need_indexing[$needs_index->nid] = $needs_index->title;
    }

    if(isset($_GET['empty_all']) && $_GET['empty_all'] == 'shit!'){
//      Importer::emptyLocations();
//      drupal_set_message(t('Removed all the locations.'), 'status');
//      $made_empty = true;
        drupal_set_message(t('This option is disabled.'), 'status');
    }
        
    if(isset($_GET['change_category']) && $_GET['change_category'] == '1'){
//      if(strlen($_GET['new_category_name']) > 0 && is_numeric($_GET['change_category_nid'])){
//        $changed_category_locations = Category::moveCategory($_GET['change_category_nid'], $_GET['new_category_name']);
//      }
        drupal_set_message(t('This option is disabled.'), 'status');
    }
    
    if(isset($_GET['remove_category']) && $_GET['remove_category'] == '1'){
//      if(is_numeric($_GET['remove_category_locations_nid'])){
//        Category::cleanupCategory($_GET['remove_category_locations_nid']);
//      }
        drupal_set_message(t('This option is disabled.'), 'status');
    }
    
//    if(isset($_POST['employer_email'])){
//      variable_set('gojira_new_employer_email', $_POST['employer_email']);
//      drupal_set_message(t('New version of the employer e-mail saved.'), 'status');
//    }
//    
//    if(isset($_POST['gojira_unsubscribe_user'])){
//      variable_set('gojira_unsubscribe_user', $_POST['gojira_unsubscribe_user']);
//      drupal_set_message(t('New version of the unsubscribe user e-mail saved.'), 'status');
//    }

    if(isset($_GET['set_payed_group'])){
//      Subscriptions::subscribeByGroupId($_GET['set_payed_group']);
//      drupal_set_message(t('Subscribed group '.$_GET['set_payed_group']), 'status');
        drupal_set_message(t('This option is disabled.'), 'status');
    }
    
    if(isset($_GET['set_not_payed_group'])){
//      Subscriptions::unsubscribe($_GET['set_not_payed_group']);
//      drupal_set_message(t('Unsubscribed group '.$_GET['set_not_payed_group']), 'status');
        drupal_set_message(t('This option is disabled.'), 'status');
    }
    
    if(isset($_GET['set_reindex_all'])){
//      db_query("UPDATE `node` SET `indexed`=0 WHERE  `type`='location'");
//      drupal_set_message(t('Just set all the locations to be reindexed.'), 'status');
        drupal_set_message(t('This option is disabled.'), 'status');
    }

    if(isset($_POST['gojira_send_mail'])){
        global $user;
        $user = user_load($user->uid);
        
        if(isset($_POST['email'])){
          $user->mail = $_POST['email'];
        }
        
        switch($_POST['gojira_send_mail']){
            case 'sendInvoiceOfNewSubscription':
                $ideal_id = db_query("select ideal_id from gojira_payments order by id DESC limit 1 ")->fetchField();
                if($ideal_id && isset($_POST['email'])){
                    $file = Subscriptions::generateSubscribePDF($ideal_id);
                    Mailer::sendInvoiceOfNewSubscription($_POST['email'], $file, $ideal_id);
                    drupal_set_message(t('Just send a test Invoice Of New Subscription e-mail to '.$_POST['email'].'.'), 'status');
                }else{
                    drupal_set_message(t('Just failed to send a test Invoice Of New Subscription e-mail. No Ideal Id found or false e-mail.'), 'error');
                }
            break;
            case 'sendWelcomeMailToEmployee':
                Mailer::sendWelcomeMailToEmployee($user);
                drupal_set_message(t('Just send the welcome e-mail that get\'s send to a new employee: '.$user->mail.'.'), 'status');
            break;
            case 'sendWelcomeMailToEmployer':
                Mailer::sendWelcomeMailToEmployer($user);
                drupal_set_message(t('Just send the welcome e-mail that get\'s send to a new employer: '.$user->mail.'.'), 'status');
            break;
            case 'sendUnsubscribeMail':
                Mailer::sendUnsubscribeMail($user);
                drupal_set_message(t('Just send the e-mail a employer & employee recieve when unsubscribed to '.$user->mail.'.'), 'status');
            break;
            case 'sendSubscriptionEndWarning':
                Mailer::sendSubscriptionEndWarning($user);
                drupal_set_message(t('Just send the e-mail a doctor get\'s when the subscription is going to end in 30 days from now, to: '.$user->mail.'.'), 'status');
            break;
            case 'sendSubscriptionEnded':
                Mailer::sendSubscriptionEnded($user);
                drupal_set_message(t('Just send the e-mail a doctor get\'s when the subscription is ended, to: '.$user->mail.'.'), 'status');
            break;
            case 'sendAccountMergeRequest':
                Mailer::sendAccountMergeRequest($user, auto_login_url_create($user->uid, '/?q=loginlink/but/fake', true));
                drupal_set_message(t('Just send the e-mail a doctor get\'s when the subscription is ended, to: '.$user->mail.'.'), 'status');
            break;
            case 'sendSubscribeActivationMail':
                Mailer::sendSubscribeActivationMail($user);
                drupal_set_message(t('Just send the e-mail a employee & employer recieves when the account get\'s activated after it\'s unsubscribed to '.$user->mail.'.'), 'status');
            break;
            case 'accountActivatedByAdmin':
                Mailer::accountActivatedByAdmin($user);
                drupal_set_message(t('Just send the mail to the user that gets send when an admin activates the account.'), 'status');
            break;
            case 'newAccountThroughSSO':
                Mailer::newAccountThroughSSO($user);
                drupal_set_message(t('Just send the e-mail that gets send when a new account is created through sso to the user of that account.'), 'status');
            break;
            case 'sendAccountNeedsValidation':
                Mailer::sendAccountNeedsValidation($user);
                drupal_set_message(t('Just send the e-mail to the admin to tell hem an account needs activation.'), 'status');
            break;
            case 'sendDoubleAccountWarning':
                Mailer::sendDoubleAccountWarning($user);
                drupal_set_message(t('Just send the e-mail to the user that he has 2 accounts, one in Haweb and one in SK. Not linked.'), 'status');
            break;
        }
    }
    
    if(isset($_GET['replace_labels_cat_id'])){
//      $category_id = $_GET['replace_labels_cat_id'];
//      $locations = Category::getAllLocationsFromCategory($category_id);
//      $labels = explode(' ',trim($_GET['labels']));
//      
//      $labels_to_add = array();
//      foreach($labels as $label){
//        $clean_label = Labels::prepairLabel($label);
//        $tid = Labels::saveLabel($clean_label);
//        $labels_to_add[$tid] = $clean_label;
//      }
//      
//      if(count($labels)>0){
//        foreach($locations as $location){
//          $location->field_location_vocabulary[LANGUAGE_NONE] = array();
//          foreach($labels_to_add as $tid=>$clean_label){
//            $location->field_location_vocabulary[LANGUAGE_NONE][count($location->field_location_vocabulary[LANGUAGE_NONE])]['tid'] = $tid;
//          }
//          node_save($location);
//          Search::getInstance()->updateSearchIndex($location->nid);
//        }
//      }
//      drupal_set_message(t('Replaced the labels (<i>'.  implode(',', $labels).'</i>) of all the locations of the category '.$_GET['replace_labels_cat_id'].'.'), 'status');
        drupal_set_message(t('This option is disabled.'), 'status');
    }    
    
    
  }
  
  $adhoc_need_import = db_query("SELECT count(id) FROM adhocdata_addresses WHERE imported = 0 AND ready_to_import = 1")->fetchField(0);
  $spider_need_import = db_query("SELECT count(id) FROM locations WHERE imported = 0")->fetchField(0);
  $adhoc_imported = db_query("SELECT count(id) FROM adhocdata_addresses WHERE imported = 1")->fetchField(0);
  $spider_imported = db_query("SELECT count(id) FROM locations WHERE imported = 1")->fetchField(0);
  $spider_double = db_query("SELECT count(id) FROM locations WHERE `double` = 1")->fetchField(0);
  $spider_notallowed = db_query("SELECT count(id) FROM locations WHERE `notallowed` = 1")->fetchField(0);
  $categories = db_query("select node.nid, node.title from {node} where type = '".GojiraSettings::CONTENT_TYPE_CATEGORY."'")->fetchAll();  
  $text_pages = db_query("select node.nid as nid from {node} where type = '".GojiraSettings::CONTENT_TYPE_TEXT."'")->fetchAll(); 
  
  return theme('tools',array(
      'groups'=>$groups, 
      'changed_category_locations'=>$changed_category_locations, 
      'categories' => $categories, 
      'need_indexing'=>$need_indexing, 
      'made_empty'=>$made_empty, 
      'filled_all'=>$filled_all,
      'adhoc_need_import'=>$adhoc_need_import,
      'spider_need_import'=>$spider_need_import,
      'adhoc_imported'=>$adhoc_imported,
      'spider_imported'=>$spider_imported,
      'spider_notallowed' => $spider_notallowed,
      'spider_double' => $spider_double,
      'text_pages' => $text_pages,
//      'found_by_dtb' => $found_by_dtb,
//      'found_by_google' => $found_by_google,
//      'found_by_postcodenl' => $found_by_postcodenl,
          ));
}