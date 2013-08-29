<?
if(!class_exists("Page"))
  return;

class Page_logout extends Page {
  function content() {
    global $auth_form;

    $auth_form->auth->clear_authentication();

    return "Logged out.<p><a href='?{$this->param['return']}'>Continue</a>";
  }
}
