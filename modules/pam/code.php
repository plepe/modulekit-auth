<?
class Auth_pam extends Auth_default {
  function __construct($config) {
    parent::__construct($config);
  }

  function authenticate($username, $password, $options=array()) {
    $error=null;

    $result=pam_auth($username, $password, &$error);

    if($error)
      return $error;

    if($result)
      return posix_getpwnam($username);

    return false;
  }
}
