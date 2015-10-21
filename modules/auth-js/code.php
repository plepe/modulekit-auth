<?php
register_hook("auth_current_user", function() {
  global $auth;

  $auth->export_js();
});

function ajax_auth_authenticate($param, $postdata) {
  global $auth;

  $domain = null;
  if(array_key_exists('domain', $param) && $param['domain'])
    $domain = $param['domain'];

  return $auth->authenticate($param['username'], $postdata, $domain);
}

function ajax_auth_clear_authentication($param, $postdata) {
  global $auth;

  $auth->clear_authentication();
  return true;
}
