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

    if($this->form->is_complete()) {
      $data = $this->form->get_data();

      $this->auth_result =
	$this->auth->authenticate($data['username'], $data['password'],
        (isset($data['domain'])?$data['domain']:null));
    }
  }

  function show_status() {
    if($this->auth->is_logged_in()) {
      $user = $this->auth->current_user();

      return "Logged in as {$user->name()}.";
    }
    else {
      if(class_exists("Page_login")) {
	$return="&return=";
	if(sizeof($_GET))
	  $return="&return=".urlencode(url_params($_GET));

	return "<a href='?page=login$return'>Login.</a>";
      }
      else
	return "Not logged in.";
    }
  }

  function show_form($return) {
    if($this->auth->is_logged_in())
      return "";

    $ret  = "<form method='post'>\n";
    $ret .= "<ul>\n";
    if ($this->form) {
      $ret .= "  <li>Login:\n";

      if($this->auth_result === false) {
	$ret .= "  <div class='field_errors'>\n";
	$ret .= "Username or Password wrong\n";
	$ret .= "  </div>\n";
      }

      $ret .= $this->form->show();
      $ret .= "<input type='submit' value='Login'>\n";
      $ret .= "  </li>\n";
    }

    if($return !== null) {
      $ret .= "<li><a href='?{$return}'>Continue anonymously</a>\n";
    }

    $ret .= "</ul>\n";
    $ret .= "</form>\n";

    return $ret;
  }
}
