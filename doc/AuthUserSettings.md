AuthUserSettings
================
Your project needs an additional dependency, 'modulekit-auth-user-settings'.

Init
====
```php
$auth = new Auth();
$current_user = $auth->current_user();
$current_user_settings = new AuthUserSettings($current_user, $auth_user_settings_config);
```

Configuration
=============
`$auth_user_setting_config` is a hash which configures where the user settings will be saved.

Option     | Description | Default value
-----------|-------------|-----------------
type       | type of storage, currently supported: `file` | `file`
default_settings    | Default user settings for new users | `{}`
anonymous_settings  | Default user settings for the anonymous user | defaults to default_settings

Type `file`
-----------
Option     | Description | Default value
-----------|-------------|-----------------
path       | Path in which files will be stored |

Methods
=======
__construct($user, $auth_user_settings_config)
---------------------------------------------

load()
------
(Re-)loads user settings.

data()
------
Return a hash of all user settings for the current user.

data($k)
--------
Return a specific setting for the current user.

save($data)
-----------
Overwrite the given settings by the new values. Settings which are not present in $data will not be modified. Values which are `null` will be removed from the current settings.

Returns true on success or a string with an error message.

The javascript variant of save() accept an additional parameter $callback which will be called after successful saving. It will be passed a single parameter, the return value.
