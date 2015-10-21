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
      elseif(isset($_SERVER['HTTP_REFERER']))
	page_reload($_SERVER['HTTP_REFERER']);
      else
	page_reload(array());

      return "<p><a href='?{$this->param['return']}'>Continue</a>";
    }

    return $auth_form->show_form();
  }
}
