<?php
class Page_user_settings extends Page {
  function content() {
    global $auth;

    $settings_form = array();

    call_hooks("auth_user_settings_form", $settings_form);

    $form = new form("user_settings", $settings_form);

    if($form->is_complete()) {
      $data = $form->save_data();

      $auth->current_user()->settings()->save($data);

      $form->clear();
    }
    
    if($form->is_empty()) {
      $form->set_data($auth->current_user()->settings()->data());
    }

    $ret .= "<h1>User settings</h1>";
    $ret .= "<form method='post'>\n";
    $ret .= $form->show();
    $ret .= "<input type='submit' value='Save'>\n";
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

register_hook("auth_user_settings_form", function(&$list) {
  $list['foo'] = array(
    'type'=>'text',
    'name'=>'Foo'
  );
});
