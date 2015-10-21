function auth_user_menu_toggle(ev) {
  if(!ev)
    ev = window.event;

  var parent_div = ev.target.parentNode.parentNode;

  var menu = parent_div.getElementsByClassName("menu");
  for(var i = 0; i < menu.length; i++)
    if(menu[i].style.display == "block")
      menu[i].style.display = "none";
    else
      menu[i].style.display = "block";

  return false;
}

function auth_user_menu_login() {
  var form_def = {
    'username': {
      'name': 'Username',
      'type': 'text',
      'html_attributes': { 'autofocus': true }
    },
    'password': {
      'name': 'Password',
      'type': 'password'
    }
  };

  var auth_form = new form('auth_form', form_def);

  var div = document.createElement('form');
  div.method = 'post';
  if(modulekit_loaded("auth_form"))
    div.action = page_url({ page: 'login' });
  document.body.appendChild(div);

  if(modulekit_loaded("modulekit-auth-js") && modulekit_loaded("modulekit-ajax"))
    div.onsubmit = auth_user_menu_login_submit.bind(this, auth_form);

  auth_form.show(div);

  var input = document.createElement('input');
  input.type = 'submit';
  input.value = "Login";
  div.appendChild(input);

  return false;
}

function auth_user_menu_login_submit(auth_form) {
  var data = auth_form.get_data();

  auth.authenticate(data.username, data.password, data.domain, null,
    function(result) {
      if(result === true)
	location.reload();
      else
	alert(result);
    }
  );

  return false;
}
