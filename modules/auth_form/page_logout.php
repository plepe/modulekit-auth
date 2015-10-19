<?php
if(!class_exists("Page"))
  return;

class Page_logout extends Page {
  function content() {
    global $auth;

    $auth->clear_authentication();

    if(isset($this->param['return']))
      page_reload($this->param['return']);

    return "<p>Logged out.<p><a href='?{$this->param['return']}'>Continue</a>";
  }
}
