<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?
session_start();
$auth=new Auth();

if(isset($_REQUEST['username'])) {
  $auth_result=$auth->authenticate($_REQUEST['username'], $_REQUEST['password']);
}

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
    </form>
  </body>
</html>
