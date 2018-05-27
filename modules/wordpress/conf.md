<?php
```php
$auth_config=array(
  'domains'=>array(
    'wp' => array(
      'type' => 'wordpress',
      'path' => '/var/www/wordpress', // for including files
      'dsn' => 'mysql:dbname=testdb;host=127.0.0.1',
      'username' => 'username',
      'password' => 'password',
      'prefix' => 'wp_',
    ),
  ),
);
```
