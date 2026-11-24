<?php
/*
Plugin Name: face_tag
Version: auto
Description: afficher les visages tagués avec face_tag_editor et autres
Plugin URI: https://piwigo.org/ext/extension_view.php?eid=1051
Author: Charles69
Author URI:
Has Settings: webmaster
*/

// ============  VERSIONS =========================================================
 // historique des versions
 /*
 version 2.6 - 24/11/2026 - regroupe 2.5a.1 et 2.5a.2
      corrigé : cadres de visage mal placés ou manquants sur certaines photos. Les noms et
      les zones étaient lus séparément dans tout le XMP puis associés dans l'ordre : un nom
      sans rectangle (Microsoft), un visage sans nom, une région de mise au point ou une
      zone écrite en attributs décalaient les noms (un nom posé sur le visage d'un autre)
      ou faisaient disparaître des cadres. Les cadres sont maintenant lus région par
      région (nom et zone de la même région, visages seulement), comme face_tag_editor 2.6
      et tag_manager 1.8 ; l'éventuel « Extended XMP » est lu aussi. Un XMP abîmé garde
      l'ancienne lecture en secours. Effet aussi sur la synchro des tags : un nom sans
      cadre n'est plus enregistré comme tag visage.
      nettoyage : la valeur 'manual' de la colonne source de facetag_person_tags
      n'était écrite par aucun plugin (écran de correction jamais réalisé) ; ses
      protections sont retirées, la colonne est gardée pour compatibilité avec
      face_tag_editor (toujours 'auto'). Texte du bouton « Effacer la table des tags
      visage » corrigé : il annonçait des corrections manuelles perdues qui n'existent pas

 version 2.5a - 19/11/2026 - correction 2.5.1
      corrigé : le masquage des tags visage ne s'appliquait pas au widget « Tags »
      de PWG_Stuffs, sauf surcharge manuelle de stuffs_tags.tpl dans le thème ;
      face_tag filtre désormais lui-même ce widget, quel que soit le thème

 version 2.5 - 18/11/2026 - regroupe 2.4.1 et 2.4.2
      ajouté : mode d'affichage « Voir en survolant » (les cadres des visages
      n'apparaissent qu'au survol, utile pour les photos de groupe) - merci Gvf63 (PR #3)
      corrigé : nom de visage avec apostrophe jamais transformé en tag lors de la
      correction automatique (synchro et auto-sync à l'affichage) - issue #4
      corrigé : noms de visages non échappés dans la liste des visages non résolus
      corrigé : fichiers de langue sans « ?> » final (les lignes vides qui le
      suivaient corrompaient les images servies par private_derivative_protection)
      corrigé : libellé des boutons « Enregistrer » partagé avec d'autres plugins
      (clé de traduction générique 'Enregistrer' → 'facetag_save') : Face Tag
      changeait le bouton d'ip_location et de smart_albums_copier, et inversement

 version 2.4 - 14/09/2026
      corrigé : bug JS (apostrophe dans une traduction cassait la page
      "Affichage des tags") - merci Gvf63
      ajouté traduction italienne (it_IT) - merci Gvf63
      ajouté fichier manquant language/en_UK/index.php

 version 2.3a - 18/08/2026
      ajout auto des tags visage aux tags pwg s'ils n'y sont pas
      activé par $conf['face_tag_check'] = true;

 version 2.3 - 16/08/2026
      aide mise à jour
      version auto pour PEM
      langues FR UK mises à jour

 version 2.2b - 15/08/2026
      automatisaton de la correction des tags
 
 version 2.2a - 15/08/2026
     controle des tags

 version 2.2 - 14/08/2026
    ajouté la fonctionnalité de ne pas afficher les noms dans le nuage de tag
    -> ajout d'une table supplémentaire 'facetag_person_tags'
    Prise en compte de PWG_Stuffs
        
 version 2.1 - 08/03/2026
    modification mode global avec indication visuelle quand il y un tag visage
    et choix des couleurs des icones
    
version 2.0 - 18/12/2025
    numérotation de version = face_tag_editor
    corrigé erreur sur orientation 8 - 270CW
    neutralisation des console.log
    modifié alignement des icones sur modus + css

version 1.9 - 10/12/2025 diffusée
    REFONTE COMPLÈTE - Approche REGEX au lieu de parsing XML DOM
    Extraction directe du segment XMP + regex simples

version 1.9 - 09/12/2025 (non diffusée)
    tentative support format Lightroom (bugs)

version 1.8A - en cours
    corrigé page configuration

version 1.8 - 04/12/2025 - diffusé
    si PHP Imagick n'est pas actif , External Magick est utilisé , sinon message d'avertissement
    modification des icones en svg

version 1.7 - 30/11/2025
    corrigé : lecture des visages créés par xnview
    orientation 8 corrigé , ajouté orientation 2,3,4

version 1.6 - 25/11/2025
    ajouté support orientation EXIF pour photos en mode portrait
    corrigé orientation type6 = 90 CW

version 1.5 - 20/11/2025
    corrigé conflit avec l'absence de super_zoom

version 1.4 - 16/11/2025
    corrigé jQuery non chargé dans certaines configurations
    testé avec les thèmes Bootstrap_Darkroom, Modus et Elegant

version 1.3 - 16/11/2025
    paramètres ajout bouton valeurs par défaut
    correction taduction uk
    changement couleur sur bouton toggle
    console.log en remarques

version 1.2 - 15/11/2025
    erreur dans maintain.inc.php

version 1.1 - 15/11/2025
    modifé les paramétres par défaut - fichier de paramétres config_defaut.php
    ajouté Plugin URI

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
define('FACETAG_ID', basename(dirname(__FILE__)));
define('FACETAG_PATH', PHPWG_PLUGINS_PATH . FACETAG_ID . '/');
define('FACETAG_TABLE', $prefixeTable . 'facetag');
define('FACETAG_PERSON_TAGS_TABLE', $prefixeTable . 'facetag_person_tags');
define('FACETAG_UNRESOLVED_FACES_TABLE', $prefixeTable . 'facetag_unresolved_faces');
define('FACETAG_ADMIN', get_root_url() . 'admin.php?page=plugin-' . FACETAG_ID);
define('FACETAG_PUBLIC', get_absolute_root_url() . make_index_url(array('section' => 'face_tag')) . '/');
define('FACETAG_DIR', PHPWG_ROOT_PATH . PWG_LOCAL_DIR . 'face_tag/');

// ==================== CHARGER LA BIBLIOTHÈQUE XMP v2.0 ====================
require_once(FACETAG_PATH . 'lib/face_xmp_extraction.php');

// ==================== INITIALISATION ====================
add_event_handler('init', 'facetag_init');

function facetag_init()
{
  load_language('plugin.lang', FACETAG_PATH);
}

// ==================== CHARGER JQUERY ====================
add_event_handler('loc_begin_page_header', 'facetag_load_jquery');

function facetag_load_jquery()
{
  global $template;

  // Charger jQuery seulement s'il n'est pas déjà chargé
  $template->append('head_elements', '
  <script type="text/javascript">
    if (typeof jQuery === "undefined") {
      document.write(\'<script type="text/javascript" src="' . get_root_url() . 'themes/default/js/jquery.min.js"><\/script>\');
    }
  </script>
  ');
}

// ==================== CHARGER CSS ET JS ====================
add_event_handler('loc_end_page_tail', 'facetag_add_extension');

function facetag_add_extension()
{
  global $template;

  $template->append('footer_elements', '
  <link rel="stylesheet" href="plugins/face_tag/template/facetag.css">
  <script src="plugins/face_tag/facetag.js"></script>
  ');
}

// ==================== BOUTON DANS LA BARRE DE NAVIGATION ====================
add_event_handler('loc_end_picture', 'facetag_picture_toolbar');

function facetag_picture_toolbar()
{
  global $template, $user, $picture;

  // Charger les valeurs par défaut
  include(dirname(__FILE__) . '/install/config_defaut.php');

  // Charger la configuration
  $config_raw = conf_get_param('face_tag_config', false);
  $config = ($config_raw !== false) ? @unserialize($config_raw) : false;

  if (!is_array($config)) {
    $config = $config_defaut;
  } else {
    $config = array_merge($config_defaut, $config);
  }

  // Ne rien afficher si les deux boutons sont désactivés
  if (!$config['show_xmp_button'] && !$config['show_faces_button']) {
    return;
  }

  $template->set_template_dir(FACETAG_PATH.'template/');

  // Construire l'URL du bouton (AJAX)
  $ajax_url = get_root_url() . 'ws.php?format=json&method=facetag.getXMP&image_id=' . $picture['current']['id'];
  $icon = get_root_url() . 'plugins/' . FACETAG_ID . '/images/face.svg';

  // Assigner les variables au template
  $template->assign(array(
    'FACETAG_AJAX_URL' => $ajax_url,
    'FACETAG_ICON' => $icon,
    'FACETAG_PATH' => FACETAG_PATH,
    'FACETAG_SHOW_XMP' => (bool)$config['show_xmp_button'],
    'FACETAG_SHOW_FACES' => (bool)$config['show_faces_button'],
    'FACETAG_DISPLAY_MODE' => isset($config['faces_display_mode']) ? $config['faces_display_mode'] : $config_defaut['faces_display_mode'],
    'FACETAG_RECT_COLOR' => isset($config['rect_color']) ? $config['rect_color'] : $config_defaut['rect_color'],
    'FACETAG_RECT_WIDTH' => isset($config['rect_width']) ? $config['rect_width'] : $config_defaut['rect_width'],
    'FACETAG_RECT_OPACITY' => isset($config['rect_opacity']) ? $config['rect_opacity'] : $config_defaut['rect_opacity'],
    'FACETAG_LABEL_TEXT_COLOR' => isset($config['label_text_color']) ? $config['label_text_color'] : $config_defaut['label_text_color'],
    'FACETAG_LABEL_FONT_SIZE' => isset($config['label_font_size']) ? $config['label_font_size'] : $config_defaut['label_font_size'],
    'FACETAG_LABEL_BG_COLOR' => isset($config['label_bg_color']) ? $config['label_bg_color'] : $config_defaut['label_bg_color'],
    'FACETAG_LABEL_BG_OPACITY' => isset($config['label_bg_opacity']) ? $config['label_bg_opacity'] : $config_defaut['label_bg_opacity'],
    'FACETAG_ICON_ACTIVE_COLOR' => isset($config['icon_active_color']) ? $config['icon_active_color'] : $config_defaut['icon_active_color'],
    'FACETAG_ICON_TAGGED_COLOR' => isset($config['icon_tagged_color']) ? $config['icon_tagged_color'] : $config_defaut['icon_tagged_color'],
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

// ==================== SYNCHRONISATION AUTOMATIQUE DES TAGS VISAGE ====================
// Corrige silencieusement, à l'affichage d'une photo, les tags visage absents
// de la base Piwigo (cas fréquent : synchro non déclenchée après un upload
// utilisateur). Hooké sur loc_begin_picture (avant get_common_tags() dans
// picture.php) pour que le tag nouvellement créé apparaisse dès ce chargement.
//
// Désactivé par défaut : ajoute une lecture du fichier (XMP) à chaque
// affichage de photo, donc réservé aux installs qui rencontrent réellement
// des discordances après upload. Activation via local/config/config.inc.php :
//   $conf['face_tag_check'] = true;
global $conf;
if (!empty($conf['face_tag_check']))
{
  add_event_handler('loc_begin_picture', 'facetag_auto_sync_current_image_tags');
}

function facetag_auto_sync_current_image_tags()
{
  global $page;

  if (empty($page['image_id']))
  {
    return;
  }

  $image_id = intval($page['image_id']);

  $result = pwg_query('
SELECT path
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$image_id.'
;');
  $row = pwg_db_fetch_assoc($result);
  if (!$row)
  {
    return;
  }

  $image_path = PHPWG_ROOT_PATH . $row['path'];
  if (!is_file($image_path))
  {
    return;
  }

  $data = facetag_lib_extract_faces_data($image_path);
  if (empty($data['faces']))
  {
    return;
  }

  // Noms détectés dans le XMP, dédupliqués (minuscule => nom original)
  $face_names = array();
  foreach ($data['faces'] as $face)
  {
    if (!empty($face['nom']))
    {
      $nom = trim($face['nom']);
      $face_names[mb_strtolower($nom)] = $nom;
    }
  }
  if (empty($face_names))
  {
    return;
  }

  // Tags déjà attachés à cette photo
  $attached_tag_ids = query2array('
SELECT tag_id FROM '.IMAGE_TAG_TABLE.'
  WHERE image_id = '.$image_id.'
;', null, 'tag_id');

  $attached_names = array();
  if (!empty($attached_tag_ids))
  {
    $result = pwg_query('
SELECT name FROM '.TAGS_TABLE.'
  WHERE id IN ('.implode(',', $attached_tag_ids).')
;');
    while ($row = pwg_db_fetch_assoc($result))
    {
      $attached_names[mb_strtolower(trim($row['name']))] = true;
    }
  }

  $missing_names = array();
  foreach ($face_names as $lower => $original)
  {
    if (!isset($attached_names[$lower]))
    {
      $missing_names[] = $original;
    }
  }
  if (empty($missing_names))
  {
    return;
  }

  // Même logique de création que facetag_retry_and_record_unresolved()
  // (admin/functions.inc.php), appliquée ici photo par photo à l'affichage
  // plutôt qu'en synchro par lot depuis l'admin.
  if (!function_exists('tag_id_from_tag_name'))
  {
    include_once(PHPWG_ROOT_PATH . 'admin/include/functions.php');
  }

  $now = date('Y-m-d H:i:s');

  foreach ($missing_names as $name)
  {
    try
    {
      // nom échappé : voir facetag_retry_and_record_unresolved() (issue #4)
      $tag_id = tag_id_from_tag_name(pwg_db_real_escape_string($name));

      $already_attached = query2array('
SELECT tag_id FROM '.IMAGE_TAG_TABLE.'
  WHERE image_id = '.$image_id.' AND tag_id = '.intval($tag_id).'
;', null, 'tag_id');

      if (empty($already_attached))
      {
        pwg_query('
INSERT INTO '.IMAGE_TAG_TABLE.' (image_id, tag_id)
VALUES ('.$image_id.', '.intval($tag_id).')
;');
      }

      pwg_query('
INSERT INTO '.FACETAG_PERSON_TAGS_TABLE.' (tag_id, is_face_tag, source, example_image_id, updated_at)
VALUES ('.intval($tag_id).', 1, \'auto\', '.$image_id.', \''.$now.'\')
ON DUPLICATE KEY UPDATE
  example_image_id = VALUES(example_image_id),
  updated_at = VALUES(updated_at)
;');
    }
    catch (\Throwable $e)
    {
      error_log('[face_tag] facetag_auto_sync_current_image_tags : échec création tag pour "'.$name.'" (image '.$image_id.') : '.$e->getMessage());
    }
  }
}

// ==================== EXCLUSION DES TAGS VISAGE (helper partagé) ====================

/**
 * Retourne la liste des tag_id à exclure du nuage de tags public, ou un
 * tableau vide si l'option est désactivée. Utilisé à la fois pour le
 * filtrage direct de tags.php et pour exposer la liste aux templates
 * (ex: widgets tiers comme PWG_Stuffs qui n'appellent pas loc_end_tags).
 *
 * @return int[]
 */
