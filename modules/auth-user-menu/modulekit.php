<?php
$id = "modulekit-auth-user-menu";

$depend = array(
  'weight_sort',
  'hooks',
  'auth-pages',
  'modulekit-auth',
);

$include = array(
  'php' => array(
    'code.php',
  ),
  'js' => array(
    'code.js',
  ),
  'css' => array(
    'style.css',
  ),
);
