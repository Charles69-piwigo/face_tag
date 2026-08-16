<?php

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

// Installation du plugin avec les valeurs par défaut
function plugin_install()
{

}

// Activation du plugin
function plugin_activate($plugin_id, $plugin_version, &$errors)
{
    global $prefixeTable;

    pwg_query('
CREATE TABLE IF NOT EXISTS ' . $prefixeTable . 'facetag_person_tags (
  tag_id           INT UNSIGNED NOT NULL,
  is_face_tag      TINYINT(1)   NOT NULL DEFAULT 1,
  source           ENUM(\'auto\',\'manual\') NOT NULL DEFAULT \'auto\',
  example_image_id INT UNSIGNED NULL,
  updated_at       DATETIME NOT NULL,
  PRIMARY KEY (tag_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');

    pwg_query('
CREATE TABLE IF NOT EXISTS ' . $prefixeTable . 'facetag_unresolved_faces (
  image_id   INT UNSIGNED NOT NULL,
  face_name  VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (image_id, face_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
}

// Désinstallation du plugin
function plugin_uninstall()
{
    global $prefixeTable;

    conf_delete_param('face_tag_config');
    pwg_query('DROP TABLE IF EXISTS ' . $prefixeTable . 'facetag_person_tags');
    pwg_query('DROP TABLE IF EXISTS ' . $prefixeTable . 'facetag_unresolved_faces');
}

// Désactivation du plugin
function plugin_deactivate()
{

}

?>
