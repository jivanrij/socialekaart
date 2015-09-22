<?php
// $Id: $

/**
 * @file
 * Client side handling functions for single sign on functionality.
 */

/**
 * Prepares a redirect call to the SSO server instance.
 */
function onesignin_client_redirect($needs_login = TRUE, $destination = '', $form_id = NULL) {
  $_SESSION['onesignin_request_id'] = drupal_get_token(mt_rand());

  // Store the current destination in the session for use when the
  // authentication site redirects back to this site logged in.
  if (isset($_REQUEST['destination']) || isset($_GET['destination'])) {
    $_SESSION['onesignin_destination'] = isset($_GET['destination'])?$_GET['destination']:$_REQUEST['destination'];
    unset($_REQUEST['destination'], $_GET['destination']);
  }

  // Remember form_id for later so we always know the last form that caused a request to the server.
  $_SESSION['onesignin_client_form_id'] = $form_id;

  $query = array(
    'site_id' => variable_get('onesignin_client_site_id', onesignin_client_site_id()),
    'request_id' => $_SESSION['onesignin_request_id'],
    'hash' => onesignin_client_hash(array(variable_get('onesignin_client_site_id', onesignin_client_site_id()), $_SESSION['onesignin_request_id'], $destination, $needs_login, variable_get('onesignin_client_secret', ''))),
    'login' => $needs_login,
  );
  if ($destination) {
    $query['destination'] = $destination;
  }

  unset($_SESSION['onesignin_server_anonymous']);
  drupal_goto(rtrim(variable_get('onesignin_client_server', ''), '/') . '/onesignin/request', array('query' => $query));
}

/**
 * Handles the final step in logging in a user through SSO functionality.
 */
function onesignin_client_user_authenticate($account) {
  // Fake a form_state so that some of the default validators can be called.
  $form_state = array('values' => array('name' => $account->name, 'pass' => ''));
  // This function checks if the user is blocked, or that the name is not allowed.
  user_login_name_validate(array(), $form_state);

  // Check if the e-mail address is disallowed.
  // this does not work anymore in D7 (see http://drupal.org/node/228594)
  // drupal_is_denied now only checks ip-addresses
  // functionality to check for mail or users was moved to seperate contrib module
  // http://drupal.org/project/user_restrictions
  /*
  if (drupal_is_denied('mail', $account->mail)) {
    form_set_error('name', t('The name %name is registered using a reserved e-mail address and therefore could not be logged in.', array('%name' => $account->name)));
  }*/

  // @todo Check thoroughly if this check is secure enough and not prone to hidden errors.
  if (!form_get_errors()) {
    global $user;
    $user = $account;
    // D7 expects the user object to contain timezone. If the server is running
    // on D6, this is missing, so we add it here.
    if (!isset($user->timezone)) {
      $user->timezone = NULL;
    }
    user_login_finalize($form_state['values']);
    return $user;
  }
  else {
    watchdog('onesignin', 'Login attempt failed for %user.', array('%user' => $account->name));
    // If logged in, perform log-out action because apparently the login has become invalid.
    if (user_is_logged_in()) {
      user_logout();
    }
  }

  return FALSE;
}

/**
 * Menu callback for handling the SSO response from the authentication server.
 * Visitors are redirected to '/onesignin/response' which will call this
 * function.
 *
 * In this function the following steps are taken:
 * 1. Hash validation
 * 2. Synchronization of user information.
 * 3. Initializing authenticated session.
 * 4. Redirecting to where the user initially started the login process.
 *
 */
