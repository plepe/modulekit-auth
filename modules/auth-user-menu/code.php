<?php
function auth_user_menu() {
  global $auth;
  $menu_entries = array();

  $ret  = "<div class='auth_user_menu'>\n";
  $ret .= "<div class='info'>\n";
  if($auth->is_logged_in()) {
    $ret .= "<a href='#' onclick='auth_user_menu_toggle()'>";
    $ret .= htmlspecialchars($auth->current_user()->name());
    $ret .= " ⚙</a>";

    $menu_entries[] = array(
      'url' => '?page=logout',
      'weight' => 10,
      'text' => 'Logout',
    );
  }
  else {
    $ret .= "<a href='?page=login'>Login</a>\n";
    $ret .= "<a href='#' onclick='auth_user_menu_toggle()'>";
    $ret .= "⚙</a>";
  }
  $ret .= "</div>\n";

  call_hooks('auth_user_menu', $menu_entries);

  if(sizeof($menu_entries)) {
    $ret .= "<ul class='menu'>\n";
    $menu_entries = weight_sort($menu_entries);
    foreach($menu_entries as $entry) {
      $ret .= "<li><a href='{$entry['url']}'>{$entry['text']}</a></li>\n";
    }
    $ret .= "</ul>\n";
  }

  $ret .= "</div>\n";

  return $ret;
}
