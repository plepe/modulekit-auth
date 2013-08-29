<?
function authenticate($username, $password, $options=array()) {
  $ret=call_hooks("authenticate", $username, $password, $options);

  if(!sizeof($ret))
    return false;

  return $ret[0];
}

function auth_get_group($groupname, $options=array()) {
  $ret=call_hooks("auth_get_group", $groupname, $options);

  if(!sizeof($ret))
    return array();

  return $ret[0];
}

function url_params($a, $prefix="") {
  $ret=array();

  if(!$a)
    return null;

  foreach($a as $k=>$v) {
    $ret[]=urlencode($k)."=".urlencode($v);
  }

  return implode("&amp;", $ret);
}
