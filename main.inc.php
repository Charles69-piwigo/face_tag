<?php
/*
Plugin Name: face_tag
Version: 1.0
Description: afficher les visages tagués avec Digikam
Plugin URI:
Author: Charles69
Author URI:
Has Settings: webmaster
*/

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

//echo FACETAG_DIR;



// initialisation du plugin ======================================================
add_event_handler('init', 'facetag_init');

// Menu d'administration =========================================================

if (defined('IN_ADMIN'))
{
  add_event_handler('get_admin_plugin_menu_links', 'face_tag_admin_menu');
}

function face_tag_admin_menu($menu)
{
  $menu[] = array(
    'NAME' => 'Configuration générale',
    'URL' => FACETAG_ADMIN . '&tab=config'
  );
  
  $menu[] = array(
    'NAME' => 'Paramètres avancés',
    'URL' => FACETAG_ADMIN . '&tab=config2'
  );
  
    $menu[] = array(
    'NAME' => 'Aide',
    'URL' => FACETAG_ADMIN . '&tab=help'
  );

  return $menu;
}


// Menu utilisation ================================================================

// Menu utilisation - Bouton dans la barre de navigation ================================================================

// Ajouter le bouton dans la barre de navigation de la page photo
add_event_handler('loc_end_picture', 'facetag_picture_toolbar');

function facetag_picture_toolbar()
{
  global $template, $user, $picture;
  
  $template->set_template_dir(FACETAG_PATH.'template/');
  
  // Construire l'URL du bouton
  $url = get_root_url() . 'picture.php?/' . $picture['current']['id'] . '/face_tag';
  $icon = get_root_url() . 'plugins/' . FACETAG_ID . '/images/face.png';
  
  // Assigner les variables au template
  $template->assign(array(
    'FACETAG_URL' => $url,
    'FACETAG_ICON' => $icon,
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


//================================================================================
// fonction d'initialisation du plugin
function facetag_init()
{
  // language
load_language('plugin.lang', FACETAG_PATH);

}

?>