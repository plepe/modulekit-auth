<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?php
session_start();
call_hooks("init");

if(isset($_REQUEST['username'])) {
  $auth_result=$auth->authenticate($_REQUEST['username'], $_REQUEST['password']);
}

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

?>
<!DOCTYPE HTML>
<html>
  <head>
    <title>Framework Example</title>
    <?php print modulekit_to_javascript(); /* pass modulekit configuration to JavaScript */ ?>
    <?php print modulekit_include_js(); /* prints all js-includes */ ?>
    <?php print modulekit_include_css(); /* prints all css-includes */ ?>
  </head>
  <body>
<?php
if($error) {
  print $error;
}
?>
    <form method='post'>
    Username: <input type='text' name='username' value='' autofocus /><br/>
    Password: <input type='password' name='password' /><br/>
    <input type='submit' value='Login'>
    <input type='submit' name='logout' value='Logout'>
    </form>
    <hr/>
    <?php
print "Userdata: <pre>\n";
print_r($auth->current_user());
print "</pre><hr/>\n";

if(modulekit_loaded("modulekit-auth-user-settings")) {
  $current_user_settings = new AuthUserSettings($current_user, $auth_user_settings_config);
  print "User Settings: <pre>\n";
  print_r($current_user_settings->data());
  print "</pre><hr/>\n";
}

print "Users:\n";
print "<pre>\n";
print_r($auth->users());
print "</pre>\n";

    ?>

    <a href='test.php?auth=logout'>Logout</a>
  </body>
</html>
