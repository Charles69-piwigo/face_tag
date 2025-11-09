<?php
defined('FACETAG_PATH') or die('Hacking attempt!');

// Inclure les fonctions communes avec chemin relatif
//include_once(dirname(__FILE__) . '/functions.inc.php');


// Afficher le tabsheet
facetag_admin_tabsheet('help');

// Charger le template
$template->set_filename('face_tag_help', dirname(__FILE__).'/../template/help.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'face_tag_help');
?>