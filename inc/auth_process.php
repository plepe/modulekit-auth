<?php
function _auth_process($auth) {
  $error = null;
  if(isset($_REQUEST['logout'])) {
    $auth->clear_authentication();

    if(isset($_REQUEST['return_to'])) {
      page_reload($_REQUEST['return_to']);
      print "You have been logged out.";
      exit(0);
    }

    if(isset($_SERVER['HTTP_REFERER'])) {
      page_reload($_SERVER['HTTP_REFERER']);
      print "You have been logged out.";
      exit(0);
    }
  }

  $default_domain = array_keys($auth->domains());
  $default_domain = $default_domain[0];
  $form_auth_def = array(
    'username' => array(
      'type' => 'text',
      'name' => 'Username',
      'html_attributes' => array('autofocus'=>true),
    ),
    'password' => array(
      'type' => 'password',
      'name' => 'Password',
    ),
    'domain' => array(
      'type' => 'select',
      'name' => 'Domain',
      'values' => array_keys($auth->domains()),
      'default' => $default_domain,
    ),
  );
  $form_auth = new form('auth', $form_auth_def);

  if($form_auth->is_complete()) {
    $data = $form_auth->get_data();
    
    if($auth->authenticate($data['username'], $data['password'])) {
      if(isset($_REQUEST['return_to'])) {
        page_reload($_REQUEST['return_to']);
        print "You have been logged in.";
        exit(0);
      }

      if(isset($_SERVER['HTTP_REFERER'])) {
        page_reload($_SERVER['HTTP_REFERER']);
        print "You have been logged in.";
        exit(0);
      }
    }
    else {
      $error = "Username or password wrong!";
      $form_auth->set_data(array("password" => null));
    }
  }
  ?>
  <!DOCTYPE html>
  <html>
  <head>
      <?php print modulekit_to_javascript(); /* pass modulekit configuration to JavaScript */ ?>
      <?php print modulekit_include_js(); /* prints all js-includes */ ?>
      <?php print modulekit_include_css(); /* prints all css-includes */ ?>
      <?php print_add_html_headers(); /* print additional html headers */ ?>
  </head>
  <body>
  <?php
  if(!$auth->is_logged_in()) {
    print $error;
    print "<form method='post'>\n";
    print $form_auth->show();

    if(isset($_REQUEST['return_to'])) {
      print "<input type='hidden' name='return_to' value='" . htmlspecialchars($_REQUEST['return_to']) . "' />\n";
    }
    elseif(isset($_SERVER['HTTP_REFERER'])) {
      print "<input type='hidden' name='return_to' value='" . htmlspecialchars($_SERVER['HTTP_REFERER']) . "' />\n";
    }

    print "<input type='submit' value='Login' />\n";
    print "</form>\n";
  }
  ?>
  </body>
  </html>
<?php
}
