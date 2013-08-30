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

    $r=ldap_list($this->connection, $this->config['userdn'], "uid={$username}", array("displayname", "mail"));
    $result=ldap_get_entries($this->connection, $r);

    if($result['count']==0)
      return null;

    if(!ldap_bind($this->connection, "uid={$username},{$this->config['userdn']}", $password))
      return false;

    return array(
      "username"=>$username,
      "name"=>$result[0]['displayname'][0],
      "email"=>$result[0]['mail'][0],
    );
  }
}
