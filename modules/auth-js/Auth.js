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
