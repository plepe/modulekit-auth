<?php
/*
config: array(
  'host'=>      "ldap.example.com",
  'basedn'=>    "dc=example,dc=com",
  'userdn'=>    "ou=people,dc=example,dc=com",
);
*/
class Auth_wordpress extends Auth_default {
  function __construct($id, $config) {
    parent::__construct($id, $config);
    $this->connection=null;
  }

  function connect() {
    if (!$this->connection) {
      define('WP_USE_THEMES', false);
      include("{$this->config['path']}/wp-load.php");
      $this->connection = true;
    }
  }

  function authenticate($username, $password, $options=array()) {
    $this->connect();

    $result = wp_authenticate($username, $password);

    if (is_wp_error($result)) {
      return false;
    }

    return $this->get_user($username);
  }

  function get_user($username) {
    $userinfo = get_user_by('login', $username);

    return new Auth_User(
      $username,
      $this->id,
      array(
	"name"=>$userinfo->data->display_name,
	"email"=>$userinfo->data->user_email,
      ));
  }

  function group_members($group) {
    $this->connect();
    $ret = array();

    foreach (get_users(array('role' => $group)) as $user) {
      $ret[] = $user->user_login;
    }

    return $ret;
  }

  function users() {
    $this->connect();
    $ret = array();

    foreach (get_users() as $user) {
      $ret[] = $user->user_login;
    }

    return $ret;
  }
}
