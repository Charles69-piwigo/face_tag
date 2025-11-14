<?php
/*
Plugin Name: face_tag
Version: 1.0
Description: afficher les visages tagués avec digiKam
Plugin URI:
Author: Charles69
Author URI:
Has Settings: webmaster
*/

// ============  VERSIONS =========================================================
 // historique des versions
 /*
 
 version 1.0 - 14/11/2025
  
*/
//=================================================================================

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

if (basename(dirname(__FILE__)) != 'face_tag')
{
  add_event_handler('init', 'face_tag_error');
  function face_tag_error()
  {
    global $page;
    $page['errors'][] = 'uninstall the plugin and rename it to "face_tag"';
  }
  return;
}

// Plugin constants
global $prefixeTable;
define('FACETAG_ID', basename(dirname(__FILE__)));    // nom du répertoire du plugin
define('FACETAG_PATH', PHPWG_PLUGINS_PATH . FACETAG_ID . '/'); // ./plugins/face_tag/
define('FACETAG_TABLE', $prefixeTable . 'facetag'); // piwigo_facetag
define('FACETAG_ADMIN', get_root_url() . 'admin.php?page=plugin-' . FACETAG_ID); // admin.php?page=plugin-face_tag
define('FACETAG_PUBLIC', get_absolute_root_url() . make_index_url(array('section' => 'face_tag')) . '/');
    // https://nom_nas.synology.me/photodev/index.php?/face_tag/
define('FACETAG_DIR', PHPWG_ROOT_PATH . PWG_LOCAL_DIR . 'face_tag/'); // ./local/face_tag/


// SCRIPT =======================================================================================

add_event_handler('loc_begin_page_tail', 'facetag_add_extension');
function facetag_add_extension()
{
    global $page, $template;
    
    $template->append('footer_elements', '
    <script src="plugins/face_tag/facetag.js"></script>
    <link rel="stylesheet" href="plugins/face_tag/template/facetag.css">
    ');
    
}

// fin SCRIPT ==================================================================================


// initialisation du plugin ======================================================
add_event_handler('init', 'facetag_init');



// Bouton dans la barre de navigation de ThePicturePage  =========================

// Ajouter le bouton dans la barre de navigation de la page photo
add_event_handler('loc_end_picture', 'facetag_picture_toolbar');

function facetag_picture_toolbar()
{
  global $template, $user, $picture;
  
  // Charger la configuration avec gestion d'erreur
  $config_raw = conf_get_param('face_tag_config', false);
  
  if ($config_raw === false || $config_raw === null) {
    // Pas de config : valeurs par défaut
    $config = array(
      'show_xmp_button' => true,
      'show_faces_button' => true,
    );
  } else {
    $config = @unserialize($config_raw);
    
    // Si unserialize échoue
    if (!is_array($config)) {
      $config = array(
        'show_xmp_button' => true,
        'show_faces_button' => true,
      );
    }
  }
  
  // S'assurer que les clés existent
  if (!isset($config['show_xmp_button'])) $config['show_xmp_button'] = true;
  if (!isset($config['show_faces_button'])) $config['show_faces_button'] = true;

  // Ne rien afficher si les deux boutons sont désactivés
  if (!$config['show_xmp_button'] && !$config['show_faces_button']) {
    return;
  }
  
  $template->set_template_dir(FACETAG_PATH.'template/');
  
  // Construire l'URL du bouton (AJAX)
  $ajax_url = get_root_url() . 'ws.php?format=json&method=facetag.getXMP&image_id=' . $picture['current']['id'];
  $icon = get_root_url() . 'plugins/' . FACETAG_ID . '/images/face.png';
  
  // Assigner les variables au template - FORCER les valeurs
  $template->assign(array(
    'FACETAG_AJAX_URL' => $ajax_url,
    'FACETAG_ICON' => $icon,
    'FACETAG_PATH' => FACETAG_PATH,
    'FACETAG_SHOW_XMP' => (bool)$config['show_xmp_button'],
    'FACETAG_SHOW_FACES' => (bool)$config['show_faces_button'],
    'FACETAG_DISPLAY_MODE' => isset($config['faces_display_mode']) ? $config['faces_display_mode'] : 'toggle', // NOUVEAU

// NOUVEAU : Paramètres visuels
  'FACETAG_RECT_COLOR' => isset($config['rect_color']) ? $config['rect_color'] : '#00ff00',
  'FACETAG_RECT_WIDTH' => isset($config['rect_width']) ? $config['rect_width'] : 3,
  'FACETAG_RECT_OPACITY' => isset($config['rect_opacity']) ? $config['rect_opacity'] : 0.8,
  'FACETAG_LABEL_TEXT_COLOR' => isset($config['label_text_color']) ? $config['label_text_color'] : '#ffffff',
  'FACETAG_LABEL_FONT_SIZE' => isset($config['label_font_size']) ? $config['label_font_size'] : 12,
  'FACETAG_LABEL_BG_COLOR' => isset($config['label_bg_color']) ? $config['label_bg_color'] : '#000000',
  'FACETAG_LABEL_BG_OPACITY' => isset($config['label_bg_opacity']) ? $config['label_bg_opacity'] : 0.8,


  ));
  
  // Choix du template selon le thème
  if ($user['theme'] == 'bootstrapdefault' || $user['theme'] == 'bootstrap_darkroom') {
    $tpl_file = 'picture_facetag_Bootstrap.tpl';
  } else {
    $tpl_file = 'picture_facetag.tpl';
  }
  
  // Assigner le template au bloc d'actions
  $template->set_filename('facetag_button', $tpl_file);
  $template->concat('PLUGIN_PICTURE_ACTIONS', $template->parse('facetag_button', true));
}

// Récuperation des XMP  du jpg ==============================================================================

// Web Service pour récupérer les XMP
add_event_handler('ws_add_methods', 'facetag_ws_add_methods');

function facetag_ws_add_methods($arr)
{
  $service = &$arr[0];
  
  $service->addMethod(
    'facetag.getXMP',
    'facetag_ws_get_xmp',
    array(
      'image_id' => array('default' => null),
    ),
    'Récupère les données XMP d\'une image',
    null,
    array('admin_status' => ACCESS_GUEST)
  );
}

function facetag_ws_get_xmp($params, &$service)
{
  if (empty($params['image_id']))
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'Missing image_id');
  }
  
  // Récupérer le chemin de l'image
  $query = '
