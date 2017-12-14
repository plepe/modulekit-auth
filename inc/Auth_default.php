<?php
class Auth_default {
  public $usesUsernamePassword = true;

  function __construct($id, $config) {
    $this->id = $id;
    $this->config = $config;
  }

  /**
   * Tries to authenticate with username/password to the domain
   * @param string Username
   * @param string Password
   * @param mixed[] Reserved for future use
   * @return false|string|Auth_User false: not found, string: an error occured
   *   - Error message, Auth_User: user object of successful authentication
   */
  function authenticate($username, $password, $options=array()) {
    return false;
  }

  function name () {
    return isset($this->config['name']) ? $this->config['name'] : $this->id;
  }

  /**
   * Return user object for the given username
   * @param string Username
   * @return null|Auth_User null: not found, Auth_User: user object
   */
  function get_user($username) {
    return null;
  }

  /**
   * Return group members for the given group name
   * @param string Group name
   * @return string[] List of user names
   */
  function group_members($group) {
    return array();
  }

  /**
   * Return all users in the given domain
   * @return string[] List of user names
   */
  function users() {
    return array();
  }
}