function onesignin_client_response() {
  // Avoid page caching by all means necessary.
  // @TODO:
  // CACHE_DISABLED doesn't exist anymore in D7
  // http://drupal.org/node/730046
  /*
  $GLOBALS['cache'] = CACHE_DISABLED;
  */
    
  $GLOBALS['cache'] = 0;

  $request_id = (isset($_GET['request_id']) ? $_GET['request_id'] : '');
  $uid_server = (isset($_GET['uid']) ? (int) $_GET['uid'] : 0);
  $messages = isset($_GET['messages']) ? $_GET['messages'] : FALSE;
  $hash_server = (isset($_GET['hash']) ? $_GET['hash'] : '');
  $session_request_id = (isset($_SESSION['onesignin_request_id']) ? $_SESSION['onesignin_request_id'] : '');
  
  if(array_key_exists('onesignin_request_id', $_SESSION)){
    unset($_SESSION['onesignin_request_id']);
  }  

  // Re-set the destination and remove the temporary one from the session.
  if (isset($_SESSION['onesignin_destination']) && empty($_GET['destination'])) {
    $_GET['destination'] = $_SESSION['onesignin_destination'];
    unset($_SESSION['onesignin_destination']);
  }

  // Check if SSO functionality is enabled.
  if (!variable_get('onesignin_client', FALSE)) {
    drupal_set_message(t('Single-sign-on functionality is not enabled.'), 'warning');
    // Redirect to front-page, or the page where the login attempt came from.
    drupal_goto();
  }

  // Recreate the hash for validation.
  $hash_client = onesignin_client_hash(array($request_id, $uid_server, $messages, variable_get('onesignin_client_secret', '')));
  if ($hash_client !== $hash_server) {
    watchdog('onesignin', 'Hash validation failed in authentication request for user %uid_server.', array('%uid_server' => $uid_server), WATCHDOG_NOTICE);
    // This could just mean the request form the server is stale. So go to homepage.
    unset($_REQUEST['destination']);
    drupal_goto();
  }

  if ($session_request_id && $session_request_id == $request_id) {
    // Synchronize messages?
    if ($messages) {
      onesignin_client_get_server_messages($request_id);
    }
    $_SESSION['onesignin_response'] = TRUE;
    // Do not synchronize anonymous users.
    if ($uid_server == 0) {
      $_SESSION['onesignin_server_anonymous'] = TRUE;  // @todo? make this a timestamp so we can expire it?
      // Remove destination if we get an anonymous user back from a login form
      if (isset($_SESSION['onesignin_client_form_id']) && $_SESSION['onesignin_client_form_id'] == 'user_login') {
        unset($_REQUEST['destination']);
      }
      drupal_goto();  // Return to origin or homepage.
    }
    unset($_SESSION['onesignin_server_anonymous']);
    unset($_SESSION['onesignin_disabled']);

    // Validation succeeded, proceed to synchronization.
    $account = onesignin_client_userdata_synchronize($uid_server);
    if ($account && $account->uid) {

      // Let other modules hook into this function
      drupal_alter('onesignin_client_response', $account);

      // Last and final checks happen here.
      if (onesignin_client_user_authenticate($account)) {
        // Redirects to user account by default, but $_REQUEST['destination']
        // might have other plans.
        $query = drupal_get_query_parameters();
        if (isset($_GET['popup']) && $_GET['popup'] == 'false') {
          unset($_REQUEST['destination']);
        }

        // Add the full url, to prevent this error (js sso login popup)
        //  "Uncaught SecurityError: Blocked a frame with origin
        //  "http://domain" [^] from accessing a frame with origin
        //  "http://subdomain.lhv". [^] Protocols, domains, and ports must match."
        $prefix = '';
        if (isset($_SESSION['domain_url'])) {
          $prefix = $_SESSION['domain_url'];
          unset($_SESSION['domain_url']);
        }

        drupal_goto($prefix . 'user/' . $account->uid, array('query' => $query));
      }
      else {
        $_SESSION['onesignin_server_anonymous'] = TRUE;  // Disable further automatic attempts.
        // Remove destination form user_login forms to avoid infinite loops.
        if (isset($_SESSION['onesignin_client_form_id']) && $_SESSION['onesignin_client_form_id'] == 'user_login') {
          unset($_REQUEST['destination']);
        }
        drupal_goto();  // Return to origin or homepage.
      }
    }
    else {
      watchdog('onesignin', 'Failed to synchronize server uid %uid to here.', array('%uid' => $uid_server), WATCHDOG_NOTICE);
      $_SESSION['onesignin_server_anonymous'] = TRUE;  // Disable further automatic attempts.
      // Remove destination form user_login forms to avoid infinite loops.
      if (isset($_SESSION['onesignin_client_form_id']) && $_SESSION['onesignin_client_form_id'] == 'user_login') {
        unset($_REQUEST['destination']);
      }
      drupal_goto();  // Return to origin or homepage.
    }
  }
  else {
    watchdog('onesignin', 'No matching request ID %request_id found for user %uid_server.', array('%request_id' => $request_id, '%uid_server' => $uid_server), WATCHDOG_NOTICE);
    // This could just mean the request form the server is stale. So go to homepage.
    unset($_REQUEST['destination']);
    drupal_goto();
  }

  return MENU_ACCESS_DENIED;
}

