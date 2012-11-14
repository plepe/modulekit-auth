<?
function authenticate($username, $password, $options=array()) {
  $ret=call_hooks("authenticate", $username, $password, $options);

  print_r($ret);
}
