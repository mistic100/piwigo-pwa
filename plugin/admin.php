<?php
defined('PWA_BACKEND_PATH') or die('Hacking attempt!');

global $template, $page, $conf;

if (isset($_POST['submit'])) {
  $valid = true;
  if (!filter_var($_POST['frontend_url'], FILTER_VALIDATE_URL)) {
    $valid = false;
    $page['errors'][] = 'Frontend URL must be an absolute URL';
  }

  $conf[PWA_BACKEND_ID] = array(
    'frontend_url' => $_POST['frontend_url'],
  );

  if ($valid) {
    conf_update_param(PWA_BACKEND_ID, $conf[PWA_BACKEND_ID]);
    $page['infos'][] = l10n('Information data registered in database');
  }
}

$template->assign(array(
  'PWA_BACKEND_ADMIN' => PWA_BACKEND_ADMIN,
  'config' => $conf[PWA_BACKEND_ID],
));

$template->set_filename('pwa_backend_config', realpath(PWA_BACKEND_PATH . 'template/admin.html'));

$template->assign_var_from_handle('ADMIN_CONTENT', 'pwa_backend_config');