/**
 * Synchronizes user data from server to client if needed. Please note that
 * this functionality uses a local mapping of server UID's to local UID's in
 * order to allow a difference in user sets and to not overwrite any existing
 * user account data.
 *
 * The function must be fed with a user ID from the server, and a last change
 * date. It uses both values to check if updating the user information is
 * needed.
 *
 * @param int $uid_server
 *   User identifier from the server installation.
 *
 * @param int $last_change
 *   Optional timestamp for the last change of the data in the server instance.
 *   When no timestamp is given, the information is always updated/inserted
 *   locally from information from the server instance.
 */
function onesignin_client_userdata_synchronize($uid_server, $last_change = 0) {
  watchdog('onesignin_client', 'uid:' . $uid_server);

  // Do not allow the (remote) user 1 account to be synchronized.
  if ($uid_server == 1) {
    watchdog('onesignin', 'Skipped user data synchronization for user remote user uid %uid_server.', array('%uid_server' => $uid_server), WATCHDOG_WARNING);
    if (user_access('administer onesignin client')) {
      drupal_set_message(t('Synchronization for user account 1 is not allowed.'), 'error');
    }
    return FALSE;
  }

  // First, fetch the local user id from the mapping table.
  $mapping = db_query("SELECT uid, changed FROM {onesignin_client_uids} WHERE uid_server = :uid_server", array(':uid_server' => $uid_server))->fetchObject();

  $needs_update = TRUE;
  $uid_local = FALSE;

  // Check for modification date.
  if ($mapping && $mapping->uid) {
    $uid_local = $mapping->uid;
    $mapping->uid_server = $uid_server;
    if ($last_change && $mapping->changed && $mapping->changed == $last_change) {
      $needs_update = FALSE;
    }
  }

  // Do not allow the (local) user 1 account to be synchronized.
  if ($mapping && $mapping->uid == 1) {
    watchdog('onesignin', 'Skipped user data synchronization for user %uid_local (server uid %uid_server).', array('%uid_local' => $mapping->uid, '%uid_server' => $uid_server), WATCHDOG_WARNING);
    if (user_access('administer onesignin client')) {
      drupal_set_message(t('Synchronization for user account 1 is not allowed.'), 'error');
    }
    return FALSE;
  }

  if (!$needs_update) {
    watchdog('onesignin', 'No need for user data synchronization for user %uid_local (server uid %uid_server).', array('%uid_local' => $mapping->uid, '%uid_server' => $uid_server), WATCHDOG_INFO);
    trigger_error('no update needed', E_USER_WARNING);

    return user_load($mapping->uid);
  }

  // Fetch user data from server instance.
  $data = onesignin_client_userdata_fetch($uid_server);
  if ($data) {
    
    //SOCIALEKAART.CARE CHANGE START
    $new_account = false;
    //SOCIALEKAART.CARE CHANGE END
      
    $edit = array(
      'name' => $data['name'],
      'mail' => $data['mail'],
      'status' => $data['status'],
    );

    if ($uid_local) {
      $account = user_load($uid_local);
      if (!$account) {
        watchdog('onesignin', 'Failed loading account for user %uid_local (server uid %uid_server).', array('%uid_local' => $mapping->uid, '%uid_server' => $uid_server), WATCHDOG_ERROR);
        drupal_set_message(t('There was an error in retrieving your account information from the authentication server.'), 'error');
        trigger_error('failed loading local user info', E_USER_WARNING);
        return FALSE;
      }
    }
    else {
      // New user account.
      $account = new stdClass();
    }

    // Allow other modules to change the data that's going to be updated in the
    // user object.
    drupal_alter('onesignin_userdata_request', $edit, $data, $account);

    // Check for existing user with conflicting name or email.
    $sql = "SELECT 1 FROM {users} u WHERE (u.name = :name OR u.mail = :mail) AND u.uid <> :uid";
    
    //watchdog('onesignin_client',"SELECT 1 FROM {users} u WHERE (u.name = :name OR u.mail = :mail) AND u.uid <> :uid", 'error');
    
    $conflict = db_query($sql, array(':name' => $edit['name'], ':mail' => $edit['mail'], ':uid' => $uid_local))->fetchField();
    if ($conflict) {
      drupal_set_message(t("Error saving user account, the name already exists."), 'error');
      watchdog('onesignin', 'Duplicate name %name while saving user account for user %uid_local (server uid %uid_server).', array('%name' => $edit['name'], '%uid_local' => ($mapping ? $mapping->uid : 'unknown'), '%uid_server' => $uid_server), WATCHDOG_ERROR);
      trigger_error('error saving account', E_USER_WARNING);
      
      //SOCIALEKAART.CARE CHANGE START
      if(module_exists('gojira')){
          Haweb::dublicateWarning($edit['mail']);
      }
      //SOCIALEKAART.CARE CHANGE END
      return FALSE;
    }

    // Call the user save action.
    $account = user_save($account, $edit);
    
    
    // need to do some changes for socialekaart.care here
    
    if (!$account) {
      drupal_set_message(t("Error saving user account."), 'error');
      watchdog('onesignin', 'An error occurred while saving user account for user %uid_local (server uid %uid_server).', array('%uid_local' => ($mapping ? $mapping->uid : 'unknown'), '%uid_server' => $uid_server), WATCHDOG_ERROR);
      trigger_error('error saving account', E_USER_WARNING);
      return FALSE;
    }

    // Update the synchronization in the mapping table.
    if ($mapping) {
      $mapping->changed = (!empty($data['changed']) ? $data['changed'] : 0);
      if (!drupal_write_record('onesignin_client_uids', $mapping, 'uid_server')) {
        watchdog('onesignin', 'Updating mapping for user %uid_local (server uid %uid_server) failed.', array('%uid_local' => $mapping->uid, '%uid_server' => $uid_server), WATCHDOG_ERROR);
        return FALSE;
      }
    }
    else {
      // Insert a new mapping.
      $mapping = array(
        'uid' => $account->uid,
        'uid_server' => $uid_server,
        'changed' => (!empty($data['changed']) ? $data['changed'] : 0),
      );
      if (!drupal_write_record('onesignin_client_uids', $mapping)) {
        watchdog('onesignin', 'Creating mapping for user %uid_local (server uid %uid_server) failed.', array('%uid_local' => $mapping->uid, '%uid_server' => $uid_server), WATCHDOG_ERROR);
        trigger_error('error inserting mapping', E_USER_WARNING);
        return FALSE;
      }
    }

    if ($data['status'] == 0) {
      onesignin_client_user_logout_server($uid_server);
      return FALSE;
    }

    return $account;
  }
  else {
    drupal_set_message(t('Unable to retrieve account information from authentication server.'), 'error');
    watchdog('onesignin', 'Unable to retrieve information from authentication server for user %uid_local (server uid %uid_server).', array('%uid_local' => ($mapping ? $mapping->uid : 'unknown'), '%uid_server' => $uid_server), WATCHDOG_ERROR);
    return FALSE;
  }

}

