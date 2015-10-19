<?php
register_hook("auth_current_user", function() {
  global $auth;

  $auth->export_js();
});
