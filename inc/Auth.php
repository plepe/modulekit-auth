<?php
class Auth {
  function __construct($config=null) {
    global $auth;
    if(isset($auth))
      trigger_error("\$auth already set! Remove '\$auth = new Auth();' from your code!", E_USER_WARNING);
    $auth = $this;

    if($config) {
      $this->config=$config;
    }
    else {
      global $auth_config;
      $this->config=$auth_config;
    }

    if(isset($_SESSION['auth_current_user'])) {
      $this->current_user = $_SESSION['auth_current_user'];
    }
    else {
      $this->current_user=new Auth_User(null, null, array("name"=>"Anonymous"));
    }

    call_hooks("auth_current_user", $this->current_user);
  }

  function current_user() {
    return $this->current_user;
  }

  function current_domain() {
    $domain_id = $this->current_user->domain;
    if (!$domain_id) {
      return null;
    }

    return $this->domains()[$domain_id];
  }

  function set_current_user($user) {
    $this->current_user=$user;
    $_SESSION['auth_current_user'] = $user;

    call_hooks("auth_current_user", $this->current_user);
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
        $this->domains[$domain]=new $class($domain, $domain_config);
    }

    return $this->domains;
  }

  function authenticate($username, $password, $domain=null, $options=array()) {
    $errors=array();

    foreach($this->domains() as $d=>$domain_object) {
      if(($domain === null) || ($d == $domain)) {
	$user = $domain_object->authenticate($username, $password, $options);

	if(is_string($user)) {
	  $errors[]="Domain '{$d}': {$user}";
	}
	elseif($user) {
	  if(array_key_exists('require-group', $this->config)) {
	    if(!$this->access($this->config['require-group'], $user))
	      return false;
	  }

	  $this->current_user = $user;
	  $_SESSION['auth_current_user'] = $user;
	  call_hooks("auth_current_user", $this->current_user);

	  return true;
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
    call_hooks("auth_current_user", $this->current_user);
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
	  return $user;
      }
    }

    return null;
  }

  /**
   * List all group members of the given group(s).
   * @param string|string[] Group or groups. A list of groups may be passed as array or as concatenated string, joined by ';'. Groups can by references from the $auth_config array (simple string), users (e.g. 'user1@default'), domain groups (e.g. '&group1@default') or all users of domain ('*@default').p
   * @return string[] List of users including their domain (e.g. 'user1@default').
   */
  function group_members($groups) {
    if(!is_array($groups))
      $groups=explode(";", $groups);

    if(sizeof($groups)>1) {
      $ret=array();

      foreach($groups as $group) {
	$list = $this->group_members($group);

	if(is_array($list))
	  $ret = array_merge($ret, $list);
      }

      return $ret;
    }

    $group = $groups[0];
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

    // anonymous user
    if($group === '!')
      return array('!');

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
      if($m[1] == "*") {
	if(!array_key_exists($m[2], $this->domains()))
	  return array();

	$members = $this->domains[$m[2]]->users();

	if(!$members)
	  return array();

	foreach($members as $i=>$member)
	  $members[$i]="{$member}@{$m[2]}";

	return $members;
      }
      else
	return array($group);
    }

    return array();
  }

  /**
   * return list of usernames of all domains
   * @return string[] list of usernames, including domain name (e.g.
   *   user@domain)
   */
  function users() {
    $ret = array();

    foreach($this->domains() as $d=>$domain_object) {
      foreach($domain_object->users() as $user)
	$ret[] = "{$user}@{$d}";
    }

    return $ret;
  }

  function access($group, $user=null) {
    if(!$group)
      return true;

    if(!$user)
      $user=$this->current_user();

    if(($group === true) || ($group === '*'))
      return ($user->id() !== "!");

    $members=$this->group_members($group);

    return in_array($user->id(), $members);
  }

  function http_authenticate($realm="My Realm") {
    // Executed from CLI - assume successful authentication
    if(php_sapi_name() == "cli")
      return true;

    if(isset($_SERVER['PHP_AUTH_USER']) &&
       ($this->authenticate($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) === true)) {
      return true;
    }
    else {
      header("WWW-Authenticate: Basic realm=\"{$realm}\"");
      header('HTTP/1.0 401 Unauthorized');
      exit();
    }
  }

  function _export_js($var_name) {
    $export_config = array('domains' => array());
    foreach($this->config['domains'] as $domain=>$domain_config)
      $export_config['domains'][$domain] = null;

    return "var {$var_name} = new Auth(" . json_encode($export_config) . ", " . $this->current_user->_export_js() . ");\n";
  }

  function export_js($var_name='auth') {
    $ret  = "<script type='text/javascript'>\n";
    $ret .= $this->_export_js($var_name);
    $ret .= "</script>\n";

    add_html_header($ret);
  }
}

register_hook("init", function() {
  new Auth();
});
