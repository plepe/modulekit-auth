function Auth_User(username, domain, data) {
  this.username = username;
  this.domain = domain;
  this.data = data;
}

Auth_User.prototype.id = function() {
  if(this.username === null)
    return '';

  return this.username + "@" + this.domain;
}

Auth_User.prototype.name = function() {
  if(this.username === null)
    return 'Anonymous';

  if(this.data.name)
    return this.data.name;

  return this.username;
}

Auth_User.prototype.email = function() {
  if(this.data.email)
    return this.data.email;

  return null;
}

Auth_User.prototype.settings = function() {
  if(!this._settings &&
     modulekit_loaded("modulekit-auth-user-settings-js"))
    this._settings = new AuthUserSettings(this, null, _auth_user_settings_data);

  return this._settings;
}
