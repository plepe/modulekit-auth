<?php
function auth_user_menu() {
  global $auth;
  $menu_entries = array();

  $ret  = "<div class='auth_user_menu'>\n";
  $ret .= "<div class='info'>\n";
  if($auth->is_logged_in()) {
    $ret .= "<a href='#' onclick='return auth_user_menu_toggle()'>";
    $ret .= htmlspecialchars($auth->current_user()->name());
    $ret .= " ⚙</a>";

    $menu_entries[] = array(
      'href' => '?page=logout',
      'onclick' => 'return auth_user_menu_logout()',
      'weight' => 10,
      'text' => 'Logout',
    );
  }
  else {
    $ret .= "<a href='?page=login' onclick='return auth_user_menu_login()'>Login</a>\n";
    $ret .= "<a href='#' onclick='return auth_user_menu_toggle()'>";
    $ret .= "⚙</a>";
  }
  $ret .= "</div>\n";

  call_hooks('auth_user_menu', $menu_entries);

  if(sizeof($menu_entries)) {
    $ret .= "<ul class='menu'>\n";
    $menu_entries = weight_sort($menu_entries);
    foreach($menu_entries as $entry) {
      $ret .= "<li><a";

      if(array_key_exists('href', $entry))
	$ret .= " href='{$entry['href']}'";
      if(array_key_exists('onclick', $entry))
	$ret .= " onclick='{$entry['onclick']}'";

      $ret .= ">{$entry['text']}</a></li>\n";
    }
    $ret .= "</ul>\n";
  }

  $ret .= "</div>\n";

  return $ret;
}
