<?php
class AuthDisplay {
  function __construct($auth, $options=array()) {
    $this->auth = $auth;
    $this->options = $options;
  }

  function show() {
    $ret  = "<div class='auth_display'>\n";
    if($this->auth->is_logged_in()) {
      $ret .= htmlspecialchars($this->auth->current_user()->name());
      $ret .= " <a href='auth.php?logout'>Logout</a>";
    }
    else {
      $ret .= " <a href='auth.php'>Login</a>";
    }
    $ret .= "</div>\n";

    return $ret;
  }
}
