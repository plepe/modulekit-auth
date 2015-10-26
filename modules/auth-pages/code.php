<?php
class auth_form {
  function __construct() {
    global $auth;

    $domains=array();

    foreach($auth->domains() as $k=>$d) {
      $domains[] = $k;
    }

    $this->form_def=array(
      'username'	=>array(
        'name'		=>"Username",
	'type'		=>"text",
	'html_attributes'=>array("autofocus"=>true),
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
	$auth->authenticate($data['username'], $data['password'],
        (isset($data['domain'])?$data['domain']:null));
    }

    if(isset($_REQUEST['return']))
      $this->return = $_REQUEST['return'];
    elseif(isset($_SERVER['HTTP_REFERER']))
      $this->return = $_SERVER['HTTP_REFERER'];
    else
      $this->return = array();
  }

  function show_status() {
    global $auth;

    if($auth->is_logged_in()) {
      $user = $auth->current_user();

      $ret  = "Logged in as {$user->name()}.";

      if(class_exists("Page_logout")) {
	$return="&return=";
	if(sizeof($_GET))
	  $return="&return=".urlencode(url_params($_GET));

        return $ret . " <a href='?page=logout$return'>Logout.</a>";
      }
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

  function show_form() {
    global $auth;

    if($auth->is_logged_in())
      return "";

    $ret  = "<form method='post'>\n";
    if ($this->form) {
      if(isset($this->auth_result) && ($this->auth_result === false)) {
	$ret .= "  <div class='field_errors'>\n";
	$ret .= "Username or Password invalid.\n";
	$ret .= "  </div>\n";
      }

      $ret .= $this->form->show();
      $ret .= "<input type='submit' value='Login'>\n";
      $ret .= "  </li>\n";
    }

    $ret .= html_export_to_input('return', $this->return);

    $ret .= "</ul>\n";
    $ret .= "</form>\n";

    return $ret;
  }
}