function facetag_get_excluded_tag_ids()
{
  $config_raw = conf_get_param('face_tag_config', false);
  $config = ($config_raw !== false) ? @unserialize($config_raw) : false;

  if (!is_array($config) || empty($config['hide_face_tags_cloud']))
  {
    return array();
  }

  $result = pwg_query('SELECT tag_id FROM '.FACETAG_PERSON_TAGS_TABLE.' WHERE is_face_tag = 1;');
  $excluded_ids = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    $excluded_ids[] = intval($row['tag_id']);
  }

  return $excluded_ids;
}

// Rendre la liste disponible à tous les templates (ex: stuffs_tags.tpl surchargé
// dans le thème, pour le widget de tags de PWG_Stuffs qui contourne tags.php)
add_event_handler('init', 'facetag_assign_excluded_tag_ids_to_template');

function facetag_assign_excluded_tag_ids_to_template()
{
  global $template;

  $excluded_ids = facetag_get_excluded_tag_ids();
  $template->assign('FACETAG_EXCLUDED_TAG_IDS', $excluded_ids);

  if (!empty($excluded_ids))
  {
    // Widget "Tags" de PWG_Stuffs : il appelle get_available_tags() et affiche
    // son bloc dans la même fonction, sans hook entre les deux. On insère donc,
    // par préfiltre sur le template 'stuffs' (stuffs_blocks.tpl), un appel à
    // {facetag_filter_stuffs_tags} juste avant l'inclusion du bloc Tags.
    // Fonctionne avec le stuffs_tags.tpl d'origine comme avec une surcharge de thème.
    $template->smarty->registerPlugin('function', 'facetag_filter_stuffs_tags', 'facetag_smarty_filter_stuffs_tags');
    $template->set_prefilter('stuffs', 'facetag_prefilter_stuffs_blocks');
  }
}

