<?
$id="modulekit-auth";

$depend=array();

$include=array(
  'php'=>array(
    "inc/*.php",
  ),
);
$default_include=array(
  'php'=>array(
    "*.php",
  ),
);

if(!function_exists("modulekit_auth_check_modules")) {
  function modulekit_auth_check_modules() {
    global $auth_config;

    if(isset($auth_config)&&
       is_array($auth_config)&&
       isset($auth_config['domains'])&&
       is_array($auth_config['domains'])) {

      $load=array();

      foreach($auth_config['domains'] as $c) {
        if(isset($c['type'])) {
          $module_name="modulekit-auth-{$c['type']}";

          if(!in_array($module_name, $load))
            $load[]=$module_name;
        }
      }
    }

    return $load;
  }
}
$load=modulekit_auth_check_modules();
