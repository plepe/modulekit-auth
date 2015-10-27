function Auth(config, user) {
  this.config = config;
  this._current_user = user;
}

Auth.prototype.current_user = function() {
  return this._current_user;
}

Auth.prototype.is_logged_in = function() {
  return this._current_user.id() != '';
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
      if(callback)
	callback(result);
    }.bind(this, callback)
  );
}

Auth.prototype.clear_authentication = function(callback) {
  ajax('auth_clear_authentication',
    {},
    null,
    function(callback, result) {
      if(callback)
	callback(result);
    }.bind(this, callback)
  );
}

Auth.prototype.domains = function() {
  return this.config.domains;
}