/**
 * Préfiltre Smarty du template 'stuffs' (PWG_Stuffs) : ajoute le filtrage des
 * tags visage avant l'inclusion du bloc stuffs_tags.tpl. Sans effet si le
 * motif n'est pas trouvé (stuffs_blocks.tpl surchargé autrement).
 */
function facetag_prefilter_stuffs_blocks($source, $smarty)
{
  return preg_replace(
    '/\{include\s+file=\$block\.TEMPLATE\s*\}/',
    "{if \$block.TEMPLATE == 'stuffs_tags.tpl'}{facetag_filter_stuffs_tags}{/if}$0",
    $source
  );
}

/**
 * Fonction Smarty {facetag_filter_stuffs_tags} : retire les tags visage des
 * variables 'tags' et 'letters' du template courant (héritées par l'include).
 */
function facetag_smarty_filter_stuffs_tags($params, $smarty_tpl)
{
  $excluded_ids = $smarty_tpl->getTemplateVars('FACETAG_EXCLUDED_TAG_IDS');
  if (empty($excluded_ids))
  {
    return '';
  }

  $tags = $smarty_tpl->getTemplateVars('tags');
  if (is_array($tags))
  {
    $smarty_tpl->assign('tags', facetag_strip_tags_list($tags, $excluded_ids));
  }

  $letters = $smarty_tpl->getTemplateVars('letters');
  if (is_array($letters))
  {
    $smarty_tpl->assign('letters', facetag_strip_tags_letters($letters, $excluded_ids));
  }

  return '';
}

