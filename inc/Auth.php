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

    $this->current_user=new Auth_User(null, null, array("name"=>"Anonymous"));
  }

  function current_user() {
    return $this->current_user;
  }

  function authenticate($username, $password, $options=array()) {
    return false;
  }
}
