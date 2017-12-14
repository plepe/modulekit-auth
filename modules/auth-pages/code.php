<?php
class AuthPages {
  function __construct($auth) {
    $this->auth = $auth;

    $domains = array();
    $this->nonUsernamePasswordDomains = array();

    foreach($this->auth->domains() as $k=>$d) {
      if ($d->usesUsernamePassword) {
        $domains[] = $k;
      } else {
        $this->nonUsernamePasswordDomains[] = $d;
      }
    }

    if (sizeof($domains) > 0) {
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
    }

    if(sizeof($domains) > 1) {
      $this->form_def['domain'] = array(
        'name'		=>"Domain",
	'type'		=>"select",
	'values'	=>$domains,
      );
    }

    if (sizeof($domains)) {
      $this->form = new form("auth_form", $this->form_def);

      if($this->form->is_complete() && csrf_check_token(true)) {
        $data = $this->form->get_data();

        $this->auth_result =
          $this->auth->authenticate($data['username'], $data['password'],
          (isset($data['domain'])?$data['domain']:null));
      }
    }

    if (isset($_REQUEST['login'])) {
      $domain = array_keys($_REQUEST['login'])[0];
      $this->auth->authenticate(null, null, $domain);
    }

    if(isset($_REQUEST['return']))
      $this->return = $_REQUEST['return'];
    elseif(isset($_SERVER['HTTP_REFERER']))
      $this->return = $_SERVER['HTTP_REFERER'];
    else
      $this->return = array();
  }

  function show_status() {
    if($this->auth->is_logged_in()) {
      $user = $this->auth->current_user();

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

  function show_form($return=null) {
    if($this->auth->is_logged_in())
      return "";

    $ret  = "<form method='post'>\n";
    if ($this->form) {
      $ret .= csrf_show_token();

      if(isset($this->auth_result) && $this->auth_result === false) {
	$ret .= "  <div class='field_errors'>\n";
	$ret .= "Username or Password invalid.\n";
	$ret .= "  </div>\n";
      }

      $ret .= $this->form->show();

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $d = $_POST;
        unset($d['auth_form']);
        unset($d['csrf_token']);
        $ret .= $this->print_post_data($d);
      }

      $ret .= "<input type='submit' value='Login'>\n";
      $ret .= "  </li>\n";
    }

    foreach ($this->nonUsernamePasswordDomains as $d) {
      $ret .= "<li>{$d->name()}: <input type='submit' name='login[{$d->id}]' value='{$d->loginText()}'></li>\n";
    }

    if($return !== null) {
      $ret .= "<li><a href='?{$return}'>Continue anonymously</a>\n";
    }

    $ret .= html_export_to_input('return', $this->return);

    $ret .= "</ul>\n";
    $ret .= "</form>\n";

    return $ret;
  }

  private function print_post_data ($data, $varname=null) {
    $ret = '';

    if (is_array($data)) {
      foreach ($data as $k => $v) {
        $ret .= $this->print_post_data($v, $varname === null ? $k : "{$varname}[{$k}]");
      }
    }
    else {
      $ret .= '<input type="hidden" name="' . htmlspecialchars($varname) . '" value="' . htmlspecialchars($data) . '"/>';
    }

    return $ret;
  }
}