SELECT path
FROM ' . IMAGES_TABLE . '
WHERE id = ' . intval($params['image_id']);
  
  $result = pwg_query($query);
  $row = pwg_db_fetch_assoc($result);
  
  if (!$row)
  {
    return new PwgError(404, 'Image not found');
  }
  
  $image_path = PHPWG_ROOT_PATH . $row['path'];
  
  if (!file_exists($image_path))
  {
    return new PwgError(404, 'Image file not found');
  }
  
  // Extraire les XMP avec Imagick
  $xmp_data = facetag_extract_xmp($image_path);
  
  return array(
    'xmp' => $xmp_data,
    'image_id' => $params['image_id']
  );
}

//=====================================================================================

// Fonction d'extraction des XMP avec Imagick
function facetag_extract_xmp($image_path)
{
  if (!extension_loaded('imagick'))
  {
    return array('error' => 'Imagick extension not loaded');
  }
  
  try
  {
    $imagick = new Imagick($image_path);
    
    // Récupérer toutes les propriétés
    $properties = $imagick->getImageProperties();
    
    $xmp_data = array();
    
    // Extraire les données XMP
    foreach ($properties as $key => $value)
    {
      // On récupère toutes les propriétés qui commencent par xmp, exif, etc.
      if (strpos($key, 'xmp:') === 0 || 
        //  strpos($key, 'exif:') === 0 || 
          strpos($key, 'dc:') === 0 ||
          strpos($key, 'Iptc4xmpCore:') === 0 ||
          strpos($key, 'mwg-rs:') === 0 ||
          strpos($key, 'MP:') === 0)
      {
        $xmp_data[$key] = $value;
      }
    }
    
    // Récupérer aussi le profil XMP brut si disponible
    $xmp_profile = $imagick->getImageProfile('xmp');
    if ($xmp_profile)
    {
      $xmp_data['_raw_xmp'] = $xmp_profile;
    }
    
    $imagick->clear();
    $imagick->destroy();
    
    return $xmp_data;
  }
  catch (Exception $e)
  {
    return array('error' => $e->getMessage());
  }
}


//================================================================================
// fonction d'initialisation du plugin
function facetag_init()
{
  // language
load_language('plugin.lang', FACETAG_PATH);

}

?>