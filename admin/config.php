<?php
defined('FACETAG_PATH') or die('Hacking attempt!');

// Afficher le tabsheet
facetag_admin_tabsheet('config');

 // Charger les valeurs par défaut
include(FACETAG_PATH.'install/config_defaut.php');
  


// Charger les paramètres existants

$config_string = conf_get_param('face_tag_config', false);

if ($config_string === false) {
  // Première utilisation : créer la config par défaut
  $config = $config_defaut;
  conf_update_param('face_tag_config', serialize($config));
} else {
  // Charger la config existante
  $config = @unserialize($config_string);
  
  // Si unserialize échoue ou retourne null, utiliser les valeurs par défaut
  if (!is_array($config)) {
    $config = $config_defaut;
  } else {
    // S'assurer que toutes les clés existent
    $config = array_merge($config_defaut, $config);
  }
}


// Fonction de sauvegarde 
function save_config($params) {
    global $page;
    conf_update_param('face_tag_config', serialize($params));
    $sauve = l10n('Configuration enregistrée avec succès');
    array_push($page['infos'], $sauve);
}


///    Sauvegarde de la configurtion SUBMIT =========================
if (isset($_POST['submit']))   // quand on clique sur Enregistrer la configuration
{
  $config = array(
    'show_xmp_button' => isset($_POST['show_xmp_button']),
    'show_faces_button' => isset($_POST['show_faces_button']),
    'faces_display_mode' => $_POST['faces_display_mode'],
    'rect_color' => $_POST['rect_color'],
    'rect_width' => intval($_POST['rect_width']),
    'rect_opacity' => floatval($_POST['rect_opacity']),
    'label_text_color' => $_POST['label_text_color'],
    'label_font_size' => intval($_POST['label_font_size']),
    'label_bg_color' => $_POST['label_bg_color'],
    'label_bg_opacity' => floatval($_POST['label_bg_opacity']),

  );
  save_config($config) ;
}

//  Sauvegarde des valeurs par defaut INIT   ====================================

if (isset($_POST['reinit']))   // quand on clique sur Valeurs par défaut
{
$config =$config_defaut ;  
save_config($config)  ;
}

//======================================================================

// Assigner au template
$template->assign(array(
  'SHOW_XMP_BUTTON' => $config['show_xmp_button'],
  'SHOW_FACES_BUTTON' => $config['show_faces_button'],
  'FACES_DISPLAY_MODE' => $config['faces_display_mode'],
  'RECT_COLOR' => $config['rect_color'],
  'RECT_WIDTH' => $config['rect_width'],
  'RECT_OPACITY' => $config['rect_opacity'],
  'LABEL_TEXT_COLOR' => $config['label_text_color'],
  'LABEL_FONT_SIZE' => $config['label_font_size'],
  'LABEL_BG_COLOR' => $config['label_bg_color'],
  'LABEL_BG_OPACITY' => $config['label_bg_opacity'],
));

// Charger le template
$template->set_filename('face_tag_config', dirname(__FILE__).'/../template/config.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'face_tag_config');

//---------------------------------------------------------------------
?>