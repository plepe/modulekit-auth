<?
// username and domain are null for the anonymous user
class Auth_User {
  function __construct($username, $domain, $data) {
    $this->username=$username;
    $this->domain=$domain;
    $this->data=$data;
  }

  function id() {
    if($this->username === null)
      return "";

    return "{$this->username}:{$this->domain}";
  }

  function name() {
    if($this->username === null)
      return "Anonymous";

    return $this->username;
  }
}