/**
 * Retire d'une liste plate de tags ceux dont l'id est exclu.
 */
function facetag_strip_tags_list($tags, $excluded_ids)
{
  return array_values(array_filter($tags, function($tag) use ($excluded_ids) {
    return !isset($tag['id']) || !in_array($tag['id'], $excluded_ids);
  }));
}

/**
 * Mode "lettres" : filtre les tags de chaque lettre, supprime les lettres
 * devenues vides en reportant leur changement de colonne sur la suivante.
 */
function facetag_strip_tags_letters($letters, $excluded_ids)
{
  $filtered_letters = array();
  $pending_change_column = false;
  foreach ($letters as $letter)
  {
    if (isset($letter['tags']) && is_array($letter['tags']))
    {
      $letter['tags'] = facetag_strip_tags_list($letter['tags'], $excluded_ids);
    }
    if (empty($letter['tags']))
    {
      if (!empty($letter['CHANGE_COLUMN']))
      {
        $pending_change_column = true;
      }
      continue;
    }
    if ($pending_change_column)
    {
      $letter['CHANGE_COLUMN'] = true;
      $pending_change_column = false;
    }
    $filtered_letters[] = $letter;
  }
  return $filtered_letters;
}

// ==================== FILTRAGE DU NUAGE DE TAGS PUBLIC (tags.php) ====================
add_event_handler('loc_end_tags', 'facetag_filter_public_tag_cloud');

