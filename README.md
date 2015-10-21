modulekit-auth
--------------
Authentication module (PHP only)

modulekit-auth-js
-----------------
Authentication frontend (JS scope)

modulekit-auth-user-settings
----------------------------
User Settings (PHP only)

modulekit-auth-user-settings-js
-------------------------------
User Settings (PHP/JS)

modulekit-auth-user-settings-page
---------------------------------
A page for modifying user settings. Will call hook 'auth_user_settings_form' for loading values.

Example:
```php
register_hook("auth_user_settings_form", function(&$form_def) {
  $form_def['foo'] = array(
    'type'=>'text',
    'name'=>'Foo'
  );
});
```

auth-pages
----------
Pages for login / logout

auth-user-menu
--------------
Show login/user name on right upper corner + login/logout + more menu entries
