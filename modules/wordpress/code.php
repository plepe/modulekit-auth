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

    if (!array_key_exists('options', $this->config)) {
      $this->config['options'] = array();
    }
  }

  function connect() {
    if (!$this->connection) {
      $this->connection = new PDO($this->config['dsn'], $this->config['username'], $this->config['password'], $this->config['options']);
      // $this->prefix = $this->config['prefix'] ?? ''; // PHP7.0
      $this->prefix = array_key_exists('prefix', $this->config) ? $this->config['prefix'] : '';
    }
  }

  function authenticate($username, $password, $options=array()) {
    global $wp_hasher;

    $this->connect();

    $res = $this->connection->query("select * from {$this->prefix}users where user_status=0 and user_login=" . $this->connection->quote($username));
    if ($result = $res->fetch()) {
      if (strlen($result['user_pass']) === 32) {
        if (md5($password) !== $result['user_pass']) {
          return false;
        }
      }
      else {
        if (!isset($wp_hasher)) {
          require_once $this->config['path'] . '/wp-includes/class-phpass.php';
          $wp_hasher = new PasswordHash(8, true);
        }

        if (!$wp_hasher->CheckPassword($password, $result['user_pass'])) {
          return false;
        }
      }

      return new Auth_User(
        $username,
        $this->id,
        array(
          "name"=>$result['display_name'],
          "email"=>$result['user_email'],
        ));
    }

    return false;
  }

  function get_user($username) {
    $this->connect();

    $res = $this->connection->query("select * from {$this->prefix}users where user_status=0 and user_login=" . $this->connection->quote($username));
    if ($result = $res->fetch()) {
      return new Auth_User(
        $username,
        $this->id,
        array(
          "name"=>$result['display_name'],
          "email"=>$result['user_email'],
        ));
    }

    return null;
  }

  function group_members($group) {
    $this->connect();
    $ret = array();

    $res = $this->connection->query("select user_login, (select meta_value from {$this->prefix}usermeta where user_id=ID and meta_key='wp_capabilities') wp_capabilities from {$this->prefix}users where user_status=0");
    while ($result = $res->fetch()) {
      $groups = unserialize($result['wp_capabilities']);

      if (array_key_exists($group, $groups) && $groups[$group]) {
        $ret[] = $result['user_login'];
      }
    }

    return $ret;
  }

  function users() {
    $this->connect();
    $ret = array();

    $res = $this->connection->query("select user_login from {$this->prefix}users where user_status=0");
    if ($result = $res->fetch()) {
      $ret[] = $result['user_login'];
    }

    return $ret;
  }
}
