<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?php
session_start();
$auth=new Auth();

if(isset($_REQUEST['username'])) {
  $auth_result=$auth->authenticate($_REQUEST['username'], $_REQUEST['password']);
}

if(isset($_REQUEST['logout']))
  $auth->clear_authentication();

print "Userdata: <pre>\n";
print_r($auth->current_user());
print "</pre><hr/>\n";

if(isset($auth_result)) {
  if($auth_result===false) {
    print "Cannot login!<br/>\n";
  }
  elseif($auth_result!==true) {
    print "Cannot login: ".implode("; ", $auth_result)."<br/>\n";
  }
}

?>
<html>
  <head>
    <title>Framework Example</title>
    <?php print modulekit_to_javascript(); /* pass modulekit configuration to JavaScript */ ?>
    <?php print modulekit_include_js(); /* prints all js-includes */ ?>
    <?php print modulekit_include_css(); /* prints all css-includes */ ?>
  </head>
  <body>
    <form method='post'>
    Username: <input type='text' name='username' value='' autofocus /><br/>
    Password: <input type='password' name='password' /><br/>
    <input type='submit' value='Login'>
    <input type='submit' name='logout' value='Logout'>
    </form>
    <?php
    print "Users:\n";
    print "<pre>\n";
    print_r($auth->users());
    print "</pre>\n";

$current_user = $auth->current_user();
$current_user_settings = new AuthUserSettings($current_user, $auth_user_settings_config);
print "<pre>\n";
print_r($current_user_settings->data());
print "</pre>\n";

    ?>

    <a href='test.php?auth=logout'>Logout</a>
  </body>
</html>
