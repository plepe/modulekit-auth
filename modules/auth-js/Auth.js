function Auth(config, user) {
  this.config = config;
  this._current_user = user;
}

Auth.prototype.current_user = function() {
  return this._current_user;
}

Auth.prototype.is_logged_in = function() {
  return this._current_user.is_logged_in();
}

Auth.prototype.authenticate = function(username, password, domain, options, callback) {
  ajax('auth_authenticate',
    {
      username: username,
      domain: domain,
    },
    password,
    function(callback, result) {
      // TODO: update current user
      callback(result);
    }.bind(this, callback)
  );
}
