<?php
defined('FACETAG_PATH') or die('Hacking attempt!');

// Inclure les fonctions communes avec chemin relatif
//include_once(dirname(__FILE__) . '/functions.inc.php');

// Traitement du formulaire AVANT tout affichage
if (isset($_POST['submit']))
{
  $config = array(
    'numeric_value' => intval($_POST['numeric_value']),
    'radio_option' => $_POST['radio_option'],
    'checkbox_cas1' => isset($_POST['checkbox_cas1']),
    'checkbox_cas2' => isset($_POST['checkbox_cas2']),
    'checkbox_cas3' => isset($_POST['checkbox_cas3']),
    'select_option' => $_POST['select_option'],
    'slider_value' => intval($_POST['slider_value'])
  );
  
  conf_update_param('face_tag_advanced', serialize($config));
  redirect(FACETAG_ADMIN . '&tab=config2&saved=1');
}

// Message de confirmation si redirection
if (isset($_GET['saved']))
{
  $page['infos'][] = 'Configuration enregistrée avec succès';
}

// Afficher le tabsheet
facetag_admin_tabsheet('config2');

// Charger les paramètres existants
$config = unserialize(conf_get_param('face_tag_advanced', serialize(array(
  'numeric_value' => 0,
  'radio_option' => 'option1',
  'checkbox_cas1' => false,
  'checkbox_cas2' => false,
  'checkbox_cas3' => false,
  'select_option' => 'A1',
  'slider_value' => 50
))));

// Assigner au template
$template->assign(array(
  'NUMERIC_VALUE' => $config['numeric_value'],
  'RADIO_OPTION' => $config['radio_option'],
  'CHECKBOX_CAS1' => $config['checkbox_cas1'],
  'CHECKBOX_CAS2' => $config['checkbox_cas2'],
  'CHECKBOX_CAS3' => $config['checkbox_cas3'],
  'SELECT_OPTION' => $config['select_option'],
  'SLIDER_VALUE' => $config['slider_value']
));

$template->set_filename('face_tag_config', dirname(__FILE__).'/../template/config2.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'face_tag_config');
?>