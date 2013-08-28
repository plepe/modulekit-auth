<?
class auth_form {
  function __construct($auth) {
    $this->auth = $auth;

    $domains=array();

    foreach($this->auth->domains() as $k=>$d) {
      $domains[] = $k;
    }

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

    if(sizeof($domains) > 1) {
      $this->form_def['domain'] = array(
        'name'		=>"Domain",
	'type'		=>"select",
	'values'	=>$domains,
      );
    }

    $this->form = new form("auth_form", $this->form_def);
  }

  function show_status() {
    return "Not logged in.";
  }

  function show_form() {
    $ret  = "<form method='post'>\n";
    $ret .= "<ul>\n";
    if ($this->form) {
      $ret .= "  <li>Login:\n";
      $ret .= $this->form->show();
      $ret .= "<input type='submit' value='Login'>\n";
      $ret .= "  </li>\n";
    }
    $ret .= "</ul>\n";
    $ret .= "</form>\n";

    return $ret;
  }
}
