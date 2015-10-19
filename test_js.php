<?php include "conf.php"; /* load a local configuration */ ?>
<?php $modulekit_load[] = "auth-js"; ?>
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
    <?php print_add_html_headers(); /* prints all css-includes */ ?>
  <script type='text/javascript'>
window.onload = function() {
  call_hooks("init");

  var div = document.getElementById("userdata");

  var ret = "";
  ret += "ID: " + auth.current_user().id() + "\n";
  ret += "Name: " + auth.current_user().name() + "\n";
  ret += "Domain: " + auth.current_user().domain + "\n";
  ret += "E-Mail: " + auth.current_user().email() + "\n";
  ret += "Userdata: " + JSON.stringify(auth.current_user().data, null, '    ');

  div.appendChild(document.createTextNode(ret));

  if(modulekit_loaded("modulekit-auth-user-settings-js")) {
    var div = document.getElementById("usersettings");
    var ret = JSON.stringify(auth.current_user().settings().data(), null, '    ');

    div.appendChild(document.createTextNode(ret));
  }
}
  </script>
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

print "Userdata: <pre id='userdata'>\n";
print "</pre><hr/>\n";

if(modulekit_loaded("modulekit-auth-user-settings-js")) {
  print "User Settings: <pre id='usersettings'>\n";
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
