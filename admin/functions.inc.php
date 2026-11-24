<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

/**
 * Crée et affiche le tabsheet (onglets) pour les pages d'administration
 * @param string $selected L'onglet actuellement sélectionné ('config', 'config2', 'help')
 */
function facetag_admin_tabsheet($selected = 'config')
{
  include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
  $tabsheet = new tabsheet();
  $tabsheet->set_id('face_tag');
  $tabsheet->add('config', l10n('Configuration générale'), FACETAG_ADMIN . '&tab=config');
  $tabsheet->add('tags_display', l10n('Affichage des tags'), FACETAG_ADMIN . '&tab=tags_display');
  $tabsheet->add('help', l10n('Aide'), FACETAG_ADMIN . '&tab=help');
  $tabsheet->select($selected);
  $tabsheet->assign();
}

/**
 * Charge la configuration du plugin, fusionnée avec les valeurs par défaut.
 * Crée l'entrée en base au premier appel si elle n'existe pas encore.
 *
 * @return array
 */
function facetag_load_config()
{
  include(FACETAG_PATH.'install/config_defaut.php');

  $config_string = conf_get_param('face_tag_config', false);

  if ($config_string === false)
  {
    $config = $config_defaut;
    conf_update_param('face_tag_config', serialize($config));
  }
  else
  {
    $config = @unserialize($config_string);
    if (!is_array($config))
    {
      $config = $config_defaut;
    }
    else
    {
      $config = array_merge($config_defaut, $config);
    }
  }

  return $config;
}

/**
 * Enregistre la configuration du plugin et pousse un message de succès.
 *
 * @param array $params
 */
function facetag_save_config($params)
{
  global $page;
  conf_update_param('face_tag_config', serialize($params));
  array_push($page['infos'], l10n('Configuration enregistrée avec succès'));
}

/**
 * Crée les tables du plugin si nécessaire (plugin mis à jour sans être
 * réactivé), et migre la collation de facetag_unresolved_faces vers
 * utf8mb4_bin sur les tables déjà créées avant ce correctif : selon la
 * collation par défaut du serveur (ex: utf8mb4_0900_ai_ci, insensible aux
 * accents), deux noms différents comme "Béatrice" et "Beatrice" pouvaient
 * être considérés identiques par la clé primaire (image_id, face_name) et
 * provoquer une erreur "Duplicate entry" fatale à l'insertion, alors qu'ils
 * sont bien distincts pour PHP. utf8mb4_bin compare octet à octet.
 *
 * facetag_person_tags est partagée avec face_tag_editor et tag_manager. Sa colonne
 * `source` vaut toujours 'auto' : 'manual' était prévu pour un écran de correction
 * jamais réalisé. Elle est gardée pour compatibilité, car face_tag_editor crée la table
 * lui-même et la nomme dans ses INSERT.
 */
