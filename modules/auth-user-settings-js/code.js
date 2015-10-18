function AuthUserSettings(user, config, data) {
  this.user = user;
  this.config = config;
  this._data = data;
}

AuthUserSettings.prototype.load = function(callback) {
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

  // call ajax to update server values
}
