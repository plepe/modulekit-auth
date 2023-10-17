<?php
// .htpasswd file may have additional fields:
// user_name:password hash:Full Name:E-Mail
// e.g.
// john:SyTGdf8WV4yv2:John Doe:john.doe@example.com
class Auth_htpasswd extends Auth_default {
  function __construct($id, $config) {
    parent::__construct($id, $config);
  }

  function authenticate($username, $password, $options=array()) {
    @$f=fopen($this->config['file'], "r");
    if(!$f) {
      $error=error_get_last();
      trigger_error("Domain {$this->id}, can't open htpasswd file: {$error['message']}", E_USER_WARNING);
      return $error['message'];
    }

    while($row=fgets($f)) {
      $row=trim($row);
      $row=explode(":", $row);

      if($row[0]==$username) {
        if(hash_equals($row[1], crypt($password, $row[1]))) {
          $ret=array();

          if(isset($row[2]))
            $ret['name']=$row[2];
          if(isset($row[3]))
            $ret['email']=$row[3];

          $user = new Auth_User($username, $this->id, $ret);
          $user->set_domain($this);
          return $user;
        }
        else
          return false;
      }
    }

    return false;
  }

  function get_user($username) {
    @$f=fopen($this->config['file'], "r");
    if(!$f) {
      $error=error_get_last();
      trigger_error("Domain {$this->id}, can't open htpasswd file: {$error['message']}", E_USER_WARNING);
      return $error['message'];
    }

    while($row=fgets($f)) {
      $row=trim($row);
      $row=explode(":", $row);

      if($row[0]==$username) {
        $ret=array();

        if(isset($row[2]))
          $ret['name']=$row[2];
        if(isset($row[3]))
          $ret['email']=$row[3];

        $user = new Auth_User(
	  $username,
	  $this->id,
	  $ret
	);
        $user->set_domain($this);
        return $user;
      }
    }

    return null;
  }

  function group_members($group) {
    return array();
  }

  function users() {
    @$f=fopen($this->config['file'], "r");
    if(!$f) {
      $error=error_get_last();
      trigger_error("Domain {$this->id}, can't open htpasswd file: {$error['message']}", E_USER_WARNING);
      return array();
    }

    $ret=array();
    while($row=fgets($f)) {
      $row=trim($row);
      $row=explode(":", $row);

      $ret[] = $row[0];
    }
    fclose($f);

    return $ret;
  }
}
