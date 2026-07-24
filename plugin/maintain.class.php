<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

define('PWA_BACKEND_ID', 'pwa_backend');

class pwa_backend_maintain extends PluginMaintain
{
  private $default_conf = array(
    'frontend_url' => null,
    'add_cors_headers' => true,
  );

  function install($plugin_version, &$errors = array())
  {
    global $conf;

    if (empty($conf[PWA_BACKEND_ID])) {
      conf_update_param(PWA_BACKEND_ID, $this->default_conf, true);
    }
  }

  function update($old_version, $new_version, &$errors = array())
  {
    $this->install($new_version, $errors);
  }

  function uninstall()
  {
    conf_delete_param(PWA_BACKEND_ID);
  }
}