/**
 * Fetches information from the configured server instance.
 *
 * @param int $uid_server
 *   The user ID as it is used in the server (remote) instance.
 * @return
 *   Returns an associative array on success, FALSE on failure.
 */
function onesignin_client_userdata_fetch($uid_server) {
  if (variable_get('onesignin_client', FALSE) && variable_get('onesignin_client_server', '')) {

    $site_id = variable_get('onesignin_client_site_id', onesignin_client_site_id());
    $hash = onesignin_client_hash(array($site_id, variable_get('onesignin_client_secret', ''), $uid_server));

    $url = rtrim(variable_get('onesignin_client_server', ''), '/') . '/xmlrpc.php';
    $result = xmlrpc($url, array('onesignin.getUserData' => array($hash, $site_id, (int) $uid_server)));

    // XML-RPC Error handling.
    if (!$result && $error = xmlrpc_error_msg()) {
      trigger_error('One Signin XML-RPC error, ' . $error, E_USER_ERROR);
      return FALSE;
    }

    // Checking authentication status.
    if (isset($result['success']) && !$result['success']) {
      trigger_error('Authentication server responded with failure: ' . (isset($result['message']) ? check_plain($result['message']) : ''), E_USER_ERROR);
      return FALSE;
    }

    $data = isset($result['data']) ? (array) $result['data'] : array();

    // Validate the response.
    $data_hash_server = (isset($result['hash']) ? $result['hash'] : '');
    $data_hash_client = onesignin_client_hash(array($uid_server, print_r($data, TRUE), variable_get('onesignin_client_secret', '')));
    if ($data_hash_server !== $data_hash_client) {
      trigger_error('Data validation failed', E_USER_ERROR);
      return FALSE;
    }

    // Checking for minimum required data.
    if (empty($data['uid']) || empty($data['name']) || !isset($data['mail']) || !isset($data['status'])) {
      trigger_error('Insufficient user information available from authentication server.', E_USER_ERROR);
      return FALSE;
    }

    return $data;
  }

  return FALSE;
}

