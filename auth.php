<?php include "conf.php"; /* load a local configuration */ ?>
<?php
$modulekit_load[] = "page";
$modulekit_load[] = "modulekit-form";
?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?php call_hooks('init'); ?>
<?php session_start(); ?>
<?php $auth = new Auth(); ?>
<?php _auth_process($auth); ?>
