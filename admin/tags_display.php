<?php
defined('FACETAG_PATH') or die('Hacking attempt!');

// Afficher le tabsheet
facetag_admin_tabsheet('tags_display');

// Charger la configuration existante
$config = facetag_load_config();

//  Sauvegarde de l'affichage des tags  ====================================

if (isset($_POST['submit_tags_display']))
{
  $config['hide_face_tags_cloud'] = isset($_POST['hide_face_tags_cloud']);
  facetag_save_config($config);
}

//  Synchronisation de la table des tags visage  ====================================
// Gérée entièrement en JS, en petits lots via l'API AJAX chunkée
// (admin/tags_display_ajax.php) — même principe que le plugin AlbumPilot :
// jamais une seule requête assez longue pour risquer un timeout (504),
// quelle que soit la taille de la photothèque. Rien à traiter ici en POST.

$sync_cat_selected = array();

//  Réinitialisation de la table des tags visage  ====================================

if (isset($_POST['reset_person_tags']))
{
  facetag_ensure_sync_tables();
  pwg_query('TRUNCATE TABLE '.FACETAG_PERSON_TAGS_TABLE.';');

  array_push($page['infos'], l10n('Table des tags visage réinitialisée : plus aucun tag n\'est marqué comme tag visage. Relancez une synchronisation pour la repeupler.'));
}

//  Visages détectés dans le XMP sans tag Piwigo correspondant  ====================================
// (typiquement : la synchronisation des métadonnées de Piwigo a tronqué ou
// ignoré le mot-clé — à corriger à la main sur la photo, puis resynchroniser)

// Garde-fou : la table peut ne pas exister si aucune synchronisation n'a
// encore été lancée depuis la mise à jour du plugin.
facetag_ensure_sync_tables();

$facetag_unresolved_by_image = array();
$result = pwg_query('
SELECT uf.image_id, uf.face_name, img.file
  FROM '.FACETAG_UNRESOLVED_FACES_TABLE.' AS uf
  INNER JOIN '.IMAGES_TABLE.' AS img ON img.id = uf.image_id
  ORDER BY img.file ASC, uf.face_name ASC
;');
while ($row = pwg_db_fetch_assoc($result))
{
  $image_id = (int)$row['image_id'];
  if (!isset($facetag_unresolved_by_image[$image_id]))
  {
    $facetag_unresolved_by_image[$image_id] = array(
      'FILE'   => $row['file'],
      'U_EDIT' => get_root_url().'admin.php?page=photo-'.$image_id.'-properties',
      'FACES'  => array(),
      'TAGS'   => array(),
    );
  }
  $facetag_unresolved_by_image[$image_id]['FACES'][] = $row['face_name'];
}

if (!empty($facetag_unresolved_by_image))
{
  $facetag_unresolved_image_ids = array_keys($facetag_unresolved_by_image);
  $result = pwg_query('
SELECT it.image_id, t.name
  FROM '.IMAGE_TAG_TABLE.' AS it
  INNER JOIN '.TAGS_TABLE.' AS t ON t.id = it.tag_id
  WHERE it.image_id IN ('.implode(',', $facetag_unresolved_image_ids).')
  ORDER BY t.name ASC
;');
  while ($row = pwg_db_fetch_assoc($result))
  {
    $facetag_unresolved_by_image[(int)$row['image_id']]['TAGS'][] = $row['name'];
  }
}

// Jointure faite ici (pas dans le template : le modificateur Smarty
// "implode" est marqué deprecated et affiche un warning dans la page)
$facetag_unresolved_list = array();
foreach ($facetag_unresolved_by_image as $entry)
{
  $facetag_unresolved_list[] = array(
    'FILE'   => $entry['FILE'],
    'U_EDIT' => $entry['U_EDIT'],
    'FACES'  => implode(', ', $entry['FACES']),
    'TAGS'   => !empty($entry['TAGS']) ? implode(', ', $entry['TAGS']) : '',
  );
}
$template->assign('FACETAG_UNRESOLVED_LIST', $facetag_unresolved_list);

// Sélecteur d'albums (même mécanisme que la page Synchroniser de Piwigo).
// Les albums virtuels (dir NULL, créés depuis l'admin, sans dossier lié)
// peuvent contenir des photos au même titre que les albums réels — c'est
// même le seul type d'album que voit un utilisateur qui n'utilise que
// l'upload web. On les affiche donc tous, avec un simple indice visuel
// (triangle dépli/replie en vert) pour les distinguer dans l'arbre.
$facetag_cat_query = '
SELECT id, name, dir, uppercats, global_rank
  FROM '.CATEGORIES_TABLE.'
;';
$facetag_all_cats = query2array($facetag_cat_query);
usort($facetag_all_cats, 'global_rank_compare');

$facetag_cat_tree = array();
foreach ($facetag_all_cats as $cat)
{
  $facetag_cat_tree[] = array(
    'id'         => (int)$cat['id'],
    'name'       => $cat['name'],
    'depth'      => substr_count($cat['global_rank'], '.'),
    'is_virtual' => empty($cat['dir']),
  );
}

display_select_categories($facetag_all_cats, $sync_cat_selected, 'facetag_category_options', false);
$template->assign('FACETAG_CAT_JSON', json_encode($facetag_cat_tree, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP));

// Assigner au template
$template->assign(array(
  'HIDE_FACE_TAGS_CLOUD' => $config['hide_face_tags_cloud'],
  'FACETAG_AJAX_URL'     => FACETAG_ADMIN,
  'FACETAG_PWG_TOKEN'    => get_pwg_token(),
));

// Charger le template
$template->set_filename('face_tag_tags_display', FACETAG_PATH . 'template/tags_display.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'face_tag_tags_display');

//---------------------------------------------------------------------
?>
