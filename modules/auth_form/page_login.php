<?
if(!class_exists("Page"))
  return;

class Page_login extends Page {
  function content() {
    global $auth_form;

    if($auth_form->auth->is_logged_in()) {
      print "<p><a href='?{$this->param['return']}'>Continue</a>";
    }

    return $auth_form->show_form($this->param['return']);
  }
}
