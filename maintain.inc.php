<?php

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

// Installation du plugin avec les valeurs par défaut
function plugin_install()
{
    
}

// Désinstallation du plugin
function plugin_uninstall()
{
    conf_delete_param('face_tag_config');
}

// Désactivation du plugin
function plugin_deactivate()
{

}

?>
