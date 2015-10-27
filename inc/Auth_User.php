<?php
// username and domain are null for the anonymous user
class Auth_User {
  function __construct($username, $domain, $data) {
    $this->username=$username;
    $this->domain=$domain;
    $this->data=$data;
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

    return $auth->access($group, $this);
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
