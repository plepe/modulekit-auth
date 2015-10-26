<?php $modulekit_load = array("page", "html"); ?>
<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?php
session_start();
call_hooks("init");

class Page_default {
  function content() {
    global $auth;

    $ret = "";

    if(!modulekit_loaded('modulekit-auth-user-menu')) {
      if($auth->is_logged_in())
        print "<a href='?page=logout'>Logout</a><hr/>\n";
      else
        print "<a href='?page=login'>Login</a><hr/>\n";
    }

    $ret .= "Userdata (PHP): <pre>\n";
    $ret .= "ID: " . $auth->current_user()->id() . "\n";
    $ret .= print_r($auth->current_user()->data(), 1);
    $ret .= "</pre><hr/>\n";

    if(modulekit_loaded("modulekit-auth-user-settings")) {
      $ret .= "User Settings (PHP): <pre>\n";
      $ret .= print_r($auth->current_user()->settings()->data(), 1);
      $ret .= "</pre><hr/>\n";
    }

    if(modulekit_loaded("modulekit-auth-js")) {
      $ret .= "Userdata (JS): <pre id='userdata'>\n";
      $ret .= "</pre><hr/>\n";
    }

    if(modulekit_loaded("modulekit-auth-user-settings-js")) {
      $ret .= "User Settings (JS): <pre id='usersettings'>\n";
      $ret .= "</pre><hr/>\n";
    }

    return $ret;
  }
}

$page = get_page($_REQUEST);
if($page) {
  $content = $page->content();
}
else {
  $content = "Invalid page!";
}

if(method_exists($page, 'title'))
  $title = $page->title();

$user_menu = "";
if(modulekit_loaded('auth-user-menu'))
  $user_menu = auth_user_menu();

Header("Content-Type: text/html; charset=utf-8");
?>
<!DOCTYPE HTML>
<html>
  <head>
    <title>Framework Example</title>
    <?php print modulekit_to_javascript(); /* pass modulekit configuration to JavaScript */ ?>
    <?php print modulekit_include_js(); /* prints all js-includes */ ?>
    <?php print modulekit_include_css(); /* prints all css-includes */ ?>
    <?php print_add_html_headers(); /* prints all css-includes */ ?>
  <script type='text/javascript'>
window.onload = function() {
  call_hooks("init");

  if(modulekit_loaded("modulekit-auth-js")) {
    var div = document.getElementById("userdata");

    if(div) {
      var ret = "";
      ret += "ID: " + auth.current_user().id() + "\n";
      ret += "Name: " + auth.current_user().name() + "\n";
      ret += "Domain: " + auth.current_user().domain + "\n";
      ret += "E-Mail: " + auth.current_user().email() + "\n";
      ret += "Userdata: " + JSON.stringify(auth.current_user().data(), null, '    ');

      div.appendChild(document.createTextNode(ret));
    }
  }

  if(modulekit_loaded("modulekit-auth-user-settings-js")) {
    var div = document.getElementById("usersettings");

    if(div) {
      var ret = JSON.stringify(auth.current_user().settings().data(), null, '    ');

      div.appendChild(document.createTextNode(ret));
    }
  }
}
  </script>
  </head>
  <body>
<?php
if(isset($title))
  print "<h1>{$title}</h1>\n";
print $content;
print $user_menu;
?>
  </body>
</html>
