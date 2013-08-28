<?
class auth_form {
  function __construct($auth) {
    $this->auth = $auth;

    $this->form_def=array(
      'username'	=>array(
        'name'		=>"Username",
	'type'		=>"text",
      ),
      'password'	=>array(
        'name'		=>"Password",
	'type'		=>"password",
      ),
    );

    $this->form = new form("auth_form", $this->form_def);
  }

  function show_status() {
    return "Not logged in.";
  }

  function show_form() {
    return $this->form->show();
  }
}
