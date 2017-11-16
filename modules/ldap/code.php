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
  }

  function connect() {
    if($this->connection)
      return $this->connection;

    $this->connection=ldap_connect($this->config['host']);
    ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
  }

  function authenticate($username, $password, $options=array()) {
    $this->connect();

    $ret=$this->get_user($username);

    if(!$password)
      return false;

    if(!$ret)
      return false;

    if(!ldap_bind($this->connection, "uid={$username},{$this->config['userdn']}", $password))
      return false;

    return $ret;
  }

  function get_user($username) {
    $this->connect();

    $r=ldap_list($this->connection, $this->config['userdn'], "uid={$username}", array("displayname", "mail"));
    $result=ldap_get_entries($this->connection, $r);

    if($result['count']==0)
      return null;

    return new Auth_User(
      $username,
      $this->id,
      array(
	"name"=>$result[0]['displayname'][0],
	"email"=>$result[0]['mail'][0],
      ));
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
