<?php
// .htpasswd file may have additional fields:
// user_name:password hash:Full Name:E-Mail
// e.g.
// john:SyTGdf8WV4yv2:John Doe:john.doe@example.com
class Auth_htpasswd extends Auth_default {
  function __construct($config) {
    parent::__construct($config);
  }

  function authenticate($username, $password, $options=array()) {
    @$f=fopen($this->config['file'], "r");
    if(!$f) {
      $error=error_get_last();
      return $error['message'];
    }

    while($row=fgets($f)) {
      $row=trim($row);
      $row=explode(":", $row);

      if($row[0]==$username) {
        if(crypt($password, $row[1])==$row[1]) {
          $ret=array();

          if(isset($row[2]))
            $ret['name']=$row[2];
          if(isset($row[3]))
            $ret['email']=$row[3];

          return new Auth_User($username, $this->id, $ret);
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

        return new Auth_User(
	  $username,
	  $this->id,
	  $ret
	);
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
      return $error['message'];
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