function facetag_ensure_sync_tables()
{
  pwg_query('
CREATE TABLE IF NOT EXISTS ' . FACETAG_PERSON_TAGS_TABLE . ' (
  tag_id           INT UNSIGNED NOT NULL,
  is_face_tag      TINYINT(1)   NOT NULL DEFAULT 1,
  source           ENUM(\'auto\',\'manual\') NOT NULL DEFAULT \'auto\',
  example_image_id INT UNSIGNED NULL,
  updated_at       DATETIME NOT NULL,
  PRIMARY KEY (tag_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');

  pwg_query('
CREATE TABLE IF NOT EXISTS ' . FACETAG_UNRESOLVED_FACES_TABLE . ' (
  image_id   INT UNSIGNED NOT NULL,
  face_name  VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (image_id, face_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');

  pwg_query('ALTER TABLE '.FACETAG_UNRESOLVED_FACES_TABLE.' MODIFY face_name VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL;');
}

/**
 * Retourne les id de catégories correspondant à la portée demandée, prête à
 * être utilisée dans une clause SQL IN(...) via IMAGE_CATEGORY_TABLE (le
 * vrai lien image <-> album, contrairement à img.storage_category_id qui
 * n'est renseigné que pour les photos ajoutées par synchronisation de
 * dossier — il reste NULL pour les photos ajoutées par upload web, qui ne
 * seraient donc jamais retrouvées par un scan restreint à un album).
 *
 * @param int|null $cat_id
 * @param bool $subcats_included
 * @return int[]|null null = toute la photothèque (pas de restriction)
 */
function facetag_resolve_scope_cat_ids($cat_id, $subcats_included)
{
  if (empty($cat_id))
  {
    return null;
  }
  return $subcats_included ? get_subcat_ids(array(intval($cat_id))) : array(intval($cat_id));
}

/**
 * Compare les tags actuellement attachés à une photo aux noms de visage
 * détectés dans son XMP. Les tags qui correspondent sont confirmés comme
 * "tag visage" (FACETAG_PERSON_TAGS_TABLE).
 * N'écrit RIEN sur les visages non résolus : à la charge de l'appelant, qui
 * peut vouloir retenter une correspondance après un sync_metadata() avant
 * de les enregistrer (voir facetag_retry_and_record_unresolved()).
 *
 * @param int $image_id
 * @param int[] $tag_ids Tags actuellement attachés à la photo
 * @param array &$all_tags tag_id => nom en minuscule (complété si un tag inconnu est rencontré)
 * @param array $face_names nom en minuscule => nom original du visage
 * @param array &$found_tag_ids set des tag_id déjà confirmés durant ce run (par référence)
 * @param string $now horodatage à utiliser pour les écritures de ce run
 * @return string[] noms originaux des visages toujours sans tag correspondant
 */
function facetag_match_faces($image_id, $tag_ids, &$all_tags, $face_names, &$found_tag_ids, $now)
{
  $matched = array(); // nom en minuscule des visages retrouvés parmi les tags de cette photo
  foreach ($tag_ids as $tag_id)
  {
    if (isset($all_tags[$tag_id]) && isset($face_names[$all_tags[$tag_id]]))
    {
      $matched[$all_tags[$tag_id]] = true;

      if (!isset($found_tag_ids[$tag_id]))
      {
        $found_tag_ids[$tag_id] = true;

        pwg_query('
INSERT INTO '.FACETAG_PERSON_TAGS_TABLE.' (tag_id, is_face_tag, source, example_image_id, updated_at)
VALUES ('.intval($tag_id).', 1, \'auto\', '.intval($image_id).', \''.$now.'\')
ON DUPLICATE KEY UPDATE
  example_image_id = VALUES(example_image_id),
  updated_at = VALUES(updated_at)
;');
      }
    }
  }

  $unresolved_names = array();
  foreach ($face_names as $lower => $original)
  {
    if (!isset($matched[$lower]))
    {
      $unresolved_names[] = $original;
    }
  }

  return $unresolved_names;
}

/**
 * Lit le XMP d'UNE photo et compare ses visages aux tags Piwigo déjà
 * attachés. N'écrit jamais les visages non résolus (voir
 * facetag_match_faces()) — permet à l'appelant de regrouper une tentative
 * de correction automatique (sync_metadata()) par lot plutôt que photo par
 * photo avant d'enregistrer ce qui reste vraiment non résolu.
 *
 * @param int $image_id
 * @param array $info ('path', 'representative_ext', 'tag_ids')
 * @param array &$all_tags tag_id => nom en minuscule, déjà chargé par l'appelant
 * @param array &$found_tag_ids set des tag_id déjà confirmés durant ce run
 * @param string $now
 * @return array ('face_names' => [lower=>original,...], 'unresolved_names' => string[])
 *   'face_names' est vide si le fichier est illisible ou sans visage détecté.
 */
function facetag_read_and_match_one_image($image_id, $info, &$all_tags, &$found_tag_ids, $now)
{
  $file = PHPWG_ROOT_PATH . $info['path'];
  if (!empty($info['representative_ext']))
  {
    $file = original_to_representative($file, $info['representative_ext']);
  }
  if (!is_file($file))
  {
    return array('face_names' => array(), 'unresolved_names' => array());
  }

  $data = facetag_lib_extract_faces_data($file);
  if (empty($data['faces']))
  {
    return array('face_names' => array(), 'unresolved_names' => array());
  }

  // nom en minuscule => nom original (pour affichage / enregistrement)
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
    return array('face_names' => array(), 'unresolved_names' => array());
  }

  $unresolved_names = facetag_match_faces($image_id, $info['tag_ids'], $all_tags, $face_names, $found_tag_ids, $now);

  return array('face_names' => $face_names, 'unresolved_names' => $unresolved_names);
}

/**
 * Pour un lot de photos toujours en discordance après une 1ère comparaison,
 * tente une correction automatique groupée en 2 niveaux :
 *
 * 1. Laisse Piwigo relire les métadonnées du fichier (sync_metadata(), en un
 *    seul appel pour tout le lot) — règle la majorité des cas "jamais
 *    synchronisé".
 * 2. Pour ce qui résiste encore (bug de relecture bas niveau de Piwigo :
 *    troncature, tiret perdu...), crée/attache directement le tag Piwigo
 *    avec le nom exact du visage lu dans le XMP — exactement ce que fait
 *    face_tag_editor en sauvegardant une photo sans rien changer (il
 *    n'utilise pas non plus sync_metadata(), pour la même raison). Ce niveau
 *    ne distingue pas "Piwigo a mal lu le fichier" d'une vraie faute de
 *    frappe côté source (ex: Chevalier/Chevallier) : dans les deux cas un
 *    tag est créé avec le nom exact — accepté par l'utilisateur, qui
 *    corrige les rares fautes de frappe à la main via le gestionnaire de
 *    tags de digiKam plutôt que de les laisser remonter ici.
 *
 * Une seule passe suffit donc à la synchro pour s'auto-corriger, pas besoin
 * d'une resynchro séparée derrière pour les cas simples.
 *
 * @param array $unresolved_by_image image_id => ['face_names' => [lower=>original,...]]
 * @param array &$all_tags
 * @param array &$found_tag_ids
 * @return int nombre de visages toujours non résolus après tentative (erreurs uniquement, normalement 0)
 */
function facetag_retry_and_record_unresolved($unresolved_by_image, &$all_tags, &$found_tag_ids)
{
  if (empty($unresolved_by_image))
  {
    return 0;
  }

  if (!function_exists('sync_metadata'))
  {
    include_once(PHPWG_ROOT_PATH . 'admin/include/functions_metadata.php');
  }
  if (!function_exists('tag_id_from_tag_name'))
  {
    include_once(PHPWG_ROOT_PATH . 'admin/include/functions.php');
  }

  $image_ids = array_keys($unresolved_by_image);
  sync_metadata($image_ids);

  $tags_of_image = array();
  $result = pwg_query('
SELECT image_id, tag_id FROM '.IMAGE_TAG_TABLE.'
  WHERE image_id IN ('.implode(',', $image_ids).')
;');
  while ($row = pwg_db_fetch_assoc($result))
  {
    $tags_of_image[(int)$row['image_id']][] = (int)$row['tag_id'];
  }

  // Recharge les noms des tags potentiellement créés par sync_metadata()
  $seen_tag_ids = array();
  foreach ($tags_of_image as $ids)
  {
    foreach ($ids as $id)
    {
      $seen_tag_ids[$id] = true;
    }
  }
  $unknown_tag_ids = array_diff(array_keys($seen_tag_ids), array_keys($all_tags));
  if (!empty($unknown_tag_ids))
  {
    $result = pwg_query('SELECT id, name FROM '.TAGS_TABLE.' WHERE id IN ('.implode(',', $unknown_tag_ids).');');
    while ($row = pwg_db_fetch_assoc($result))
    {
      $all_tags[$row['id']] = mb_strtolower(trim($row['name']));
    }
  }

  $now = date('Y-m-d H:i:s');
  $unresolved_count = 0;
  $values = array();

  foreach ($unresolved_by_image as $image_id => $entry)
  {
    $tag_ids = isset($tags_of_image[$image_id]) ? $tags_of_image[$image_id] : array();
    $still_unresolved = facetag_match_faces($image_id, $tag_ids, $all_tags, $entry['face_names'], $found_tag_ids, $now);

    foreach (array_unique($still_unresolved) as $name)
    {
      try
      {
        // tag_id_from_tag_name() insère le nom tel quel dans ses requêtes SQL
        // (Piwigo l'appelle avec des données déjà échappées) : sans
        // échappement, un nom avec apostrophe (ex: O'Brien) fait planter la
        // requête et le visage reste "non résolu" pour toujours (issue #4).
        $tag_id = tag_id_from_tag_name(pwg_db_real_escape_string($name));
        $all_tags[$tag_id] = mb_strtolower(trim($name));

        $already_attached = query2array('
SELECT tag_id FROM '.IMAGE_TAG_TABLE.'
  WHERE image_id = '.intval($image_id).' AND tag_id = '.intval($tag_id).'
;', null, 'tag_id');

        if (empty($already_attached))
        {
          pwg_query('
INSERT INTO '.IMAGE_TAG_TABLE.' (image_id, tag_id)
VALUES ('.intval($image_id).', '.intval($tag_id).')
;');
        }

        if (!isset($found_tag_ids[$tag_id]))
        {
          $found_tag_ids[$tag_id] = true;

          pwg_query('
INSERT INTO '.FACETAG_PERSON_TAGS_TABLE.' (tag_id, is_face_tag, source, example_image_id, updated_at)
VALUES ('.intval($tag_id).', 1, \'auto\', '.intval($image_id).', \''.$now.'\')
ON DUPLICATE KEY UPDATE
  example_image_id = VALUES(example_image_id),
  updated_at = VALUES(updated_at)
;');
        }
      }
      catch (\Throwable $e)
      {
        $unresolved_count++;
        $values[] = '('.intval($image_id).', \''.addslashes($name).'\', \''.$now.'\')';
        error_log('[face_tag] facetag_retry_and_record_unresolved : échec création tag pour "'.$name.'" (image '.$image_id.') : '.$e->getMessage());
      }
    }
  }

  if (!empty($values))
  {
    // ON DUPLICATE KEY UPDATE en filet de sécurité : si malgré la collation
    // utf8mb4_bin deux noms finissaient par entrer en collision sur la clé
    // (image_id, face_name), on ne veut jamais faire planter la synchro
    // pour une seule ligne.
    pwg_query('
INSERT INTO '.FACETAG_UNRESOLVED_FACES_TABLE.' (image_id, face_name, updated_at)
VALUES '.implode(',', $values).'
ON DUPLICATE KEY UPDATE updated_at = VALUES(updated_at)
;');
  }

  return $unresolved_count;
}

/**
 * Compare les tags Piwigo de chaque photo aux noms de visages présents dans
 * son XMP (mwg-rs / MPReg), et enregistre les tag_id correspondants dans la
 * table de correspondance FACETAG_PERSON_TAGS_TABLE.
 * Ne retire aucune ligne (le retrait est fait par face_tag_editor et tag_manager).
 *
 * Les visages détectés dans le XMP qui ne trouvent aucun tag Piwigo exact
 * correspondant sur leur photo sont enregistrés dans
 * FACETAG_UNRESOLVED_FACES_TABLE pour une correction manuelle via l'onglet
 * "Affichage des tags". Cette table est entièrement réinitialisée à chaque
 * appel, quel que soit le périmètre scanné.
 *
 * Traite toute la portée en une seule exécution PHP — sur une grosse
 * photothèque, préférer le point d'entrée AJAX chunké
 * (admin/tags_display_ajax.php), qui découpe le travail en petits lots pour
 * ne jamais risquer de timeout (504). Cette fonction reste disponible pour
 * un déclenchement externe (cron, URL) où un run unique est acceptable.
 *
 * @param int|null $cat_id Album sur lequel restreindre le scan (null = toute la photothèque)
 * @param bool $subcats_included Inclure les sous-albums de $cat_id
 * @return array ('images_scanned' => int, 'tags_found' => int, 'unresolved_count' => int)
 */
function facetag_sync_person_tags($cat_id = null, $subcats_included = true)
{
  facetag_ensure_sync_tables();
  pwg_query('TRUNCATE TABLE '.FACETAG_UNRESOLVED_FACES_TABLE.';');

  // Noms de tous les tags existants, en minuscule, indexés par id
  $all_tags = array();
  $result = pwg_query('SELECT id, name FROM '.TAGS_TABLE.';');
  while ($row = pwg_db_fetch_assoc($result))
  {
    $all_tags[$row['id']] = mb_strtolower(trim($row['name']));
  }

  $cat_ids = facetag_resolve_scope_cat_ids($cat_id, $subcats_included);

  // Photos de la portée choisie, avec leurs tags s'il y en a (LEFT JOIN : une
  // photo sans aucun tag Piwigo doit quand même être scannée, pour détecter
  // les visages du XMP qui n'ont alors aucune chance de correspondre à rien).
  $query = '
SELECT img.id, img.path, img.representative_ext, it.tag_id
  FROM '.IMAGES_TABLE.' AS img
  LEFT JOIN '.IMAGE_TAG_TABLE.' AS it ON it.image_id = img.id';
  if ($cat_ids !== null)
  {
    $query .= '
  WHERE img.id IN (
    SELECT DISTINCT image_id FROM '.IMAGE_CATEGORY_TABLE.'
    WHERE category_id IN ('.(count($cat_ids) > 0 ? implode(',', $cat_ids) : '0').')
  )';
  }
  $query .= '
;';
  $result = pwg_query($query);

  $tags_of_image = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    if (!isset($tags_of_image[$row['id']]))
    {
      $tags_of_image[$row['id']] = array(
        'path' => $row['path'],
        'representative_ext' => $row['representative_ext'],
        'tag_ids' => array(),
      );
    }
    if ($row['tag_id'] !== null)
    {
      $tags_of_image[$row['id']]['tag_ids'][] = $row['tag_id'];
    }
  }

  $found_tag_ids = array();
  $images_scanned = 0;
  $images_failed = 0;
  $now = date('Y-m-d H:i:s');
  $unresolved_by_image = array(); // image_id => ['face_names' => [...]], en attente de tentative de correction

  // Chaque photo est traitée dans son propre try/catch : pwg_query() peut
  // lever une mysqli_sql_exception non attrapée (mode d'erreur strict de
  // mysqli sur PHP 8.1+) qui, si elle n'est pas interceptée, stoppe tout le
  // script PHP immédiatement. Sans ce try/catch, UNE SEULE photo en erreur
  // arrêtait la synchro à ce point précis, sans jamais traiter les photos
  // suivantes.
  foreach ($tags_of_image as $image_id => $info)
  {
    $images_scanned++;

    try
    {
      $r = facetag_read_and_match_one_image($image_id, $info, $all_tags, $found_tag_ids, $now);
      if (!empty($r['unresolved_names']))
      {
        $unresolved_by_image[$image_id] = array('face_names' => $r['face_names']);
      }
    }
    catch (\Throwable $e)
    {
      $images_failed++;
      error_log('[face_tag] facetag_sync_person_tags : erreur sur image '.$image_id.' : '.$e->getMessage());
    }
  }

  // Une seule passe : les photos encore en discordance après la comparaison
  // initiale reçoivent immédiatement une tentative de correction groupée
  // (sync_metadata()) avant d'être enregistrées comme non résolues.
  $unresolved_count = facetag_retry_and_record_unresolved($unresolved_by_image, $all_tags, $found_tag_ids);

  return array(
    'images_scanned' => $images_scanned,
    'images_failed' => $images_failed,
    'tags_found' => count($found_tag_ids),
    'unresolved_count' => $unresolved_count,
  );
}
?>