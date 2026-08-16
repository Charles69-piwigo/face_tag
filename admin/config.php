<?php
defined('FACETAG_PATH') or die('Hacking attempt!');

// Afficher le tabsheet
facetag_admin_tabsheet('config');

// Charger la configuration existante
$config = facetag_load_config();

///    Sauvegarde de la configuration SUBMIT =========================
if (isset($_POST['submit']))   // quand on clique sur Enregistrer la configuration
{
  $config['show_xmp_button'] = isset($_POST['show_xmp_button']);
  $config['show_faces_button'] = isset($_POST['show_faces_button']);
  $config['faces_display_mode'] = $_POST['faces_display_mode'];
  $config['rect_color'] = $_POST['rect_color'];
  $config['rect_width'] = intval($_POST['rect_width']);
  $config['rect_opacity'] = floatval($_POST['rect_opacity']);
  $config['label_text_color'] = $_POST['label_text_color'];
  $config['label_font_size'] = intval($_POST['label_font_size']);
  $config['label_bg_color'] = $_POST['label_bg_color'];
  $config['label_bg_opacity'] = floatval($_POST['label_bg_opacity']);
  $config['icon_active_color'] = $_POST['icon_active_color'];
  $config['icon_tagged_color'] = $_POST['icon_tagged_color'];
  facetag_save_config($config);
}

//  Sauvegarde des valeurs par defaut INIT   ====================================

if (isset($_POST['reinit']))   // quand on clique sur Valeurs par défaut
{
  include(FACETAG_PATH.'install/config_defaut.php');

  // Ne réinitialise que les réglages de cet onglet (pas ceux de "Affichage des tags")
  foreach ($config_defaut as $key => $value)
  {
    if ($key !== 'hide_face_tags_cloud')
    {
      $config[$key] = $value;
    }
  }
  facetag_save_config($config);
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
  'ICON_ACTIVE_COLOR' => $config['icon_active_color'],
  'ICON_TAGGED_COLOR' => $config['icon_tagged_color'],
));

// Charger le template
$template->set_filename('face_tag_config', FACETAG_PATH . 'template/config.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'face_tag_config');

//---------------------------------------------------------------------
?>
