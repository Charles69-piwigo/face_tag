<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

// Inclure les fonctions communes avec chemin relatif
include_once(dirname(__FILE__) . '/admin/functions.inc.php'); // fonction d'affichage des onglets

// Point d'entrée AJAX de la synchro chunkée : intercepté avant le routage
// par onglet, aucune sortie HTML n'a encore été produite à ce stade (voir
// admin.php racine de Piwigo, l'inclusion du plugin précède le rendu de la
// page). Le fichier envoie sa propre réponse JSON et fait exit lui-même.
if (isset($_GET['facetag_sync_start']) || isset($_GET['facetag_sync_chunk'])
 || isset($_GET['facetag_resync_start']) || isset($_GET['facetag_resync_chunk']))
{
  include(FACETAG_PATH . 'admin/tags_display_ajax.php');
  exit;
}

// Récupérer la page demandée
$page_name = isset($_GET['tab']) ? $_GET['tab'] : 'config'; /// définit l'onglet pré selectionné

// Charger la page correspondante
$page_path = FACETAG_PATH . 'admin/' . $page_name . '.php';

if (file_exists($page_path))
{
  include($page_path);
}
else
{
  // Par défaut, charger config.php
  include(FACETAG_PATH . 'admin/config.php');
}
?>