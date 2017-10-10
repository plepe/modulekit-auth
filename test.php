<?php include "conf.php"; /* load a local configuration */ ?>
<?php
$modulekit_load[] = 'auth_form';
?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?php
call_hooks('init');
session_start();

$auth=new Auth();
$auth_form = new auth_form($auth);

if(isset($_REQUEST['logout']))
  $auth->clear_authentication();

$current_user = $auth->current_user();

$error = null;
if(isset($auth_result)) {
  if($auth_result===false) {
    $error = "Cannot login!<br/>\n";
  }
  elseif($auth_result!==true) {
    $error = "Cannot login: ".implode("; ", $auth_result)."<br/>\n";
  }
}

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
?>
<?php
print $auth_form->show_form();

if ($auth->is_logged_in()) {
?>
    <form method='post'>
    <input type='submit' name='logout' value='Logout'>
    </form>
<?php
}
?>
    <hr/>
    <?php

print "\$auth->is_logged_in() -> " . ($auth->is_logged_in() ? 'true' : 'false');
print "<hr/>\n";

print "Userdata: <pre>\n";
print_r($current_user);
print "</pre><hr/>\n";

print "Users:\n";
print "<pre>\n";
print_r($auth->users());
print "</pre>\n";

    ?>

    <a href='test.php?auth=logout'>Logout</a>
  </body>
</html>
