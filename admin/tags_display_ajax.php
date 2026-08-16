<?php
defined('FACETAG_PATH') or die('Hacking attempt!');

/**
 * Point d'entrée AJAX pour la synchronisation de la table des tags visage,
 * découpée en petits lots (même principe que le plugin AlbumPilot) : le
 * navigateur pilote une boucle d'appels courts, aucune requête HTTP
 * individuelle ne s'approche jamais d'un timeout (504 reverse proxy,
 * max_execution_time PHP...), quelle que soit la taille de la photothèque.
 *
 * Deux actions, appelées via GET :
 * - facetag_sync_start : compte les photos du périmètre demandé (pas de
 *   chargement de liste complète), réinitialise la table des visages non
 *   résolus, stocke juste le périmètre (pas la liste) en session.
 * - facetag_sync_chunk : va chercher directement en base le prochain petit
 *   lot (LIMIT/OFFSET) depuis le périmètre stocké en session, le traite, et
 *   répond avec la progression.
 *
 * Volontairement AUCUNE liste de photos n'est précalculée ni stockée en
 * session : sur un périmètre de plusieurs milliers de photos, cette
 * construction préalable en une seule requête pouvait à elle seule dépasser
 * une limite mémoire/temps du serveur (réponse vide, code 200, JSON
 * illisible côté navigateur) — exactement le problème qu'on cherchait à
 * éviter avec le découpage en lots.
 */

while (ob_get_level() > 0)
{
  ob_end_clean();
}
header('Content-Type: application/json; charset=utf-8');

// Filet de sécurité : si quelque chose de non prévu (y compris une erreur
// PHP fatale hors exception, ex: mémoire épuisée) empêche le script
// d'atteindre son echo json_encode normal, on répond quand même en JSON
// plutôt que de laisser une réponse vide que jQuery ne peut pas parser.
register_shutdown_function(function()
{
  $error = error_get_last();
  if ($error !== null && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR)))
  {
    if (!headers_sent())
    {
      header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode(array(
      'error' => 'fatal',
      'message' => $error['message'].' ('.basename($error['file']).':'.$error['line'].')',
    ));
  }
});

if (session_status() === PHP_SESSION_NONE)
{
  session_start();
}

define('FACETAG_SYNC_CHUNK_SIZE', 15);
define('FACETAG_SYNC_SESSION_KEY', 'facetag_sync_progress');

check_status(ACCESS_ADMINISTRATOR);

if (!isset($_GET['pwg_token']) || $_GET['pwg_token'] !== get_pwg_token())
{
  echo json_encode(array('error' => 'invalid_token'));
  exit;
}

/**
 * Construit la clause de portée (album + sous-albums éventuels) réutilisée
 * par le comptage et par chaque lot.
 *
 * @param int[]|null $cat_ids
 * @return string
 */
function facetag_ajax_scope_where($cat_ids)
{
  if ($cat_ids === null)
  {
    return '';
  }
  return '
  WHERE id IN (
    SELECT DISTINCT image_id FROM '.IMAGE_CATEGORY_TABLE.'
    WHERE category_id IN ('.(count($cat_ids) > 0 ? implode(',', $cat_ids) : '0').')
  )';
}

//  Démarrage : compte les photos du périmètre, réinitialise les discordances  ====================================

