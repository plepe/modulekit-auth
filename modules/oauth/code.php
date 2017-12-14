<?php
/*
config: array(
  'type' => 'oauth',
  'req_url' => 'https://example.com/oauth/request_token',
  'authurl' => 'https://example.com/oauth/authorize',
  'acc_url' => 'https://example.com/oauth/access_token',
  'api_url' => 'http://api.example.com/api/0.6/user/details',
  'conskey' => '',
  'conssec' => '',
);
*/
class Auth_oauth extends Auth_default {
  public $usesUsernamePassword = false;

  function __construct($id, $config) {
    parent::__construct($id, $config);
  }

  function loginText () {
    return "Login via OAuth";
  }

  function authenticate($username, $password, $options=array()) {
    $r = $this->check_authentication();

    if ($r !== false && $_SESSION['auth'][$this->id]['username']) {
      $user = new Auth_User($_SESSION['auth'][$this->id]['username'], $this->id, array('name' => $_SESSION['auth'][$this->id]['username']));
      $user->set_domain($this);
      return $user;
    }
  }

  function clear_authentication () {
    unset($_SESSION['auth'][$this->id]);
  }

  function check_authentication () {
    global $db;

    if (!isset($_SESSION['auth'][$this->id]['state'])) {
      $_SESSION['auth'][$this->id]['state'] = 0;
    }

    // from: http://php.net/manual/en/oauth.examples.fireeagle.php
    // In state=1 the next request should include an oauth_token.
    // If it doesn't go back to 0
    if(!isset($_GET['oauth_token']) && $_SESSION['auth'][$this->id]['state'] == 1)
      $_SESSION['auth'][$this->id]['state'] = 0;

    try {
      $oauth = new OAuth($this->config['conskey'],$this->config['conssec'],OAUTH_SIG_METHOD_HMACSHA1,OAUTH_AUTH_TYPE_URI);
      //$oauth->enableDebug();
      if(!isset($_GET['oauth_token']) && !$_SESSION['auth'][$this->id]['state']) {
        $request_token_info = $oauth->getRequestToken($this->config['req_url']);
        $_SESSION['auth'][$this->id]['secret'] = $request_token_info['oauth_token_secret'];
        $_SESSION['auth'][$this->id]['state'] = 1;
        header('Location: '.$this->config['authurl'].'?oauth_token='.$request_token_info['oauth_token']);
        exit;
      } else if($_SESSION['auth'][$this->id]['state']==1) {
        $oauth->setToken($_GET['oauth_token'],$_SESSION['auth'][$this->id]['secret']);
        $access_token_info = $oauth->getAccessToken($this->config['acc_url']);
        $_SESSION['auth'][$this->id]['state'] = 2;
        $_SESSION['auth'][$this->id]['token'] = $access_token_info['oauth_token'];
        $_SESSION['auth'][$this->id]['secret'] = $access_token_info['oauth_token_secret'];
      } 
      $oauth->setToken($_SESSION['auth'][$this->id]['token'],$_SESSION['auth'][$this->id]['secret']);

      $oauth->fetch($this->config['api_url']);
      $userdata = new DOMDocument();
      $userdata->loadXML($oauth->getLastResponse());

      $_SESSION['auth'][$this->id]['username'] = $userdata->getElementsByTagName("user")->item(0)->getAttribute("display_name");
    } catch(OAuthException $E) {
      print_r($E);
      return false;
    }

    return true;
  }
}
