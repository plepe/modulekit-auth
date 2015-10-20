<?php $modulekit_load = array("page", "html"); ?>
<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?php
session_start();
call_hooks("init");

class Page_default {
  function content() {
    global $auth;

    return "<pre>" . print_r($auth->current_user(), 1) . "</pre>\n" .
      "<a href='page.php?page=login'>Login</a> - " .
      "<a href='page.php?page=logout'>Logout</a>";
  }
}

$page = get_page($_REQUEST);
if($page) {
  $content = $page->content();
}
else {
  $content = "Invalid page!";
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
    <?php print_add_html_headers(); /* prints all css-includes */ ?>
  <script type='text/javascript'>
window.onload = function() {
  call_hooks("init");
}
  </script>
  </head>
  <body>
<?php
print $content;
?>
  </body>
</html>
