<?php
class AuthUserSettings {
  function __construct($user, $config=null) {
    if($config === null) {
      global $auth_user_settings_config;
      if(isset($auth_user_settings_config))
	$this->config = $auth_user_settings_config;
      else
	$this->config = array(
	  'type' => 'session'
	);
    }
    else
      $this->config = $config;

    $this->user = $user;

    if($this->user->id() === '')
      $this->config['type'] = 'session';

    $this->load();
  }

  function load() {
    $id = $this->user->id();

    $this->data = null;

    switch($this->config['type']) {
      case 'session':
	if(array_key_exists('auth_user_settings', $_SESSION))
	  $this->data = $_SESSION['auth_user_settings'];
	break;

      case 'file':
        if(file_exists($this->_path()))
          $this->data = json_decode(file_get_contents($this->_path()), true);
        break;

      default:
        $this->data = null;
    }

    if($this->data === null) {
      // anonymous user
      if($id === '') {
	$this->data =
	  array_key_exists('anonymous_settings', $this->config) ? $this->config['anonymous_settings'] :
	  array_key_exists('default_settings', $this->config) ? $this->config['default_settings'] :
	  array();
      }

      else {
	$this->data =
	  array_key_exists('default_settings', $this->config) ? $this->config['default_settings'] :
	  array();
      }
    }
  }

  function _path() {
    $id = $this->user->id();

    return "{$this->config['path']}/{$id}.json";
  }

  function data($k=null) {
    if($k === null)
      return $this->data;

    if(!array_key_exists($k, $this->data))
      return null;

    return $this->data[$k];
  }

  function save($data) {
    $this->load();

    foreach($data as $k=>$v) {
      if($v === null)
        unset($this->data[$k]);
      else
        $this->data[$k] = $v;
    }

    switch($this->config['type']) {
      case 'session':
        $_SESSION['auth_user_settings'] = $this->data;
	return true;

      case 'file':
        return file_put_contents($this->_path(), json_readable_encode($this->data)) !== false;

      default:
    }

    return true;
  }
}
