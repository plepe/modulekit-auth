<?php
class Auth {
  function __construct($config=null) {
    if($config) {
      $this->config=$config;
    }
    else {
      global $auth_config;
      $this->config=$auth_config;
    }

    if(isset($_SESSION['auth_current_user'])) {
      $d=$_SESSION['auth_current_user'];

      $this->current_user=new Auth_User($d[0], $d[1], $d[2]);
    }
    else {
      $this->current_user=new Auth_User(null, null, array("name"=>"Anonymous"));
    }
  }

  function current_user() {
    return $this->current_user;
  }

  function set_current_user($user) {
    $this->current_user=$user;
    $_SESSION['auth_current_user']=array($user->username, $user->domain, $user->data);
  }

  function is_logged_in() {
    return isset($_SESSION['auth_current_user']);
  }

  function domains() {
    if(isset($this->domains))
      return $this->domains;

    $this->domains=array();

    if((!isset($this->config['domains']))&&
       (!is_array($this->config['domains'])))
      return false;

    foreach($this->config['domains'] as $domain=>$domain_config) {
      $class="Auth_".$domain_config['type'];
      modulekit_load(array($domain_config['type']));

      if(class_exists($class))
        $this->domains[$domain]=new $class($domain_config);
    }

    return $this->domains;
  }

  function authenticate($username, $password, $domain=null, $options=array()) {
    $errors=array();

    foreach($this->domains() as $d=>$domain_object) {
      if(($domain === null) || ($d == $domain)) {
	$result=$domain_object->authenticate($username, $password, $options);

	if(is_array($result)) {
	  $user = new Auth_User($username, $d, $result);

	  if(array_key_exists('require-group', $this->config)) {
	    if(!$this->access($this->config['require-group'], $user))
	      return false;
	  }

	  $this->current_user = $user;
	  $_SESSION['auth_current_user'] = array($username, $d, $result);

	  return true;
	}
	elseif(is_string($result)) {
	  $errors[]="Domain '{$d}': {$result}";
	}
      }
    }

    if(sizeof($errors))
      return $errors;
    return false;
  }

  function clear_authentication() {
    unset($_SESSION['auth_current_user']);
    $this->current_user=new Auth_User(null, null, array("name"=>"Anonymous"));
  }

  function get_user($id) {
    $id=explode("@", $id);
    $username=$id[0];
    if(sizeof($id)==1)
      $domain=null;
    else
      $domain=$id[1];

    $domains=$this->domains();

    foreach($domains as $d=>$domain_object) {
      if(($domain === null) || ($d == $domain)) {
	$user=$domain_object->get_user($username);

	if($user)
	  return new Auth_User($username, $d, $user);
      }
    }

    return null;
  }

  function group_members($group) {
    $groups=explode(";", $group);
    if(sizeof($groups)>1) {
      $ret=array();

      foreach($groups as $group)
	$ret=array_merge($ret, $this->group_members($group));

      return $ret;
    }

    if(isset($this->config['groups']) &&
       isset($this->config['groups'][$group])) {
      $conf_group=$this->config['groups'][$group];

      if(is_string($conf_group)) {
	return $this->group_members($conf_group);
      }

      if(is_array($conf_group)) {
	$ret=array();
	foreach($conf_group as $g)
	  $ret=array_merge($ret, $this->group_members($g));

	return $ret;
      }

      return array();
    }

    if(preg_match("/^&(.*)@(.*)$/", $group, $m)) {
      foreach($this->domains() as $d=>$domain_object) {
	if(($m[2] === null) || ($d == $m[2])) {
	  $members=$domain_object->group_members($m[1]);

	  if(!$members)
	    return array();

	  foreach($members as $i=>$m)
	    $members[$i]="{$m}@{$d}";

          return $members;
	}
      }
    }

    if(preg_match("/^(.*)@(.*)$/", $group, $m)) {
      return array($group);
    }

    return array();
  }

  function access($group, $user=null) {
    if(!$group)
      return true;

    if(!$user)
      $user=$this->current_user();

    if(($group === true) || ($group === '*'))
      return ($user->id() !== "");

    $members=$this->group_members($group);

    return in_array($user->id(), $members);
  }

  function http_authenticate() {
    if(isset($_SERVER['PHP_AUTH_USER']) &&
       $this->authenticate($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
      return true;
    }
    else {
      header('WWW-Authenticate: Basic realm="My Realm"');
      header('HTTP/1.0 401 Unauthorized');
      exit();
    }
  }
}
