<?php
/*
Plugin Name: PWA Backend
Version: 0.0.1
Description: Support plugin for PWA frontend
Plugin URI: https://github.com/mistic100/piwigo-pwa
Author: Mistic
Author URI: https://strangeplanet.fr
Has Settings: true
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

define('PWA_BACKEND_ID', 'pwa_backend');


if (basename(dirname(__FILE__)) != PWA_BACKEND_ID) {
  add_event_handler('init', 'pwa_backend_error');
  function pwa_backend_error()
  {
    global $page;
    $page['errors'][] = 'PWA Backend folder name is incorrect, uninstall the plugin and rename it to "' . PWA_BACKEND_ID . '"';
  }
  return;
}


define('PWA_BACKEND_PATH', PHPWG_PLUGINS_PATH . PWA_BACKEND_ID . '/');
define('PWA_BACKEND_ADMIN', get_root_url() . 'admin.php?page=plugin-' . PWA_BACKEND_ID);


add_event_handler('init', 'pwa_backend_init');
function pwa_backend_init()
{
  global $conf;

  $conf[PWA_BACKEND_ID] = safe_unserialize($conf[PWA_BACKEND_ID]);
}


if (defined('IN_WS')) {
  add_event_handler('ws_add_methods', 'pwa_backend_ws_add_methods', EVENT_HANDLER_PRIORITY_NEUTRAL+1, PWA_BACKEND_PATH . 'include/functions.ws.php');
  add_event_handler('ws_result', 'pwa_backend_ws_patch_methods', EVENT_HANDLER_PRIORITY_NEUTRAL, PWA_BACKEND_PATH . 'include/functions.ws.php');
}


if (defined('IN_ADMIN')) {
  add_event_handler('init', 'pwa_backend_check_ws_core');
  function pwa_backend_check_ws_core()
  {
    global $page, $conf;

    $ws_core_file = PHPWG_ROOT_PATH . 'include/ws_core.inc.php';
    $content = file_get_contents($ws_core_file);
    if (strpos($content, "trigger_change('ws_result'") === false) {
      $page['errors'][] = 'PWA Backend plugin requires to manually patch the include/ws_core.inc.php file';
    }

    if ($conf['picture_url_style'] === 'file') {
      $page['errors'][] = 'PWA Backend plugin is not compatible with "picture_url_style"="file"';
    }

    if ($conf['tag_url_style'] === 'tag') {
      $page['errors'][] = 'PWA Backend plugin is not compatible with "tag_url_style"="tag"';
    }
  }
}
