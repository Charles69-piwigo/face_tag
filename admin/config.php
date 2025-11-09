<?php
defined('FACETAG_PATH') or die('Hacking attempt!');

// Inclure les fonctions communes avec chemin relatif
//include_once(dirname(__FILE__) . '/functions.inc.php');

// Traitement du formulaire AVANT tout affichage
if (isset($_POST['submit']))
{
  $config = array(
    'param1' => $_POST['param1'],
    'param2' => $_POST['param2'],
  );
  
  conf_update_param('face_tag_config', serialize($config));
  redirect(FACETAG_ADMIN . '&tab=config&saved=1');
}

// Message de confirmation si redirection
if (isset($_GET['saved']))
{
  $page['infos'][] = 'Configuration enregistrée avec succès';
}

// Afficher le tabsheet
facetag_admin_tabsheet('config');

// Charger les paramètres existants
$config = unserialize(conf_get_param('face_tag_config', serialize(array(
  'param1' => '',
  'param2' => '',
))));

// S'assurer que toutes les clés existent (pour éviter les warnings)
$config = array_merge(array(
  'param1' => '',
  'param2' => '',
), $config);

// Assigner au template
$template->assign(array(
  'PARAM1' => $config['param1'],
  'PARAM2' => $config['param2'],
));

// Charger le template
$template->set_filename('face_tag_config', dirname(__FILE__).'/../template/config.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'face_tag_config');
?>