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