/**
 * Perform logout request to server instance via an XML-RPC call.
 * @param int $uid_server Remote user ID.
 */
function onesignin_client_user_logout_server($uid_server) {
  $site_id = variable_get('onesignin_client_site_id', onesignin_client_site_id());
  $hash = onesignin_client_hash(array($site_id, variable_get('onesignin_client_secret', ''), $uid_server));

  $url = rtrim(variable_get('onesignin_client_server', ''), '/') . '/xmlrpc.php';
  watchdog('xxx', print_r($url, 1));
  $result = xmlrpc($url, array('onesignin.logoutUser' => array($hash, $site_id, (int) $uid_server)));
  $_SESSION['onesignin_server_anonymous'] = TRUE;
}

/**
 * Retrieve messages from erver instance via an XML-RPC call.
 * @param int $uid_server Remote user ID.
 */
function onesignin_client_get_server_messages($request_id) {
  $site_id = variable_get('onesignin_client_site_id', onesignin_client_site_id());
  $hash = onesignin_client_hash(array($site_id, variable_get('onesignin_client_secret', ''), $request_id));

  $url = rtrim(variable_get('onesignin_client_server', ''), '/') . '/xmlrpc.php';
  $result = xmlrpc($url, array('onesignin.getServerMessages' => array($hash, $site_id, $request_id)));
  // XML-RPC Error handling.
  if (!$result && $error = xmlrpc_error_msg()) {
    trigger_error('One Signin XML-RPC error, ' . $error, E_USER_ERROR);
    return FALSE;
  }
  foreach ($result as $type => $messages) {
    foreach ($messages as $message) {
      drupal_set_message($message, $type);
    }
  }
}

/**
 * Hash data with sha512 to url-safe format.
 * NOTE: This function has to be identical in the corresponding
 * onesignin_server.module code!
 *
 * @param mixed $data
 *   Data to hash. When $data is an array, it's values are concatenated with a
 *   semicolon.
 */
function onesignin_client_hash($data) {
  if (is_array($data)) {
    $data = implode(':', $data);
  }
  $hash = base64_encode(hash('sha512', $data, TRUE));
  return strtr($hash, array('+' => '-', '/' => '_', '=' => ''));
}

/**
 * Check if the current user-agent represents a bot.
 * @return bool TRUE if the current user-agent is deemed to be a bot or crawler.
 */
function onesignin_client_ignore_bot() {
  $bots = array(
    'Google',
    'Yahoo',
    'BlogPulse',
    'ia_archiver',
    'Pingdom',
    'Teoma',
    'Netcraft',
    'Mnogosearch',
    'page.store',
    'libwww.perl',
    'libcurl',
    'del.icio.us',
    'wiji',
    'WebImages',
    'User-Agent:',
    'Python-urllib',
    'Twitturly',
    'Ruby',
    'facebookexternalhit',
    'ColdFusion',
    'PycURL',
    'PEAR HTTP_Request class',
    'LWP::Simple',
    'artviper',
    'WordPress',
    'bot',
    'IlseBot',
    'VoilaBot',
    'spider',
    'crawler',
    'Scooter',
    'SPENG',
    'YANDEX',
    'Vagabondo',
    'Microsoft Small Business Indexer',
    'inktomi',
    'Apple-PubSub',
    'WWW-Mechanize',
  );
  $regex = '/' . implode('|', array_map('preg_quote', $bots)) . '/';
  return (bool) preg_match($regex, $_SERVER['HTTP_USER_AGENT']);
}

/**
 * Notification handler for delete events.
 * @param object $account Local user account.
 */
function onesignin_client_handle_delete_notification($account) {
  watchdog('onesignin', 'Deleting user %name on request of One Signin server.', array('%name' => $account->name), WATCHDOG_INFO);
  user_delete($account->uid);
}

/**
 * Notification handler for logout events.
 * @param object $account Local user account.
 */
function onesignin_client_handle_logout_notification($account) {
  watchdog('onesignin', 'Logging out user %name on request of One Signin server.', array('%name' => $account->name), WATCHDOG_INFO);
  drupal_session_destroy_uid($account->uid);
}

/**
 * Notification handler for after_update events.
 * @param object $account Local user account.
 */
function onesignin_client_handle_after_update_notification($account) {
  watchdog('onesignin', 'Updating user %name on request of One Signin server.', array('%name' => $account->name), WATCHDOG_INFO);
  onesignin_client_userdata_synchronize($account->server_uid);
}
