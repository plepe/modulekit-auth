function auth_ajax_form() {
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
    div.onsubmit = auth_ajax_form_submit.bind(this, auth_form);

  auth_form.show(div);

  var input = document.createElement('input');
  input.type = 'submit';
  input.value = "Login";
  div.appendChild(input);
}

function auth_ajax_form_submit(auth_form) {
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
