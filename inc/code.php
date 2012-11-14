<?
function authenticate($username, $password, $options=array()) {
  $ret=call_hooks("authenticate", $username, $password, $options);

  if(!sizeof($ret))
    return false;

  return $ret[0];
}