if (isset($_GET['facetag_sync_start']))
{
  try
  {
    facetag_ensure_sync_tables();
    pwg_query('TRUNCATE TABLE '.FACETAG_UNRESOLVED_FACES_TABLE.';');

    $cat_id = (isset($_GET['cat_id']) && is_numeric($_GET['cat_id'])) ? intval($_GET['cat_id']) : null;
    $subcats_included = isset($_GET['subcats_included']) && $_GET['subcats_included'] === '1';
    $cat_ids = facetag_resolve_scope_cat_ids($cat_id, $subcats_included);

    $count_row = pwg_db_fetch_assoc(pwg_query('
SELECT COUNT(*) AS nb
  FROM '.IMAGES_TABLE.
  facetag_ajax_scope_where($cat_ids).'
;'));
    $total = (int)$count_row['nb'];

    $_SESSION[FACETAG_SYNC_SESSION_KEY] = array(
      'cat_ids' => $cat_ids,
      'index' => 0,
      'total' => $total,
      'images_failed' => 0,
      'unresolved_count' => 0,
      'found_tag_ids' => array(),
    );

    echo json_encode(array(
      'done' => ($total === 0),
      'index' => 0,
      'total' => $total,
    ));
  }
  catch (\Throwable $e)
  {
    error_log('[face_tag] tags_display_ajax (start) : '.$e->getMessage());
    echo json_encode(array('error' => 'start_failed', 'message' => $e->getMessage()));
  }
  exit;
}

//  Traitement d'un lot  ====================================

if (isset($_GET['facetag_sync_chunk']))
{
  if (empty($_SESSION[FACETAG_SYNC_SESSION_KEY]))
  {
    echo json_encode(array('error' => 'no_progress'));
    exit;
  }

  $prog = &$_SESSION[FACETAG_SYNC_SESSION_KEY];

  try
  {
    $all_tags = array();
    $result = pwg_query('SELECT id, name FROM '.TAGS_TABLE.';');
    while ($row = pwg_db_fetch_assoc($result))
    {
      $all_tags[$row['id']] = mb_strtolower(trim($row['name']));
    }

    // Va chercher directement en base le prochain petit lot (pas de liste
    // précalculée en session).
    $batch_query = '
SELECT id, path, representative_ext
  FROM '.IMAGES_TABLE.
    facetag_ajax_scope_where($prog['cat_ids']).'
  ORDER BY id ASC
  LIMIT '.FACETAG_SYNC_CHUNK_SIZE.'
  OFFSET '.intval($prog['index']).'
;';
    $batch = array();
    $result = pwg_query($batch_query);
    while ($row = pwg_db_fetch_assoc($result))
    {
      $batch[] = $row;
    }

    $now = date('Y-m-d H:i:s');
    $last_path = '';
    $unresolved_by_image = array(); // en attente de tentative de correction groupée

    foreach ($batch as $item)
    {
      $image_id = (int)$item['id'];
      $last_path = $item['path'];

      $tag_ids = query2array('
SELECT tag_id FROM '.IMAGE_TAG_TABLE.' WHERE image_id = '.$image_id.';', null, 'tag_id');

      try
      {
        $r = facetag_read_and_match_one_image($image_id, array(
          'path' => $item['path'],
          'representative_ext' => $item['representative_ext'],
          'tag_ids' => $tag_ids,
        ), $all_tags, $prog['found_tag_ids'], $now);

        if (!empty($r['unresolved_names']))
        {
          $unresolved_by_image[$image_id] = array('face_names' => $r['face_names']);
        }
      }
      catch (\Throwable $e)
      {
        $prog['images_failed']++;
        error_log('[face_tag] tags_display_ajax (chunk) : erreur sur image '.$image_id.' : '.$e->getMessage());
      }

      $prog['index']++;
    }

    // Une seule passe : les photos encore en discordance dans CE lot
    // reçoivent immédiatement une tentative de correction groupée
    // (sync_metadata()) avant d'être enregistrées comme non résolues.
    $prog['unresolved_count'] += facetag_retry_and_record_unresolved($unresolved_by_image, $all_tags, $prog['found_tag_ids']);

    // Moins de lignes renvoyées que la taille du lot = on a atteint la fin
    // (couvre aussi le cas où des photos ont été supprimées entre-temps).
    $done = (count($batch) < FACETAG_SYNC_CHUNK_SIZE) || ($prog['index'] >= $prog['total']);

    $response = array(
      'done' => $done,
      'index' => $prog['index'],
      'total' => $prog['total'],
      'last_file' => basename($last_path),
      'unresolved_count' => $prog['unresolved_count'],
      'images_failed' => $prog['images_failed'],
    );

    if ($done)
    {
      $response['tags_found'] = count($prog['found_tag_ids']);
      $response['images_scanned'] = $prog['index'];
      unset($_SESSION[FACETAG_SYNC_SESSION_KEY]);
    }

    echo json_encode($response);
  }
  catch (\Throwable $e)
  {
    error_log('[face_tag] tags_display_ajax (chunk) : '.$e->getMessage());
    echo json_encode(array('error' => 'chunk_failed', 'message' => $e->getMessage()));
  }
  exit;
}

//  Resynchro ciblée des photos en discordance  ====================================
// Plutôt que de resynchroniser tout un album, on ne traite QUE les photos
// actuellement listées dans "Visages sans tag correspondant" : on relit
// leurs métadonnées via le sync_metadata() natif de Piwigo (qui fonctionne
// bien pour un nom simple sans tiret, cf. le cas "Sarah"), puis on réévalue
// juste ces photos avec facetag_read_and_match_one_image() pour mettre à
// jour la liste des discordances.
//
// Depuis que la synchro principale tente elle-même une correction
// automatique en une seule passe (facetag_retry_and_record_unresolved()),
// ce bouton sert surtout à retenter plus tard sans tout rescanner (ex:
// après une correction externe dans digiKam).
//
// Même architecture chunkée que la synchro complète, mais la file est fixée
// une fois pour toutes au démarrage (liste de simples image_id, légère même
// pour plusieurs milliers d'entrées) : contrairement à la synchro d'album,
// on ne peut pas repaginer avec LIMIT/OFFSET directement sur
// facetag_unresolved_faces, puisque les lignes résolues en disparaissent au
// fil du traitement (ça décalerait la pagination et sauterait des photos).

define('FACETAG_RESYNC_SESSION_KEY', 'facetag_resync_progress');

if (isset($_GET['facetag_resync_start']))
{
  try
  {
    facetag_ensure_sync_tables();

    $image_ids = query2array('SELECT DISTINCT image_id FROM '.FACETAG_UNRESOLVED_FACES_TABLE.' ORDER BY image_id ASC;', null, 'image_id');
    $image_ids = array_map('intval', $image_ids);

    $_SESSION[FACETAG_RESYNC_SESSION_KEY] = array(
      'queue' => $image_ids,
      'index' => 0,
      'total' => count($image_ids),
      'images_failed' => 0,
      'resolved_count' => 0,
      'found_tag_ids' => array(),
    );

    echo json_encode(array(
      'done' => (count($image_ids) === 0),
      'index' => 0,
      'total' => count($image_ids),
    ));
  }
  catch (\Throwable $e)
  {
    error_log('[face_tag] tags_display_ajax (resync start) : '.$e->getMessage());
    echo json_encode(array('error' => 'start_failed', 'message' => $e->getMessage()));
  }
  exit;
}

if (isset($_GET['facetag_resync_chunk']))
{
  if (empty($_SESSION[FACETAG_RESYNC_SESSION_KEY]))
  {
    echo json_encode(array('error' => 'no_progress'));
    exit;
  }

  $prog = &$_SESSION[FACETAG_RESYNC_SESSION_KEY];

  try
  {
    $chunk_ids = array_slice($prog['queue'], $prog['index'], FACETAG_SYNC_CHUNK_SIZE);

    $before_count = 0;
    $last_file = '';

    if (!empty($chunk_ids))
    {
      $before_row = pwg_db_fetch_assoc(pwg_query('
SELECT COUNT(*) AS nb FROM '.FACETAG_UNRESOLVED_FACES_TABLE.'
  WHERE image_id IN ('.implode(',', $chunk_ids).')
;'));
      $before_count = (int)$before_row['nb'];

      // On repart de zéro sur ces lignes : soit elles seront confirmées
      // résolues (rien à réinsérer), soit réévaluées ci-dessous.
      pwg_query('DELETE FROM '.FACETAG_UNRESOLVED_FACES_TABLE.' WHERE image_id IN ('.implode(',', $chunk_ids).');');

      $all_tags = array();
      $result = pwg_query('SELECT id, name FROM '.TAGS_TABLE.';');
      while ($row = pwg_db_fetch_assoc($result))
      {
        $all_tags[$row['id']] = mb_strtolower(trim($row['name']));
      }

      $infos = query2array('
SELECT id, path, representative_ext FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $chunk_ids).')
;', 'id');

      $now = date('Y-m-d H:i:s');
      $unresolved_by_image = array(); // en attente d'une tentative sync_metadata() de repli

      foreach ($chunk_ids as $image_id)
      {
        if (!isset($infos[$image_id]))
        {
          continue;
        }
        $last_file = $infos[$image_id]['path'];

        // Vérification simple d'abord : compare directement l'état actuel
        // (tags Piwigo tels quels vs. visages du XMP), sans rien modifier.
        // Couvre le cas où la correction a été faite autrement qu'en
        // relisant le fichier (ex: tag renommé/ajouté à la main dans
        // Piwigo) — un sync_metadata() systématique risquerait d'écraser
        // ce genre de correction manuelle.
        $tag_ids = query2array('
SELECT tag_id FROM '.IMAGE_TAG_TABLE.' WHERE image_id = '.intval($image_id).';', null, 'tag_id');

        try
        {
          $r = facetag_read_and_match_one_image($image_id, array(
            'path' => $infos[$image_id]['path'],
            'representative_ext' => $infos[$image_id]['representative_ext'],
            'tag_ids' => $tag_ids,
          ), $all_tags, $prog['found_tag_ids'], $now);

          if (!empty($r['unresolved_names']))
          {
            $unresolved_by_image[$image_id] = array('face_names' => $r['face_names']);
          }
        }
        catch (\Throwable $e)
        {
          $prog['images_failed']++;
          error_log('[face_tag] tags_display_ajax (resync chunk) : erreur sur image '.$image_id.' : '.$e->getMessage());
        }
      }

      // En dernier recours seulement, pour ce qui est encore en discordance
      // après la vérification simple : laisser Piwigo relire le fichier.
      facetag_retry_and_record_unresolved($unresolved_by_image, $all_tags, $prog['found_tag_ids']);

      $after_row = pwg_db_fetch_assoc(pwg_query('
SELECT COUNT(*) AS nb FROM '.FACETAG_UNRESOLVED_FACES_TABLE.'
  WHERE image_id IN ('.implode(',', $chunk_ids).')
;'));
      $prog['resolved_count'] += max(0, $before_count - (int)$after_row['nb']);
    }

    $prog['index'] += count($chunk_ids);
    $done = ($prog['index'] >= $prog['total']);

    $response = array(
      'done' => $done,
      'index' => $prog['index'],
      'total' => $prog['total'],
      'last_file' => basename($last_file),
      'resolved_count' => $prog['resolved_count'],
      'images_failed' => $prog['images_failed'],
    );

    if ($done)
    {
      $response['images_processed'] = $prog['index'];
      unset($_SESSION[FACETAG_RESYNC_SESSION_KEY]);
    }

    echo json_encode($response);
  }
  catch (\Throwable $e)
  {
    error_log('[face_tag] tags_display_ajax (resync chunk) : '.$e->getMessage());
    echo json_encode(array('error' => 'chunk_failed', 'message' => $e->getMessage()));
  }
  exit;
}

echo json_encode(array('error' => 'unknown_action'));
exit;
?>
