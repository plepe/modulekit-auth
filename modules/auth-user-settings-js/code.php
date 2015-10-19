<?php
function ajax_auth_user_settings_js_load($param) {
  global $auth;

  if($param['user'] !== $auth->current_user()->id()) {
    return "Authentication mismatch!";
  }

  global $current_user_settings;
  return $current_user_settings->data();
}

function ajax_auth_user_settings_js_save($param, $post) {
  global $auth;

  if($param['user'] !== $auth->current_user()->id()) {
    return "Authentication mismatch!";
  }

  global $current_user_settings;
  $current_user_settings->save(json_decode($post, true));

  return true;
}

register_hook("auth_current_user", function($user) {
  html_export_var(array("_auth_user_settings_data" => (object)$user->settings()->data()));
});
