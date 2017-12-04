<?php include "conf.php"; /* load a local configuration */ ?>
<?php
$modulekit_load[] = 'auth-pages';
?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?php
session_start();
call_hooks('init');

$auth=new Auth();
$auth_form = new AuthPages($auth);

if(isset($_REQUEST['logout']))
  $auth->clear_authentication();

$error = null;
if(isset($auth_result)) {
  if($auth_result===false) {
    $error = "Cannot login!<br/>\n";
  }
  elseif($auth_result!==true) {
    $error = "Cannot login: ".implode("; ", $auth_result)."<br/>\n";
  }
}

Header("Content-Type: text/html; charset=utf-8");
?>
<!DOCTYPE HTML>
<html>
  <head>
    <title>Framework Example</title>
    <?php print modulekit_to_javascript(); /* pass modulekit configuration to JavaScript */ ?>
    <?php print modulekit_include_js(); /* prints all js-includes */ ?>
    <?php print modulekit_include_css(); /* prints all css-includes */ ?>
    <?php print_add_html_headers(); /* print additional html headers */ ?>
  </head>
  <body>
<?php
if($error) {
  print $error;
}

print $auth_form->show_form();

if ($auth->is_logged_in()) {
?>
    <form method='post'>
    <input type='submit' name='logout' value='Logout'>
    </form>
    <hr/>
    <?php
}

print "\$auth->is_logged_in() -> " . ($auth->is_logged_in() ? 'true' : 'false');
print "<hr/>\n";

print "Userdata: <pre>\n";
print_r($auth->current_user());
print "</pre><hr/>\n";

if(modulekit_loaded("modulekit-auth-user-settings")) {
  print "User Settings: <pre>\n";
  print_r($auth->current_user()->settings()->data());
  print "</pre><hr/>\n";
}

?>
    <hr/>
    <?php

print "Users:\n";
print "<pre>\n";
print_r($auth->users());
print "</pre>\n";

    ?>

    <a href='test.php?auth=logout'>Logout</a>
  </body>
</html>
