function AuthUserSettings(user, config, data) {
  this.user = user;
  this.config = config;
  this._data = data;

  if(this._data === null) {
    this._data = {};
    this.load();
  }
}

AuthUserSettings.prototype.load = function(callback) {
  if(!modulekit_loaded("modulekit-ajax")) {
    if(callback)
      callback("'modulekit-ajax' not loaded - can't load from server");
    return;
  }

  ajax('auth_user_settings_js_load', {
    user: this.user.id()
  }, null, function(callback, result) {
    this._data = result;

    if(callback)
      callback(true);
  }.bind(this, callback));
}

AuthUserSettings.prototype.data = function(k) {
  if((k === null) || (k === undefined))
    return this._data;

  return this._data[k];
}

AuthUserSettings.prototype.save = function(data, callback) {
  // save temporarily into structure
  for(var k in data) {
    if(data[k] === null)
      delete(this._data[k]);
    else
      this._data[k] = data[k];
  }

  if(!modulekit_loaded("modulekit-ajax")) {
    if(callback)
      callback("'modulekit-ajax' not loaded - can't save to server");
    return;
  }

  // call ajax to update server values
  ajax('auth_user_settings_js_save', {
    user: this.user.id()
  }, JSON.stringify(data), function(callback, result) {
    this.load(callback);
  }.bind(this, callback));
}
