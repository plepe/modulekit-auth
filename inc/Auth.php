<?
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

  function authenticate($username, $password, $options=array()) {
    $errors=array();

    foreach($this->domains() as $domain=>$domain_object) {
      $result=$domain_object->authenticate($username, $password, $options);

      if(is_array($result)) {
        $this->current_user=new Auth_User($username, $domain, $result);
        $_SESSION['auth_current_user']=array($username, $domain, $result);

        return true;
      }
      elseif(is_string($result)) {
        $errors[]="Domain '{$domain}': {$result}";
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
}