function facetag_filter_public_tag_cloud()
{
  global $template;

  $excluded_ids = facetag_get_excluded_tag_ids();

  if (empty($excluded_ids))
  {
    return;
  }

  // Mode "nuage" : liste plate de tags
  $tags = $template->get_template_vars('tags');
  if (is_array($tags) && count($tags) > 0)
  {
    $template->assign('tags', facetag_strip_tags_list($tags, $excluded_ids));
  }

  // Mode "lettres" : groupes de lettres contenant chacun une liste de tags
  $letters = $template->get_template_vars('letters');
  if (is_array($letters) && count($letters) > 0)
  {
    $template->assign('letters', facetag_strip_tags_letters($letters, $excluded_ids));
  }
}

// ==================== MASQUAGE DU COMPTEUR DE TAGS DU MENU "DÉCOUVRIR" ====================
add_event_handler('blockmanager_apply', 'facetag_hide_tags_menu_counter');

function facetag_hide_tags_menu_counter($menu_ref_arr)
{
  $menu = $menu_ref_arr[0];

  if ($menu->get_id() != 'menubar')
  {
    return;
  }

  if (empty(facetag_get_excluded_tag_ids()))
  {
    return;
  }

  $block = $menu->get_block('mbMenu');
  if ($block !== null && isset($block->data['tags']['COUNTER']))
  {
    unset($block->data['tags']['COUNTER']);
  }
}

// ==================== WEB SERVICE ====================
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

  // === EXTRACTION v2.0 : Utiliser la bibliothèque ===
  $data = facetag_lib_extract_faces_data($image_path);

  return array(
    'image_id' => $params['image_id'],
    'wjpg' => $data['wjpg'],
    'hjpg' => $data['hjpg'],
    'orientation' => $data['or'],
    'faces' => $data['faces']
  );
}
?>
