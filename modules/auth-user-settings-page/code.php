<?php
class Page_user_settings extends Page {
  function title() {
    return 'User settings';
  }

  function content() {
    global $auth;

    $settings_form = array();

    call_hooks("auth_user_settings_form", $settings_form);

    $form = new form("user_settings", $settings_form);

    if($form->is_complete()) {
      $data = $form->save_data();

      $auth->current_user()->settings()->save($data);

      $form->clear();

      if(array_key_exists('return', $this->param))
	page_reload($this->param['return']);
    }
    
    if($form->is_empty()) {
      $form->set_data($auth->current_user()->settings()->data());
    }

    $ret  = "<form method='post'>\n";
    $ret .= $form->show();
    $ret .= "<input type='submit' value='Save'>\n";
    if(array_key_exists('return', $this->param))
      $ret .= html_export_to_input('return', $this->param['return']);
    elseif(isset($_SERVER['HTTP_REFERER']))
      $ret .= html_export_to_input('return', $_SERVER['HTTP_REFERER']);
    $ret .= "</form>\n";

    return $ret;
  }
}

register_hook("auth_user_menu", function(&$entries) {
  $entries[] = array(
    'href' => page_url(array("page" => "user_settings")),
    'text' => 'Settings',
  );
});
