<?php
/*
config: array(
  'host'=>      "ldap.example.com",
  'basedn'=>    "dc=example,dc=com",
  'userdn'=>    "ou=people,dc=example,dc=com",
);
*/
class Auth_ldap extends Auth_default {
  public $usesUsernamePassword = true;

  function __construct($id, $config) {
    parent::__construct($id, $config);
    $this->connection=null;

    if (!array_key_exists('rdn_identifier', $this->config)) {
      $this->config['rdn_identifier'] = 'uid';
    }
  }

  function connect() {
    if($this->connection)
      return $this->connection;

    $this->connection=ldap_connect($this->config['host'], array_key_exists('port', $this->config) ? $this->config['port'] : 389);
    ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);

    if (array_key_exists('bind_user', $this->config) && $this->config['bind_user']) {
      if(!ldap_bind($this->connection, $this->config['bind_user'], $this->config['bind_password']))
        return false;
    }
  }

  function authenticate($username, $password, $options=array()) {
    $this->connect();

    $ret=$this->get_user($username);

    if(!$password)
      return false;

    if(!$ret)
      return false;

    if(!ldap_bind($this->connection, "{$this->config['rdn_identifier']}={$username},{$this->config['userdn']}", $password))
      return false;

    return $ret;
  }

  function get_user($username) {
    $this->connect();

    $r=ldap_list($this->connection, $this->config['userdn'], "{$this->config['rdn_identifier']}={$username}", array("displayname", "mail", "uid"));
    $result=ldap_get_entries($this->connection, $r);

    if($result['count']==0)
      return null;

    $user = new Auth_User(
      $result[0]['uid'][0],
      $this->id,
      array(
	"name"=>$result[0]['displayname'][0],
	"email"=>$result[0]['mail'][0],
      ));
    $user->set_domain($this);
    return $user;
  }

  function group_members($group) {
    $this->connect();
    $ret=null;

    if(!$group)
      return;

    $r=ldap_list($this->connection, $this->config['groupdn'], "cn={$group}", array("displayname", "memberuid"));
    if(!$r)
      return false;

    $result=ldap_get_entries($this->connection, $r);

    if($result['count']==0)
      return null;

    $members=$result[0]['memberuid'];
    unset($members['count']);

    return $members;
  }

  function users() {
    $this->connect();
    $ret = null;

    $r = ldap_search($this->connection, $this->config['userdn'], 'objectClass=Person', array("uid"));
    if(!$r)
      return false;

    $result = ldap_get_entries($this->connection, $r);
    unset($result['count']);

    foreach($result as $r) {
      $ret[] = $r['uid'][0];
    }

    return $ret;
  }
}
