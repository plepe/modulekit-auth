<?php
if(!class_exists("Page"))
  return;

class Page_login extends Page {
  function content() {
    global $auth;

    $auth_form = new auth_form();

    if($auth->is_logged_in()) {
      if(isset($this->param['return']))
	page_reload($this->param['return']);

      return "<p><a href='?{$this->param['return']}'>Continue</a>";
    }

    return $auth_form->show_form();
  }
}
