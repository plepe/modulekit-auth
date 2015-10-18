<?php
class AuthUserSettings {
  function __construct($user, $config=null) {
    if($config === null) {
      global $auth_user_settings_config;
      $this->config = $auth_user_settings_config;
    }
    else
      $this->config = $config;

    $this->user = $user;

    $this->load();
  }

  function load() {
    $id = $this->user->id();

    // anonymous user
    if($id === '') {
      $this->data =
        array_key_exists('anonymous_settings', $this->config) ? $this->config['anonymous_settings'] :
        array_key_exists('default_settings', $this->config) ? $this->config['default_settings'] : array();
      return;
    }

    switch($this->config['type']) {
      case 'file':
        if(file_exists($this->_path()))
          $this->data = json_decode(file_get_contents($this->_path()), true);
        else
          $this->data = array_key_exists('default_settings', $this->config) ? $this->config['default_settings'] : array();

        break;

      default:
        $this->data = array();
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
      case 'file':
        return file_put_contents($this->_path(), json_readable_encode($this->data)) !== false;

      default:
    }

    return true;
  }

  function _export_js($var_name) {
    return "var {$var_name} = new AuthUserSettings(auth.current_user(), null, " . json_encode($this->data, JSON_FORCE_OBJECT) . ");\n";
  }

  function export_js($var_name="current_user_settings") {
    $ret  = "<script type='text/javascript'>\n";
    $ret .= $this->_export_js($var_name);
    $ret .= "</script>\n";

    add_html_header($ret);
  }
}
