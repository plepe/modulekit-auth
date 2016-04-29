<?php
// username and domain are null for the anonymous user
class Auth_User {
  private $access_cache;

  function __construct($username, $domain, $data) {
    $this->username=$username;
    $this->domain=$domain;
    $this->data=$data;

    $this->access_cache = array();
  }

  function id() {
    if($this->username === null)
      return "!";

    return "{$this->username}@{$this->domain}";
  }

  function data($k=null) {
    if($k === null)
      return $this->data;

    if(!array_key_exists($k, $this->data))
      return null;

    return $this->data[$k];
  }

  function name() {
    if($this->username === null)
      return "Anonymous";

    if(isset($this->data['name']))
      return $this->data['name'];

    return $this->username;
  }

  function email() {
    if(isset($this->data['email']))
      return $this->data['email'];

    return null;
  }

  function access($group) {
    global $auth;

    if(array_key_exists($group, $this->access_cache))
      return $this->access_cache[$group];

    $ret = $auth->access($group, $this);

    $this->access_cache[$group] = $ret;

    return $ret;
  }

  function _export_js() {
    return 'new Auth_User(' . json_encode($this->username) . ', ' .
              json_encode($this->domain) . ', ' .
              json_encode($this->data) . ')';
  }

  function settings() {
    if(!isset($this->_settings) &&
       (modulekit_loaded("auth-user-settings")))
      $this->_settings = new AuthUserSettings($this);

    return $this->_settings;
  }
}
