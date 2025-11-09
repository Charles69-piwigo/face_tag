<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

// Pas besoin de traitement de formulaire
$template->set_filename('plugin_help', dirname(__FILE__).'/template/help.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_help');
?>