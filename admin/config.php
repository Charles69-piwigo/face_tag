<?php
defined('FACETAG_PATH') or die('Hacking attempt!');

// Traitement du formulaire AVANT tout affichage
if (isset($_POST['submit']))
{
  $config = array(
    'show_xmp_button' => isset($_POST['show_xmp_button']),
    'show_faces_button' => isset($_POST['show_faces_button']),
    'faces_display_mode' => $_POST['faces_display_mode'], // NOUVEAU

    // NOUVEAU : Paramètres du rectangle
    'rect_color' => $_POST['rect_color'],
    'rect_width' => intval($_POST['rect_width']),
    'rect_opacity' => floatval($_POST['rect_opacity']),
    // NOUVEAU : Paramètres du libellé
    'label_text_color' => $_POST['label_text_color'],
    'label_font_size' => intval($_POST['label_font_size']),
    'label_bg_color' => $_POST['label_bg_color'],
    'label_bg_opacity' => floatval($_POST['label_bg_opacity']),

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

// Valeurs par défaut
$default_config = array(
  'show_xmp_button' => false,
  'show_faces_button' => true,
  'faces_display_mode' => 'toggle', // NOUVEAU : 'toggle' ou 'persistent'

  // NOUVEAU : Paramètres du rectangle
  'rect_color' => '#00ff00',
  'rect_width' => 3,
  'rect_opacity' => 0.8,
  // NOUVEAU : Paramètres du libellé
  'label_text_color' => '#ffffff',
  'label_font_size' => 12,
  'label_bg_color' => '#000000',
  'label_bg_opacity' => 0.8,


);

// Charger les paramètres existants
$config_string = conf_get_param('face_tag_config', false);

if ($config_string === false) {
  // Première utilisation : créer la config par défaut
  $config = $default_config;
} else {
  // Charger la config existante
  $config = @unserialize($config_string);
  
  // Si unserialize échoue ou retourne null, utiliser les valeurs par défaut
  if (!is_array($config)) {
    $config = $default_config;
  } else {
    // S'assurer que toutes les clés existent
    $config = array_merge($default_config, $config);
  }
}

// Assigner au template
$template->assign(array(
  'SHOW_XMP_BUTTON' => $config['show_xmp_button'],
  'SHOW_FACES_BUTTON' => $config['show_faces_button'],
  'FACES_DISPLAY_MODE' => $config['faces_display_mode'], // NOUVEAU

// NOUVEAU
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
?>