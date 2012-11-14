<?
/* Add this to conf.php:

$ldapconfig=array(
  'host'=>      "ldap.example.com",
  'basedn'=>    "dc=example,dc=com",
  'userdn'=>    "ou=people,dc=example,dc=com",
);

*/
function auth_ldap_authenticate($username, $password, $options=array()) {
  global $ldapconfig;
  $ret=null;

  if(!$username)
    return;

  if(!$password)
    return false;

  if(!isset($ldapconfig)) {
    print "\$ldapconfig not defined, please add to conf.php";
    return null;
  }

  if(!isset($ldapconfig['conn'])) {
    $ldapconfig['conn']=ldap_connect($ldapconfig['host']);
    ldap_set_option($ldapconfig['conn'], LDAP_OPT_PROTOCOL_VERSION, 3);
  }

  $r=ldap_list($ldapconfig['conn'], $ldapconfig['userdn'], "uid={$username}", array("displayname", "mail"));
  $result=ldap_get_entries($ldapconfig['conn'], $r);

  if($result['count']==0)
    return null;

  if(!($result=ldap_bind($ldapconfig['conn'], "uid={$username},{$ldapconfig['userdn']}", $password)))
    return false;

  return array(
    "username"=>$username,
    "name"=>$result[0]['displayname'][0],
    "email"=>$result[0]['mail'][0],
  );
}

register_hook("authenticate", "auth_ldap_authenticate");
