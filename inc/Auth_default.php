<?php
class Auth_default {
  function __construct($config) {
    $this->config = $config;
  }

  function users() {
    return array();
  }
}
