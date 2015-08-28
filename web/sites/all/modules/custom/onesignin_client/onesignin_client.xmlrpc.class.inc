<?php
/**
 * @file
 * Class that should do all the xmlrpc calls to the server and handles some basic
 * formatting and parsing.
 */

class onesignin_client_xmlrpc {
  protected $site_id;
  protected $server_url;
  protected $secret_key;

  public function __construct() {
    /**
     * @TODO
     * Make these settable through methods?
     */
    $this->site_id = variable_get('onesignin_client_site_id', onesignin_client_site_id());
    $this->secret_key = variable_get('onesignin_client_secret', '');
    $this->server_url = variable_get('onesignin_client_server', '') . 'xmlrpc.php';
  }

  /**
   * Fetch all available fields from the server site.
   */
  public function get_server_fields($reset = FALSE) {
    if (!$reset) {
      $cache = cache_get('onesignin_client_server_fields');
    }

    if (empty($cache) || !isset($cache->data)) {
      $request_id = $this->generate_request_id();
      $hash = $this->create_hash($request_id);

      $data = $this->xmlrpc('fetch_allowed_fields', $hash, $this->site_id, $request_id);
      $data = $data['fields'];
      cache_set('onesignin_client_server_fields', $data);
    }
    else {
      $data = $cache->data;
    }

    return $data;
  }

  /**
   * Fetch all available roles from the server site.
   */
  public function get_server_roles($reset = FALSE) {
    if (!$reset) {
     $cache = cache_get('onesignin_client_server_roles');
    }

    if (empty($cache) || !isset($cache->data)) {
      $request_id = $this->generate_request_id();
      $hash = $this->create_hash($request_id);

      $data = $this->xmlrpc('fetch_allowed_roles', $hash, $this->site_id, $request_id);
      $data = $data['roles'];
      cache_set('onesignin_client_server_roles', $data);
    }
    else {
      $data = $cache->data;
    }

    return $data;
  }

  /**
   * Fetch all data about a user from the master site.
   */
  public function fetch_user($uid) {
    $request_id = $this->generate_request_id();
    $hash = $this->create_hash($request_id, $uid);

    $data = $this->xmlrpc('fetch_user_data', $hash, $this->site_id, $request_id, intval($uid));

    return $data;
  }

  /**
   * Helper function to do the actual xmlrpc request.
   *
   * @param $method string
   * The method to call on the server.
   * @param ... mixed
   * The parameters to forward to the server.
   *
   * @return mixed
   * Whatever the called xmlrpc method returns, or FALSE on failure.
   */
  protected function xmlrpc($method) {
    $args = func_get_args();
    array_shift($args);
    return xmlrpc($this->server_url, array('haweb_sso.' . $method => $args));
  }

  /**
   * Helper function to create the hash to send with the request.
   *
   * @param ... mixed
   * The optional exta parameters to create a hash of.
   *
   * @return string
   * The generated hash.
   */
  protected function create_hash() {
    module_load_include('inc', 'onesignin_client');
    $args = array(
      $this->secret_key,
    );

    $extra_args = func_get_args();
    $args = array_merge($args, $extra_args);

    return onesignin_client_hash($args);
  }

  /**
   * Helper function to create a request id.
   *
   * @return string
   * A 43-character random string.
   */
  protected function generate_request_id($reset = FALSE) {
    return drupal_get_token(mt_rand());
  }
}
