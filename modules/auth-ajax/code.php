<?php
register_hook("auth_user_menu", function(&$menu_entries) {
  $menu_entries[] = array(
    'url' => 'javascript:auth_ajax_form()',
    'text' => 'login'
  );
});
