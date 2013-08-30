<?
/*
config: array(
  'host'=>      "ldap.example.com",
  'basedn'=>    "dc=example,dc=com",
  'userdn'=>    "ou=people,dc=example,dc=com",
);
*/
class Auth_ldap extends Auth_default {
  function __construct($config) {
    parent::__construct($config);
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

    return array(
      "username"=>$username,
      "name"=>$result[0]['displayname'][0],
      "email"=>$result[0]['mail'][0],
    );
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
}
