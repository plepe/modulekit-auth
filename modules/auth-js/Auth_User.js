function Auth_User(username, domain, data) {
  this.username = username;
  this.domain = domain;
  this._data = data;
}

Auth_User.prototype.id = function() {
  if(this.username === null)
    return '!';

  return this.username + "@" + this.domain;
}

Auth_User.prototype.data = function(k) {
  if((k === null) || (k === undefined))
    return this._data;

  return this._data[k];
}

Auth_User.prototype.name = function() {
  if(this.username === null)
    return 'Anonymous';

  if(this._data.name)
    return this._data.name;

  return this.username;
}

Auth_User.prototype.email = function() {
  if(this._data.email)
    return this._data.email;

  return null;
}

Auth_User.prototype.settings = function() {
  if(!this._settings &&
     modulekit_loaded("modulekit-auth-user-settings-js")) {
    var data = null;
    if(this == auth.current_user())
      data = _auth_user_settings_data;

    this._settings = new AuthUserSettings(this, null, data);
  }

  return this._settings;
}
