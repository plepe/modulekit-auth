<?php
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

  $fields = array("displayname", "mail");
  if(isset($options['fields']))
    $fields = array_unique(array_merge($fields, $options['fields']));

  $r=ldap_list($ldapconfig['conn'], $ldapconfig['userdn'], "uid={$username}", $fields);
  $result=ldap_get_entries($ldapconfig['conn'], $r);

  if($result['count']==0)
    return null;

  if(!ldap_bind($ldapconfig['conn'], "uid={$username},{$ldapconfig['userdn']}", $password))
    return false;

  $user_data = array(
    "username"=>$username,
    "name"=>$result[0]['displayname'][0],
    "email"=>$result[0]['mail'][0],
  );

  if(isset($options['fields'])) foreach($options['fields'] as $field)
    $user_data[$field] = $result[0][$field][0];

  return $user_data;
}

function auth_ldap_get_group($groupname, $options=array()) {
  global $ldapconfig;
  $ret=null;

  if(!$groupname)
    return;

  if(!isset($ldapconfig)) {
    print "\$ldapconfig not defined, please add to conf.php";
    return null;
  }

  if(!isset($ldapconfig['conn'])) {
    $ldapconfig['conn']=ldap_connect($ldapconfig['host']);
    ldap_set_option($ldapconfig['conn'], LDAP_OPT_PROTOCOL_VERSION, 3);
  }

  $r=ldap_list($ldapconfig['conn'], $ldapconfig['groupdn'], "cn={$groupname}", array("displayname", "memberuid"));
  if(!$r)
    return false;

  $result=ldap_get_entries($ldapconfig['conn'], $r);

  if($result['count']==0)
    return null;

  $members=$result[0]['memberuid'];
  unset($members['count']);

  return array(
    "groupname"=>$groupname,
    "name"=>$result[0]['displayname'][0],
    "members"=>$members,
  );
}

register_hook("authenticate", "auth_ldap_authenticate");
register_hook("auth_get_group", "auth_ldap_get_group");
