Global variables
================
The following global variables will be declared by these module(s):

Variable name | Class      | Description | Modules
--------------|------------|-------------|---------
$auth         | Auth       | Contains access to the current active authentication. There are function to get/set the currently logged on user, clear authentication, user list, e.t.c. | modulekit-auth, modulekit-auth-js

The JavaScript pendants of the variables will only be available when the modules 'modulekit-auth-js' resp. 'modulekit-auth-user-settings-js' are loaded.

Hooks
=====
Hook | Parameters | Description
-----|------------|--------------
auth_current_user | $user | Called every time an active user gets loaded, either due to logon or next request during session.

